<?php

/**
 * This is the model class for table "user".
 */
Yii::import('User.models.base.UserBase');
class User extends UserBase
{
    const STATUS_DEACTIVE = 0, STATUS_ACTIVE = 1;
    
    public $confirmPassword;
    public $passwordOld;
    public $verifyCode;
    
    public $subject;
    public $message;
    
    public $createdDate = array('from'=>'','to'=>'');
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('password, email', 'required', 'on'=>'login'),
            array('password, email', 'required', 'on'=>'register'),
            array('password', 'required', 'on'=>'reset, change_password'),
            array('validation_code','required','on'=>'reset_confirm'),
            array('email, subject, message', 'required', 'on'=>'contact'),
            
            array('username', 'required', 'on'=>'register'),
            array('username, email', 'unique', 'on'=>'register'),
            array('email', 'email', 'on'=>'register, login, register_frontend, forgot_password, contact, edit'),
            array('username', 'filter', 'filter'=>'strtolower', 'on'=>'register'),
            array('username', 'match', 'pattern'=>'/^[a-z0-9_]{6,50}$/', 'on'=>'register'),
            array('password', 'length', 'min'=>5),
            array('confirmPassword', 'compare', 'compareAttribute' => 'password', 'on'=>'register, reset, change_password, edit_profile, edit'),
            
            array('email', 'exist', 'on'=>'forgot, forgot_password'),
            
            array('passwordOld', 'required', 'on'=>'change_password'),
            
