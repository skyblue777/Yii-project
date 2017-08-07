<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ExpirationCommand extends CConsoleCommand
{
  public function run($args)
  {
    // clean cache images
    $path = Yii::app()->basePath.'/runtime/fleximage_cache';
    if ($handle = opendir($path)) 
    {
        $files = array();
        while (false !== ($file = readdir($handle))) 
        {
            if (!is_dir($file))
                unlink($path.'/'.$file);
        }

    }      
    Yii::import('Ads.models.Annonce');
    $ads = Annonce::model()->findAll();
    foreach($ads as $ad)
    {
      $isExpired = true;

      $start = time();
      $end = time();
      if (trim($ad->date) == '' || substr($ad->date,0,10) == '0000-00-00')
        $start = strtotime ($ad->update_time);
      else
        $start = strtotime ($ad->date);
      $days_diff = round(($end - $start)/60/60/120);

      if ($ad->featured == 1)
      {
        $ad->feature_days -= $days_diff;

        if ($ad->feature_days > 0)
          $isExpired = false;
        else {
          $ad->feature_days = 0;
          $ad->featured = 0;
        }
      }

      if ($ad->homepage == 1)
      {
        $ad->homepage_days -= $days_diff;

        if ($ad->homepage_days > 0)
          $isExpired = false;
        else {
          $ad->homepage_days = 0;
          $ad->homepage = 0;
        }
      }

      if ($isExpired) {
        $isExpired = false;
        $start = strtotime($ad->create_time);
        $days_diff = round(($end - $start)/60/60/24);

        if ($days_diff > Settings::EXPIRATION)
        {
          $isExpired = true;
          $ad->deletePhotos();
          if (Annonce::model()->deleteByPk($ad->id))
          {
            Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
            $messageObj = new YiiMailMessage;
            $messageObj->setSubject('['.Settings::SITE_NAME.'] '.Language::t(Yii::app()->language,'Frontend.Message.MailSubject','Your ad has expired'));
            $messageObj->setFrom(Settings::ADMIN_EMAIL);
            $messageObj->setTo($ad->email);
            Yii::app()->mail->viewPath = 'application.runtime.emails'; 
            $messageObj->view = 'expriation_email'.'_'.Yii::app()->language;
            // create content 
            $messageObj->setBody(array(
                'user_email'=>$ad->email,
                'site_name'=>Settings::SITE_NAME,
                'ad_title'=>$ad->title,
                'site_url'=>Settings::SITE_URL,
            ), 'text/html');
            if (!Yii::app()->mail->send($messageObj))
                $this->result->fail(SEND_MAIL_FAILED, Yii::t('Expiration','Expiraton Email was sent failed!'));
          }
        }
      }

      if (!$isExpired) {
        Annonce::model()->updateByPk($ad->id, array('featured'=>$ad->featured,
                                                    'feature_days'=>$ad->feature_days,
                                                    'homepage'=>$ad->homepage,
                                                    'homepage_days'=>$ad->homepage_days,
                                                    'date'=>date('Y-m-d H:i:s')));
      }
    }    
  }
}

?>
