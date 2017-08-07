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


Yii::import('Core.models.base.FUserBase');
class FUser extends FUserBase
{
    const STATUS_INACTIVE = 0;
    const STATUS_MEMBER = 1;
    const STATUS_BANNED = -1;

    const VALIDATION_TYPE_ACTIVATE = 1;

    public $rePassword = '';
    
    public $StatusCollection = array(0 => 'Inactive', 1 => 'Active');

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, password', 'required'),
            array('email, password', 'required', 'on'=>'login'),
            array('email', 'email'),
            array('validation_type, validation_expired, status', 'numerical', 'integerOnly'=>true),
            array('username, email, validation_code', 'length', 'max'=>64),
            array('password', 'length', 'max'=>32),
            array('created_date, last_login', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, username, password, email, created_date, last_login, validation_code, validation_type, validation_expired, status', 'safe', 'on'=>'search'),
        );
    }

    
    /**
    * Get user roles
    * @return array
    */
    public function getRoles() {
        $assignmentItems = Yii::app()->authManager->getAuthAssignments($this->Id);
        $roles = array();
        foreach ($assignmentItems as $assignment) {
            $roles[] = $assignment->itemName;
        }
        return $roles;
    }

    /**
     * @desc Check if username already exists
     */
    public function usernameExists($attribute, $params) {
        if (!$this->hasErrors($attribute))
		{
	        $user = User::model()->findByAttributes(array('username' => $this->Username));
	        if (!is_null($user) && $user->id != $this->id)
	        {
	            $this->addError('username', Yii::t('User', 'USER_USERNAME_EXISTED'));
	            return false;
	    	}
        }
    }

    /**
     * @desc Check if email already exists
     */
    public function emailExists($attribute, $params) {
        if (!$this->hasErrors($attribute))
        {
            $user = User::model()->findByAttributes(array('email' => $this->Email));
	        if (!is_null($user) && $user->id != $this->id){
	            $this->addError('email', Yii::t('User', 'USER_EMAIL_EXISTED'));
                return false;
            }
        }
    }

    public function emailNotExist($attribute, $params)
    {
        if (!$this->hasErrors($attribute))
        {
            $user = User::model()->findByAttributes(array('Email' => $this->Email));
            if ($this->scenario == 'forgotPassword' && is_null($user))
            {
                $this->addError('Email', Yii::t('User', 'USER_EMAIL_NOT_EXISTED'));
                return false;
            }
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('FirstName', $this->FirstName, true);
        $criteria->compare('Username', $this->Username, true);
        $criteria->compare('Email', $this->Email, true);
        $criteria->compare('Status', $this->Status, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>Settings::BO_PAGE_SIZE,
            ),
            'sort'=>array(
                'defaultOrder'=>'t.CreatedDate DESC',
            )
        ));
    }
}
?>