            array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements(), 'on'=>'forgot_confirm, register_frontend, forgot_password, contact'),
            
            array('validation_code, validation_type, validation_expired, status, created_date, last_login', 'safe'),
            array('email, first_name, last_name, status, createdDate', 'safe', 'on'=>'search'),
            array('email, last_name, verifyCode', 'required', 'on'=>'register_frontend'),
            array('email, verifyCode', 'required', 'on'=>'forgot_password'),
            array('email', 'emailExists', 'on'=>'register_frontend, edit'),
            
            array('username, email', 'required', 'on'=>'edit'),
            
            array('last_name', 'required', 'on'=>'edit_profile'),
        );
    }
    
    /**
     * @desc Check if email already exists
     */
    public function emailExists($attribute, $params)
    {
        if (!$this->hasErrors($attribute))
        {
            $attrs = array('email' => $this->email);
            if ($this->scenario == 'register_frontend')
                $attrs['status'] = 1;
            $user = self::model()->findByAttributes($attrs);
            if (!is_null($user) && $user->id != $this->id){
                $this->addError('email', Language::t(Yii::app()->language,'Frontend.User.Register','Sorry! This e-mail has been already used by another user.'));
                return false;
            }
        }
    }
    
    public function getStatusOptions()
    {
        return array(
            self::STATUS_DEACTIVE => 'Deactive',
            self::STATUS_ACTIVE => 'Active',
        );
    }
    
    public function getStatusText()
    {
        $options = $this->getStatusOptions();
        return isset($options[$this->status]) ? $options[$this->status] : Yii::t('app', 'Unknown {att}', array('{att}'=>$this->status));
    }

    /**
     * This method is invoked after saving a record successfully.
     * The default implementation raises the {@link onAfterSave} event.
     * You may override this method to do postprocessing after record saving.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function beforeSave()
    {
        
        if ($this->getIsNewRecord()) {
            $this->created_date = date('Y-m-d H:i:s');
            if (!isset($this->status))
                $this->status = self::STATUS_ACTIVE;
        }
        return parent::beforeSave();
    }
    
    /**
     * This method is invoked before validation starts.
     * The default implementation calls {@link onBeforeValidate} to raise an event.
     * You may override this method to do preliminary checks before validation.
     * Make sure the parent implementation is invoked so that the event can be raised.
     * @return boolean whether validation should be executed. Defaults to true.
     * If false is returned, the validation will stop and the model is considered invalid.
     */
    protected function beforeValidate()
    {
        $this->username = strtolower($this->username);
        return parent::beforeValidate();
    }
    
    public function search($filterByAdminRole=FALSE)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        if ($filterByAdminRole)
        {
            $criteria->join = "INNER JOIN AuthAssignment ON AuthAssignment.userid = t.id";
            $criteria->addCondition("AuthAssignment.itemname = 'administrators'");
            $this->status = 1;
        }
        else
            $criteria->addCondition("(SELECT COUNT(*) FROM AuthAssignment WHERE AuthAssignment.userid = t.id AND AuthAssignment.itemname = 'administrators') <= 0");

        $criteria->compare('email',$this->email,true);
        $criteria->compare('first_name',$this->first_name,true);
        $criteria->compare('last_name',$this->last_name,true);
        $criteria->compare('status',$this->status);
        
        // filter date_created - dateCreated
        if (is_array($this->createdDate) && isset($this->createdDate['from'], $this->createdDate['to']) && (empty($this->createdDate['from']) === false || empty($this->createdDate['to']) === false)) {
            $from = $this->createdDate['from'];
            $from = CDateTimeParser::parse($from, 'dd/MM/yyyy');
            if ($from === false) {
                $from = date('Y-m-d', strtotime('-1 years'));
            } else
                $from = date('Y-m-d', $from);
            $to = $this->createdDate['to'];
            $to = CDateTimeParser::parse($to, 'dd/MM/yyyy');
            if ($to === false) {
                $to = date('Y-m-d', strtotime('+1 years'));
            } else
                $to = date('Y-m-d', $to);
            
            $criteria->addCondition("DATE(t.created_date) BETWEEN DATE('".$from."') AND DATE('".$to."')");
        }

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination'=>array(
              'pageSize'=>50)
        ));
    }
    
    public function getBannedIps()
    {
        $banned_ips = array();
        $strBannedIps = nl2br(trim(UserSettings::BANNED_IP));
        if ($strBannedIps != '')
        {
            $arr = explode('<br />',$strBannedIps);
            if (count($arr) > 0)
            {
                foreach($arr as $item)
                {
                    $item = trim($item);
                    if ($item != '')
                        $banned_ips[] = $item;
                }
            }         
        }
        return $banned_ips;    
    }
    
    public function getBannedEmails()
    {
        $banned_emails = array();
        $strBannedEmails = nl2br(trim(UserSettings::BANNED_EMAIL));
        if ($strBannedEmails != '')
        {
            $arr = explode('<br />',$strBannedEmails);
            if (count($arr) > 0)
            {
                foreach($arr as $item)
                {
                    $item = trim($item);
                    if ($item != '')
                        $banned_emails[] = $item;
                }
            }         
        }
        return $banned_emails;    
    }
    
    public function getActionsButtonColumn()
    {
        // icon delete
        $delLinkClassName = 'delete-user';
        $delLinkTitle = Language::t(Yii::app()->language,'Backend.User.ListAdmin','Delete user');
        $delImageUrl = CHtml::image(themeUrl().'/images/buttons/delete.png',Language::t(Yii::app()->language,'Backend.User.ListAdmin','Delete user'));
        if ($this->status==0)
        {
            $delLinkClassName = 'restore-user';
            $delLinkTitle = Language::t(Yii::app()->language,'Backend.User.ListAdmin','Register user');
            $delImageUrl = CHtml::image(themeUrl().'/images/buttons/regacc.png',Language::t(Yii::app()->language,'Backend.User.ListAdmin','Register user'));   
        }
        $strDel = '<a class="'.$delLinkClassName.'" href="'.$this->id.'" title="'.$delLinkTitle.'">'.$delImageUrl.'</a>';
        // icon view user ads
        $strView = '<a href="'.Yii::app()->controller->createUrl('/Ads/Ads/list',array('Annonce[email]'=>$this->email,'Annonce_sort'=>'create_time.desc')).'" title="'.Language::t(Yii::app()->language,'Backend.User.ListAdmin','View user ads').'">'.CHtml::image(themeUrl().'/images/buttons/view.png','View user ads').'</a>';
        // icon ban
        $banLinkClassName = 'ban-user';
        $banLinkTitle = Language::t(Yii::app()->language,'Backend.User.ListAdmin','Ban user');
        $banImageUrl = CHtml::image(themeUrl().'/images/buttons/ban.png',Language::t(Yii::app()->language,'Backend.User.ListAdmin','Ban user'));
        $bannedEmails = $this->getBannedEmails();
        if (in_array($this->email,$bannedEmails))
        {
            $banLinkClassName = 'unban-user';
            $banLinkTitle = Language::t(Yii::app()->language,'Backend.User.ListAdmin','Unban user');
            $banImageUrl = CHtml::image(themeUrl().'/images/buttons/restore.png',Language::t(Yii::app()->language,'Backend.User.ListAdmin','Unban user'));   
        }
        $strBan = '<a class="'.$banLinkClassName.'" href="'.$this->id.'" title="'.$banLinkTitle.'">'.$banImageUrl.'</a>';
        return $strDel.$strView.$strBan;
    }
}