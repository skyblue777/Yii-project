<style type="text/css">
.grid-view { padding: 5px 0 0; }
.grid-view .status-column a { background: none; }
.grid-view a.lnk-title { text-decoration: none; }
.grid-view a.lnk-title:hover { color: #377ffb; text-decoration: underline; }
.grid-view .pager { margin: 9px 0 0; }
#txtFilteredAdId { border: 1px solid #ccc; width: 70px; }
</style>

<?php
$this->breadcrumbs=array(
	Language::t(Yii::app()->language,'Backend.Common.Menu','Ads')=>array('index'),
	Language::t(Yii::app()->language,'Backend.Common.Common','All'),
);
//$this->menu = array(array('label' => 'Create Ads', 'url'=>$this->createUrl('/Ads/Ads/create')));
?>

<div>
    <?php
    echo CHtml::dropDownList('ddlAdsStatus','',array(''=>Language::t(Yii::app()->language,'Backend.Common.Common','All'),'1'=>Language::t(Yii::app()->language,'Backend.Common.Common','Active'),'0'=>Language::t(Yii::app()->language,'Backend.Common.Common','Inactive')));
    echo CHtml::hiddenField('filteredEmail',$model->email);
    ?>
</div>

<div style="text-align: right;">
    <form>
        <label><?php echo Language::t(Yii::app()->language,'Backend.Common.Common','Search By ID:')?></label>
        <input type="text" id="txtFilteredAdId" />
        <input type="submit" id="btnSearchAdId" value="<?php echo Language::t(Yii::app()->language,'Backend.Common.Common','OK')?>" />
    </form>
</div>

<?php
$grid = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'ads-grid',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate'=>'afterAjaxUpdateGrid',
    'selectableRows'=>2,
    'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
            'value'=>'$data->id',
            'htmlOptions'=>array('width'=>'3%','class'=>'checkbox-column'),
        ),
        array(
            'header'=>'',
            'type'=>'raw',
            'value'=>'$data->getImageSectionInGrid()',
        ),
        array(
            'name'=>'title',
            'filter'=>false,
            'type'=>'raw',
            'value'=>'$data->getTitleSectionInGrid()',
        ),        
        array(
            'name'=>'categoryTitle',
            'header'=>Language::t(Yii::app()->language,'Backend.Common.Common','Category'),
            'filter' => false,
            'type'=>'raw',
            'value'=>'(!is_null($data->category)) ? $data->category->title : ""',
        ),
        array(
            'name'=>'price',
            'type'=>'raw',
            'filter'=>false,
            'value'=>'$data->getPriceSection()',
            'htmlOptions' => array('width'=>'7%'),
        ),
        array(
            'name'=>'viewed',
            'type'=>'raw',
            'filter'=>false,
            'value'=>'$data->viewed',
            'htmlOptions' => array('width'=>'5%'),
        ),
        array(
            'name'=>'email',
            'type'=>'raw',
            'filter'=>false,
            'value'=>'$data->getShortEmail()',
	    	'htmlOptions'=>array('class'=>'short-email'),
        ),
        array(
            'name'=>'create_time',
            'htmlOptions'=>array('width'=>'10%'),
            'filter'=>false,
            'value'=>'(!empty($data->create_time) && $data->create_time != "0000-00-00 00:00:00") ? date("m/d/Y H:i:s",strtotime($data->create_time)) : ""',
        ),
        array(
            'name'=>'pricePlan',
            'header'=>Language::t(Yii::app()->language,'Backend.Ads.Common','Price Plan'),
            'type'=>'raw',
            'value'=>'$data->getPricePlanSection()',
	    'headerHtmlOptions'=>array('nowrap'=>'nowrap')
        ),
        array(
            'name'=>'reportReplied',
            'header'=>Language::t(Yii::app()->language,'Backend.Ads.Common','Report'),
            'filter'=>false,
            'type'=>'raw',
            'value'=>'$data->getReportSection()',
            'htmlOptions'=>array('width'=>'8%')
        ),
        array(
            'header'=>'',
            'type'=>'raw',
            'value'=>'$data->getActionsButtonColumn()',
            'htmlOptions'=>array('style'=>'text-align: center;','width'=>'10%','nowrap'=>'nowrap'), 
        ),
    ),
    'summaryText'=>'{start}-{end} '.Language::t(Yii::app()->language,'Frontend.Common.Common','of').' {count} '.Language::t(Yii::app()->language,'Frontend.Ads.List','result(s)'),
)); 

        if ($grid->dataProvider->ItemCount) {
            $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.Common.Common','Delete'), 'url'=>'#', 'linkOptions' => array('id'=>'lnk-delete-multiple'));
            $this->menu[] = array('label' => Language::t(Yii::app()->language,'Backend.Common.Common','Activate'), 'url'=>'#', 'linkOptions' => array('id'=>'lnk-activate-multiple'));
        }
        //Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/gridview.js', CClientScript::POS_BEGIN);*/
?>

<script type="text/javascript">
function afterAjaxUpdateGrid()
{
    var status = $('#ddlAdsStatus').val();
    $('#ads-grid thead tr.filters td:first').append('<input type="hidden" name="Annonce[public]" value="'+status+'" />');
}
afterAjaxUpdateGrid();

