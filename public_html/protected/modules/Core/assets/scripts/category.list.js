$(document).ready(function(){
    $('A.CategoryHandler').bind('click', onHandlerClick);

    $('a.editCat').bind('click', editCategory);
    $('a.deleteCat').bind('click', deleteCategory);

    $('#categoryList').sortable({
        cursor: 'ns-resize',
        items: '.CategoryRow',
        helper: fixHelper,
        start: function(event, ui){
                    collapseRow(ui.item);
               },
        stop: updateCategoryPosition
    });
    
    restoreState();
});

//Padding (in pixel) for one category level
var LEVELPADDING = 30;

//Get category level by cat ID
function categoryLevel(catId){
    if (catId == 'Cat0') return -1;
    var indent = parseInt($('#'+catId+' > td:eq(0)').css('padding-left'));
    if (indent < LEVELPADDING)
        return 0;
    else
        return (indent-5)/LEVELPADDING;
}

//Get category Id of a category given its HTML row
function categoryIdFromRow(row){
    if (!row.attr('id')) return 0;
    return row.attr('id').substr(3);
}

//Update category total children as a result of moving or deleting sub category
function updateChildrenCount(catId, added){
    var total = parseInt($('#'+catId+' > td:eq(1)').html());
    if (added == true){
        total ++;
    }else{
        total --;
    }

    if (total == 0)
        var handler = $('#'+catId+' > td:eq(1) > a').hide();

    if (total == 1 && added== true){
        $('#'+catId+' > td:eq(0) > a').html('<img src="'+themeUrl+'/images/ico-arrow-3.gif" />');
        $('#'+catId+' > td:eq(0) > a').show();
    }

    $('#'+catId+' > td:eq(1)').html(total);
}

//Show all direct children of a category given its HTML row
function expandRow(row){
    var handler = row.find('td:eq(0) a');
    //Expand children change hanlder arrow
    handler.html('<img src="'+themeUrl+'/images/ico-arrow-3.gif" />');
    $('TR[rel='+row.attr('id')+']').show();
    row.removeClass('collapsed').addClass('expanded');
}

//Hide all descendant of a category given its HTML row
function collapseRow(row){
    if (!row.hasClass('expanded')) return;
    //Collapse children and change hanlder arrow
    $('TR[rel='+row.attr('id')+']').each(function(i, elm){
        if ($(elm).hasClass('expanded'))
            collapseRow($(elm));
    });

    var handler = row.find('td:eq(0) a');
    handler.html('<img src="'+themeUrl+'/images/ico-arrow-2.gif" />');
    $('TR[rel='+row.attr('id')+']').hide();
    row.removeClass('expanded').addClass('collapsed');
}

//Show or hide children of a category
function toggleCategory(catId){
    var row = $('#'+catId);
    //The category 's children are not populated yet
    if (!row.hasClass('expanded') && !row.hasClass('collapsed')) {
        updateState(row, false);
        return false;
    }

    if(row.hasClass('expanded')){
        collapseRow(row);
    } else if(row.hasClass('collapsed')){
        expandRow(row);
    }
    updateState(row, true);
    return true;
}

function onHandlerClick(event, restore){
    var catId = $(this).parent().parent().attr('id');
    if (toggleCategory(catId))
        return false;

    $.ajax({
        type: 'post',
        url: baseUrl +'/index.php?r=Core/service/ajax',
        data: 'SID=Core.Category.findByParentId&SIMPLIFIED=1&includeStat=1&skipDescrition=1&parentId='+catId.substr(3),
        success: function(json){
            result = eval(json);
            level = categoryLevel(catId);
            addChildCategories(catId, result.categories, level + 1);
            toggleCategory(catId);
            if (restore == 'restore')
                restoreState();
        }
    });
    return false;
}

