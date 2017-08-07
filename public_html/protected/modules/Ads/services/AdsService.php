<?php
class AdsService extends FServiceBase
{
    public function delete($params) {        
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid ID.'));
        }
        $emails = $this->getParam($params, 'emails', array());
        $codes = $this->getParam($params, 'codes', array());
      
        if (!is_array($ids)) $ids = array($ids);
        if (!is_array($emails)) $emails = array($emails);
        if (!is_array($codes)) $codes = array($codes);
        foreach($ids as $key => $id)
        {
            $model = Annonce::model()->findByPk($id);
            if (is_null($model))
            {
                $this->result->fail('ADS_NOT_FOUND', Language::t(Yii::app()->language,'Frontend.Ads.Message','Ads is not found'));
                continue;    
            }
            if (!Yii::app()->user->checkAccess('administrators'))
            {
                if (!Yii::app()->user->isGuest)
                {
                    if ($model->email != Yii::app()->user->email)
                    {
                        $this->result->fail('HAVE_NO_RIGHT', Language::t(Yii::app()->language,'Frontend.Ads.Message','You have no permission to delete this ad.'));
                        continue;    
                    }    
                }
                else
                {
                    if (!isset($emails[$key]) || !isset($codes[$key]))
                    {
                        $this->result->fail('HAVE_NO_RIGHT', Language::t(Yii::app()->language,'Frontend.Ads.Message','Email or Code is required.'));
                        continue;    
                    }
                    $email = $emails[$key];
                    $code = $codes[$key];
                    if ($model->email != $email || $model->code != $code)
                    {
                        $this->result->fail('HAVE_NO_RIGHT', Language::t(Yii::app()->language,'Frontend.Ads.Message','You have no permission to delete this ad.'));
                        continue;    
                    }    
                }    
            }
            AdFavorites::model()->deleteAll('annonce_id=:ad_id',array(':ad_id'=>$model->id));
            $model->delete();
        }
        return $this->result;
    }
    
    public function activate($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id)
        {
            $model = Annonce::model()->findByPk($id);
            if (is_null($model))
            {
                $this->result->fail('ADS_NOT_FOUND', 'Ads is not found');
                continue;    
            }
            $model->public = 1;
            $model->update(array('public'));
        }
        return $this->result;
    }
    
    public function deleteFileInTemp($params=array())
    {
        $fileName = $this->getParam($params,'fileName','');
        $uploadedFiles = $this->getParam($params,'uploadedFiles','');
        
        $arrUploadedFiles = explode(',',$uploadedFiles);           
        
        if($fileName!=''){
            $attachmentFolder = 'uploads/ads/temp/';
            $basePath = Yii::getPathOfAlias("webroot").'/'.$attachmentFolder;
            $filePath = $basePath.'/'.$fileName;                                
            if (file_exists($filePath))
            {
                unlink($filePath);
                for($i=0;$i<count($arrUploadedFiles);$i++)
                {
                    if ($arrUploadedFiles[$i]==$fileName)
                    {
                        unset($arrUploadedFiles[$i]);
                        break;
                    }
                }
            }
        }
        $this->result->processed('uploadedFiles',implode(',',$arrUploadedFiles));
        return $this->result;
    }
    
    public function save($params)
    {
        if (Settings::SITE_ACCESS==2 && Yii::app()->user->isGuest)
        {
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Only registered users can post ads.'));
            return $this->result;    
        }
        
        $model = $this->getModel($params['Annonce'],'Annonce');
        $this->result->processed('model', $model);
        
        $model->description = $params['Annonce']['description'];
        $model->video = $params['Annonce']['video'];
        
        $featured = 0;
        $homepage = 0;
        if (isset($params['Annonce']['featured'])) $featured = $params['Annonce']['featured'];
        if (isset($params['Annonce']['homepage'])) $homepage = $params['Annonce']['homepage'];
        
        if (empty($model->id))
        {
            $model->type = 1;
            $model->featured = $model->feature_days = $model->feature_total = 0;
            $model->homepage = $model->homepage_days = $model->homepage_total = 0;
        }
        if (empty($model->price) || in_array($model->opt_price,array(Annonce::FREE_PRICE_OPTION,Annonce::CONTACT_PRICE_OPTION,Annonce::SWAP_TRADE_PRICE_OPTION)))
            $model->price = 0;    
        
        if (! $model->validate())
        {
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        }
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        else
        {
            if (empty($model->id))
            {
                $this->result->processed('action','create');
                // date created
                $model->create_time = $model->update_time = date('Y-m-d H:i:s');
                // if user not login, public = 0, if user login, public = 1
                if (!Yii::app()->user->isGuest)
                    $model->public = 1;
                else
                {
                    include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
                    $model->public = 0;
                    $model->code = randomString('upper',8);
                }
                $model->id = null;
                if (!$model->save())
                    $this->result->fail(ERROR_HANDLING_DB, Language::t(Yii::app()->language,'Frontend.Ads.Message','Error while saving submitted data into database.'));
                else
                {
                    // if user not login, create new user with new email
                    if ($model->public == 0)
                    {
                        Yii::import('User.models.User');
                        $user = User::model()->findByAttributes(array('email' => $model->email));
                        if (is_null($user))
                        {
                            $user = new User();
                            $user->email = $model->email;
                            $user->username = $model->email;
                            $user->first_name = $user->last_name = ' ';
                            $user->password = md5('12345');
                            $user->status = User::STATUS_DEACTIVE;
                            $user->save(FALSE);   
                        }
                    }
                    
                    if (isset($params['hdUploadedFiles']) && $params['hdUploadedFiles']!='')
                    {
                        $arrImages = explode(',',$params['hdUploadedFiles']);
                        if (count($arrImages) > 0)
                        {
                            $tempFolder = 'uploads/ads/temp/';
                            $uploadFolder = 'uploads/ads/';
                            $newImages = array();
                            foreach($arrImages as $fileName)
                            {
                                $fileName = trim($fileName);
                                if ($fileName != '')
                                {
                                    if (!file_exists($tempFolder.$fileName)) continue;
                                    $newFileName = $model->id.'_'.$fileName;
                                    $imagePath = $uploadFolder.$newFileName;
                                    rename($tempFolder.$fileName, $imagePath);
                                    $newImages[] = $newFileName;   
                                }    
                            }
                            if (count($newImages) > 0)
                            {
                                $model->photos = serialize($newImages);
                                $model->update(array('photos'));    
                            }    
                        }
                        $_POST['hdUploadedFiles'] = '';
                    }
                    // if user not login, send activation mail
                    if ($model->public == 0)
                    {
                        Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
                        $messageObj = new YiiMailMessage;
                        $messageObj->setSubject(Language::t(Yii::app()->language,'Frontend.Message.MailSubject','Ad Confirmation'));
                        $messageObj->setFrom(Settings::ADMIN_EMAIL);
                        $messageObj->setTo($model->email);
                        Yii::app()->mail->viewPath = 'application.runtime.emails'; 
                        $messageObj->view = 'activation_email'.'_'.Yii::app()->language;
                        
                        // create content
                        $activateLink = /*baseUrl().*/Yii::app()->createAbsoluteUrl('/Ads/ad/activate',array('id'=>$model->id,'code'=>$model->code));
                        $editLink = /*baseUrl().*/Yii::app()->createAbsoluteUrl('/Ads/ad/update',
                                                                                array('id'=>$model->id,
                                                                                      'alias'=>str_replace(array(' ','/','\\'),'-',$model->title),
                                                                                      'email'=>$model->email,
                                                                                      'code'=>$model->code)); 
                        $messageObj->setBody(array(
                            'site_name'=>Settings::SITE_NAME,
                            'title_of_post'=>$model->title,
                            'activate_link'=>$activateLink,
                            'modified_link'=>$editLink,
                            'site_url'=>baseUrl(),
                        ), 'text/html');
                        if (!Yii::app()->mail->send($messageObj))
                            $this->result->fail('SEND_MAIL_FAILED', Language::t(Yii::app()->language,'Frontend.Ads.Message','Activation Email was sent failed!'));    
                    }
                }
            }
            else
            {
                $this->result->processed('action','update');
                $updatedAttrs = array(
                    'category_id' => $model->category_id,
                    'title' => $model->title,
                    'price' => $model->price,
                    'opt_price' => $model->opt_price,
                    'description' => $model->description,
                    'description_notag' => trim(strip_tags($model->description)),
                    'area' => $model->area,
                    'zipcode' => $model->zipcode,
                    'lat' => $model->lat,
                    'lng' => $model->lng,
                    'video' => $model->video,
                    'update_time' => date('Y-m-d H:i:s'),    
                );
                if (Annonce::model()->updateByPk($model->id,$updatedAttrs) > 0)
                {
                    if (isset($params['hdUploadedFiles']) && $params['hdUploadedFiles']!='')
                    {
                        $arrImages = explode(',',$params['hdUploadedFiles']);
                        if (count($arrImages) > 0)
                        {
                            // get current images
                            $newImages = array();
                            if (!empty($model->photos))
                                $newImages = unserialize($model->photos);
                            
                            $tempFolder = 'uploads/ads/temp/';
                            $uploadFolder = 'uploads/ads/';
                            foreach($arrImages as $fileName)
                            {
                                $fileName = trim($fileName);
                                if ($fileName != '')
                                {
                                    if (!file_exists($tempFolder.$fileName)) continue;
                                    $newFileName = $model->id.'_'.$fileName;
                                    $imagePath = $uploadFolder.$newFileName;
                                    rename($tempFolder.$fileName, $imagePath);
                                    $newImages[] = $newFileName;   
                                }    
                            }
                            if (count($newImages) > 0)
                            {
                                $model->photos = serialize($newImages);
                                Annonce::model()->updateByPk($model->id,array('photos'=>$model->photos));    
                            }    
                        }
                        $_POST['hdUploadedFiles'] = '';
                    }        
                }
            }   
        }
        
        return $this->result;
    }
    
    public function sendReplyToAd($params)
    {
        $model = new ReplyForm();
        $model->setAttributes($params['ReplyForm'],FALSE);
        $this->result->processed('model', $model);
        $ad = $this->getModel($params['Annonce'],'Annonce');
        if (!$model->validate())
        {
            $this->result->fail('', Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid data!'));
            return $this->result;
        }    
        // send mail
        Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
        $messageObj = new YiiMailMessage;
        $messageObj->setSubject('['.Settings::SITE_NAME.'] '.
	        Language::t(Yii::app()->language,'Frontend.Message.MailSubject','A reply sent to your ad')
        );

	    $messageObj->setFrom($model->senderEmail);
	    $messageObj->setTo($ad->email);

	    Yii::app()->mail->viewPath = 'application.runtime.emails';
	    $messageObj->view = 'reply_to_ad'.'_'.Yii::app()->language;
	    // create content
	    $urlParams = array('id'=>$ad->id,
	                       'alias'=>str_replace(array(' ','/','\\'),'-',$ad->title));
	    if ($ad->area != '')
		    $urlParams['area'] = $ad->area;
	    $ad_url = Yii::app()->createAbsoluteUrl('/Ads/ad/viewDetails',$urlParams);

	    $messageObj->setBody(array(
		    'poster_email'=>$ad->email,
		    'sender_name'=>$model->senderName.' ',
		    'sender_email'=>$model->senderEmail,
		    'ad_title'=>$ad->title,
		    'message'=>nl2br($model->content),
		    'ad_url'=>$ad_url,
		    'site_name'=>Settings::SITE_NAME,
	        ), 'text/html');

        if (!Yii::app()->mail->send($messageObj))
            $this->result->fail('SEND_MAIL_FAILED',Language::t(Yii::app()->language,'Frontend.Ads.Message','E-mail was sent failed!'));
        return $this->result;        
    }
    
    public function emailAdToFriend($params)
    {
        $model = new EmailAdToFriendForm();
        $model->setAttributes($params['EmailAdToFriendForm'],FALSE);
        $this->result->processed('model', $model);
        $ad = $this->getModel($params['Annonce'],'Annonce');
        if (!$model->validate())
        {
            $this->result->fail('', Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid data!'));
            return $this->result;
        }    
        // send mail
        Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
        $messageObj = new YiiMailMessage;
        $messageObj->setSubject('Check out "'.$ad->title.'"');
        $messageObj->setFrom($model->senderEmail);
        $messageObj->setTo($model->receiverEmail);
        Yii::app()->mail->viewPath = 'application.modules.Ads.views.mail';
        $messageObj->view = 'email_ad_to_friend';
        $messageObj->setBody(array('ad'=>$ad,'model'=>$model), 'text/html');
        if (!Yii::app()->mail->send($messageObj))
            $this->result->fail('SEND_MAIL_FAILED', Language::t(Yii::app()->language,'Frontend.Ads.Message','E-mail was sent failed!'));
        return $this->result;        
    }
    
    public function report($params)
    {
        $id = $this->getParam($params, 'id', '');
        $action = $this->getParam($params, 'action', 'spam');
        $ad = Annonce::model()->find('id=:id AND public=1',array(':id'=>$id));
        if (is_null($ad))
        {
            $this->result->fail('AD_NOT_FOUND', Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found'));
            return $this->result;    
        }
        
        $ad->replied++;
        $ad->update(array('replied'));    
        
        return $this->result;
    }
    
    public function addToFavorites($params)
    {
        if (Yii::app()->user->isGuest)
        {
            $this->result->fail('NOT_LOGIN_YET', Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry, only registered users can add favorite ads'));
            return $this->result;    
        }
        
        $id = $this->getParam($params, 'id', '');
        $ad = Annonce::model()->find('id=:id AND public=1',array(':id'=>$id));
        if (is_null($ad))
        {
            $this->result->fail('AD_NOT_FOUND', Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found'));
            return $this->result;    
        }
        
        if (AdFavorites::model()->count('annonce_id=:ad_id AND user_id=:user_id',array(':ad_id'=>$ad->id,':user_id'=>Yii::app()->user->id)) > 0)
        {
            $this->result->fail('FAVORITE_EXISTS', Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! You have already added this ad to your favorite.'));
            return $this->result;    
        }
        $fa = new AdFavorites();
        $fa->user_id = Yii::app()->user->id;
        $fa->annonce_id = $ad->id;
        if (!$fa->save())
            $this->result->fail('SAVE_FAILED', Language::t(Yii::app()->language,'Frontend.Ads.Message','Add to favorites failed.'));    
        
        return $this->result;
    }
    
    public function deleteUploadedPhoto($params) {
        if (Yii::app()->user->isGuest)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Please login to ferform this action'));
        $photo_name = trim($this->getParam($params, 'photo_name', ''));
        if ($photo_name=='')
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Please provide photo name'));
        $ad_id = $this->getParam($params, 'ad_id', '');
        $model = Annonce::model()->find('id=:id AND public = 1',array(':id'=>$ad_id));
        if (is_null($model))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found')); 
        if (!Yii::app()->user->checkAccess('administrators') && $model->email != Yii::app()->user->email)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','You have no permission to delete photo of this ad'));
      
        if (!empty($model->photos))
        {
            $currentPhotos = unserialize($model->photos);
            if (is_array($currentPhotos) && count($currentPhotos) > 0)
            {
                for($i=0;$i<count($currentPhotos);$i++)
                {
                    if ($currentPhotos[$i] == $photo_name)
                    {
                        if (file_exists('uploads/ads/'.$photo_name))
                            unlink('uploads/ads/'.$photo_name);
                        unset($currentPhotos[$i]);
                        break;        
                    }
                }
                $currentPhotos = array_values($currentPhotos);
            }
            if (count($currentPhotos) > 0)
                $model->photos = serialize($currentPhotos);
            else
                $model->photos = ''; 
            $model->update(array('photos'));
        }

        return $this->result;
    }
    
    public function deleteFromFavorite($params) {
        if (Yii::app()->user->isGuest)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Please login to ferform this service'));
        
        $ad_id = $this->getParam($params,'ad_id','');
        $ad = Annonce::model()->find('id=:id',array(':id'=>$ad_id));
        if (is_null($ad))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found'));
            
        AdFavorites::model()->deleteAll('annonce_id=:ad_id AND user_id=:userId',array(':ad_id'=>$ad->id,':userId'=>Yii::app()->user->id));
        return $this->result;
    }
    
    public function makeTopAd($params) {
        if (Yii::app()->user->isGuest)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Please login to ferform this service'));
        if (!Yii::app()->user->checkAccess('administrators'))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','You do not have permission to perform this service'));
        
        $ad_id = $this->getParam($params,'ad_id','');
        $ad = Annonce::model()->find('id=:id',array(':id'=>$ad_id));
        if (is_null($ad))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found'));
            
        $ad->featured = 1;
        $ad->feature_days += intval(MoneySettings::TOP_TIME2);
        $ad->feature_total += intval(MoneySettings::TOP_PRICE2);
        if (!$ad->update(array('featured','feature_days','feature_total')))
            $this->result->fail('UPDATE_FAILED', Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad is not made to top ad successfully'));
        
        $this->result->processed('days',$ad->feature_days);
        return $this->result;
    }
    
    public function addIntoHomepageGallery($params) {
        if (Yii::app()->user->isGuest)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Please login to ferform this service'));
        if (!Yii::app()->user->checkAccess('administrators'))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','You do not have permission to perform this service'));
        
        $ad_id = $this->getParam($params,'ad_id','');
        $ad = Annonce::model()->find('id=:id',array(':id'=>$ad_id));
        if (is_null($ad))
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','This ad is not found'));
            
        $ad->homepage = 1;
        $ad->homepage_days += intval(MoneySettings::HG_TIME2);
        $ad->homepage_total += intval(MoneySettings::HG_PRICE2);
        if (!$ad->update(array('homepage','homepage_days','homepage_total')))
            $this->result->fail('UPDATE_FAILED', Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! This ad is not made to top ad successfully'));
        
        $this->result->processed('days',$ad->homepage_days);
        return $this->result;
    }   
}