$('#ddlAdsStatus').change(function(){
    var status = $(this).val();
    $('#ads-grid div.keys').attr('title',baseUrl+'/index.php?r=Ads/Ads/list');
    $.fn.yiiGridView.update('ads-grid', { data : { 'Annonce[public]' : status, 'Annonce[email]' : $('#filteredEmail').val(), 'Annonce_sort' : 'create_time.desc' } });
    
    if (status == '1')
    {
        $('#lnk-activate-multiple').parent().hide();
        $('#lnk-delete-multiple').parent().addClass('last');
    }
    else
    {
        $('#lnk-activate-multiple').parent().show();
        $('#lnk-delete-multiple').parent().removeClass('last');
    }    
    
    return false;    
});

$('#lnk-delete-multiple').live('click',function(){
    var ads_ids = new Array();
    $('#ads-grid table.items tbody tr td.checkbox-column :checkbox').each(function(){
        if (this.checked == true)
        {
            ads_ids.push(this.value);
        }
    });
    
    if (ads_ids.length <= 0)
    {
        alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Please select at least 1 ads!')?>");
        return false;
    }
    
    if (!confirm("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Do you want to delete selected ads?')?>")) return false;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'Ads.Ads.delete',
            'ids' : ads_ids
        },
        'success' : function(json) {
            $.fn.yiiGridView.update('ads-grid');
        }
    });
    
    return false;    
});

$('#lnk-activate-multiple').live('click',function(){
    var ads_ids = new Array();
    $('#ads-grid table.items tbody tr td.checkbox-column :checkbox').each(function(){
        if (this.checked == true)
        {
            ads_ids.push(this.value);
        }
    });
    
    if (ads_ids.length <= 0)
    {
        alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Please select at least 1 ads!')?>");
        return false;
    }
    
    if (!confirm("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Do you want to activate selected ads?')?>")) return false;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'Ads.Ads.activate',
            'ids' : ads_ids
        },
        'success' : function(json) {
            $.fn.yiiGridView.update('ads-grid');
        }
    });
    
    return false;    
});

$('#ads-grid table.items tbody tr td a.ban-user, #ads-grid table.items tbody tr td a.unban-user').live('click',function(){
    var ad_id = $(this).attr('href');
    var type = 'ban';
    var msgConfirm = "<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Do you want to ban author of this ad?')?>";
    if ($(this).hasClass('unban-user'))
    {
        type = 'unban';
        msgConfirm = "<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Do you want to unban author of this ad?')?>";
    }
    
    if (!confirm(msgConfirm)) return false;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'User.User.banOrUnbanUser',
            'ad_id' : ad_id,
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
            $.fn.yiiGridView.update('ads-grid');
        }
    });
    
    return false;
});

$('#ads-grid table.items tbody tr td a.make-top-ad, #ads-grid table.items tbody tr td a.add-into-homepage-gallery').live('click',function(){
    var ad_id = $(this).attr('href');
    var service_id = 'Ads.Ads.makeTopAd';
    if ($(this).hasClass('add-into-homepage-gallery'))
        service_id = 'Ads.Ads.addIntoHomepageGallery';
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : service_id,
            'ad_id' : ad_id
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
            else
            {
                if (service_id == 'Ads.Ads.makeTopAd')
                    alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','This ad has been made a top ad. This top ad will expire after')?> "+result.days+" <?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','days')?>");
                else
                    alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','This ad has been added into the Homepage Gallery. It will be removed from the Homepage Gallery after')?> "+result.days+" <?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','days')?>");    
            }
        }
    });
    
    return false;
});

$('#btnSearchAdId').click(function(){
    var ad_id = $.trim($('#txtFilteredAdId').val());
    if (ad_id == '')
    {
        alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Please input an Ad Id!')?>");
        return false;    
    }
    if (isNaN(ad_id) == true)
    {
    	alert("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Please input Ad Id as a number!')?>");
        return false;
    }
    $('#ddlAdsStatus').val('');
    $('#ads-grid div.keys').attr('title',baseUrl+'/index.php?r=Ads/Ads/list');
    $.fn.yiiGridView.update('ads-grid', { data : { 'Annonce[id]' : ad_id } });
    return false;    
});

$('#ads-grid table.items tbody tr td a.delete').live('click',function(){
    var delete_url = $(this).attr('href');
    if (!confirm("<?php echo Language::t(Yii::app()->language,'Backend.Ads.Message','Do you want to delete this ad?')?>")) return false;
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : delete_url,
        'success' : function(json) {
            $.fn.yiiGridView.update('ads-grid');    
        }
    });
    
    return false;
});

$(document).ready(function(){
    var elem = $('#menu ul li:eq(0) a.top-menu-item');
    $("#menu ul li").removeClass("over");
    $("#menu ul li a span").removeClass("item-active");
    $("#menu ul ul").hide();        
    $("#menu ul li a").css('background-position','left 0');
    $("#menu ul li").css('background-position','right 0');        
    elem.parent().find('ul').show();
    elem.parent().css('background-position','right -27px');
    elem.css('background-position','left -27px').find('span').addClass('item-active');
    elem.next('ul').find('li:eq(0)').addClass('active');
});
</script>