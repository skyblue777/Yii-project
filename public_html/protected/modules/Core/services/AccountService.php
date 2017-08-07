<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */


class AccountService extends FServiceBase
{
    /**
    * Register an user
    *
    * @param mixed $params
    * @return ServiceResult
    */
    public function register($params)
    {
        $user = $this->getModel($params['FUser'], 'FUser');

        $user->CreatedDate = date('Y/m/d H:i:s', time());

        // Keep raw password to send mail to user
        $rawPassword = $user->Password;

        //Encrypt password
        $user->Password = md5($user->Password);
        $user->rePassword = md5($user->rePassword);

        //Validation info just in case it is used
        $user->ValidationCode = md5(time());
        $user->ValidationType = FUser::VALIDATION_TYPE_ACTIVATE;
        $user->Status = FUser::STATUS_INACTIVE;

        $this->result->processed('user', $user);

        //Set primarykey to null in order to recieve auto increase ID from DB
        $user->Id = null;
        if (!$user->save()) 
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User','Cannot save user information.'));
        
        return $this->result;
    }

    /**
    * Send email when register
    *
    * @param mixed $params
    * @return result
    */
    public function sendRegisterMail($user, $rawPassword = null)
    {
        Yii::import('Messaging.models.Email');
        //EmailService::init('Messaging');
        $email = new Email();
        $email->Subject = Yii::t('User', 'REGISTER_EMAIL_SUBJECT');
        $email->From = Settings::ADMIN_EMAIL;
        $email->To = $user->Email;
        $email->Template = 'Register';
        /**
        * Email params
        */
        $email->Params['DisplayedName'] = $user->Username;
        $email->Params['SiteUrl'] = Yii::app()->request->getBaseUrl(true);
        $email->Params['Email'] = $user->Email;
        $pwdInEmail = (!is_null($rawPassword) ? $rawPassword : '[Your registered password]');
        $email->Params['Password'] = $pwdInEmail;

        if (Yii::app()->controller instanceof CmsBaseController) {
            $email->Params['ActivateUrl'] = Yii::app()->controller->createCommandUrl('User/Account/activate', Settings::DEFAULT_PAGE_ID, array('id' => $user->Id, 'code' => $user->ValidationCode), true);
        } else {
            $email->Params['ActivateUrl'] = Yii::app()->request->getBaseUrl(true).'/index.php?cmd=User/Account/activate&id='.$user->Id.'&code='.$user->ValidationCode;
        }

        $emailParams['Email'] = $email;

        $this->result = Cms::rawService('Messaging/Email/send', $emailParams);
        $this->result->ReturnedData['User'] = $user;

        return $this->result;
    }

    /**
    * Resend activation email (for other members). This action is usually for admin and it is just allowed if user logges in the system successfully.
    *
    * @param mixed $params
    * @return mixed
    */
    public function resendActivation($params)
    {
		$user = $this->getParam($params, 'user', null);
		$rawPassword = $this->getParam($params, 'rawPassword', null);
		return self::sendRegisterMail($user, $rawPassword);
    }

