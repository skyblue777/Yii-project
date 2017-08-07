<?php
Yii::import('User.models.User');
class DefaultController extends BackOfficeController
{
    
    /**
    * Reconfig access for user guest
    * 
    */
    public function getGuestAllowedActions(){
        return array('captcha','login','forgotPassword','forgotConfirm','resetConfirm','resetPassword');
        
    }
    /**
    * Add Captche action, generate Catcha code
    * 
    */
    public function actions(){
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=Admin/default/page&view=FileName
            'page'=>array(
                'class'=>'CViewAction',
            ),
        );
    }
    
	public function actionIndex()
	{
		$this->redirect(array('/Ads/Ads/list','Annonce_sort'=>'create_time.desc'));
	}
    
    /**
    * Login in BackOffice
    */
    public function actionLogin(){
        Yii::import('User.models.User');
        $user = new User();
        $invalidStatusCase = false;
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.login', $_POST);
            $user = $result->model;
            if ($this->post('ajax','') == 'user-login-form'){
                echo $result->getActiveErrorMessages($user);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                if (($url = Yii::app()->user->returnUrl) != '')
                    Yii::app()->controller->redirect($url);
                else
                    $this->redirect(Yii::app()->request->getBaseUrl(true));
            }
        }
        if (Yii::app()->user->isGuest === false) {
            $this->redirect(Yii::app()->request->getBaseUrl(true));
        }
        $this->layout = '//layouts/login';
        $this->render('login', array('model' => $user));
    }
    
    
    /**
    * Forgot passsword for back-end user.
    */
    public function actionForgotPassword(){
        Yii::import('User.models.User');
        $model = new User('forgot_password');
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
        $this->layout = '//layouts/login';
        $this->render('forgot_password', array('model'=>$model));
    }
    /**
    * Confirm to send mail for user forgot password
    */
    public function actionForgotConfirm()
    {
        if (Yii::app()->session->contains('email') == false)
            $this->redirect(array('forgotPassword'));
        
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
                $this->redirect(array('resetPassword', 'email'=>$email));
            }
        }
        $this->layout = '//layouts/login';
        $this->render('forgot_confirm', array('model'=>$model));
    }
    /**
    * Confirm Before reseting
    * 
    */
    public function actionResetConfirm()
    {
        $model = new User('reset_confirm');
        $email = isset($_GET['email']) ? base64_decode($_GET['email']) : '';
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
                //$this->redirect(array('resetConfirm'));
                $this->redirect(array('resetPassword'));
            }
        } else {
            $model->validation_code = isset($_GET['code']) ? $_GET['code'] : '';
        }
        $this->layout = '//layouts/login';
        $this->render('reset_confirm', array('model'=>$model, 'email'=>$email));
    }
    /**
    * Reset Password
    * 
    */
    public function actionResetPassword()
    {
        if (Yii::app()->session->contains('valid_reset_code') == false){
            $this->redirect(array('resetConfirm'));
            }
        
        $userId = (int) Yii::app()->session['valid_reset_code'];
        
        $model=User::model()->findByPk($userId);
        
        //not found user !!!
        if(!is_object($model))
            //$this->redirect(array('resetConfirm'));
            $this->redirect(array('resetConfirm'));
            
        
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('User.user.resetPassword', CMap::mergeArray($_POST, array('id'=>$model->id, 'email'=>$model->email, 'username'=>$model->username)));
            $model = $result->model;
            if ($this->post('ajax','') == 'user-form'){
                echo $result->getActiveErrorMessages($model);
                Yii::app()->end();
            }
            if (! $result->hasErrors()){
                $this->message = Language::t(Yii::app()->language,'Backend.User.Message','Your account has been changed password successfully.');
                Yii::app()->session->remove('valid_reset_code');
                /*if (Yii::app()->user->isGuest)
                    $this->redirect(array('login'));
                else
                    $this->redirect(Yii::app()->homeUrl);*/
            }
        } else {
            $model->password = '';
            $model->confirmPassword = '';
        }
        $this->layout = '//layouts/login';
        $this->render('reset',array(
            'model'=>$model,
        ));
    }
    
    
    
}