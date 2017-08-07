<?php

class AdController extends FrontController
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xE6E6E6,
                'foreColor'=>0x000000,
                //'fontFile'=>'Tahoma.ttf',
            ),
        );
    }
    
    public function actionListByCategory()
    {
        $model=new Annonce();
        
        $area = $this->get('location','');
        // category
        $cat_id = $this->get('cat_id',0);
        $cat = Category::model()->findByPk($cat_id);
        if (is_null($cat))
            throw new CHttpException(400,Language::t(Yii::app()->language,'Backend.Ads.Message','This category is not found'));
        
        $this->pageTitle = Settings::SITE_NAME.' - '.$cat->title.
                            (($area == '')?'':'('.$area.')');
                            
        // check warning page
        if ($cat->warning_page == 1 && !isset(Yii::app()->session['accept_warning_page']))
        {
            if (Yii::app()->request->isPostRequest && isset($_POST['acceptWarningPage']))
            {
                Yii::app()->session['accept_warning_page'] = 1;
                $this->redirect(baseUrl().Yii::app()->request->getUrl());    
            }
            $this->render('warning_page',array('cat'=>$cat,'area'=>$area));
        }
        else
        {
            $criteria = new CDbCriteria();
            if ($cat_id != 0 && $cat->parent_id != 0)
            {
              $filteredCatIds = $this->loadCategories($cat_id);
              $criteria->addInCondition('category_id',$filteredCatIds);
            }
            $criteria->addCondition('public = 1');
            if ($area != '')
                $criteria->compare('area',$area);
            
            $dataProvider = $model->listAds($criteria);
            //print_r($dataProvider->totalItemCount);die;
            
            $criteria_ = clone $criteria;
            $criteria_->addCondition('featured = 1');
            $dataTopAdsProvider = $model->listAds($criteria_);
            $dataTopAdsProvider->setPagination(false);
            $dataTopAdsProvider->setSort(false);
            //print_r($dataTopAdsProvider->totalItemCount);die;
            
            $this->render('list_by_category',array(
                'model'=>$model,
                'cat'=>$cat,
                'area'=>$area,
                'dataProvider'=>$dataProvider,
                'dataTopAdsProvider'=>$dataTopAdsProvider,
            ));   
        }
    }
    
    public function actionListByArea()
    {
        $model=new Annonce();
        
        // location
        $area = $this->get('location','');
        Yii::app()->user->setState('location',$area,'');
        $this->pageTitle = Settings::SITE_NAME.(($area == '')?' - '.Language::t(Yii::app()->language,'Backend.Common.Common','All').' '.Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Ad'):' - '.$area);
        
        $criteria = new CDbCriteria();
        if ($area != '')
            $criteria->compare('area',$area);
        $criteria->addCondition('public = 1');
        
        $dataProvider = $model->listAds($criteria);
        //print_r($dataProvider->totalItemCount);die;
        
        $criteria_ = clone $criteria;
        $criteria_->addCondition('featured = 1');
        $dataTopAdsProvider = $model->listAds($criteria_);
        $dataTopAdsProvider->setPagination(false);
        $dataTopAdsProvider->setSort(false);
        //print_r($dataTopAdsProvider->totalItemCount);die;
        
        $this->render('list_by_area',array(
            'model'=>$model,
            'area'=>$area,
            'dataProvider'=>$dataProvider,
            'dataTopAdsProvider'=>$dataTopAdsProvider,
        ));
    }
    
    public function actionListBySearch()
    {
        $model = new Annonce();
        
        /* keyword */
        if (Yii::app()->request->isPostRequest)
        {
            $keyword = $this->post('search_box', '');
            $cat_id = $this->post('cat_id', 0);
            $area = $this->post('location', '');
            //print_r($keyword);die;
            $alias = 'root-category';
            $searchParams = array('keyword'=>$keyword);
            if ($cat_id != 0)
            {
              $cat = Category::model()->findByPk($cat_id);
              if (!is_null($cat))
              {
                $alias = $cat->alias;
                $searchParams['cat_id'] = $cat_id;
              }
            } else
              $searchParams['cat_id'] = 0;
            $searchParams['alias'] = $alias;
            if ($area != '')
              $searchParams['location'] = $area;

            $this->redirect(Yii::app()->createUrl('Ads/ad/listBySearch',$searchParams));
            
        }
        
        $keyword = $this->get ('keyword', '');
        $cat_id = $this->get('cat_id', 0);
        $alias = $this->get('alias','');
        $area = $this->get('location', '');
        $isSort = $this->get('isSort', false);
        
        $keyword = addslashes($keyword);
        if ($keyword != '')
        {
            $this->pageTitle = Settings::SITE_NAME.' - '.$keyword
                                  .(($area == '')?'':'('.$area.')');
            
            $keyword = strip_tags($keyword);
            $pos = strpos(trim($keyword), " ");
                                                
            // check for fulltext index
            $connection=Yii::app()->db;
            $rowCount = 0;
            $sql = "SELECT * FROM `annonce` WHERE ".
                     "MATCH (title, description) AGAINST ('".$keyword."')";
            try {  
              $rowCount = $connection->createCommand($sql)->execute();
            } catch (Exception $e)
            {
              $sql1 = 'ALTER TABLE `annonce` ADD FULLTEXT(`title`)';
              $sql2 = 'ALTER TABLE `annonce` ADD FULLTEXT(`description`)';
              $sql3 = 'ALTER TABLE `annonce` ADD FULLTEXT(`title`,`description`)';
              $rowCount = $connection->createCommand($sql1)->execute();
              $rowCount = $connection->createCommand($sql2)->execute();
              $rowCount = $connection->createCommand($sql3)->execute();
              $rowCount = $connection->createCommand($sql)->execute();
            }

            
            $criteria = new CDbCriteria();            
            if ($rowCount > 0)
            {
              // fulltext search ...
              $full_keyword = $keyword;
              if ($pos == false) /* only apply for English word - searching for plural*/
              {
                $full_keyword = $this->searchEnglishWord($keyword);
              }
              $criteria->select = array("*",
                                        "MATCH (title) AGAINST ('".$full_keyword."') as rel1",
                                        "MATCH (description) AGAINST ('".$full_keyword."') as rel2");
              $criteria->addCondition("MATCH (title, description) AGAINST ('".$full_keyword."')");
              
              if (!$isSort)
                $criteria->order = '(rel1*10+rel2) DESC';
            } else {
              // in case fulltext search return nothing, let's double check
              $criteria->select = array("*",
                                        "CASE when title REGEXP '[[:<:]](".$keyword.")[[:>:]]' then 2 else 0 END as title_match", 
                                        "CASE when description REGEXP '[[:<:]](".$keyword.")[[:>:]]' then 1 else 0 END as desc_match");
              $criteria->addCondition("((title REGEXP '[[:<:]](".$keyword.")[[:>:]]') OR (description REGEXP '[[:<:]](".$keyword.")[[:>:]]'))");
              
              if (!$isSort)
                  $criteria->order = '(title_match+desc_match) DESC';
            }
            
            $criteria->addCondition('public = 1');
            if ($area != '')
              $criteria->compare('area', $area);
            $cat = Category::model()->findByPk($cat_id);
            if ($cat_id != 0 && !is_null($cat) && $cat->parent_id != 0) {
              $filteredCatIds = $this->loadCategories($cat_id);
              $criteria->addInCondition('category_id', $filteredCatIds);
            }
            
            $dataProvider = $model->listAds($criteria);
            
            $this->render('list_by_search',array(
                'model'=>$model,
                'cat_id'=>$cat_id,
                'alias'=>$alias,
                'keyword'=>stripslashes($keyword),
                'area'=>$area,
                'dataProvider'=>$dataProvider,
            ));
        }
         //else
        //$this->redirect(Yii::app()->request->baseUrl);
    }
    
    private function searchEnglishWord($keyword)
    {
      $full_keyword = $keyword;
      $str_len = strlen($keyword);
      $lastCh = strtolower($keyword[$str_len-1]);
      $preLastCh = '';
      if ($str_len >1)
        $preLastCh = strtolower($keyword[$str_len-2]);
      
      if ($lastCh == 's' || $lastCh == 'x'
          || ($lastCh == 'h' && ($preLastCh == 'c' || $preLastCh == 's')))
        $full_keyword .= ','.$keyword.'es';
      elseif ($lastCh == 'z')
        $full_keyword .= ','.$keyword.'zes';
      elseif ($preLastCh == 'f' && ($lastCh == 'f' || $lastCh == 'e'))
        $full_keyword .= ','.substr($keyword, 0, $str_len-2).'ves';
      elseif ($lastCh == 'f')
        $full_keyword .= ','.substr($keyword, 0, $str_len-1).'ves';
      elseif ($lastCh == 'y')
        $full_keyword .= ','.$keyword.'ies,'.$keyword.'s';
      elseif ($lastCh == 'o')
        $full_keyword .= ','.$keyword.'es,'.$keyword.'s';
      else
        $full_keyword .= ','.$keyword.'s';
      
      return $full_keyword;
    }
    
    private function loadCategories($cat_id)
    {
      $filteredCatIds = array();
      
      if ($cat_id != AdsSettings::ADS_ROOT_CATEGORY) 
      {
        $cat = Category::model()->findByPk($cat_id);
        if(!is_null($cat))
        {
          if ($cat->parent_id == AdsSettings::ADS_ROOT_CATEGORY)
          {
            // get sub cats of this cat
            $subCats = Category::model()->findAll('parent_id=:parentId',
                                                  array(':parentId'=>$cat->id));
            foreach($subCats as $subCat)
              $filteredCatIds[] = $subCat->id;    
          }
          else
          {
            $filteredCatIds[] = $cat->id;        
          }
        }
      } else
      {
        $subCats = Category::model()->findAll('parent_id=:parentId',
                                              array(':parentId'=>AdsSettings::ADS_ROOT_CATEGORY));
        foreach($subCats as $subCat)
        {
          $filteredCatIds[] = $subCat->id;
          
          // get sub cats of this cat
          $grSubCats = Category::model()->findAll('parent_id=:parentId',
                                                  array(':parentId'=>$subCat->id));
          foreach($grSubCats as $grSubCat)
            $filteredCatIds[] = $grSubCat->id;
        }
      }
      
      return $filteredCatIds;
    }

    public function actionViewDetails()
    {
       $id = $this->get('id', 0);
                
        $model = Annonce::model()->find('id=:id AND public = 1',array(':id'=>$id));
        $adCat = null;
        // update view
        if (!is_null($model))
        {
            include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
            $model->viewed++;
            $model->update(array('viewed'));
            // create title on very top center of page
            $this->adTitle = $model->title;
            // page title
            $this->pageTitle = $model->title.' - '.Settings::SITE_NAME;
            // description meta tag
            $adCat = $model->category; 
            $this->descriptionMetaTagContent = getFirstWordsFromString(str_replace(array('"',"'"),'',strip_tags($model->description)),200);
            if (!is_null($adCat))
                $this->descriptionMetaTagContent .= ' '.$adCat->title;
            if (!empty($model->area))
            {
                $this->adTitle .= ' - '.$model->area;
                $this->descriptionMetaTagContent .= ' '.$model->area;
            }        
        }
        $this->render('view_details', array('model'=>$model,'adCat'=>$adCat));  
    }
    
    public function actionSelectCategory()
    {
        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Common.Layout','Select a category');
        if (Settings::SITE_ACCESS==2 && Yii::app()->user->isGuest)
            throw new CHttpException(400,Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Only registered users can post ads. Please ').CHtml::link(Language::t(Yii::app()->language,'Frontend.GenericContent.Register','Register'),$this->createUrl('/site/register')));    
        elseif (!Yii::app()->user->isGuest)
        {
            if (Annonce::model()->validateEmailAndClientIP(Yii::app()->user->email)==FALSE)
                throw new CHttpException(400,UserSettings::MSG_BANNED_USER);
        }
        elseif (Yii::app()->user->isGuest)
        {
            if (Annonce::model()->validateEmailAndClientIP('')==FALSE)
                throw new CHttpException(400,UserSettings::MSG_BANNED_USER);    
        }
        //$ad_id = $this->get('ad_id', '');
        $this->render('select_category'/*, array('ad_id'=>$ad_id)*/);
    }
    
    public function actionRequirePaymentForPaidCategory()
    {
        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Preview','Views');
        $cat_id = $this->get('cat_id',0);
        //$ad_id = $this->get('id', '');
        $cat = Category::model()->findByPk($cat_id);
        $errorMsg = '';
        if (Settings::SITE_ACCESS==2 && Yii::app()->user->isGuest)
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Only registered users can post ads. Please ').CHtml::link(Language::t(Yii::app()->language,'Frontend.GenericContent.Register','Register'),$this->createUrl('/site/register'));    
        elseif (!Yii::app()->user->isGuest &&
                Annonce::model()->validateEmailAndClientIP(Yii::app()->user->email)==FALSE)
        {
            $errorMsg = UserSettings::MSG_BANNED_USER;
        }
        elseif (Yii::app()->user->isGuest &&
                Annonce::model()->validateEmailAndClientIP('')==FALSE)
        {
            $errorMsg = UserSettings::MSG_BANNED_USER;    
        }
        elseif (is_null($cat))
            $errorMsg = 'Sorry! This category is not found';
        elseif ($cat->parent_id == 0 ||
                $cat->parent_id == AdsSettings::ADS_ROOT_CATEGORY ||                
                $cat->parent_id == Settings::FAQ_CATEGORY ||
                $cat->parent_id == Settings::FOOTER_PAGES)
            	$errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You must select a sub category to create new ad');
        elseif ($cat->paid_ad_required == 0) {
            //if ($ad_id == '')  
            $this->redirect(array('/Ads/ad/create', 'cat_id'=>$cat->id, 'alias'=>$cat->alias));
            //else
              //$this->redirect(array('/Ads/ad/update', 'cat_id'=>$cat->id, 'id'=>$ad_id));
        }
        
        $this->render('require_payment_for_paid_category', array('cat'=>$cat, /*'ad_id'=>$ad_id,*/ 'errorMsg'=>$errorMsg));    
    }
    
    public function actionCreate()
    {
        if (isset(Yii::app()->session['save_ad_successfully']))
        {
            unset(Yii::app()->session['save_ad_successfully']);    
        }
        
        $model = null;
        $tx_token = '';
        $errorMsg = '';
        $cat_id = $this->get('cat_id',0);
        $cat = Category::model()->findByPk($cat_id);
                      
        if (Settings::SITE_ACCESS==2 && Yii::app()->user->isGuest)
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Only registered users can post ads. Please ').CHtml::link(Language::t(Yii::app()->language,'Frontend.GenericContent.Register','Register'),$this->createUrl('/site/register'));    
        elseif (!Yii::app()->user->isGuest &&
            Annonce::model()->validateEmailAndClientIP(Yii::app()->user->email)==FALSE)
        {
            $errorMsg = UserSettings::MSG_BANNED_USER;
        }
        elseif (Yii::app()->user->isGuest &&
            Annonce::model()->validateEmailAndClientIP('')==FALSE)
        {
            $errorMsg = UserSettings::MSG_BANNED_USER;    
        }
        elseif (is_null($cat))
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This category is not found');
        elseif ($cat->parent_id == 0 ||
                $cat->parent_id == AdsSettings::ADS_ROOT_CATEGORY ||                
                $cat->parent_id == Settings::FAQ_CATEGORY ||
                $cat->parent_id == Settings::FOOTER_PAGES)
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You must select a sub category to create new ad');
        elseif ($cat->paid_ad_required == 1 && MoneySettings::PAID_ADS_PRICE > 0) {
            $paypal = $this->paypalProcessPDT();
            
            if (!empty($paypal['errorMsg']))
              $errorMsg = $paypal['errorMsg'];
            else {
              $tx_token = $paypal['txn_id'];
              if(!empty($tx_token)) {
                $criteria = new CDbCriteria();
                $criteria->addCondition("txn_id='$tx_token'");
                if (Annonce::model()->count($criteria) > 0)
                {
                  $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message',"Sorry! This Paid transaction was already used to create another ad");
                }
              }
            }
        }
        
        if (empty($errorMsg)) {
          $model = new Annonce();
          $model->category_id = $cat->id;
          $model->txn_id = $tx_token;
          $model->opt_price = Annonce::PAYMENT_PRICE_OPTION;
          if (!Yii::app()->user->isGuest) $model->email = Yii::app()->user->email;
          $model->featured = 0;
          $model->feature_days = intval(MoneySettings::TOP_TIME1);
          $model->feature_total = intval(MoneySettings::TOP_PRICE1);
          $model->homepage = 0;
          $model->homepage_days = intval(MoneySettings::HG_TIME1);
          $model->homepage_total = intval(MoneySettings::HG_PRICE1);
          //var_dump($model);
        }
        
        $this->render('create',array('model'=>$model,'errorMsg'=>$errorMsg,'cat'=>$cat));
    }
    
    private function paypalProcessPDT()
    {
      //var_dump($_GET);
      $ret = array();
      $errorMsg = '';
      $ad_id = '';
      $promotion = array();
                  
      // read the post from PayPal system and add 'cmd'
      $req = 'cmd=_notify-synch';
      $tx_token = $this->get('tx', '');
      $cm = explode('#', $this->get('cm', ''));
      //var_dump($cm);
      $amt = $this->get('amt', 0);
      $currency_code = $this->get('cc', '');
      
      $auth_token = '';
      if (is_array($cm) && count($cm)>1)
      {
        //var_dump($cm[0]);
        if ($cm[0] == 'paid_ads')
          $auth_token = MoneySettings::PAYPAL_PDT_PAID;
        elseif ($cm[0] == 'top_ads')
          $auth_token = MoneySettings::PAYPAL_PDT_TOP;
        else
          $auth_token = MoneySettings::PAYPAL_PDT_HG;
      }
      
      $req .= "&tx=$tx_token&at=$auth_token";
      //var_dump($req);

      // post back to PayPal system to validate
      $header = "";
      $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
      $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $header .= "Host: www.paypal.com:443\r\n";
      //$header .= "Host: www.sandbox.paypal.com:443\r\n";
      //$header .= "Host: www.paypal.com:80\r\n";
      $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

      // If Live with Paypal use:
      // $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
      // $fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
      // If possible, securely post back to paypal using HTTPS
      // Your PHP server will need to be SSL enabled
      $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
      //$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

      if (!$fp) {
        // HTTP ERROR
        Yii::log("[PDT - $tx_token]HTTP error - Paypal process: $errstr", "error");
        $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! There is an HTTP error happening during Paypal process. Please check your transaction.');
      } else {
        fputs ($fp, $header . $req);
        // read the body data
        $res = '';
        $headerdone = false;
        while (!feof($fp)) {
          $line = fgets ($fp, 1024);
          if (strcmp($line, "\r\n") == 0) {
            // read the header
            $headerdone = true;
          }
          else if ($headerdone)
          {
            // header has been read. now read the contents
            $res .= $line;
          }
        }

        // parse the data
        $lines = explode("\n", $res);
        $keyarray = array();
        if (strcmp ($lines[0], "SUCCESS") == 0) {
          for ($i = 1; $i<count($lines);$i++){
            $linePair = explode("=", $lines[$i]);
            //var_dump($linePair);
            if (is_array($linePair) && (count($linePair) == 2))
            {
              list($key,$val) = $linePair;
              $keyarray[urldecode($key)] = urldecode($val);
            }
          }
          // check the payment_status is Completed
          // check that txn_id has not been previously processed
          // check that receiver_email is your Primary PayPal email
          // check that payment_amount/payment_currency are correct
          // process payment
          //var_dump($keyarray);
          $payment_status = $keyarray['payment_status'];
          $receiver_email = $keyarray['receiver_email'];
          $receiver_email = str_replace("%40", "@", $receiver_email);
          $amount = $keyarray['mc_gross'];
          $custom = explode('#', $keyarray['custom']);
          $currency = $keyarray['mc_currency'];
          $txn_id = $keyarray['txn_id'];
          if ((($payment_status != 'Completed') && 
               ($payment_status != 'Pending')) ||
              (($payment_status == 'Pending') &&
               ($keyarray['pending_reason'] != 'paymentreview')))
          {
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Your payment process is not successful or pending for review.');
          }
          else {
            $email = '';
            if (is_array($custom)) {
              if ($custom[0] == 'paid_ads')
                $email = MoneySettings::PAYPAL_EMAIL_PAID;
              else if ($custom[0] == 'top_ads')
                $email = MoneySettings::PAYPAL_EMAIL_TOP;
              else
                $email = MoneySettings::PAYPAL_EMAIL_HG;
            }
            
            //var_dump($custom, $cm);
            if (($receiver_email != $email) || ($currency != $currency_code)
                || ($amount != $amt) || ($txn_id != $tx_token) || ($custom[0] != $cm[0]))
              $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! It is an invalid Paypal confirmation.');
            else {
              $ad_id = $custom[1];
              $promotion['type'] = $custom[0];
              $promotion['days'] = isset($keyarray['item_number'])?$keyarray['item_number']:0;
              $promotion['amount'] = $amount;
            }
          }
        } else if (strcmp ($lines[0], "FAIL") == 0) {
          // log for manual investigation
          Yii::log("[PDT - $tx_token]FALL - Paypal process", "trace");
          $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! It is an invalid Paypal confirmation.');
        } else {
          if ($cm[0] == 'paid_ads')
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You must pay for the selected category first before posting an ad.');
          else
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! It is an invalid Paypal confirmation.');;
        }
      }
      fclose ($fp);
      
      if (!empty($errorMsg))
      {
        $ret['errorMsg'] = $errorMsg;
        $ret['txn_id'] = '';
      } else
      {
        $ret['errorMsg'] = '';
        $ret['txn_id'] = $tx_token;
        $ret['ad_id'] = $ad_id;
        $ret['promotion'] = $promotion;
      }
      
      return $ret;
    }
    
    public function actionUploadImage()
    {
        Yii::import("application.extensions.EAjaxUpload.qqFileUploader");
        $allowedExtensions = array('jpg','png','jpeg','gif','bmp');
        $sizeLimit = 1*1024*1024;
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

        $attachmentFolder = 'uploads/ads/temp/';
        //Create a folder tmp.userId to save file temp
        if (!is_dir(Yii::getPathOfAlias('application') . '/../' . $attachmentFolder)) {
            $r = mkdir(Yii::getPathOfAlias('application') . '/../' . $attachmentFolder, 0777, true);
            if (!$r) {
                throw new CHttpException(501, 'Could not create folder ' . $attachmentFolder);
            }
        }
        $result = $uploader->handleUpload($attachmentFolder);
        $result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result; // it's array                               
    }
    
    public function actionPreview()
    {        
        if (Yii::app()->request->IsPostRequest)
        {
            if (isset(Yii::app()->session['save_ad_successfully']))
            {
                unset(Yii::app()->session['save_ad_successfully']);
                $this->redirect(baseUrl());    
            }
            
            $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Preview your ad');
            
            if (isset($_POST['Annonce']['id']))
                $model = Annonce::model()->findByPk($_POST['Annonce']['id']);
            else
            {
                $model = new Annonce();
                $model->type = 1;
            }
            $model->setAttributes($_POST['Annonce'],FALSE);
            // if it is not allowed to insert HTML links in description
            if (AdsSettings::ALLOW_HTML_LINKS == 0)
            {
                $model->description = preg_replace('/<a [^>]+?>(.*?)<\/a>/','$1',$model->description);   
            }
             
            // validate
            if ($model->validate())
                $this->render('preview',array('model'=>$model));
            else
            {
                if ($model->isNewRecord)
                {
                    $cat = Category::model()->findByPk($model->category_id);
                    $this->render('create',array('model'=>$model,'cat'=>$cat));
                }
                else
                    $this->render('update',array('model'=>$model));
            }
            
            //$this->render('preview',array('model'=>$model));
        }
    }
    
    public function actionPerformPromotion()
    {
      $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Common.Message','Your request is invalid action!').'</h1>';
      if (AdsSettings::TOP_ADS == 1 || AdsSettings::HG == 1)
      {
        $action = $this->get('action', '');
        $id = $this->get('id', '');
        $promotion = $this->get('promotion', '');
        $days = $this->get('days', 0);
        $fee = $this->get('fee', 0);
        if (($id != '') && !is_null($model = Annonce::model()->findByPk($id)))
        {
          if ($promotion == 'top-ad') {
            if ((($days == intval(MoneySettings::TOP_TIME1)) &&
                ($fee == intval(MoneySettings::TOP_PRICE1)))
                || (($days == intval(MoneySettings::TOP_TIME2)) &&
                ($fee == intval(MoneySettings::TOP_PRICE2))))
            {
              $model->featured = 1;
              $model->feature_days = $days;
              $model->feature_total = $fee;
              $model->homepage = 0;
              $model->homepage_days = intval(MoneySettings::HG_TIME1);
              $model->homepage_total = intval(MoneySettings::HG_PRICE1);
              $msgAfterSave = '';
            }

          } elseif ($promotion == 'homepage') {
            if ((($days == intval(MoneySettings::HG_TIME1)) &&
                ($fee == intval(MoneySettings::HG_PRICE1)))
                || (($days == intval(MoneySettings::HG_TIME2)) &&
                ($fee == intval(MoneySettings::HG_PRICE2))))
            {
              $model->homepage = 1;
              $model->homepage_days = $days;
              $model->homepage_total = $fee;
              $model->featured = 0;
              $model->feature_days = intval(MoneySettings::TOP_TIME1);
              $model->feature_total = intval(MoneySettings::TOP_PRICE1);
              $msgAfterSave = '';
            }
          }

          if ($msgAfterSave == '')
            $this->render('perform_top_ad_and_homepage',
                          array('model'=>$model, 'action'=>$action,));
        }
      }
      
      if (!empty($msgAfterSave)) {
        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Error');
        $this->render('message_after_save',
                      array('msgAfterSave'=>$msgAfterSave));
      }
    }
    
    public function actionPromotionPaypalIPN()
    {
      // read the post from PayPal system and add 'cmd'
      $req = 'cmd=_notify-validate';

      foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
        Yii::log("[IPN]check IPN process: $req", "trace");
      }

      // post back to PayPal system to validate	
      $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";

      // If testing on Sandbox use: 
      // $header .= "Host: www.sandbox.paypal.com:443\r\n";
      $header .= "Host: www.paypal.com:443\r\n";
      $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

      // If testing on Sandbox use:
      //$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
      $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

      // assign posted variables to local variables
      $item_name = $this->post('item_name', '');
      $item_number = $this->post('item_number', '');
      $payment_status = $this->post('payment_status', '');
      $payment_amount = $this->post('mc_gross', '');
      $payment_currency = $this->post('mc_currency', '');
      $txn_id = $this->post('txn_id', '');
      $receiver_email = $this->post('receiver_email', '');

      if (!$fp) {
        // HTTP ERROR
        Yii::log("[IPN - $txn_id]HTTP error - Paypal process: $errstr", "error");
      } else {
        Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
        fputs ($fp, $header . $req);
        while (!feof($fp)) {
          $res = fgets ($fp, 1024);
          if (strcmp ($res, "VERIFIED") == 0) {
            // check the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment            

            $mail_Body = $req;
            
            $messageObj = new YiiMailMessage;
            $messageObj->setSubject('VERIFIED IPN');
            $messageObj->setFrom(Settings::ADMIN_EMAIL);
            $messageObj->setTo('long.tran@webflexica.com');
                        
            $emailtext = '';
            foreach ($_POST as $key => $value){
              $emailtext .= $key . " = " .$value ."\n\n";
            }
            
            $messageObj->setBody($emailtext . "\n\n" . $mail_Body,'text/html');
            if (!Yii::app()->mail->send($messageObj))
              Yii::log("[IPN - $txn_id]SEND MAIL FAIL - Paypal IPN!", "trace");

          }
          else if (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            Yii::log("[IPN - $txn_id]INVALID - Paypal IPN", "trace");

            $mail_Body = $req;
            
            $messageObj = new YiiMailMessage;
            $messageObj->setSubject('INVALID IPN');
            $messageObj->setFrom(Settings::ADMIN_EMAIL);
            $messageObj->setTo('long.tran@webflexica.com');
            
            $emailtext = '';
            foreach ($_POST as $key => $value){
              $emailtext .= $key . " = " .$value ."\n\n";
            }
            
            $messageObj->setBody($emailtext . "\n\n" . $mail_Body,'text/html');
            if (!Yii::app()->mail->send($messageObj))
              Yii::log("[IPN - $txn_id]SEND MAIL FAIL - Paypal IPN!", "trace");           
          }
        }
        fclose ($fp);
      }
    }
    
    public function actionEditUnsavedAd()
    {        
        if (Yii::app()->request->IsPostRequest)
        {
            if (isset($_POST['Annonce']['id']))
                $model = Annonce::model()->findByPk($_POST['Annonce']['id']);
            else
                $model = new Annonce();
            $model->setAttributes($_POST['Annonce'],FALSE);
            if ($model->isNewRecord)
            {
                $cat = Category::model()->findByPk($model->category_id);
                $this->render('create',array('model'=>$model,'cat'=>$cat));
            }
            else
                $this->render('update',array('model'=>$model));
        }
    }
    
    public function actionPerformSaveAd()
    {        
        if (Yii::app()->request->IsPostRequest)
        {
          //var_dump($_POST['Annonce']['featured']);die;
            $featured = $_POST['Annonce']['featured'];
            $feature_days = $_POST['Annonce']['feature_days'];
            $feature_total = $_POST['Annonce']['feature_total'];
            $homepage = $_POST['Annonce']['homepage'];
            $homepage_days = $_POST['Annonce']['homepage_days'];
            $homepage_total = $_POST['Annonce']['homepage_total'];            
            
            $result = FSM::run('Ads.Ads.save', $_POST);
            $model = $result->model;
            $msgAfterSave = '';
            
            if (!$result->hasErrors())
            {
                if ($result->action == 'create')
                {
                    if ($model->public == 0)
                    {
                        $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Common.Message','Congratulations!').'</h1>
                                          <p style="margin-bottom: 10px;">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Your posting needs an activation to be live on the site.').'</p>
                                          <p class="space-message-bottom">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','You will receive an email confirmation with the activation link also allowing you to edit or delete the ad.').'</p>';
                        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Congratulations');
                    }
                    else
                    {
                        $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Thanks for posting your ad!').'</h1>
                                          <p class="space-message-bottom">&nbsp;</p>';
                        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Thank you!');
                    }
                }
                else {
                    $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Your ad was edited successfully!').'</h1>
                                      <p class="space-message-bottom">&nbsp;</p>';
                    $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Update ad successfully');
                }
            }
            else {
                $msgAfterSave = '<p class="space-message-bottom">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Saving your ad has errors! Please try again').'</p>';
                $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Error');   
            }
            
            if (!$result->hasErrors() && ($featured == 1 || $homepage == 1))
            {
              if ($featured == 1)
              {
                $this->redirect(Yii::app()->createUrl(
                    'Ads/ad/performPromotion',
                    array('action'=>$result->action,
                          'promotion'=>'top-ad',
                          'days'=>$feature_days,
                          'fee'=>$feature_total,
                          'id'=>$model->id,)
                ));
              } else {
                $this->redirect(Yii::app()->createUrl(
                    'Ads/ad/performPromotion',
                    array('action'=>$result->action,
                          'promotion'=>'homepage',
                          'days'=>$homepage_days,
                          'fee'=>$homepage_total,
                          'id'=>$model->id,)
                ));
              }
              //$this->render('perform_top_ad_and_homepage',
              //              array('model'=>$model, 'action'=>$result->action,));
            }
            else
            {
                if (!$result->hasErrors())
                    Yii::app()->session['save_ad_successfully'] = TRUE;
                $this->render('message_after_save', array('msgAfterSave'=>$msgAfterSave));
            }
        } else {
          //var_dump($_GET);
          $action = $this->get('action','');
          $type = $this->get('type','');            
          if ($type == 'cancel') {
            if ($action == 'create')
            {                    
                if (Yii::app()->user->isGuest)
                {
                    $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Common.Message','Congratulations!').'</h1>
                                      <p style="margin-bottom: 10px;">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','You have just cancelled to promote the ad.').'</p>
                                      <p style="margin-bottom: 10px;">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Your posting needs an activation to be live on the site.').'</p>
                                      <p class="space-message-bottom">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','You will receive an email confirmation with the activation link also allowing you to edit or delete the ad.').'</p>';
                    $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Congratulations');
                }
                else
                {
                    $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Thanks for posting your ad!').'</h1>
                                      <p class="space-message-bottom">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','You have just cancelled to promote the ad.').'</p>';
                    $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Congratulations');
                }
            }
            else {
                $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Your ad was edited successfully!').'</h1>
                                  <p class="space-message-bottom">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','You have just cancelled to promote the ad.').'</p>';
                $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Update ad successfully');  
            }
          }
          elseif ($type == 'return')
          {
            $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Thanks for promoting your ad!').'</h1>';
             $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Update ad successfully');
                                   
            $paypal = $this->paypalProcessPDT();            
            if (!empty($paypal['errorMsg']))
            {
                $msgAfterSave = '<h1 class="title">'.$paypal['errorMsg'].'</h1>';
                $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Error'); 
            }
            else {
              $id = $paypal['ad_id'];
              $model = Annonce::model()->find('id=:id',array(':id'=>$id));
              if (is_null($model))
              {
                $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad is not found.').'</h1>';
                $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Error');
              }
              else {
                $model->public = 1;
                $model->update_time = date('Y-m-d H:i:s');
                $txn_id = $paypal['txn_id'];
                $promotion = $paypal['promotion'];                
                if ($promotion['type'] == 'hp_gallery')
                {
                  if ($model->homepage_txn != $txn_id)
                  {
                    $model->homepage = 1;
                    $model->homepage_days += $promotion['days'];
                    $model->homepage_total += $promotion['amount'];
                    $model->homepage_txn = $txn_id;
                    $model->update(array('public','update_time','homepage',
                                         'homepage_txn','homepage_days','homepage_total'));
                  }
                } else {
                  if ($model->feature_txn != $txn_id)
                  {
                    $model->featured = 1;
                    $model->feature_days += $promotion['days'];
                    $model->feature_total += $promotion['amount'];
                    $model->feature_txn = $txn_id;
                    $model->update(array('public','update_time','featured',
                                         'feature_txn','feature_days','feature_total'));
                  }
                }
              }
            }            
          }
          else
          {
            $msgAfterSave = '<h1 class="title">'.Language::t(Yii::app()->language,'Frontend.Common.Message','Your request is invalid action!').'</h1>';
            $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Error');
          }

          //var_dump($msgAfterSave);
          $this->render('message_after_save', array('msgAfterSave'=>$msgAfterSave));
        }
    }
    
    public function actionActivate()
    {
        $errorMsg = '';
        $id = $this->get('id','');
        $code = $this->get('code','');
        $model = Annonce::model()->find('id=:id',array(':id'=>$id));
        
        if (is_null($model))
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad is not found.');
        elseif($model->public == 1)
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad has been already activated.');
        elseif($model->code != $code)
            $errorMsg = Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! The activation code is not valid.');
        else
        {
            $model->public = 1;
            //$model->code = null;
            $model->create_time = $model->update_time = date('Y-m-d H:i:s');
            $model->update(array('public','code','create_time','update_time'));
        }
        
         $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Update ad successfully');
        
        $this->render('activate',array('errorMsg'=>$errorMsg,'model'=>$model));
    }
    
    public function actionReplyToAd()
    {
    	$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Ads.Reply','Reply to this ad');
        $model = new ReplyForm();
        $ad_id = $this->get('id','');
        $ad = Annonce::model()->find('id=:id AND public = 1',array(':id'=>$ad_id));
        $sendSuccessfully = FALSE;
        if (Yii::app()->request->isPostRequest && !is_null($ad))
        {
            $_POST['Annonce'] = $ad->attributes;
            $result = FSM::run('Ads.Ads.sendReplyToAd', $_POST);
            $model = $result->model;
            if (!$result->hasErrors())
            {
                $sendSuccessfully = TRUE;    
            }    
        }
        $this->render('reply_to_ad',array('model'=>$model,'ad'=>$ad,'sendSuccessfully'=>$sendSuccessfully));    
    }
    
    public function actionEmailAdToFriend()
    {
    	$this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Ads.EmailToFriend','Email this ad to a friend');
        $model = new EmailAdToFriendForm();
        $ad_id = $this->get('id','');
        $ad = Annonce::model()->find('id=:id AND public = 1',array(':id'=>$ad_id));
        $sendSuccessfully = FALSE;
        if (Yii::app()->request->isPostRequest && !is_null($ad))
        {
            $_POST['Annonce'] = $ad->attributes;
            $result = FSM::run('Ads.Ads.emailAdToFriend', $_POST);
            $model = $result->model;
            if (!$result->hasErrors())
            {
                $sendSuccessfully = TRUE;    
            }    
        }
        $this->render('email_ad_to_friend',array('model'=>$model,'ad'=>$ad,'sendSuccessfully'=>$sendSuccessfully));    
    }
    
    public function actionViewDetailsAsPrint()
    {
        Yii::app()->layout = 'print';
        $id = $this->get('id',0);
        $model = Annonce::model()->find('id=:id AND public = 1',array(':id'=>$id));
        $adCat = null;
        // update view
        if (!is_null($model))
        {
            include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
            $model->viewed++;
            $model->update(array('viewed'));
            // page title
            $this->pageTitle = Settings::SITE_NAME.' - '.$model->title;
            $adCat = $model->category;       
        }
        $this->render('view_details_as_print',array('model'=>$model,'adCat'=>$adCat));    
    }
    
    public function actionUpdate()
    {
        if (isset(Yii::app()->session['save_ad_successfully']))
        {
            unset(Yii::app()->session['save_ad_successfully']);    
        }
        
        if (!Yii::app()->user->isGuest)
        {
            if (Annonce::model()->validateEmailAndClientIP(Yii::app()->user->email)==FALSE)
                throw new CHttpException(400,UserSettings::MSG_BANNED_USER);
        }
        else
        {
            if (Annonce::model()->validateEmailAndClientIP($this->get('email',''))==FALSE)
                throw new CHttpException(400,UserSettings::MSG_BANNED_USER);    
        }
            
        $id = $this->get('id','');
        //$cat_id = $this->get('cat_id', 0);
        $model = Annonce::model()->find('id=:id',array(':id'=>$id));
        
        if (is_null($model))
            throw new CHttpException(400,Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad is not found.'));
        if (!Yii::app()->user->isGuest)
        {
            if (!Yii::app()->user->checkAccess('administrators') && $model->email != Yii::app()->user->email)
                throw new CHttpException(400,Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You do not have the permission to edit this ad'));
        }
        else
        {
            $email = $this->get('email','');
            $code = $this->get('code','');    
            if ($model->email != $email || $model->code != $code)
                throw new CHttpException(400,Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You do not have the permission to edit this ad'));
        }
        
//        if ($cat_id != '')
//        {
//          $cat = Category::model()->findByPk($cat_id);
//          if (is_null($cat))
//              throw new CHttpException(400,Yii::t('Ads.Ads','Sorry! This category is not found'));
//          
//          $currCat = $model->category;
//          if (($currCat->paid_ad_required == 0) && ($cat->paid_ad_required == 1)) {
//              $paypal = $this->paypalProcessPDT();
//
//              if (!empty($paypal['errorMsg']))
//                throw new CHttpException(400,Yii::t('Ads.Ads',$paypal['errorMsg']));
//              else {
//                $tx_token = $paypal['txn_id'];
//                if(!empty($tx_token)) {
//                  $criteria = new CDbCriteria();
//                  $criteria->addCondition("txn_id='$tx_token'");
//                  if (Annonce::model()->count($criteria) > 0)
//                  {
//                    throw new CHttpException(400,Yii::t('Ads.Ads','Sorry! This Paid transaction was already used to update another ad'));
//                  }                   
//                }
//              }
//          }
//          $model->category_id = $cat_id;
//        }
            
        $model->featured = 0;
        $model->feature_days = intval(MoneySettings::TOP_TIME1);
        $model->feature_total = intval(MoneySettings::TOP_PRICE1);
        $model->homepage = 0;
        $model->homepage_days = intval(MoneySettings::HG_TIME1);
        $model->homepage_total = intval(MoneySettings::HG_PRICE1);
        
        //$cat = Category::model()->findByPk($model->category_id);
        
        $this->render('update',array('model'=>$model/*, 'cat'=>$cat*/));    
    }
    
    public function actionAdvancedSearch()
    {
        $model = new Annonce();
        $dataProvider = null;
        $isSort = false;
        
        if (isset($_GET['mode']) && trim($_GET['mode'])=='search')
        {
            if (isset($_GET['Annonce']))
            {
                $model->setAttributes($_GET['Annonce'],FALSE);
                if (isset($_GET['Annonce']['searchedKeyword']))
                    $model->searchedKeyword = trim(str_replace("'",'',$_GET['Annonce']['searchedKeyword']));
                if (isset($_GET['Annonce']['exactPhrase']))
                    $model->exactPhrase = intval($_GET['Annonce']['exactPhrase']);
                if (isset($_GET['Annonce']['priceFrom']))
                    $model->priceFrom = intval($_GET['Annonce']['priceFrom']);
                if (isset($_GET['Annonce']['priceTo']))
                    $model->priceTo = intval($_GET['Annonce']['priceTo']);
                if (isset($_GET['Annonce']['searchWithPhoto']))
                    $model->searchWithPhoto = intval($_GET['Annonce']['searchWithPhoto']);
            }
            $isSort = $this->get('isSort', false);
            
            $criteria = new CDbCriteria();
            $criteria->compare('id',$model->id);
            $criteria->addCondition('public = 1');
            
            if ($model->searchedKeyword != '')
            {
                $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Frontend.Common.Layout','Advanced search').' - '.$model->searchedKeyword;
                
                $pos = strpos(trim($model->searchedKeyword), " ");
                $rowCount = 0;
                
                if ($model->exactPhrase != 1)
                {
                  // check for fulltext index
                  $connection=Yii::app()->db;
                  $sql = "SELECT * FROM `annonce` WHERE ".
                         "MATCH (title, description) AGAINST ('".$model->searchedKeyword."')";
                  try {
                    $rowCount = $connection->createCommand($sql)->execute();                    
                  } catch (Exception $e)
                  {
                    $sql1 = 'ALTER TABLE `annonce` ADD FULLTEXT(`title`)';
                    $sql2 = 'ALTER TABLE `annonce` ADD FULLTEXT(`description`)';
                    $sql3 = 'ALTER TABLE `annonce` ADD FULLTEXT(`title`,`description`)';
                    $rowCount = $connection->createCommand($sql1)->execute();
                    $rowCount = $connection->createCommand($sql2)->execute();
                    $rowCount = $connection->createCommand($sql3)->execute();
                    
                    $rowCount = $connection->createCommand($sql)->execute();
                  }
                  
                  if ($rowCount > 0) {
                    // fulltext search ...
                    $full_keyword = $model->searchedKeyword;
                    if ($pos == false)
                    {
                      $full_keyword = $this->searchEnglishWord($model->searchedKeyword);
                    }
                    $criteria->select = array("*",
                                              "MATCH (title) AGAINST ('".$full_keyword."') as rel1",
                                              "MATCH (description) AGAINST ('".$full_keyword."') as rel2");
                    $criteria->addCondition("MATCH (title, description) AGAINST ('".$full_keyword."')");
                    
                    if (!$isSort)
                      $criteria->order = '(rel1*10+rel2) DESC';
                  }
                }
                
                if ($model->exactPhrase == 1 || $rowCount == 0)
                {
                  $criteria->select = array("*",
                                            "CASE when title REGEXP '[[:<:]](".$model->searchedKeyword.
                                                            ")[[:>:]]' then 2 else 0 END as title_match", 
                                            "CASE when description REGEXP '[[:<:]](".$model->searchedKeyword.
                                                            ")[[:>:]]' then 1 else 0 END as desc_match");
                  $criteria->addCondition("((title REGEXP '[[:<:]](".$model->searchedKeyword.
                                             ")[[:>:]]') OR (description REGEXP '[[:<:]](".
                                                        $model->searchedKeyword.")[[:>:]]'))");

                  if (!$isSort)
                    $criteria->order = '(title_match+desc_match) DESC';
                }
            }
            
            if (intval($model->category_id) != 0 &&
                intval($model->category_id) != AdsSettings::ADS_ROOT_CATEGORY &&
                intval($model->category_id) != Settings::FAQ_CATEGORY &&
                intval($model->category_id) != Settings::FOOTER_PAGES)
            {
              $filteredCatIds = $this->loadCategories(intval($model->category_id));
              $criteria->addInCondition('category_id',$filteredCatIds);
            }
            $criteria->compare('area',$model->area);
            $criteria->compare('zipcode',$model->zipcode);
            if (!empty($model->priceFrom) || !empty($model->priceTo))
            {
                $criteria->compare('opt_price',Annonce::PAYMENT_PRICE_OPTION);
                if (!empty($model->priceFrom))
                {
                    $criteria->addCondition('price >= '.$model->priceFrom);
                }
                if (!empty($model->priceTo))
                {
                    $criteria->addCondition('price <= '.$model->priceTo);
                }    
            }
            if ($model->searchWithPhoto == 1)
            {
                $criteria->addCondition("(photos IS NOT NULL) AND (photos <> '')");    
            }
            
            //var_dump($criteria->condition);
            
            $dataProvider = $model->listAds($criteria);
            
//            $this->render('list_by_search',array(
//                          'model'=>$model,
//                          'cat_id'=>intval($model->category_id),
//                          'keyword'=>$model->searchedKeyword,
//                          'dataProvider'=>$dataProvider,
//            ));
        }
        $this->render('advanced_search',
                      array('model'=>$model,'dataProvider'=>$dataProvider));    
    }
    
    public function actionDelete()
    {
        $deleteSuccess = FALSE;
        $msgs = array();
        $params = array(
            'ids' => $this->get('id',0),
            'emails' => $this->get('email',''),
            'codes' => $this->get('code','')
        );
        $result = FSM::run('Ads.Ads.delete', $params);
        if (!$result->hasErrors())
        {
            $deleteSuccess = TRUE;
            $msgs[] = Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad has been deleted successfully.');
        }
        else
        {
            $errors = $result->getErrors('ErrorCode');
            if (is_array($errors) && count($errors) > 0)
            {
                foreach($errors as $error)
                    $msgs[] = $error;
            }
        }
        
        $this->pageTitle = Settings::SITE_NAME.' - '.Language::t(Yii::app()->language,'Backend.Common.Views','Ad deleted');
        $this->render('delete_ad',array('msgs'=>$msgs,'deleteSuccess'=>$deleteSuccess));    
    }
    
    public function actionFeed()
    {
        Yii::import('application.vendors.*');
        require_once 'Zend/Loader/Autoloader.php';
        spl_autoload_unregister(array('YiiBase','autoload')); 
        spl_autoload_register(array('Zend_Loader_Autoloader','autoload')); 
        spl_autoload_register(array('YiiBase','autoload'));
                
        // category
        $cat_id = $this->get('cat_id',AdsSettings::ADS_ROOT_CATEGORY);
        $cat = Category::model()->findByPk($cat_id);
        if (is_null($cat))
            throw new CHttpException(400,Language::t(Yii::app()->language,'Frontend.Ads.Message','This category is not found'));
        $area = $this->get('location','');
        $filteredCatIds = $this->loadCategories($cat_id);
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('category_id',$filteredCatIds);
        $criteria->addCondition('public = 1');
        $criteria->order = 'create_time DESC';
        if ($area != '')
            $criteria->compare('area', $area);
        
        // retrieve all ad posts
        $posts = Annonce::model()->findAll($criteria);        
        // convert to the format needed by Zend_Feed
        $entries = array();
        foreach($posts as $post)
        {
            $entries[] = array(
                'title'=>$post->title,
                'link'=>CHtml::encode($this->createAbsoluteUrl('ad/viewDetails', 
                                                               array('id'=>$post->id,
                                                                     'alias'=>  str_replace(array(' ','/','\\'), '-', $post->title)))),
                'description'=>$post->description,
                'lastUpdate'=> strtotime($post->create_time),
            );            
        }
        
        // generate and render RSS feed
        $feedTitle = Settings::SITE_NAME.' - Ads Feed';
        $cat = Category::model()->findByPk($cat_id);
        if (!is_null($cat))
          $feedTitle .= ' - '.$cat->title;
        if ($area != '')
          $feedTitle .= ' - '.$area;
        
        $feed=Zend_Feed::importArray(array(
            'title'   => $feedTitle,
            'link'    => $this->createUrl(''),
            'charset' => 'UTF-8',
            'entries' => $entries,      
        ), 'rss');
        
        $feed->send();
    }
}
