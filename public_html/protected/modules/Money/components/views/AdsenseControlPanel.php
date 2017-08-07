<style type="text/css">
.wide form label { width: 250px; height: 40px; }
.wide form .buttons { margin-left: 260px; }
</style>

<?php echo CHtml::textArea($this->name,$this->value,array('class'=>'txt-adsense-code','rows'=>5,'cols'=>50)); ?>

<div class="row homepage-placements">
    <label><?php echo Language::t(Yii::app()->language,'Backend.Money.Setting','Select placement in Homepage')?></label>
    <?php echo CHtml::checkBox('ADSENSE_HOMEPAGE_TOP_PLACEMENT',MoneySettings::ADSENSE_HOMEPAGE_TOP_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Top'); ?><br />
    <?php echo CHtml::checkBox('ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT',MoneySettings::ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'); ?>
</div>

<div class="row listingpages-placements">
    <label><?php echo Language::t(Yii::app()->language,'Backend.Money.Setting','Select placement in Listing pages')?></label>
    <?php echo CHtml::checkBox('ADSENSE_LISTINGPAGES_TOP_PLACEMENT',MoneySettings::ADSENSE_LISTINGPAGES_TOP_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Top'); ?><br />
    <?php echo CHtml::checkBox('ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT',MoneySettings::ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'); ?>
</div>

<div class="row adpage-placements">
    <label style="height: 50px;"><?php echo Language::t(Yii::app()->language,'Backend.Money.Setting','Select placement in Ad pages')?></label>
    <?php echo CHtml::checkBox('ADSENSE_ADPAGE_TOP_PLACEMENT',MoneySettings::ADSENSE_ADPAGE_TOP_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Top'); ?><br />
    <?php echo CHtml::checkBox('ADSENSE_ADPAGE_MIDDLE_PLACEMENT',MoneySettings::ADSENSE_ADPAGE_MIDDLE_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Middle'); ?><br />
    <?php echo CHtml::checkBox('ADSENSE_ADPAGE_BOTTOM_PLACEMENT',MoneySettings::ADSENSE_ADPAGE_BOTTOM_PLACEMENT).Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'); ?>
</div>

<script type="text/javascript">
$('input[type=submit]').live('click',function(){
    $('div.errorMessage').remove();
    var is_valid = true;
    
    var top_homepage = 0;
    if ($('#ADSENSE_HOMEPAGE_TOP_PLACEMENT').is(':checked')) top_homepage = 1;
    var bottom_homepage = 0;
    if ($('#ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT').is(':checked')) bottom_homepage = 1;
    var top_listingpages = 0;
    if ($('#ADSENSE_LISTINGPAGES_TOP_PLACEMENT').is(':checked')) top_listingpages = 1;
    var bottom_listingpages = 0;
    if ($('#ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT').is(':checked')) bottom_listingpages = 1;
    var top_adpage = 0;
    if ($('#ADSENSE_ADPAGE_TOP_PLACEMENT').is(':checked')) top_adpage = 1;
    var bottom_adpage = 0;
    if ($('#ADSENSE_ADPAGE_BOTTOM_PLACEMENT').is(':checked')) bottom_adpage = 1;
    
    $.ajax({
        'type' : 'POST',
        'async' : false,
        'url' : baseUrl + '/index.php?r=Core/service/ajax',
        'data' :
        {
            'SID' : 'Money.Adsense.validatePlacement',
            'top_homepage' : top_homepage,
            'bottom_homepage' : bottom_homepage,
            'top_listingpages' : top_listingpages,
            'bottom_listingpages' : bottom_listingpages,
            'top_adpage' : top_adpage,
            'bottom_adpage' : bottom_adpage,
            'adsense_code' : $('#ADSENSE_CODE').val()
        },
        'success' : function(json) {
            var result = eval(json);
            if (result.errors.ErrorCode)
            {
                is_valid = false;
                if (result.homepageErrorMsgs.length > 0)
                {
                    var msgs = '<div style="margin-left: 260px;" class="errorMessage">';
                    for (var i in result.homepageErrorMsgs)
                    {
                        msgs += result.homepageErrorMsgs[i]+'<br />';       
                    }
                    msgs += '</div>';
                    $('.homepage-placements').append(msgs);   
                }
                if (result.listingpagesErrorMsgs.length > 0)
                {
                    var msgs = '<div style="margin-left: 260px;" class="errorMessage">';
                    for (var i in result.listingpagesErrorMsgs)
                    {
                        msgs += result.listingpagesErrorMsgs[i]+'<br />';       
                    }
                    msgs += '</div>';
                    $('.listingpages-placements').append(msgs);   
                }
                if (result.adpageErrorMsgs.length > 0)
                {
                    var msgs = '<div style="margin-left: 260px;" class="errorMessage">';
                    for (var i in result.adpageErrorMsgs)
                    {
                        msgs += result.adpageErrorMsgs[i]+'<br />';       
                    }
                    msgs += '</div>';
                    $('.adpage-placements').append(msgs);   
                }    
            }
        }
    });
    return is_valid;
});
</script>