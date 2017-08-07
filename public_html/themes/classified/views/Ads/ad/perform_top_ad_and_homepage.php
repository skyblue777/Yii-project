<?php
$breadcrumbItems = array(
    Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a Category') => array('/Ads/ad/selectCategory'),
);

if (!empty($model->id))
 $breadcrumbItems[Language::t(Yii::app()->language,'Frontend.Ads.Common','Update your ad')] = array('/Ads/ad/update',
                                            'id'=>$model->id,
                                            'alias'=>str_replace(array(' ','/','\\'),'-',$model->title));
else
{
  $cat = Category::model()->findByPk($model->category_id);
  $breadcrumbItems[Language::t(Yii::app()->language,'Frontend.Ads.Common','Create your ad')] = array('/Ads/ad/create', 
                                             'cat_id'=>$cat->id,
                                             'alias'=>$cat->alias);
}

$breadcrumbItems[Language::t(Yii::app()->language,'Frontend.Ads.Preview','Preview')] = '#';

$newItems = array();
if ($model->featured==1) $newItems[] = Language::t(Yii::app()->language,'Frontend.Ads.Form','Top Ad');
if ($model->homepage==1) $newItems[] = Language::t(Yii::app()->language,'Backend.Common.Menu','Homepage Gallery');
$breadcrumbItems[] = implode(' & ',$newItems);
$this->breadcrumbs = $breadcrumbItems;

$this->pageTitle = Settings::SITE_NAME.' - '.$newItems[0];
?>

