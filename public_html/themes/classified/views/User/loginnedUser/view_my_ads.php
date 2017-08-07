<?php
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
$this->breadcrumbs=array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','My account')
);
$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.User.Login','My ads');
?>

<div class="link"> 
<span class="active">
<a href="<?php echo $this->createUrl('/User/loginnedUser/viewMyAds'); ?>">
<?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My ads')?>
</a> 
</span> 
<span>|</span> 
<a href="<?php echo $this->createUrl('/User/loginnedUser/viewMyFavoriteAds'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My Favorites')?></a> 
<span>|</span> 
<a href="<?php echo $this->createUrl('/User/loginnedUser/myProfile'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My Profile')?></a>
</div>
<h1 class="title-3"><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','My ads')?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'ads-grid',
    'dataProvider'=>$dataProvider,
    'cssFile'=>themeUrl().'/styles/ad_grid.css',
    'template'=>"{items}\n{pager}",
    'columns'=>array(
        array(
            'filter'=>false,
            'header'=>Language::t(Yii::app()->language,'Backend.Common.Common','Title'),
            'type'=>'raw',
            'value'=>'CHtml::link($data->title,Yii::app()->createUrl("/Ads/ad/viewDetails",array("id"=>$data->id,"alias"=>str_replace(array(" ","/","\\\"),"-",$data->title),"area"=>$data->area)),array("target"=>"_blank"))',
            'htmlOptions' => array('width'=>'50%','class'=>'ad-title-column'),
        ),        
        array(
            'header'=>Language::t(Yii::app()->language,'Frontend.User.Login','Visits'),
            'filter' => false,
            'type'=>'raw',
            'value'=>'$data->viewed',
            'htmlOptions' => array('width'=>'10%'),
        ),
        array(
            'header'=>Language::t(Yii::app()->language,'Frontend.User.Login','Posted'),
            'type'=>'raw',
            'value'=>'(!empty($data->create_time) && $data->create_time != "0000-00-00 00:00:00") ? date("F m, Y",strtotime($data->create_time)) : ""',
            'htmlOptions' => array('width'=>'20%'),
        ),
        array(
            'header'=>Language::t(Yii::app()->language,'Backend.Common.Common','Price'),
            'type'=>'raw',
            'value'=>'$data->getPriceSection()',
            'htmlOptions' => array('width'=>'10%'),
        ),
        array(
            'class'=>'CButtonColumn',
            'htmlOptions'=>array('style'=>'text-align: center;','width'=>'10%'),
            'header'=>Language::t(Yii::app()->language,'Frontend.User.Login','Options'),
            'template'=>'{delete-ad} {edit-ad}',
            'buttons'=>array(
                'delete-ad'=>array(
                    'label'=>Language::t(Yii::app()->language,'Backend.Common.Common','Delete'),
                    'url'=>'$data->id',
                    'imageUrl'=>baseUrl().'/images/button-del-bg.gif',
                    'options'=>array('class'=>'delete-ad')
                ),
                'edit-ad'=>array(
                    'label'=>Language::t(Yii::app()->language,'Frontend.Ads.Common','Edit ad'),
                    'url'=>'Yii::app()->createUrl("/Ads/ad/update",array("id"=>$data->id,"alias"=>str_replace(array(" ","/","\\\"),"-",$data->title)))',
                    'imageUrl'=>baseUrl().'/images/button-edit-bg.gif',
                    'options'=>array('class'=>'edit-ad')
                ),
            )
        ),
    ),
));
?>

<script type="text/javascript">
$('table.items tr td a.delete-ad').live('click',function(){
    var ad_id = $(this).attr('href');
    var currentRow = $(this).parent().parent();
    var ad_title = currentRow.find('td.ad-title-column').html();            
    if ($('table.items tbody tr#ask-del-ad-'+ad_id).length <= 0)
    {
        var rowConfirmDelete = $("<tr id='ask-del-ad-"+ad_id+"' class='row-ask-delete-ad'><td colspan='5' style='border: none; text-align: right;'><b><?php echo Language::t(Yii::app()->language,'Frontend.User.Login','Do you want to delete the ad')?>: "+ad_title+" ? <a href='#' class='agree-delete'><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Yes')?></a> / <a href='#' class='no-agree-delete'><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','No')?></a></b></td></tr>");
        currentRow.after(rowConfirmDelete);    
    }
    else
    {
        $('table.items tbody tr#ask-del-ad-'+ad_id).remove();    
    }
    return false;
});

$('table.items tbody tr.row-ask-delete-ad td a.agree-delete').live('click',function(){
    var ad_id = $(this).parent().parent().parent().attr('id').replace('ask-del-ad-','');
    $.post(
        baseUrl+"/index.php?r=Core/service/ajax",
        {
            SID : 'Ads.Ads.delete',
            'ids': ad_id                        
        },
        function(json)
        {
            var result = eval(json);
            if (result.errors.ErrorCode)
            {
                var error = '';
                for(var i in result.errors.ErrorCode)
                    error += result.errors.ErrorCode[i] + "\n\n";
                alert(error);
            }
            else
            {
                $.fn.yiiGridView.update('ads-grid');   
            }    
        }
    );
    return false;    
});

$('table.items tbody tr.row-ask-delete-ad td a.no-agree-delete').live('click',function(){
    $(this).parent().parent().parent().remove();
    return false;    
});
</script> 