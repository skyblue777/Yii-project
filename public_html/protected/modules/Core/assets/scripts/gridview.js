/**
* Delete multiple selected rows
*/
function multipleDelete(gridId, href, confirmMessage){
    if (!confirmMessage) confirmMessage = 'Are you sure ?';
    
    ids = $.fn.yiiGridView.getSelection(gridId);
    if (ids.length < 1) {alert('No item seleted.'); return false;}
    if (confirm(confirmMessage)) {
        ids = $.fn.yiiGridView.getSelection(gridId);
        $.post(
            href+'&id='+ids.join(','),
            {},
            function(msg){
                if (msg != '') alert(msg);
                $.fn.yiiGridView.update(gridId);
            }
        );
    }
    return false;
}

/**
* Update checkboxes (class='selector') when row selection status changes
*/
function updateSelectors(gridId) {
    $('#'+gridId+' table.items tr').each(function(){
        cb=$(this).find('td:first :checkbox').get(0);
        if (cb)
            cb.checked=$(this).hasClass('selected');
    })
}