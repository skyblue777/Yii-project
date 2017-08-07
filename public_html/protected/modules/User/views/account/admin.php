<style type="text/css">
.grid-view { padding: 0; }
.span-6 { clear: both; margin-left: 140px; }
.user-sections .user-panel { float: left; width: 140px; }
.user-sections .user-list { float: left; width: 780px; }
.user-panel ul.user-filters li a { text-decoration: none; }
.user-panel ul.user-filters li a:hover { color: #f00; }
.user-panel ul.user-filters li a.active { color: #f00; }
.grid-view .pager { margin: 9px 0 0; }
</style>

<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Backend.Common.Menu','Users')=>array('index'),
    Language::t(Yii::app()->language,'Backend.Common.Menu','Manage'),
);
?>

<div class="user-sections">
    <div class="user-panel">
        <h1 style="margin-bottom: 2px;"><?php echo Language::t(Yii::app()->language,'Backend.Common.Menu','Users')?></h1>
        <ul class="user-filters">
            <li><a class="active lnk-filter-user" id="lnk-filter-user" href="#"><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','All')?></a></li>
            <li><a class="lnk-filter-user" id="lnk-filter-user-1" href="#"><?php echo Language::t(Yii::app()->language,'Backend.User.Admin','Registered')?></a></li>
            <li><a class="lnk-filter-user" id="lnk-filter-user-0" href="#"><?php echo Language::t(Yii::app()->language,'Backend.User.Admin','Not Registered')?></a></li>
        </ul>
        <div>
            <input type="button" id="btnExportUserList" value="<?php echo Language::t(Yii::app()->language,'Backend.User.Admin','Export Users List')?>" />
        </div>
    </div>
    <div class="user-list">
        <h1 style="margin-bottom: 2px;"><?php echo Language::t(Yii::app()->language,'Backend.User.Admin','Manage Users')?></h1>
        <?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	        'id'=>'user-grid',
	        'dataProvider'=>$model->search(),
            'selectableRows'=>2,
            'selectionChanged'=>"updateSelectors",
            'afterAjaxUpdate'=>'afterAjaxUpdateGrid',
	        'columns'=>array(
                array(
                    'class'=>'CCheckBoxColumn',
                    'value'=>'$data->id',
                    'htmlOptions'=>array('width'=>'3%'),
                ),
		        array(
                    'name'=>'email',
                    'type'=>'raw',
                    'value'=>'$data->email',
                ),
                array(
                    'name'=>'first_name',
                    'type'=>'raw',
                    'value'=>'$data->first_name',
                ),
                array(
                    'name'=>'last_name',
                    'type'=>'raw',
                    'value'=>'$data->last_name',
                ),
                array(
                    'name'=>'createdDate',
                    'class' => 'application.modules.Core.components.CDatePickerColumn',
                    'value'=>'(!empty($data->created_date) && $data->created_date != "0000-00-00") ? date("m/d/Y",strtotime($data->created_date)) : ""',
                ),
                array(
                    'header'=>'',
                    'type'=>'raw',
                    'value'=>'$data->getActionsButtonColumn()',
                    'htmlOptions'=>array('style'=>'text-align: center;'), 
                ),
	        ),
        )); 

        if ($grid->dataProvider->ItemCount) {
            $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.User.ListAdmin','Delete selected items'), 'url'=>$this->createUrl('delete'), 'linkOptions' => array('onclick'=>'return multipleDelete("user-grid",this.href)'));
        }
        Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);
        ?>
    </div>
</div>

<script type="text/javascript">
function afterAjaxUpdateGrid()
{
    var lnk = $('ul.user-filters li a.active');
    var status = '';
    if (lnk.attr('id') != 'lnk-filter-user')
        status = lnk.attr('id').replace('lnk-filter-user-','');
    $('#user-grid thead tr.filters td:first').append('<input type="hidden" name="User[status]" value="'+status+'" />');
}
afterAjaxUpdateGrid();

$('ul.user-filters li a.lnk-filter-user').click(function(){
    $('ul.user-filters li a.lnk-filter-user').removeClass('active');
    $(this).addClass('active');
    var status = '';
    if ($(this).attr('id') != 'lnk-filter-user')
        status = $(this).attr('id').replace('lnk-filter-user-','');
    $('#user-grid div.keys').attr('title',baseUrl+'/index.php?r=User/account/admin');
    $.fn.yiiGridView.update('user-grid', { data : { 'User[status]' : status } });
    
    return false;    
});

$('#btnExportUserList').click(function(){
    var lnk = $('ul.user-filters li a.active');
    var status = '';
    if (lnk.attr('id') != 'lnk-filter-user')
        status = lnk.attr('id').replace('lnk-filter-user-','');
    window.location.href = baseUrl + '/index.php?r=User/account/exportUserList&status='+status+'&role=user';    
});

$('#user-grid table.items tbody tr td a.delete-user, #user-grid table.items tbody tr td a.restore-user').live('click',function(){
    var user_id = $(this).attr('href');
    var type = 'delete';
    var msgConfirm = '<?php echo  Language::t(Yii::app()->language,'Backend.User.ListAdmin','Do you want to delete this user?')?>';
    if ($(this).hasClass('restore-user'))
    {
        type = 'restore';
        msgConfirm = '<?php echo  Language::t(Yii::app()->language,'Backend.User.ListAdmin','Do you want to register this user?')?>';
    }
    
    if (!confirm(msgConfirm)) return false;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'User.User.activeOrDeactivateUser',
            'user_id' : user_id,
            'type' : type
        },
        'success' : function(json) {
            $.fn.yiiGridView.update('user-grid');
        }
    });
    
    return false;
});

$('#user-grid table.items tbody tr td a.ban-user, #user-grid table.items tbody tr td a.unban-user').live('click',function(){
    var user_id = $(this).attr('href');
    var type = 'ban';
    var msgConfirm = '<?php echo  Language::t(Yii::app()->language,'Backend.User.ListAdmin','Do you want to ban this user?')?>';
    if ($(this).hasClass('unban-user'))
    {
        type = 'unban';
        msgConfirm = '<?php echo  Language::t(Yii::app()->language,'Backend.User.ListAdmin','Do you want to unban this user?')?>';
    }
    
    if (!confirm(msgConfirm)) return false;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'User.User.banOrUnbanUser',
            'user_id' : user_id,
            'type' : type
        },
        'success' : function(json) {
            var result = eval(json);
            if (result.errors.ErrorCode)
            {
                var error = '';
                for(var i in result.errors.ErrorCode)
                    error += result.errors.ErrorCode[i] + "\n\n";
                alert(error);
            }
            $.fn.yiiGridView.update('user-grid');
        }
    });
    
    return false;
});
</script>