<?php if (!is_null($model)) : ?>
    <form method="post" id="ad-hidden-form">
        <h1 class="title-2"><?php echo $model->title; ?></h1>
        <div class="intro">
            <?php if ($model->featured==1) : ?>
                <div style="padding-bottom: 10px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','To place your ad in the Top ads section please select a duration then click continue.')?></div>    
                <table class="promote-options" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <?php if (intval(MoneySettings::TOP_TIME1) > 0) : ?>
                        <tr>
                            <td class="day-col">
                              <?php echo CHtml::activeRadioButton($model,'feature_days',
                                                  array('value'=>MoneySettings::TOP_TIME1,
                                                        'uncheckValue'=>NULL,'class'=>'rb-top-ad',
                                                        'checked'=>(MoneySettings::TOP_TIME1==$model->feature_days)?'checked':''))
                                         .MoneySettings::TOP_TIME1.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days')?>
                            </td>
                            <td class="price-col"><?php echo MoneySettings::PAYPAL_CURRENCY_TOP.' '.intval(MoneySettings::TOP_PRICE1); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (intval(MoneySettings::TOP_TIME2) > 0) : ?>
                        <tr>
                            <td class="day-col">
                              <?php echo CHtml::activeRadioButton($model,'feature_days',
                                                  array('value'=>MoneySettings::TOP_TIME2,
                                                        'uncheckValue'=>NULL,'class'=>'rb-top-ad',
                                                        'checked'=>(MoneySettings::TOP_TIME2==$model->feature_days)?'checked':''))
                                         .MoneySettings::TOP_TIME2.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days'); ?></td>
                            <td class="price-col"><?php echo MoneySettings::PAYPAL_CURRENCY_TOP.' '.intval(MoneySettings::TOP_PRICE2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <script type="text/javascript">
                $('input.rb-top-ad').click(function(){
                  $('input.rb-top-ad').removeAttr('checked');
                  $(this).attr('checked', true);
                });
                </script>
            <?php elseif ($model->homepage==1) : ?>
                <br />
                <div style="padding-bottom: 10px;"><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Form','To place your ad in the Homepage Gallery section please select a duration then click continue.')?></div>    
                <table class="promote-options" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <?php if (intval(MoneySettings::HG_TIME1) > 0) : ?>
                        <tr>
                            <td class="day-col">
                              <?php echo CHtml::activeRadioButton($model,'homepage_days',
                                                  array('value'=>MoneySettings::HG_TIME1,
                                                        'uncheckValue'=>NULL,'class'=>'rb-homepage',
                                                        'checked'=>(MoneySettings::HG_TIME1==$model->homepage_days)?'checked':''))
                                         .MoneySettings::HG_TIME1.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days'); ?></td>
                            <td class="price-col"><?php echo MoneySettings::PAYPAL_CURRENCY_HG.' '.intval(MoneySettings::HG_PRICE1); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (intval(MoneySettings::HG_TIME2) > 0) : ?>
                        <tr>
                            <td class="day-col">
                              <?php echo CHtml::activeRadioButton($model,'homepage_days',
                                                  array('value'=>MoneySettings::HG_TIME2,
                                                        'uncheckValue'=>NULL,'class'=>'rb-homepage',
                                                        'checked'=>(MoneySettings::HG_TIME2==$model->homepage_days)?'checked':''))
                                         .MoneySettings::HG_TIME2.' '.Language::t(Yii::app()->language,'Backend.Language.Time','days'); ?></td>
                            <td class="price-col"><?php echo MoneySettings::PAYPAL_CURRENCY_HG.' '.intval(MoneySettings::HG_PRICE2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <script type="text/javascript">
                $('input.rb-homepage').click(function(){
                  $('input.rb-homepage').removeAttr('checked');
                  $(this).attr('checked', true);
                });
                </script>
            <?php endif; ?>
        </div>
        <div class="btn-links">
            <input class="btn" type="submit" value="<?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','Previous')?>" name="previewAd" id="btn-previewAd" />
            <input class="btn" type="submit" value="<?php echo Language::t(Yii::app()->language,'Frontend.Common.Common','Continue')?>" name="saveAd" id="btn-saveAd" />
        </div>
        <?php
        if (!$model->isNewRecord)
        {
            echo CHtml::activeHiddenField($model,'id');
            echo CHtml::activeHiddenField($model,'photos');
        }
        echo CHtml::activeHiddenField($model,'description');
        ?>
     </form>

     <script type="text/javascript">
       
     $('#btn-previewAd').click(function(){
        $('#ad-hidden-form').attr('action','<?php echo $this->createUrl('/Ads/ad/preview'); ?>');
        
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->category_id ?>",
                           id:"Annonce_category_id",
                           name:"Annonce[category_id]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->txn_id ?>",
                           id:"Annonce_txn_id",
                           name:"Annonce[txn_id]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->title ?>",
                           id:"Annonce_title",
                           name:"Annonce[title]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->price ?>",
                           id:"Annonce_price",
                           name:"Annonce[price]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->opt_price ?>",
                           id:"Annonce_opt_price",
                           name:"Annonce[opt_price]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->area ?>",
                           id:"Annonce_area",
                           name:"Annonce[area]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->zipcode ?>",
                           id:"Annonce_zipcode",
                           name:"Annonce[zipcode]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->lat ?>",
                           id:"Annonce_lat",
                           name:"Annonce[lat]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->lng ?>",
                           id:"Annonce_lng",
                           name:"Annonce[lng]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $this->post('hdUploadedFiles','') ?>",
                           id:"hdUploadedFiles",
                           name:"hdUploadedFiles"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->video ?>",
                           id:"Annonce_video",
                           name:"Annonce[video]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->email ?>",
                           id:"Annonce_email",
                           name:"Annonce[email]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->featured ?>",
                           id:"Annonce_featured",
                           name:"Annonce[featured]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->feature_total ?>",
                           id:"Annonce_feature_total",
                           name:"Annonce[feature_total]"}).appendTo('#ad-hidden-form');             
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->feature_days ?>",
                           id:"Annonce_feature_days",
                           name:"Annonce[feature_days]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->homepage ?>",
                           id:"Annonce_homepage",
                           name:"Annonce[homepage]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->homepage_days ?>",
                           id:"Annonce_homepage_days",
                           name:"Annonce[homepage_days]"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo $model->homepage_total ?>",
                           id:"Annonce_homepage_total",
                           name:"Annonce[homepage_total]"}).appendTo('#ad-hidden-form');        
     });
     
     $('#btn-saveAd').click(function(){
        $('#ad-hidden-form').attr('action','https://www.paypal.com/cgi-bin/webscr');
        
        $('<input>').attr({type:"hidden",
                           value:"_xclick",
                           name:"cmd"}).appendTo('#ad-hidden-form');
                
        $('<input>').attr({type:"hidden",
                           value:"<?php echo baseUrl().'/uploads/'.Settings::SITE_LOGO; ?>",
                           name:"image_url"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo Yii::app()->createAbsoluteUrl('Ads/ad/performSaveAd',array('action'=>$action,'type'=>'return')); ?>",
                           name:"return"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"<?php echo Yii::app()->createAbsoluteUrl('/Ads/ad/performSaveAd',array('action'=>$action,'type'=>'cancel')); ?>",
                           name:"cancel_return"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"2",
                           name:"rm"}).appendTo('#ad-hidden-form');                   
        $('<input>').attr({type:"hidden",
                           value:"1",
                           name:"no_shipping"}).appendTo('#ad-hidden-form');
        $('<input>').attr({type:"hidden",
                           value:"1",
                           name:"no_note"}).appendTo('#ad-hidden-form');                         
        
        if ($('input.rb-top-ad').length>0) {
          var arrTopAdDayPrice = new Array();
          <?php if (intval(MoneySettings::TOP_TIME1) > 0) : ?>
            arrTopAdDayPrice['<?php echo MoneySettings::TOP_TIME1; ?>'] = '<?php echo intval(MoneySettings::TOP_PRICE1); ?>';
          <?php endif; ?>
          <?php if (intval(MoneySettings::TOP_TIME2) > 0) : ?>
            arrTopAdDayPrice['<?php echo MoneySettings::TOP_TIME2; ?>'] = '<?php echo intval(MoneySettings::TOP_PRICE2); ?>';
          <?php endif; ?>
          var day = $('input.rb-top-ad:checked').val();
          var price = arrTopAdDayPrice[day];

          $('<input>').attr({type:"hidden",
                             value:"Top Ad: "+day+" days",
                             name:"item_name"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:day,
                             name:"item_number"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:price,
                             name:"amount"}).appendTo('#ad-hidden-form');
          
          $('<input>').attr({type:"hidden",
                             value:"<?php echo MoneySettings::PAYPAL_EMAIL_TOP; ?>",
                             name:"business"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:"<?php echo (trim(MoneySettings::PAYPAL_CURRENCY_TOP)=='$')?'USD':MoneySettings::PAYPAL_CURRENCY_TOP; ?>",
                             name:"currency_code"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:"top_ads#<?php echo $model->id; ?>",
                             name:"custom"}).appendTo('#ad-hidden-form');
        }                         
        else if ($('input.rb-homepage').length>0) {
          var arrHomeAdDayPrice = new Array();
          <?php if (intval(MoneySettings::HG_TIME1) > 0) : ?>
            arrHomeAdDayPrice['<?php echo MoneySettings::HG_TIME1; ?>'] = '<?php echo intval(MoneySettings::HG_PRICE1); ?>';
          <?php endif; ?>
          <?php if (intval(MoneySettings::HG_TIME2) > 0) : ?>
            arrHomeAdDayPrice['<?php echo MoneySettings::HG_TIME2; ?>'] = '<?php echo intval(MoneySettings::HG_PRICE2); ?>';
          <?php endif; ?>
          var day = $('input.rb-homepage:checked').val();
          var price = arrHomeAdDayPrice[day];
          $('<input>').attr({type:"hidden",
                             value:"Homepage Gallery: "+day+" days",
                             name:"item_name"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:day,
                             name:"item_number"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:price,
                             name:"amount"}).appendTo('#ad-hidden-form');
                           
          $('<input>').attr({type:"hidden",
                             value:"<?php echo MoneySettings::PAYPAL_EMAIL_HG; ?>",
                             name:"business"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:"<?php echo (trim(MoneySettings::PAYPAL_CURRENCY_HG)=='$')?'USD':MoneySettings::PAYPAL_CURRENCY_HG; ?>",
                             name:"currency_code"}).appendTo('#ad-hidden-form');
          $('<input>').attr({type:"hidden",
                             value:"hp_gallery#<?php echo $model->id; ?>",
                             name:"custom"}).appendTo('#ad-hidden-form');
        }
     });
     // breadcrumb
     $('#pageBreadCrumb a:last').click(function(){
        $('#btn-previewAd').trigger('click');       
     });
     $('#pageBreadCrumb a:eq(2)').click(function(){
        ;       
     });
     </script>   
<?php else : ?>
    <p><?php echo Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad does not exist.')?></p>
<?php endif; ?>