    /**
    * Activate an account registered
    */
    public function activate($params)
    {
        // Get account info
        $id = $this->getParam($params, 'id', 0);
        $code = $this->getParam($params, 'code', null);
        // Check valid account
        $user = User::model()->findByPk($id);
        if (!empty($user)) {
            if ($user->ValidationCode != $code) {
                // Invalide account, the input validation code differs from in db
                $this->result->fail(ServiceResult::ERR_INVALID_DATA, Yii::t('User', 'USER_ACTIVATE_ACCOUNT_FAILED'));
            } else {
                // Update status to active
                $user->Status = User::STATUS_MEMBER;
                if ($user->update()) {
                    $this->result->ReturnedData['message'] = Yii::t('User', 'USER_ACTIVATE_ACCOUNT_SUCCESSFUL');
                } else {
                    $this->result->fail(ServiceResult::ERR_INVALID_DATA, Yii::t('User', 'USER_UPDATE_FAILED'));
                }
            }
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
        $user = $this->getModel($params['FUser'], 'FUser');
        $this->result->processed('user', $user);

        $user->setScenario('login');
        if (!$user->validate())
            return $this->result->fail(0, Yii::t('User','Email or password is invalid'));

        $ui = new FUserIdentity($user->email, $user->password);
        $ui->authenticate();

        if ($ui->errorCode === FUserIdentity::ERROR_NONE) {
            $remember = $this->getParam($params, 'remember', false);
            Yii::app()->user->login($ui, $remember ? 24*60*60 : 0);
            return $this->result;
        } else {
            $this->result->fail($ui->errorCode, Yii::t('User', $ui->getErrorMessage($ui->errorCode)));
            return $this->result;
        }
    }

    public function cmdLogout()
    {
        Yii::app()->user->logout();
    }

    /**
    * Update user account at BO
    *
    * @param mixed $params
    * @return ServiceResult
    */
    public function update($params)
    {
        $user = $this->getModel($params['FUser'], 'FUser');

        $updatedFields = array(
            'Username'  => $user->Username,
            'Email'     => $user->Email,
            'FirstName' => $user->FirstName,
            'LastName'  => $user->LastName,
            'Status'	=> $user->Status,
            'UpdatedDate'  => date('Y/m/d H:i:s'),
        );

        //Encrypt password
        if ($user->Password != '') {
            $user->Password = md5($user->Password);
            $user->rePassword = md5($user->rePassword);
            $updatedFields['Password'] = $user->Password;
        }

        $this->result->processed('user', $user);
        
        // Update user
        $user->updateByPk($user->Id, $updatedFields);
        if ($user->hasErrors())
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('User','Cannot saved user information.'));
            
        return $this->result;
    }
    
    public function deleteUsers($params)
    {
        $ids = $this->getParam($params, 'ids', array());
        foreach ($ids as $id) {
            if (! FUser::model()->deleteByPk($id))
                return $this->result->fail(ERROR_HANDLING_DB,  Yii::t('User','Cannot delete user with Id {id}.', array('{id}' => $id)));
            elseif (Yii::app()->authManager instanceof FAuthManager) {
                Yii::app()->authManager->revoke(null, $id);
            }
        }
        return $this->result;
    }

    /**
    * Update account at FE
    *
    * @param mixed $params
    * @return ServiceResult
    */
    public function edit($params)
    {
        $user = $this->getModel($params['FUser'], 'FUser');

        $updatedFields = array(
            'Username'  => $user->Username,
            'Email'     => $user->Email,
            'FirstName'  => $user->FirstName,
            'LastName'  => $user->LastName,
            'UpdatedDate'  => date('Y/m/d H:i:s'),
        );
        //Encrypt password
        if ($user->Password != '') {
            $user->Password = md5($user->Password);
            $user->rePassword = md5($user->rePassword);
            $updatedFields['Password'] = $user->Password;
        }

        if (isset($params['User']['Status']))
            $updatedFields['Status'] = $user->Status;

        // Update account
        if ($user->updateByPk($user->Id, $updatedFields)===false) {
            $this->result->addError('user', $user->getErrors());
        }

        $this->result->processed('user', $user);

        return $this->result;
    }

    public function changePassword($params)
    {
        $userId = Yii::app()->user->Id;
        $user = new User();
        $this->getModel($params, $user, 'changePassword');
        if ($this->result->isFailed())
        {
            return $this->result;
        }
        $password = md5($user->Password);
        // Get an instance of this user
        $user = User::model()->findByPk($userId);
        $user->Password = $password;
        $user->setIsNewRecord(false);
        if (! $user->update())
        {
            $this->result->fail(ServiceResult::ERR_INVALID_DATA, $this->normalizeModelErrors($user->getErrors()));
        }
        return $this->result;
    }