function addChildCategories(parentId, categories, level){
    var parent = $('#'+parentId);

    for(var i = categories.length-1; i>=0; i--){
        var cat = categories[i];

        var handler = ' <a href="#" class="CategoryHandler" style="display: none"><img src="'+themeUrl+'/images/ico-arrow-2.gif" /></a>';
        if (cat.CountChildren > 0){
            handler = ' <a href="#" class="CategoryHandler"><img src="'+themeUrl+'/images/ico-arrow-2.gif" /></a>';
        }
        var title = $('<td class="CategoryTitle LeftAlign" style="padding-left:' + (5+level*LEVELPADDING) + 'px;">'+cat.Title+handler+'</td>');
        var countChildren = $('<td>'+cat.CountChildren+'</td>');
//        var countArticles = $('<td>'+cat.CountArticles+'</td>');

//        var order = $('<td>'+(parseInt(cat.Ordering) + 1)+'</td>');
//        var moveUp = $('<a href="#" class="moveUp"><img alt="icon up" src="'+themeUrl+'/images/ico-up.gif"/></a>');
//        var moveDown = $('<a href="#" class="moveDown"><img alt="icon down" src="'+themeUrl+'/images/ico-down.gif"/></a>');

        var editCat = $('<a href="#" class="moveEdit"><img alt="icon edit" src="'+themeUrl+'/images/ico-edit.gif"/></a>');
        editCat.bind('click',editCategory);

        var deleteCat = $('<a href="#" class="moveDelete"><img alt="icon delete" src="'+themeUrl+'/images/ico-delete.gif"/></a>');
        deleteCat.bind('click', deleteCategory);

//        var actions = $('<td />').append(moveUp).append(moveDown).append(editCat).append(deleteCat);
        var actions = $('<td />').append(editCat).append(deleteCat);

        var row = $('<tr rel="'+parentId+'" id="Cat'+cat.Id+'" class="CategoryRow" />');
        row.append(title).append(countChildren).append(actions);

//        row.hide();

        parent.after(row);
        row.find('.CategoryHandler').bind('click', onHandlerClick);
    }
    parent.addClass('collapsed');
}

// Handle sort stop event to update category new position
function updateCategoryPosition(event, ui){
    var prev = ui.item.prev(':visible');
    var prevId = categoryIdFromRow(prev);
    var prevLevel = categoryLevel('Cat' + prevId);

    var currentId = categoryIdFromRow(ui.item);
    var curLevel = categoryLevel('Cat' + currentId);

    var next = ui.item.next(':visible');
    var nextId = categoryIdFromRow(next);
    var nextLevel = categoryLevel('Cat' + nextId);

    $.ajax({
        type: 'post',
        url: baseUrl +'/index.php?r=Core/service/ajax',
        data: 'SID=Core.Category.reorder&SIMPLIFIED=1&prevId='+prevId+
                '&curId='+currentId+'&prevLevel='+prevLevel+
                '&curLevel='+curLevel+'&nextId='+nextId+
                '&nextLevel='+nextLevel,
        success: function(json){
            moveSubCategories(ui.item.attr('id'));
            result = eval(json);

            if ('Cat'+result.parent != ui.item.attr('rel')){
                //Move from one parent to another
                updateChildrenCount(ui.item.attr('rel'), false);
                updateChildrenCount('Cat'+result.parent, true);
            }

            if (curLevel != result.level)
                fixLevel(ui.item, result.level);

            moveSubCategories(ui.item.prev(':visible').attr('id'));
        }
    })
}

function moveSubCategories(parentId){
    var parent = $('#'+parentId);
    var children = $('TR[rel='+parentId+']');
    for(var i = children.length-1; i>=0; i--){
        var currentRow = $(children[i]);
        parent.after(currentRow.clone(true));
        currentRow.remove();
        moveSubCategories(currentRow.attr('id'));
    }
}

//Preserved width of cells while ui.sortable moves the row
function fixHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};

//Fix category title padding left to review its new level in the tree
function fixLevel(row, level, updateRelation){
    row.find('td:eq(0)').css('padding-left',level * LEVELPADDING + 5);
    if (updateRelation != 0){
        if (level == 0)
            row.attr('rel','');
        else{
            var prevRow = row.prev();
            var prevLevel = categoryLevel(prevRow.attr('id'));
            while (prevLevel > level){
                prevRow = $('#'+prevRow.attr('rel'));
                prevLevel = categoryLevel(prevRow.attr('id'));
            }

            if (prevLevel == level)
                row.attr('rel', prevRow.attr('rel'));
            if (prevLevel < level)
                row.attr('rel', prevRow.attr('id'));
        }
    }

    $('tr[rel='+row.attr('id')+']').each(function(index, elm){
        fixLevel($(elm), level + 1, 0);
    });
}

