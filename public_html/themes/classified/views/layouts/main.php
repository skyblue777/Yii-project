<?php cs()->registerCoreScript('jquery'); 
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/FlexImage.php');
include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if (in_array(strtolower($this->route),array('ads/ad/viewdetails','site/index'))) : ?> xmlns:fb="http://ogp.me/ns/fb#"<?php endif; ?>>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="<?php echo $this->descriptionMetaTagContent; ?>" />
    <meta name="keywords" content="<?php echo $this->keyWordsMetaTagContent; ?>" />
    <title><?php echo $this->pageTitle; ?></title>
    <link href="<?php echo themeUrl()?>/styles/reset.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/<?php if (AppearanceSettings::BUTTON_COLOR!='') echo AppearanceSettings::BUTTON_COLOR; else echo 'green'; ?>.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/general.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/form.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/skin.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo themeUrl()?>/styles/<?php if (AppearanceSettings::BACKGROUND_COLOR!='') echo AppearanceSettings::BACKGROUND_COLOR; else echo 'blue-back'; ?>.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo themeUrl(); ?>/scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link href="<?php echo themeUrl()?>/scripts/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
    <?php if (trim(Settings::GOOGLE_CODE) != '') echo trim(stripslashes(Settings::GOOGLE_CODE)); ?>
</head>
<body>
<?php if (in_array(strtolower($this->route),array('ads/ad/viewdetails','site/index'))) : ?>
    <div id="fb-root"></div>
    <script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
