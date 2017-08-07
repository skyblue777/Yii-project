<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $created_date
 * @property string $last_login
 * @property string $validation_code
 * @property integer $validation_type
 * @property integer $validation_expired
 * @property integer $status
 */
class FUserBase extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return User the static model class
     */
    public static function model($className='User')
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, password', 'required'),
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Language::t(Yii::app()->language,'Backend.Common.Common','ID'),
            'username' => Language::t(Yii::app()->language,'Backend.Common.Common','Username'),
            'password' => Language::t(Yii::app()->language,'Backend.Common.Common','Password'),
            'email' => Language::t(Yii::app()->language,'Backend.Common.Common','Email'),
            'created_date' => Language::t(Yii::app()->language,'Backend.System.User','Created Date'),
            'last_login' => Language::t(Yii::app()->language,'Backend.System.User','Last Login'),
            'validation_code' => Language::t(Yii::app()->language,'Backend.System.User','Validate Code'),
            'validation_type' => Language::t(Yii::app()->language,'Backend.System.User','Validation Type'),
            'validation_expired' => Language::t(Yii::app()->language,'Backend.System.User','Validation Expired'),
            'status' => Language::t(Yii::app()->language,'Backend.Common.Common','Status'),
        );
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

        $criteria->compare('id',$this->id);
        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('created_date',$this->created_date,true);
        $criteria->compare('last_login',$this->last_login,true);
        $criteria->compare('validation_code',$this->validation_code,true);
        $criteria->compare('validation_type',$this->validation_type);
        $criteria->compare('validation_expired',$this->validation_expired);
        $criteria->compare('status',$this->status);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
    }
}