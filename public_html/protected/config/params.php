<?php
return array(
    'BO_PAGE_SIZE' => array(
        'rules' => array('numerical' => array('min' => 5, 'max' => 50))
    ),
    
// **************** Ads parameters ***************** //
    // settings
    'ALLOW_HTML_LINKS' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'DISPLAY_MAP' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'CURRENCY' => array(
        'type' => 'CurrencyDropDownList',
        'params' => array('name'=>'CURRENCY'),
    ),
    'SHOW_VIEW_COUNTER' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'PHOTO_MAX_COUNT'=>array(
        'type'=>'Ads.components.PhotoMaxSelector',
        'params' => array('name'=>'PHOTO_MAX_COUNT'),
        'rules' => array('numerical' => array('min' => 1,'max'=>'5')),
    ),
    'AREA_LIST'=>array(
        'type'=>'Ads.components.LocationsManager',
        'params' => array('name'=>'AREA_LIST'),
    ),
    'BANNED_WORDS' => array(
        'type' => 'Ads.components.BannedWordsTextArea',
        'params' => array('name'=>'BANNED_WORDS'),
    ),
    'MSG_BANNED_CONTENT' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 50, 'rows' => 5),
    ),
    // listing
    'MAX_RESULTS'=>array(
        'rules' => array('numerical' => array('min' => 5, 'max' => 50)),
    ),
    
    'RESULT_SORT' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Date'), 2=>Language::t(Yii::app()->language,'Backend.Common.Common','Price'))
    ),
    
    'RSS_FEED' => array(
        'type' => 'dropdownlist',
        'items'=> array(0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'), 1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'))
    ),
    // banned emails and ips
    'BANNED_EMAIL' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 50, 'rows' => 5),
    ),
    'BANNED_IP' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 50, 'rows' => 5),
    ),
    'MSG_BANNED_USER' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 50, 'rows' => 5),
    ),
    // Money Top Ads
    'TOP_TIME1'=>array(
        'type'=>'Money.components.PromotionTimeTextBox',
        'params' => array('name'=>'TOP_TIME1'),
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'TOP_TIME2'=>array(
        'type'=>'Money.components.PromotionTimeTextBox',
        'params' => array('name'=>'TOP_TIME2'),
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'TOP_PRICE1'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'TOP_PRICE2'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'PAYPAL_EMAIL_TOP'=>array(
        'rules' => array('email'=>array()),
    ),
    'PAYPAL_CURRENCY_TOP' => array(
        'type' => 'CurrencyDropDownList',
        'params' => array('name'=>'PAYPAL_CURRENCY_TOP'),
    ),
    'PAYPAL_PDT_TOP'=>array(
        'type'=>'Money.components.PDTIdentityTokenTextBox',
        'params' => array('name'=>'PAYPAL_PDT_TOP'),
    ),
    // Money Homepage Gallery
    'HG_TIME1'=>array(
        'type'=>'Money.components.PromotionTimeTextBox',
        'params' => array('name'=>'HG_TIME1'),
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'HG_TIME2'=>array(
        'type'=>'Money.components.PromotionTimeTextBox',
        'params' => array('name'=>'HG_TIME2'),
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'HG_PRICE1'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'HG_PRICE2'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'PAYPAL_EMAIL_HG'=>array(
        'rules' => array('email' => array()),
    ),
    'PAYPAL_CURRENCY_HG' => array(
        'type' => 'CurrencyDropDownList',
        'params' => array('name'=>'PAYPAL_CURRENCY_HG'),
    ),
    'PAYPAL_PDT_HG'=>array(
        'type'=>'Money.components.PDTIdentityTokenTextBox',
        'params' => array('name'=>'PAYPAL_PDT_HG'),
    ),
    // Money Paid ads
    'PAID_ADS_PRICE'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    'PAYPAL_EMAIL_PAID'=>array(
        'rules' => array('email' => array()),
    ),
    'PAYPAL_CURRENCY_PAID' => array(
        'type' => 'CurrencyDropDownList',
        'params' => array('name'=>'PAYPAL_CURRENCY_PAID'),
    ),
    'PAYPAL_PDT_PAID'=>array(
        'type'=>'Money.components.PDTIdentityTokenTextBox',
        'params' => array('name'=>'PAYPAL_PDT_PAID'),
    ),
    // Money Banners
    'BANNER_HOMEPAGE_CODE' => array(
        'type'=>'Money.components.BannerCodeTextArea',
        'params' => array('name'=>'BANNER_HOMEPAGE_CODE'),
    ),
    'BANNER_LISTINGPAGES_CODE' => array(
        'type'=>'Money.components.BannerCodeTextArea',
        'params' => array('name'=>'BANNER_LISTINGPAGES_CODE'),
    ),
    'BANNER_ADPAGE_CODE' => array(
        'type'=>'Money.components.BannerCodeTextArea',
        'params' => array('name'=>'BANNER_ADPAGE_CODE'),
    ),
    'BANNER_HOMEPAGE_PLACEMENT' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top'), 2=>Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'), 3=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top and Buttom'))
    ),
    'BANNER_LISTINGPAGES_PLACEMENT' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top'), 2=>Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'), 3=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top and Buttom'))
    ),
    'BANNER_ADPAGE_PLACEMENT' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top'), 2=>Language::t(Yii::app()->language,'Backend.Money.Setting','Buttom'), 3=>Language::t(Yii::app()->language,'Backend.Money.Setting','Top and Buttom'))
    ),
    'ADSENSE_CODE' => array(
        'type'=>'Money.components.AdsenseControlPanel',
        'params' => array('name'=>'ADSENSE_CODE'),
    ),
    // Map - General
    'GAPI' => array(
        'type'=>'Map.components.GAPITextBox',
        'params' => array('name'=>'GAPI'),
    ),
    'LONGITUDE' => array(
        'type'=>'Map.components.LongitudeTextBox',
        'params' => array('name'=>'LONGITUDE'),
    ),
    // Map - homepage
    'DISPLAY_MAP_HOMEPAGE' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'MAP_TYPE' => array(
        'type' => 'dropdownlist',
         'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Map.Setting','Map'), 2=>Language::t(Yii::app()->language,'Backend.Map.Setting','Satellite'), 3=>Language::t(Yii::app()->language,'Backend.Map.Setting','Hybrid'))
    ),
    'MAP_ZOOM' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'ZOOM'=>array(
        'rules' => array('numerical' => array('min' => 0, 'max' => 20)),
    ),
    'MAP_MARKER'=>array(
        'rules' => array('numerical' => array('min' => 0)),
    ),
    // Map - ads
    'DISPLAY_MAP_ADS' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    'MAP_TYPE_ADS' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Map.Setting','Map'), 2=>Language::t(Yii::app()->language,'Backend.Map.Setting','Satellite'), 3=>Language::t(Yii::app()->language,'Backend.Map.Setting','Hybrid'))
    ),
    'MAP_ZOOM_ADS' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','Yes'), 0=>Language::t(Yii::app()->language,'Backend.Common.Common','No'))
    ),
    // Appearance
    'BUTTON_COLOR'=>array(
        'type'=>'Appearance.components.ButtonColorSelector',
        'params' => array('name'=>'BUTTON_COLOR'),
    ),
    'BACKGROUND_COLOR'=>array(
        'type'=>'Appearance.components.BackgroundColorSelector',
        'params' => array('name'=>'BACKGROUND_COLOR'),
    ),
    // General settings
    'SITE_LOGO' => array(
        'type' => 'SiteLogoUploader',
        'params' => array('name'=>'SITE_LOGO'),
    ),
    'SITE_NAME'=>array(
        'htmlOptions'=>array('style'=>'width: 360px;')
    ),
    'DEFAULT_META_DESCRIPTION' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 50, 'rows' => 5),
    ),
    'SITE_CONTACT'=>array(
        'htmlOptions'=>array('style'=>'width: 200px;')
    ),
    'EXPIRATION' => array(
        'type' => 'ExpireDayTextBox',
        'params' => array('name'=>'EXPIRATION'),
        'rules' => array('numerical' => array('min' => 30)),
    ),
    'SITE_ACCESS' => array(
        'type' => 'dropdownlist',
        'items'=> array(1=>Language::t(Yii::app()->language,'Backend.Common.Common','All'), 2=>Language::t(Yii::app()->language,'Backend.User.Setting','Registered users'))
    ),
    'GOOGLE_CODE' => array(
        'type'=>'textarea',
        'htmlOptions' => array('cols' => 60, 'rows' => 10),
    ),
     'LANG' => array(
        'type' => 'Language.components.LanguageSelector',
    ),
    
//    'uploadFolder'=>'uploads/',
//    'adsUploadFolder'=>'uploads/ads/',
//    'adsTempUploadFolder'=>'uploads/ads/temp/',
//    'adsUploadFileType'=>array('jpg','png','jpeg','gif','bmp'),
//    'adsUploadFileSize'=>1*1024*1024,
);