<?php endif; ?>

    <div id="container">
        <!-- header -->
        <div id="header">
            <?php if (!empty($this->adTitle)) : ?><p class="product-ad"><?php echo $this->adTitle; ?></p><?php endif; ?>
            <ul class="link">
                <li>
                <?php if (!Yii::app()->user->isGuest) : ?>
                	<span style="border-left: 1px solid #0000ff; padding: 0 10px; text-transform: none; color: #999999;">
                		<?php echo Yii::app()->user->email; ?>
                	</span>
                	<a href="<?php echo $this->createUrl('/site/logout'); ?>">
                		<?php echo Language::t(Yii::app()->language,'Backend.Common.Views','Logout');?>
                	</a>
                <?php else : ?>
                	<a style="padding: 0px 0px 0px 10px;" href="<?php echo $this->createUrl('/site/login'); ?>">
                		<?php echo Language::t(Yii::app()->language,'Backend.Admin.Login','Sign in')?>
                	</a> 
                	<span style="text-transform: none;">
                		<?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','or')?>
                	</span>
                	<a style="border: none; padding: 0px;" href="<?php echo $this->createUrl('/site/register'); ?>">
                		<?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Register','Register')?>
                	</a>
                	<?php endif; ?>
                	</li>
                	<li>
                		<a href="<?php echo $this->createUrl('/User/loginnedUser/viewMyAds'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','My account')?></a>
                	</li>
                	<li class="first">
                		<a href="<?php echo $this->createUrl('/Ads/ad/advancedSearch'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Advanced search')?></a>
                	</li>                
            </ul>
            <?php
            $selectedLocation = $this->get('location','');
            $route = strtolower($this->route);
            if ($route == 'ads/ad/viewdetails')
              $selectedLocation = Yii::app()->user->getState('location','');

            if($selectedLocation != '') : ?>
                <div id="selected-location-name"><?php echo $selectedLocation; ?></div>
            <?php endif; ?>
            <p class="logo">
                <a href="<?php echo baseUrl(); ?>">
                    <?php
                    if (Settings::SITE_LOGO!='' && Settings::SITE_LOGO!='none')
                    {
                        $pathLogo = Settings::UPLOAD_FOLDER.'/'.Settings::SITE_LOGO;
                        $logoUrl = Yii::app()->request->getBaseUrl(TRUE).'/image.php?thumb=';
                        $logoUrl .= FlexImage::createThumbFilename($pathLogo,240,60);
                        echo CHtml::image($logoUrl,'Logo');
                    }
                    else
                        echo '<span class="big-title">'.Settings::SITE_NAME.'</span>';
                    ?>
                </a>
            </p>
            <form name="search_form" onSubmit="return validateSearch()" class="find-form" action="<?php echo $this->createUrl('/Ads/ad/listBySearch'); ?>" method="post">
                <fieldset>
                    <input class="text" type="text" name="search_box" value="<?php echo $this->get('search_box',''); ?>" />
                    <?php
                    echo CHtml::dropDownList('cat_id','',
                        CHtml::listData(Category::model()->findAll(
                            new CDbCriteria(
                                array('condition'=>'parent_id='.AdsSettings::ADS_ROOT_CATEGORY,
                                  'order'=>'ordering ASC')
                                )),'id','title'),
                        array('id'=>'ddlSearchedCategory',
                          'prompt'=>Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a category')
                          )
                        ); 
                    ?>
                    <input type="hidden" name="location" value="<?php echo $selectedLocation; ?>" />
                    <input class="btn-search" type="submit" name="search-button"/>
                    <span><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','or')?></span>
                    <a class="btn" href="<?php echo $this->createUrl('/Ads/ad/selectCategory'); ?>"><?php echo Language::t(Yii::app()->language,'Frontend.Common.Layout','Post an ad')?></a>
                </fieldset>
            </form>
            <script type="text/javascript">
              function validateSearch()
              {
                var x = document.search_form.search_box.value;
                if (x == null || x == "")
                {
                  return false;
                }
                
                return true;
              }
            </script>
        </div>
        <!-- header.end -->
        <hr />
        
        <!-- Top Banner -->
        <?php 
        // homepage
        if (get_class($this) == 'SiteController' && $this->action->Id == 'index') :
            $top_home_code = '';
            if (MoneySettings::ADSENSE_HOMEPAGE_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                $top_home_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
            elseif (in_array(MoneySettings::BANNER_HOMEPAGE_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_HOMEPAGE_CODE) != '')
                $top_home_code = trim(stripslashes(MoneySettings::BANNER_HOMEPAGE_CODE));
            if ($top_home_code != '') :           
        ?>
                <div style="clear:both;text-align:center;margin:25px 0 0;">
                <?php echo $top_home_code; ?>
                </div>
        <?php endif; endif;?>
        <?php 
        // category listing
        if (get_class($this) == 'AdController' && strtolower($this->action->Id) == 'listbycategory') :
            $top_listing_code = '';
            if (MoneySettings::ADSENSE_LISTINGPAGES_TOP_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                $top_listing_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
            elseif (in_array(MoneySettings::BANNER_LISTINGPAGES_PLACEMENT,array(1,3)) && trim(MoneySettings::BANNER_LISTINGPAGES_CODE) != '')
                $top_listing_code = trim(stripslashes(MoneySettings::BANNER_LISTINGPAGES_CODE));
            if ($top_listing_code != '') :    
        ?>
                <div style="clear:both;text-align:center;margin:25px 0 0;">
                <?php echo $top_listing_code; ?>
                </div>
        <?php endif; endif;?>
        <!-- Top Banner.end -->
        
        <!-- main -->
        <div id="main">
            <?php
            $this->widget('zii.widgets.CBreadcrumbs', array(
                'homeLink'=>'<a class="Home" href="'.baseUrl().'">'.Language::t(Yii::app()->language,'Frontend.Common.Common','Home').'</a>',
                'links'=>$this->breadcrumbs,
                'htmlOptions'=>array(
                    'id' => 'pageBreadCrumb',
                    'class' => 'link-top'
                ),
                'separator' => " &gt; "
            ));
            ?>
            <?php echo $content; ?>
        </div>
        <!-- main.end -->
        <hr />
        
        <!-- Bottom Banner -->
        <?php 
        // homepage
        if (get_class($this) == 'SiteController' && $this->action->Id == 'index') :
            $bottom_home_code = '';
            if (MoneySettings::ADSENSE_HOMEPAGE_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                $bottom_home_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
            elseif (in_array(MoneySettings::BANNER_HOMEPAGE_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_HOMEPAGE_CODE) != '')
                $bottom_home_code = trim(stripslashes(MoneySettings::BANNER_HOMEPAGE_CODE));
            if ($bottom_home_code != '') :
        ?>
                <div style="clear:both;text-align:center;margin:25px 0 0;">
                <?php echo $bottom_home_code; ?>
                </div>
        <?php endif; endif;?>
        <?php 
        // category listing
        if (get_class($this) == 'AdController' && strtolower($this->action->Id) == 'listbycategory') :
            $bottom_listing_code = '';
            if (MoneySettings::ADSENSE_LISTINGPAGES_BOTTOM_PLACEMENT == 1 && trim(MoneySettings::ADSENSE_CODE) != '')
                $bottom_listing_code = trim(stripslashes(MoneySettings::ADSENSE_CODE));
            elseif (in_array(MoneySettings::BANNER_LISTINGPAGES_PLACEMENT,array(2,3)) && trim(MoneySettings::BANNER_LISTINGPAGES_CODE) != '')
                $bottom_listing_code = trim(stripslashes(MoneySettings::BANNER_LISTINGPAGES_CODE));
            if ($bottom_listing_code != '') :            
        ?>
                <div style="clear:both;text-align:center;margin:25px 0 0;">
                <?php echo $bottom_listing_code; ?>
                </div>
        <?php endif; endif;?>
        <!-- Bottom Banner.end -->
        
        <!-- footer -->
        <div id="footer">
            <p>
            <a href="<?php echo url('/site/faqs') ?>"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Faqs','Help')?></a> | 
            <?php
            Yii::import('Article.models.*');
            $criteria = new CDbCriteria();
            $criteria->compare('status',1);
            $criteria->compare('category_id',settings::FOOTER_PAGES);
            $criteria->compare('lang',Yii::app()->language);
            $articles = Article::model()->findAll($criteria);
            foreach($articles as $article)
                echo CHtml::link($article->title,url('/site/support', array('alias' => $article->alias))), ' | ';
            ?>
            <a href="<?php echo url('/site/contact') ?>"><?php echo Language::t(Yii::app()->language,'Frontend.GenericContent.Contact','Contact')?></a>
            </p>
        </div>
        <!-- footer.end -->
    </div>   
</body>
</html>