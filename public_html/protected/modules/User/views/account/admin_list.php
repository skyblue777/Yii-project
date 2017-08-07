<style type="text/css">
.grid-view { padding: 0; }
.user-sections .user-list { width: 920px; }
.user-panel ul.user-filters li a { text-decoration: none; }
.user-panel ul.user-filters li a:hover { color: #f00; }
.user-panel ul.user-filters li a.active { color: #f00; }
.grid-view .pager { margin: 9px 0 0; }
</style>

<?php
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Backend.Common.Menu','Settings')=>array('index'),
    Language::t(Yii::app()->language,'Backend.User.ListAdmin','Manage administrators'),
);
$this->menu=array(
    array('label'=>Language::t(Yii::app()->language,'Backend.User.ListAdmin','Add an admin'), 'url'=>array('createAdmin')),
);
?>

<div class="user-sections">
    <div class="user-list">
        <h1 style="margin-bottom: 2px;"><?php echo Language::t(Yii::app()->language,'Backend.User.ListAdmin','Manage administrators')?></h1>
        <?php $grid = $this->widget('zii.widgets.grid.CGridView', array(
	        'id'=>'user-grid',
	        'dataProvider'=>$model->search(TRUE),
            'selectableRows'=>2,
            'selectionChanged'=>"updateSelectors",
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
                    'class'=>'CButtonColumn',
                    'htmlOptions'=>array('width'=>'5%','style'=>'text-align: center;'),
                    'template'=>'{delete-user} {view-user-ads}',
                    'buttons'=>array(
                        'delete-user'=>array(
                            'label'=>Language::t(Yii::app()->language,'Backend.User.ListAdmin','Delete admin'),
                            'url'=>'$data->id',
                            'imageUrl'=>themeUrl().'/images/buttons/delete.png',
                            'options'=>array('class'=>'delete-user')
                        ),
                        'view-user-ads'=>array(
                            'label'=>Language::t(Yii::app()->language,'Backend.User.ListAdmin','View admin ads'),
                            'imageUrl'=>themeUrl().'/images/buttons/view.png',
                            'url'=>'Yii::app()->controller->createUrl("/Ads/Ads/list",array("Annonce[email]"=>$data->email,"Annonce_sort"=>"create_time.desc"))',
                        ),
                    )
                ),
	        ),
        )); 

        if ($grid->dataProvider->ItemCount) {
            $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.Common.Common','Delete selected items'), 'url'=>$this->createUrl('delete'), 'linkOptions' => array('onclick'=>'return multipleDelete("user-grid",this.href)'));
        }
        Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);
        ?>
    </div>
</div>

<div style="clear: both; margin-top: 10px;"><input type="button" id="btnExportUserList" value="<?php echo Language::t(Yii::app()->language,'Backend.User.ListAdmin','Export Admins List')?>" /></div> 

<script type="text/javascript">
$('#btnExportUserList').click(function(){
    window.location.href = baseUrl + '/index.php?r=User/account/exportUserList&status=1&role=admin';    
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
</script>