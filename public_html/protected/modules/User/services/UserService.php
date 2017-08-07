<?php

class UserService extends FServiceBase
{    
    /**
    * Get a User model given its ID
    * 
    * @param int id User ID
    * @return FServiceModel
    */
    public function get($params){
        $model = User::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        if ($params['User']['password'] == '') {
            unset($params['User']['password']);
            unset($params['User']['confirmPassword']);
        }
        
        $model = $this->getModel($params['User'],'User');
        $this->result->processed('model', $model);
        
        if (!isset($params['User']['password'])) {
            $model->confirmPassword = $model->password;
        } else {
            $model->confirmPassword = md5($params['User']['confirmPassword']);
            $model->password = md5($params['User']['password']);
        }
        
        if (empty($model->id))
            $model->setScenario('register');
        else
            $model->setScenario('edit');
        
        if (! $model->validate())
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User', 'Submitted data is missing or invalid.'));
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        else
        {
            $isNewRecord = FALSE;
            if (empty($model->id))
                $isNewRecord = TRUE;
            if ($model->save())
            {
                if ($isNewRecord && isset($params['create_admin']) && $params['create_admin'] == 1)
                {
                    // create admin
                    Yii::app()->authManager->assign('administrators',$model->id);    
                }           
            }
            else
            {
                $this->result->fail(ERROR_HANDLING_DB, Yii::t('User.User','Error while saving submitted data into database.'));    
            }
        }
        
        return $this->result;
    }


    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        foreach($ids as $id) {
            $model = User::model()->findByPk($id);
            /**
            * TODO: Check related data if this User is deletable
            * This can be done in onBeforeDelete or here or in extensions
            *
            if (Related::model()->count("UserId = {$id}") > 0)
                $this->result->fail(ERROR_VIOLATING_BUSINESS_RULES, Yii::t('User.User',"Cannot delete User ID={$id} as it has related class data."));
            else
            */
                try {
                    $model->delete();
                } catch (CDbException $ex) {
                    $this->result->fail(ERROR_HANDLING_DB, $ex->getMessage());
                }
        }
        return $this->result;
    }
    
    public function changeStatus($params) {
        $ids = $this->getParam($params, 'ids', array());
        $value = $this->getParam($params, 'value', false);
        
        if (count($ids) <= 0)
            return $this->result;
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $ids);
        $models = User::model()->findAll($criteria);
        foreach ($models as $model) {
            /**
            * @var Site $model
            */
            $model->status = (boolean) $value;
            if (! $model->save(false, array('status')))
                $this->result->fail(ERROR_HANDLING_DB, Yii::t('CmsModule.Site','Error while saving submitted data into database.'));
        }
        
        return $this->result;
    }

    /**
    * Authenticate an user
    *
    * @param mixed $params
    */
    public function login($params)
    {
        $user = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $user);

        $user->setScenario('login');
        if (!$user->validate(array('email', 'password'))) {
            Yii::trace(CVarDumper::dumpAsString($user->getErrors()));
            return $this->result->fail(0, Yii::t('User','Email or password is invalid'));
        }

        $ui = new FUserIdentity($user->email, $user->password);
        $ui->authenticate();

        if ($ui->errorCode === FUserIdentity::ERROR_NONE) {
            $remember = $this->getParam($params, 'remember', false);
            Yii::app()->user->login($ui, $remember ? 24*60*60 : 0);
            if (Yii::app()->user->isGuest === false) {
                //update last login
                User::model()->updateByPk(Yii::app()->user->id, array('last_login'=>date('Y-m-d')));
            }
            return $this->result;
        } else {
            $this->result->fail($ui->errorCode, Yii::t('User', $ui->getErrorMessage($ui->errorCode)));
            return $this->result;
        }
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function signup($params)
    {
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);

        $model->setScenario('register');
        if (!$model->validate(array('email', 'username', 'password'))) {
            Yii::trace(CVarDumper::dumpAsString($model->getErrors()));
            return $this->result->fail(0, Yii::t('User','Email or password is invalid'));
        }
        
        $model->confirmPassword = md5($params['User']['confirmPassword']);
        $model->password = md5($params['User']['password']);
        $model->status = User::STATUS_DEACTIVE;
        $model->validation_code = $this->generateCode();
        $model->validation_expired = date('Y-m-d H:i:s', time()+24*60*60);

        if ($model->save(false)) {
            //send welcome email
            $this->sendWelcomeEmail($model);
            //sent confirm mail
            $this->sendConfirmEmail($model);
        } else
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('User.User','Error while saving submitted data into database.'));
        return $this->result;
    }
    
    /**
     * reset password
     * @param mixed $params
     */
    public function forgotPassword($params)
    {
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);

        $model->setScenario('forgot_password');
        if (!$model->validate(array('email'))) {
            return $this->result->fail(ERROR_INVALID_DATA, 'Submitted data is missing or invalid.');
        }
        
        include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
        $passwordForSendEmail = randomString('lower',12);
        $model->password = md5($passwordForSendEmail);
        
        $criteria = new CDbCriteria();
        $criteria->compare('email', $model->email);
        $user = User::model()->find($criteria);
        //var_dump($user);
        if (is_object($user)) {
            $result = User::model()->updateByPk($user->id, array('password'=>$model->password));
            Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
            $messageObj = new YiiMailMessage;
            $messageObj->setSubject('['.Settings::SITE_NAME.'] '. Language::t(Yii::app()->language,'Backend.User.Message','Your password was reset'));
            $messageObj->setFrom(Settings::ADMIN_EMAIL);
            $messageObj->setTo($model->email);
            Yii::app()->mail->viewPath = 'application.runtime.emails'; 
            $messageObj->view = 'reset_password_email'.'_'.Yii::app()->language;
            // create content 
            $messageObj->setBody(array(
                'user_email'=>$model->email,
                'site_name'=>Settings::SITE_NAME,
                'login_page_url'=>Yii::app()->createAbsoluteUrl('/site/login'),
                'site_url'=>baseUrl(),
                'user_password'=>$passwordForSendEmail,
            ), 'text/html');
            if (!Yii::app()->mail->send($messageObj))
                $this->result->fail('SEND_MAIL_FAILED', Yii::t('Ads/Ads','Reset Password Email was sent failed!'));
        } else
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid email.'));
        
        return $this->result;
    }
    
    /**
     * send a contact message
     * @param mixed $params
     */
    public function contact($params)
    {
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);

        $model->setScenario('contact');
        if (!$model->validate()) {
            return $this->result->fail(ERROR_INVALID_DATA, 'Submitted data is missing or invalid.');
        }
        
        include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
        
        Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
        $messageObj = new YiiMailMessage;
        $messageObj->setSubject($model->subject);
        $messageObj->setFrom($model->email);
        $messageObj->setTo(Settings::SITE_CONTACT);
        // create content 
        $messageObj->setBody($model->message, 'text/html');
        if (!Yii::app()->mail->send($messageObj))
            $this->result->fail('SEND_MAIL_FAILED', Yii::t('Ads/Ads','Reset Password Email was sent failed!'));
        
        return $this->result;
    }
    
    /**
    * register an user in frontend
    *
    * @param mixed $params
    */
    public function registerInFrontEnd($params)
    {
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);

        $model->setScenario('register_frontend');
        if (!$model->validate()) {
            return $this->result->fail(ERROR_INVALID_DATA, 'Submitted data is missing or invalid.');
        }
        
        include_once(Yii::getPathOfAlias("webroot").'/protected/modules/Core/extensions/web/helpers/string.php');
        $registerSuccess = FALSE;
        $passwordForSendEmail = randomString('lower',12);
        $anotherUser = User::model()->findByAttributes(array('email'=>$model->email));
        if (!is_null($anotherUser))
        {
            $anotherUser->first_name = $model->first_name;
            $anotherUser->last_name = $model->last_name;
            $anotherUser->password = md5($passwordForSendEmail);
            $anotherUser->status = User::STATUS_ACTIVE;
            if ($anotherUser->update(array('first_name','last_name','password','status')))
                $registerSuccess = TRUE;        
        }
        else
        {
            $model->username = $model->email;
            $model->password = md5($passwordForSendEmail);
            $model->status = User::STATUS_ACTIVE;
            if ($model->save(FALSE)) $registerSuccess = TRUE;    
        }

        if ($registerSuccess == TRUE) {
            Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
            $messageObj = new YiiMailMessage;
            $messageObj->setSubject(
	            '['.Settings::SITE_NAME.'] '.
	            Language::t(Yii::app()->language,'Frontend.Message.MailSubject','Thanks for registering !')
            );
            $messageObj->setFrom(Settings::ADMIN_EMAIL);
            $messageObj->setTo($model->email);
            Yii::app()->mail->viewPath = 'application.runtime.emails'; 
            $messageObj->view = 'registration_email'.'_'.Yii::app()->language;
            // create content
            $messageObj->setBody(array(
                'user_email'=>$model->email,
                'site_name'=>Settings::SITE_NAME,
                'login_page_url'=>Yii::app()->createAbsoluteUrl('/site/login'),
                'site_url'=>baseUrl(),
                'user_password'=>$passwordForSendEmail,
            ), 'text/html');
            if (!Yii::app()->mail->send($messageObj))
                $this->result->fail('SEND_MAIL_FAILED', Yii::t('Ads/Ads','Registration Email was sent failed!'));
        } else
            $this->result->fail(ERROR_HANDLING_DB, Yii::t('User.User','Error while saving submitted data into database.'));
        return $this->result;
    }
    
    /**
    * Create a user account for a given email, other information
    * is generated automatically.
    * 
    * @param string $email
    */
    public function signupByEmail($params)
    {
        $user = new User('register');
        $user->email = $this->getParam($params, 'email','');
        $user->name = $this->getParam($params, 'name','');
        $user->username = '___'.substr($user->email,0,strpos($user->email,'@')).'___';
        $user->password = randomString('all');
        
        $params = array('User' => $user->attributes);
        //manually add confirmPassword as it's not part of the attributes
        $params['User']['confirmPassword'] = $user->password;
        return $this->signup($params);
    }
    
    protected function sendWelcomeEmail($model)
    {
        Yii::import('application.modules.Core.extensions.mail.YiiMailMessage');
        Yii::app()->mail->viewPath = 'application.modules.User.views.mail';
        
        $message = new YiiMailMessage;
        $message->view = 'welcome';
        $message->setSubject('Welcome to Flexicore');
        //data
        $username = ucfirst($model->username);
        $message->setBody(array('username'=>$username), 'text/html');
        $message->addTo($model->email);
        if (defined('Settings::ADMIN_EMAIL'))
            $message->setFrom(array(Settings::ADMIN_EMAIL=>'Flexicore'));
        Yii::app()->mail->send($message);
    }
    
    protected function sendConfirmEmail($model)
    {
        Yii::import('application.modules.Core.extensions.mail.YiiMailMessage');
        Yii::app()->mail->viewPath = 'application.modules.User.views.mail';
        //send mail
        $message = new YiiMailMessage;
        $message->view = 'registration_confirmation';
        $message->setSubject('Flexicore Member Registration Confirmation');
        //data
        $username = ucfirst($model->username);
        $link = ucfirst($model->username);
        $url = Yii::app()->createAbsoluteUrl('/site/confirm', array('code'=>$model->validation_code));
        $link = CHtml::link($url, $url, array('target'=>'_blank'));
        $code = strtoupper($model->validation_code);
        $message->setBody(array(
            'username'=>$username,
            'link'=>$link,
        ), 'text/html');
        $message->addTo($model->email);
        if (defined('Settings::ADMIN_EMAIL'))
            $message->setFrom(array(Settings::ADMIN_EMAIL=>'Flexicore'));
        Yii::app()->mail->send($message);
    }
    
    public function confirm($params)
    {
        $code = $this->getParam($params, 'code', '');
        if (empty($code))
            $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid code.'));
        
        $criteria = new CDbCriteria();
        $criteria->compare('validation_code', $code);
        $criteria->compare('validation_expired', ">='".date('Y-m-d H:i:s')."'");
        $model = User::model()->find($criteria);
        if (is_object($model)) {
            $result = User::model()->updateByPk($model->id, array(
                'status'=>User::STATUS_ACTIVE,
                'validation_code'=>'',
                'validation_expired'=>'',
            ));
        } else
            $this->result->fail(0, Yii::t('User','Confirm Code is invalid'));
        return $this->result;
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function forgot($params)
    {
        $user = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $user);

        $user->setScenario('forgot');
        if (!$user->validate(array('email'))) {
            Yii::trace(CVarDumper::dumpAsString($user->getErrors()));
            return $this->result->fail(0, Yii::t('User','Email is invalid'));
        }
        return $this->result;
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function forgotConfirm($params)
    {
        $user = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $user);

        $user->setScenario('forgot_confirm');
        if ($user->validate(array('verifyCode'))) {
            $this->sendResetCode($params);
        } else {
            Yii::trace(CVarDumper::dumpAsString($user->getErrors()));
            return $this->result->fail(0, Yii::t('User','Verify Code is invalid'));
        }
        return $this->result;
    }
    
    protected function generateCode()
    {
        //generate code
        do {
            $codeLength = rand(8, 10);
            $code = substr(hash('md5',uniqid(rand(), true).microtime()), 0, $codeLength);;
            $criteria = new CDbCriteria();
            $criteria->compare('validation_code', $code);
        } while(User::model()->exists($criteria));
        return $code;
    }
    
    public function sendResetCode($params)
    {
        $email = $this->getParam($params, 'email', '');
        $isAdmin=$this->getParam($params, 'isAdmin', 0);
        if (empty($email))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid email.'));
        
        //find user
        $model = User::model()->findByAttributes(array('email'=>$email));
        if (is_object($model)) {
            $resetCode = $this->generateCode();
            
            $model->validation_code = $resetCode;
            $model->validation_expired = date('Y-m-d H:i:s', time()+24*60*60);
            if ($model->save(false, array('validation_code', 'validation_expired'))) {
                Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
                Yii::app()->mail->viewPath = 'application.modules.User.views.mail';
                //send mail
                $message = new YiiMailMessage;
                $message->view = 'password_reset_confirmation';
                $message->setSubject(Settings::SITE_NAME.' Password Reset Confirmation');
                //data
                $username = ucfirst($model->username);
                if($isAdmin!=1){
                $urlResetConfirm = Yii::app()->createAbsoluteUrl('/site/resetConfirm', array('code'=>$model->validation_code));
                $url = Yii::app()->createAbsoluteUrl('/site/resetConfirm');
                }
                else {
                    $urlResetConfirm = Yii::app()->createAbsoluteUrl('/Admin/default/resetConfirm', array('code'=>$model->validation_code));
                $url = Yii::app()->createAbsoluteUrl('/Admin/default/resetConfirm');
                }
                $link = CHtml::link($urlResetConfirm, $urlResetConfirm, array('target'=>'_blank'));
                
                $code = strtoupper($model->validation_code);
                $message->setBody(array(
                    'username'=>$username,
                    'link'=>$link,
                    'url'=>$url,
                    'code'=>$code,
                ), 'text/html');
                $message->addTo($model->email);
                if (defined('Settings::ADMIN_EMAIL'))
                    $message->setFrom(array(Settings::ADMIN_EMAIL=>Settings::SITE_NAME));
                Yii::app()->mail->send($message);
            } else {
                Yii::log(CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR);
                $this->result->fail(ERROR_HANDLING_DB, Yii::t('User.User','Error while saving submitted data into database.'));
            }
        } else {
            return $this->result->fail(0, Yii::t('User','Not found user with email:'.$email));
        }
        return $this->result;
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function resetConfirm($params)
    {
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);

        $criteria = new CDbCriteria();
        $criteria->compare('validation_code', $model->validation_code);
        $criteria->compare('validation_expired', ">='".date('Y-m-d H:i:s')."'");
        $model = User::model()->find($criteria);
        if (!is_object($model)) {
            //Yii::trace(CVarDumper::dumpAsString($user->getErrors()));
            //Yii::trace(CVarDumper::dumpAsString($model->getErrors()));
            return $this->result->fail(0, Yii::t('User','Verify Code is invalid or expired'));
        }
        
        $this->result->processed('model', $model);
        
        return $this->result;
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function resetPassword($params)
    {
        $id = $this->getParam($params, 'id', 0);
        if (!$id)
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid user id.'));
        $email = $this->getParam($params, 'email', '');
        if (empty($email))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid email.'));
        $username = $this->getParam($params, 'username', '');
        if (empty($username))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User','Invalid username.'));
        
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);
        $params['User']['email'] = $model->email = $email;
        $model->username = $username;
        
        $model->setScenario('reset');
        if ($model->validate(array('password', 'confirmPassword'))) {
            $result = User::model()->updateByPk($id, array(
                'password'=>md5($model->password),
                'validation_code'=>'',
                'validation_expired'=>'',
            ));
//            if ($result) {
                Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
                Yii::app()->mail->viewPath = 'application.modules.User.views.mail';
                //send mail alert password changed
                $message = new YiiMailMessage;
                $message->view = 'password_changed';
                $message->setSubject(Settings::SITE_NAME.' Password Change');
                //data
                $username = ucfirst($model->username);
                $message->setBody(array('username'=>$username), 'text/html');
                $message->addTo($email);
                if (defined('Settings::ADMIN_EMAIL'))
                    $message->setFrom(array(Settings::ADMIN_EMAIL=>Settings::SITE_NAME));
                Yii::app()->mail->send($message);
//            } else
//                return $this->result->fail(0, Yii::t('User','Error while saving submitted data into database'));
        } else {
            Yii::log(CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR);
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User', 'Submitted data is missing or invalid.'));
        }
        
        $this->login($params);
        
        return $this->result;
    }

    /**
    * signup an user
    *
    * @param mixed $params
    */
    public function changePassword($params)
    {
        if (Yii::app()->user->isGuest)
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User', 'Not allow.'));
        
        $model = $this->getModel($params['User'], 'User');
        $this->result->processed('model', $model);
        
        $model->setScenario('change_password');
        if ($model->validate(array('passwordOld', 'password', 'confirmPassword'))) {
            $criteria = new CDbCriteria();
            $criteria->compare('password', md5($model->passwordOld));
            $criteria->compare('id', Yii::app()->user->id);
            $user = User::model()->find($criteria);
            if (is_object($user)) {
                $result = User::model()->updateByPk(Yii::app()->user->id, array('password'=>md5($model->password)));
                Yii::import('application.modules.Core.extensions.vendors.mail.YiiMailMessage');
                Yii::app()->mail->viewPath = 'application.modules.User.views.mail';
                //send mail alert password changed
                $message = new YiiMailMessage;
                $message->view = 'password_changed';
                $message->setSubject(Settings::SITE_NAME.' Password Change');
                //data
                $username = ucfirst($user->username);
                $message->setBody(array('username'=>$username), 'text/html');
                $message->addTo($user->email);
                if (defined('Settings::ADMIN_EMAIL'))
                    $message->setFrom(array(Settings::ADMIN_EMAIL=>Settings::SITE_NAME));
                Yii::app()->mail->send($message);
            } else
                return $this->result->fail(0, Yii::t('User','Current password invalid'));
        } else {
            Yii::log(CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR);
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User.User', 'Submitted data is missing or invalid.'));
        }
        
        return $this->result;
    }
    
    public function activeOrDeactivateUser($params)
    {
        $user_id = $this->getParam($params, 'user_id', 0);
        $type = $this->getParam($params, 'type', 'delete');
        if ($type=='delete')
            $status = 0;
        else
            $status = 1;
        User::model()->updateByPk($user_id,array('status'=>$status));
        return $this->result;    
    }
    
    public function banOrUnbanUser($params)
    {
        $user_id = $this->getParam($params, 'user_id', 0);
        $ad_id = $this->getParam($params, 'ad_id', 0);
        $type = $this->getParam($params, 'type', 'ban');
        
        $email = '';
        if ($user_id > 0)
        {
            $user = User::model()->findByPk($user_id);
            if (is_null($user))
            {
                $this->result->fail('USER_NOT_FOUND', 'This user is not found');
                return $this->result;
            }
            $email = $user->email;
        }
        elseif ($ad_id > 0)
        {
            Yii::import('Ads.models.Annonce');
            $ad = Annonce::model()->findByPk($ad_id);
            if (is_null($ad))
            {
                $this->result->fail('AD_NOT_FOUND', 'This ad is not found');
                return $this->result;
            }
            $email = $ad->email;    
        }
        
        if ($email=='') return $this->result->fail('EMAIL_NOT_FOUND', 'There is no email to ban or unban');
        
        $bannedEmails = User::model()->getBannedEmails();
        
        if ($type=='ban')
        {
            if (in_array($email,$bannedEmails))
            {
                $this->result->fail('USER_BANNED', 'This user was already in banned list.');
                return $this->result;    
            }
            $bannedEmails[] = $email;
        }
        else
        {
            if (!in_array($email,$bannedEmails))
            {
                $this->result->fail('USER_NOT_BANNED', 'This user is not in banned list');
                return $this->result;    
            }
            foreach($bannedEmails as $key => $banned_email)
            {
                if ($banned_email == $email)
                {
                    unset($bannedEmails[$key]);
                    break;
                }
            }    
        }
        
        $strBannedEmails = implode("\n",$bannedEmails);
        $param = SettingParam::model()->find('name=:name',array(':name'=>'BANNED_EMAIL'));
        if (is_null($param))
        {
            $this->result->fail('PARAM_NOT_FOUND', 'Param for Banned Email is not found.');
            return $this->result;    
        }
        $param->value = $strBannedEmails;
        $param->update(array('value'));
        //rebuild cache Constant Settings class
        FSM::run('Core.Settings.db2php', array('module'=>$param->module));
        
        return $this->result;    
    }    
}