    public function forgotPassword($params)
    {
        $user = new User();
        $this->getModel($params, $user, 'forgotPassword');
        if ($this->result->isFailed())
            return $this->result;

        // Get an instance of this user
        $user = User::model()->find('Email=:email', array(':email'=>$user->Email));

        // This user does not exist
        if (is_null($user))
        {
			$this->result->fail(ServiceResult::ERR_INVALID_DATA, Yii::t('User', 'USER_EMAIL_NOT_EXISTED'));
        }
        // Send email to this user
        else
        {
	        Yii::import('Messaging.models.Email');
	        $email = new Email();
	        $email->Subject = Yii::t('User', 'FORGOT_PASSWORD_EMAIL_SUBJECT');
	        $email->From = Settings::ADMIN_EMAIL;
	        $email->To = $user->Email;
	        $email->Template = 'ForgotPassword';
	        /**
	        * Email params
	        */
	        $email->Params['Email'] = $user->Email;
	        $email->Params['DisplayedName'] = $user->Username;
	        $email->Params['LoginLink'] = Yii::app()->controller->createAbsoluteUrl('/User/admin/user/login');
	        $email->Params['Password'] = Utility::generateRandomString(6);

	        $emailParams['Email'] = $email;

	        // Update user's new pwd
	        $user->Password = md5($email->Params['Password']);
	        $user->setIsNewRecord(false);
	        $user->update();

	        $this->result = Cms::service('Messaging/Email/send', $emailParams);
        }

        $this->result->ReturnedData['User'] = $user;

        return $this->result;

    }

    /**
    * Send activation email
    *
    * @param string user email
    * @return ServiceResult
    */
    public function sendActivation($params)
    {
		$user = new User();
        $this->getModel($params, $user, 'sendActivation');
        if ($this->result->isFailed())
            return $this->result;

        // Get an instance of this user
        $user = User::model()->find('Email=:email', array(':email'=>$user->Email));

        // This user does not exist
        if (is_null($user))
        {
			$this->result->fail(ServiceResult::ERR_INVALID_DATA, Yii::t('User', 'USER_EMAIL_NOT_EXISTED'));
        }
        // Send email to this user
        else
        {
        	$this->result = self::sendRegisterMail($user,null);
		}
		return $this->result;
    }

    /**
    * Check if email or username exists or not
    *
    * @param mixed $params
    * - index email: email to check
    * - index username: username to check
    */
    public function checkUsernameEmailExistence($params)
    {
        $email = $this->getParam($params, 'email', '');
        $user = User::model()->find('Email=:email', array(':email'=>$email));
        if (! empty($user)) {
            $this->result->ReturnedData['emailExists'] = 1;
        } else {
            $this->result->ReturnedData['emailExists'] = 0;
        }

        $username = $this->getParam($params, 'username', '');
        $user = User::model()->find('Username=:username', array(':username'=>$username));
        if (! empty($user)) {
            $this->result->ReturnedData['usernameExists'] = 1;
        } else {
            $this->result->ReturnedData['usernameExists'] = 0;
        }
        return $this->result;
    }

    /**
    * Service hook to post call of User/Account/register
    *
    * @param mixed $params
    *   - User user
    */
    public function post_user_account_register($params)
    {
        $result = $this->getParam($params, 'SERVICE_RESULT');
        $user = $result->ReturnedData['User'];

		// Get country code of user
		$countryCode = Utility::getVisitorCountryCode();
        $result = new ServiceResult();

        Yii::import('User.models.UserProfile');
        $profile = new UserProfile();
        $profile->UserId = $user->Id;
        $profile->Nationality = $countryCode;

        if (! $profile->save(false)) {
            $result->fail(ServiceResult::ERR_SERVICE_SPECIFIC, 'Could not save profile');
        }
        return $result;
    }
}
?>