//Go to Edit category page
function editCategory(){
    var id = categoryIdFromRow($(this).parent().parent());

    if(rootCategroyParam != '' && module != '')
        location = baseUrl + '/index.php?r=Core/category/edit&categoryId='+id+'&module='+module+'&param='+rootCategroyParam;
    else if(module != '')
        location = baseUrl + '/index.php?r=Core/category/edit&categoryId='+id+'&module='+module;
    else
        location = baseUrl + '/index.php?r=Core/category/edit&categoryId='+id;

}
//Ajax delete one category
function deleteCategory(){
    if (!confirm("Are you sure ?")) return false;
    var id = categoryIdFromRow($(this).parent().parent());
    var tr = $(this).parent().parent();
    $.ajax({
        type: 'post',
        url: baseUrl +'/index.php?r=Core/category/delete',
        data: 'SID=Cms.Category.delete&categoryId='+id,
        success: function(json){
            result = eval(json);
            if(result.errors.length == 0){
                alert("Category is deleted.");
                updateChildrenCount(tr.attr('rel'), false);
                $(tr).remove();
            }else{
                var error = '';
                for(var i in result.errors)
                    error += result.errors[i]+'\n';
                alert(error);
            }
        }
    });
    
    return false;
}


function checkBullAction(elm) {
    if($("#bulkAction").val()=='') {
        alert("Please select one action.");
        return false;
    }
    else if($("#bulkAction").val() == '1') {
        return deleteAllClicked(elm, 'selectedItem');
    }
}

function deleteAllClicked(elm, className)
{
	// Get selected items
	var listCategoryChecked = getCheckedItemIds(className);
	if (listCategoryChecked == '')
	{
		alert('Please select at least 1 category.');
	}
	else
	{
	    if (confirm('Are you sure to delete ?') == true) {
			// Delete by Ajax
	        $.ajax({
	            type: 'post',
	            url: baseUrl +'/service.php',
	            data: 'SID=Cms.Category.deleteAll&categoryIds='+listCategoryChecked,
	            success: function(json){
	                result = eval(json);
	                if(result.ReturnCode == 'R00'){
	                    for(index=0; index<listCategoryChecked.length; index++)
	                    {
	                    	$('#Cat'+listCategoryChecked[index]).remove();
	                        //$('#'+className+listCategoryChecked[index]).parent().parent().remove();
	                    }

	                }else{
	                    var error = '';
	                    for(var i in result.ErrorMessages)
	                        error += result.ErrorMessages[i]+'\n';
	                    alert(error);
	                }
	            }
	        });
	       //jQuery.yii.submitForm(elm,'index.php?r=KnowledgeBase/admin/category/bulk',{});
	    }
    }

    return false;
}

var catOpened = [];
var catOpen = new Array();
var categoryState = $.cookie('categoryState')+'';
if (categoryState != 'null')
    catOpen = categoryState.split(',');

function restoreState() {
    if (catOpen.length > 0) {
        for (var i in catOpen) {
            if ($.inArray(catOpen[i], catOpened) === -1) {
                var el = $('#'+catOpen[i]);
                if (el.length > 0) {
                    catOpened.push(catOpen[i]);
                    $('.CategoryHandler', el).trigger('click', ['restore']);
                    break;
                }
            }
        }
    }
}

function updateState(el, hasContent) {
    if (el.hasClass('expanded')){
        if ($.inArray(el.attr('id'), catOpen) === -1)
            catOpen.push(el.attr('id'));//add new
        //open all childs of item just clicked
        openChilds(el, hasContent);
    }
    else if (el.hasClass('expanded') == false && $.inArray(el.attr('id'), catOpen) !== -1) {
        catOpen = $.grep(catOpen, function(item){return item != el.attr('id');});//remove
        closeChilds(el, hasContent);
    }
    
    
    //save state to cookie
    var categoryState = '';
    if (catOpen.length > 0)
        categoryState = catOpen.join(',');
    $.cookie('categoryState', categoryState);
}

function openChilds(el, hasContent)
{
    $('TR[rel='+el.attr('id')+']').each(function(){
        var elm = $(this);
        if ($.inArray(elm.attr('id'), catOpen) !== -1 && elm.hasClass('expanded') == false){
            if (hasContent)
                $('.CategoryHandler', elm).trigger('click');
            else
                $('.CategoryHandler', elm).trigger('click', ['restore']);
        }
    });
}

function closeChilds(el, hasContent)
{
    $('TR[rel='+el.attr('id')+']').each(function(){
        var elm = $(this);
        if (elm.hasClass('expanded') == true){
            if (hasContent)
                $('.CategoryHandler', elm).trigger('click');
            else
                $('.CategoryHandler', elm).trigger('click', ['restore']);
        }
    });
}