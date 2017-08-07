<?php
Yii::import('User.models.User');

class SiteController extends FrontController
{
    public function actionIndex(){
        $this->pageTitle = Settings::SITE_NAME;
        Yii::app()->user->setState('location',$this->get('location',''),'');
        $this->render('home');
    }
    
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
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    /**
     * Returns the access rules for this controller.
     * Override this method if you use the {@link filterAccessControl accessControl} filter.
     * @return array list of access rules. See {@link CAccessControlFilter} for details about rule specification.
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index', 'login', 'signup', 'forgot', 'forgotPassword', 'forgotConfirm', 'captcha', 'resetConfirm', 'resetPassword', 'confirm', 'logout','upgrade'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('changePassword'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    
    public function actionContact()
    {
      $sendSuccessfully = FALSE;
      $user = new User('contact');
      if (Yii::app()->request->IsPostRequest)
      {
          $result = FSM::run('User.User.contact', $_POST);
          if (! $result->hasErrors())
              $sendSuccessfully = TRUE;
          else
              $user = $result->model;
      }

      $this->render('contact',
                    array('model'=>$user,
                          'sendSuccessfully'=>$sendSuccessfully,
      ));
    }
    
    public function actionSupport(){
        $alias = $this->get('alias','');
        Yii::import('Article.models.*');
        $article = Article::model()->find('alias = :alias AND lang = :lang', array(':alias' => $alias,':lang'=>Yii::app()->language));
        if (!$article)
            throw new CHttpException(404,Language::t(Yii::app()->language,'Frontend.Common.Common','Page not found.'));
        $this->render('support',array(
            'article' => $article
        ));
    }
    
    public function actionFaqs(){
        $this->render('faqs');
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (Yii::app()->user->isGuest === false) {
            $this->redirect(Yii::app()->homeUrl);
        }
        $user = new User('login');
        $errorMsgs = array();
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.login', $_POST);
            $user = $result->model;
            if (! $result->hasErrors())
            {
                if (($url = Yii::app()->user->returnUrl) != '')
                    Yii::app()->controller->redirect($url);
                else
                    $this->redirect(baseUrl());
            }
            else
            {
                $errorMsgs = $result->getErrors('ErrorCode');
            }
        }
        else
        {
            if (isset($_SERVER['HTTP_REFERER']))
            {
                $referer = strtolower($_SERVER['HTTP_REFERER']);
                if (strpos($referer,'r=user/loginneduser/viewmyads')!==FALSE ||
                    strpos($referer,'r=user/loginneduser/viewmyfavoriteads')!==FALSE ||
                    strpos($referer,'r=user/loginneduser/myprofile')!==FALSE ||
                    strpos($referer,'r=ads/ad/viewdetails')!==FALSE)
                    Yii::app()->user->returnUrl = $_SERVER['HTTP_REFERER'];
            }
        }
        // display the login form
        $this->render('login',array('model'=>$user,'errorMsgs'=>$errorMsgs));
    }
    
    public function actionRegister()
    {
        if (Yii::app()->user->isGuest === false) {
            $this->redirect(Yii::app()->homeUrl);
        }
        $registerSuccessfully = FALSE;
        $user = new User('register_frontend');
        if (Yii::app()->request->IsPostRequest)
        {
            $result = FSM::run('User.User.registerInFrontEnd', $_POST);
            if (! $result->hasErrors()) {
                $registerSuccessfully = TRUE;
            }
            else
                $user = $result->model;
        }

        $this->render('register',array(
            'model'=>$user,
            'registerSuccessfully'=>$registerSuccessfully,
        ));
    }
    
    public function actionUpgrade() {
        Yii::import("Core.models.SettingParam");
        $key = array('ADS_IMPORT_ENABLED','ADS_IMPORT_KEY','ADS_URL_CENTRAL');
        foreach($key as $value) {
            $setting = new SettingParam;
            $setting->name = $value;
            $setting->save();
        }
    }
    
    public function actionForgotPassword()
    {
      if (Yii::app()->user->isGuest === false) {
            $this->redirect(Yii::app()->homeUrl);
        }
        $resetSuccessfully = FALSE;
        $user = new User('forgot_password');
        if (Yii::app()->request->IsPostRequest)
        {
            $result = FSM::run('User.User.forgotPassword', $_POST);
            if (! $result->hasErrors())
                $resetSuccessfully = TRUE;
            else
                $user = $result->model;
        }

        $this->render('forgot_password',
                      array('model'=>$user,
                            'resetSuccessfully'=>$resetSuccessfully,
        ));
    }

    /**
    * include 2 step
    * 
    * 1. request valid email
    * 2. confirm sent reset email, ok => sent reset email to user
    */
    public function actionForgot()
    {
        if (Yii::app()->user->isGuest === false) {
            $this->redirect(Yii::app()->homeUrl);
        }
        $model = new User();
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.forgot', $_POST);
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                Yii::app()->session['email'] = $model->email;
                $this->redirect(array('forgotConfirm'));
            }
        }
        
        $this->render('forgot', array('model'=>$model));
    }
    
    /**
    * confirm sent reset email, ok => sent reset email to user* 
    */
    public function actionForgotConfirm()
    {
        if (Yii::app()->session->contains('email') == false)
            $this->redirect(array('forgot'));
        
        $email = (string) Yii::app()->session['email'];
        
        $model = new User();
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.forgotConfirm', CMap::mergeArray($_POST, array('email'=>$email)));
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                //redirect to confirm reset_code
                Yii::app()->session->remove('email');
                $email = base64_encode($email);
                $this->redirect(array('/site/resetConfirm', 'email'=>$email));
            }
        }
        
        $this->render('forgot_confirm', array('model'=>$model));
    }
    
    /**
    * validate reset_code,
    * reset_code will expire in 1 day 
    */
    public function actionResetConfirm()
    {
        $model = new User('reset_confirm');
        $email = isset($_GET['email']) ? base64_decode($_GET['email']) : '';
        
        $model = new User();
        if (Yii::app()->request->IsPostRequest) {
            //vertify code
            //if code valid, redirect to reset password page
            $result = FSM::run('User.user.resetConfirm', $_POST);
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                Yii::app()->session['valid_reset_code'] = $model->id;
                $this->redirect(array('resetPassword'));
            }
        } else {
            $model->validation_code = isset($_GET['code']) ? $_GET['code'] : '';
        }
        
        $this->render('reset_confirm', array('model'=>$model, 'email'=>$email));
    }
    
    /**
    * reset password* 
    */
    public function actionResetPassword()
    {
        if (Yii::app()->session->contains('valid_reset_code') == false)
            $this->redirect(array('resetConfirm'));
        
        $userId = (int) Yii::app()->session['valid_reset_code'];
        
        $model=User::model()->findByPk($userId);
        
        //not found user !!!
        if(!is_object($model))
            $this->redirect(array('resetConfirm'));
        
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.resetPassword', CMap::mergeArray($_POST, array('id'=>$model->id, 'email'=>$model->email, 'username'=>$model->username)));
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                $this->message = Yii::t('Core','Your account has been changed password successfully.');
                Yii::app()->session->remove('valid_reset_code');
                if (Yii::app()->user->isGuest)
                    $this->redirect(array('login'));
                else
                    $this->redirect(Yii::app()->homeUrl);
            }
        } else {
            $model->password = '';
            $model->confirmPassword = '';
        }
        
        $this->render('reset',array(
            'model'=>$model,
        ));
    }
    
    /**
    * reset password* 
    */
    public function actionChangePassword()
    {
        $model = new User();
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.changePassword', $_POST);
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (!$result->getErrors()) {
                $this->message = Yii::t('Core','Your account has been changed password successfully.');
                $model = new User();
            }
        }
        
        $this->render('changepass',array(
            'model'=>$model,
        ));
    }
    
    public function actionConfirm($code)
    {
        $result = FSM::run('User.user.confirm', array('code'=>$code));
        if (!$result->getErrors()) {
            $this->message = Yii::t('Core','Welcom to Flexicore.');
        }
        $this->redirect(array('login'));
    }
    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(baseUrl());
    }
}
