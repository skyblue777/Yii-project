<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Categories')=>array('admin'),
	Language::t(Yii::app()->language,'Backend.Common.Menu','Manage'),
);

$this->menu=array(
	array('label'=>Language::t(Yii::app()->language,'Backend.Admin.Category','Create Category'), 'url'=>array('create')),
);
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCoreScript('bbq');
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/scripts/jquery.ui.nestedSortable.js');
$script = "
var timer = null;
jQuery('#list-container').nestedSortable({
    disableNesting: 'no-nest',
    forcePlaceholderSize: true,
    handle: '.sort-handle',
    items: '.sortable',
    opacity: .6,
    placeholder: 'placeholder',
    tabSize: 25,
    tolerance: 'pointer',
    toleranceElement: '> table',
    'listType' : 'ul',
    update: function(event, ui){
        jQuery('#list-container').nestedSortable('disable');
        var serialized = jQuery('#list-container').nestedSortable('serialize');
        jQuery('.ordering-updated').remove();
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
        jQuery.get('".$this->createUrl('/Core/service/ajax', array('SID'=>'Core.Category.sort'))."', serialized, function(res){
            jQuery('#list-container').nestedSortable('enable');
            res = eval(res);
            if (res.errors != undefined && res.errors.length <= 0) {
                var message = jQuery('<div></div');
                message.addClass('ordering-updated')
                    .text(\"".Language::t(Yii::app()->language,'Backend.Admin.Category','The display order of your categories has been updated successfully.')."\");
                jQuery('.grid-view').before(message);
                timer = setTimeout(function(){jQuery('.ordering-updated').remove()}, 5000);
            } else {
                var message = 'Error';
                if (jQuery.isArray(res.errors.ErrorCode))
                    message = res.errors.ErrorCode.join('. ');
                alert(message);
            }
        });
    }
});
";
Yii::app()->clientScript->registerScript(__CLASS__.'#InitNestedSortable', $script, CClientScript::POS_READY);
$script = "
jQuery('.status-column a, .price-required-column a').click(function(){
    var _this = jQuery(this);
    var href = jQuery(this).attr('href');
    jQuery.get(href, function(res){
        res = eval(res);
        if (res.errors != undefined && res.errors.length <= 0) {
            //update class, value
            var params = jQuery.deparam.querystring(href);
            params.value = parseInt(params.value) == 1 ? 0 : 1;
            href = jQuery.param.querystring(href, {value : params.value});
            _this.attr('href', href);
            if (params.value == 0)
                _this.addClass('active');
            else
                _this.removeClass('active');
        } else {
            var message = 'Error';
            if (jQuery.isArray(res.errors.ErrorCode))
                message = res.errors.ErrorCode.join('. ');
            alert(message);
        }
    });
    return false;
});
jQuery('.paid-ad-column a').click(function(){
    var _this = jQuery(this);
    var href = jQuery(this).attr('href');
    jQuery.get(href, function(res){
        res = eval(res);
        if (res.errors != undefined && res.errors.length <= 0) {
            //update class, value
            var params = jQuery.deparam.querystring(href);
            params.value = parseInt(params.value) == 1 ? 0 : 1;
            href = jQuery.param.querystring(href, {value : params.value});
            _this.attr('href', href);
            if (params.value == 0)
                _this.addClass('active');
            else
                _this.removeClass('active');
                
            // update all childs
            var child_eles = _this.closest('li.sortable').find('ul');
            if (child_eles.length > 0)
            {
                child_eles.find('li.sortable').each(function(){
                    var lnk = $(this).find('.paid-ad-column a');
                    var url = lnk.attr('href');
                    url = jQuery.param.querystring(url, {value : params.value});
                    lnk.attr('href', url);
                    if (params.value == 0)
                        lnk.addClass('active');
                    else
                        lnk.removeClass('active');
                });
            }
        } else {
            var message = 'Error';
            if (jQuery.isArray(res.errors.ErrorCode))
                message = res.errors.ErrorCode.join('. ');
            alert(message);
        }
    });
    return false;
});
jQuery('.show-banner-column a').click(function(){
    var _this = jQuery(this);
    var href = jQuery(this).attr('href');
    jQuery.get(href, function(res){
        res = eval(res);
        if (res.errors != undefined && res.errors.length <= 0) {
            //update class, value
            var params = jQuery.deparam.querystring(href);
            params.value = parseInt(params.value) == 1 ? 0 : 1;
            href = jQuery.param.querystring(href, {value : params.value});
            _this.attr('href', href);
            if (params.value == 0)
                _this.addClass('active');
            else
                _this.removeClass('active');
                
            // update all childs
            var child_eles = _this.closest('li.sortable').find('ul');
            if (child_eles.length > 0)
            {
                child_eles.find('li.sortable').each(function(){
                    var lnk = $(this).find('.show-banner-column a');
                    var url = lnk.attr('href');
                    url = jQuery.param.querystring(url, {value : params.value});
                    lnk.attr('href', url);
                    if (params.value == 0)
                        lnk.addClass('active');
                    else
                        lnk.removeClass('active');
                });
            }
        } else {
            var message = 'Error';
            if (jQuery.isArray(res.errors.ErrorCode))
                message = res.errors.ErrorCode.join('. ');
            alert(message);
        }
    });
    return false;
});
jQuery('.actions-column a.delete').click(function(){
    var _this = jQuery(this);
    if (!confirm(\"".Language::t(Yii::app()->language,'Backend.Admin.Category','Are you sure you want to delete this category ?')."\"))
        return false;
    jQuery.get(jQuery(this).attr('href'), function(res){
        res = eval(res);
        if (res.errors != undefined && res.errors.length <= 0) {
            _this.closest('li').remove();
        } else {
            var message = 'Error';
            if (jQuery.isArray(res.errors.ErrorCode))
                message = res.errors.ErrorCode.join('. ');
            alert(message);
        }
    });
    return false;
});
jQuery('.check-all').click(function(){
    jQuery('.checkbox-column input').attr('checked', jQuery(this).attr('checked'));
});
jQuery('.checkbox-column input').click(function(){
    if (!jQuery(this).attr('checked'))
        jQuery('.check-all').attr('checked', false);
});
jQuery('.delete-multi').click(function(){
    var data = [];
    jQuery.each(jQuery('.checkbox-column input:checked'), function(){
        data.push('ids[]='+jQuery(this).val());
    });
    if (data.length <= 0) {
        alert(\"".htmlspecialchars(Language::t(Yii::app()->language,'Backend.Admin.Category','Please select at least one category'))."\");
        return false;
    }
    
    data = data.join('&');
    jQuery.get(\"".$this->createUrl('/Core/service/ajax', array('SID'=>'Core.Category.delete'))."\", data, function(res){
        res = eval(res);
        if (res.errors != undefined && res.errors.length <= 0) {
            //success
        } else {
            var message = 'Error';
            if (jQuery.isArray(res.errors.ErrorCode))
                message = res.errors.ErrorCode.join('. ');
            alert(message);
        }
        location = '".$this->createUrl('/Admin/category/admin')."';
    });
    return false;
});
";
Yii::app()->clientScript->registerScript(__CLASS__.'#Actions', $script, CClientScript::POS_READY);
?>

<h1><?php echo Language::t(Yii::app()->language,'Backend.Admin.Category','Manage Categories')?></h1>
<style type="text/css">
.grid-view ul {padding: 0; display: block; margin: 0;}
.grid-view ul li {list-style: none;}
.grid-view .sort-handle {cursor: move;}
.grid-view table.items {border-bottom: 1px solid #FFFFFF;}
#list-container ul {padding-left: 20px;}
.ordering-updated {background: url(<?php echo Yii::app()->theme->baseUrl;?>/images/notifications.gif) 10px center #EEEEEE no-repeat; padding: 5px 0 5px 30px; margin: 10px 0;}
.placeholder {background: #DBDBDB; border-bottom: 1px solid #FFFFFF;}
</style>

<div class="grid-view">
    <table class="items">
        <thead>
            <tr>
                <th align="center" width="20"><?php echo CHtml::checkBox('check_all', false, array('class'=>'check-all'));?></th>
                <th align="left"><?php echo Language::t(Yii::app()->language,'Backend.Admin.Category','Category Name')?></th>
                <th style="width: 25px;"><?php echo Language::t(Yii::app()->language,'Backend.Admin.Category','Price required')?></th>
                <th style="width: 25px;"><?php echo Language::t(Yii::app()->language,'Backend.Admin.Category','Paid ads')?></th>
                <th width="100"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Action')?></th>
            </tr>
        </thead>
    </table>
<?php if (count($models)):?>
    <ul id="list-container">
        <?php $this->renderNestedCategory(dirname(__FILE__).DIRECTORY_SEPARATOR.'_item.php', $models);?>
    </ul>
    <?php
    $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.Common.Common','Delete selected items'), 'url'=>$this->createUrl('delete'), 'linkOptions' => array('class'=>'delete-multi'));
    ?>
<?php else:?>
    <table class="items">
        <tr class="odd">
            <td colspan="4"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','No results found.');?></td>
        </tr>
    </table>
<?php endif;?>
</div>