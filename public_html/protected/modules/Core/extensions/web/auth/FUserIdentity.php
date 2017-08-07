<?php
/**
* $Id: UserIdentity.php 2166 2009-11-12 07:20:12Z phong.quach $
*
* Class UserIdentity
*
* Authenticate user using database. The 'Users' table must have these three columns
* - Username
* - Password
* - Status
* By default, it assumes to use table 'users' in the database but you can choose
* other table by setting UserModel property. This is helpful in case of a site has
* member table and admin table separately
*
* @author Hung Nguyen, Flexica Solution
*/
class FUserIdentity extends CUserIdentity
{
    const ERROR_STATUS_INVALID = 3;
    const ERROR_NOT_ALLOWED = 4;
    protected $user;

    /**
    * Database authentication with checking of user status to support account activation
    * which use Status as Role
    */
    public function authenticate(){
        $auth = Yii::app()->authManager;
        $user = Yii::createComponent($auth->UserClass);
        
        $this->user = $user->findByAttributes(array($auth->UsernameField => $this->username));

        if (is_null($this->user)){
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }elseif($this->user->Attributes[$auth->PasswordField] != md5($this->password)){
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        }elseif(($err = $this->statusIsValid($this->user)) !== TRUE){
            $this->errorCode = $err;
        }else{
            $this->errorCode = self::ERROR_NONE;
        }

        if ($this->errorCode == self::ERROR_NONE)
            foreach($auth->UserStatefulFields as $field)
                $this->setState($field, $this->user->Attributes[$field]);

        return $this->user;
    }
    
    public function statusIsValid($user) {
        $auth = Yii::app()->authManager;
        if (empty($auth->UserInvalidStatuses)) return true;
        
        $status = $user->Attributes[$auth->StatusField];
        if (!isset($auth->UserInvalidStatuses[$status])) return true;

        return $auth->UserInvalidStatuses[$status];
    }
    
    /**
    * Translate login error code into English message
    *
    * @param mixed $code
    * @return mixed
    */
    public function getErrorMessage($code){
        if (is_string($code)) return $code;
        
        switch($code){
            case self::ERROR_NONE:
                return '';
            case self::ERROR_USERNAME_INVALID:
                return Language::t(Yii::app()->language,'Backend.System.User','Sorry but your username is not found.');
            case self::ERROR_PASSWORD_INVALID:
            case self::ERROR_UNKNOWN_IDENTITY:
                return Language::t(Yii::app()->language,'Backend.System.User','Invalid username or password.');
            case self::ERROR_NOT_ALLOWED:
                return Language::t(Yii::app()->language,'Backend.System.User','You do not have enough previlege to access the requested page.');
            default:
            	return Language::t(Yii::app()->language,'Backend.System.User','Sorry, you cannot login as some errors occur.');
        }
    }

    /**
    * Return User's Id instead of username
    *
    * CWebUser use this Id value to store as app()->user->id.
    * By overriding this function, the app()->user object is very similar to user object queried from database
    *
    */
    public function getId(){
        return $this->user->PrimaryKey;
    }
}
?>
