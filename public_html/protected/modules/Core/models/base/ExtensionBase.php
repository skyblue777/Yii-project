<?php

/**
 * This is the model class for table "extension".
 *
 * The followings are the available columns in table 'extension':
 * @property integer $id
 * @property string $event
 * @property string $class
 * @property string $method
 * @property string $config
 * @property integer $enabled
 */
class ExtensionBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Extension the static model class
	 */
	public static function model($className='Extension')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'extension';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event, class, method, enabled', 'required'),
			array('enabled', 'numerical', 'integerOnly'=>true),
			array('event', 'length', 'max'=>255),
			array('class, method', 'length', 'max'=>64),
			array('config', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event, class, method, config, enabled', 'safe', 'on'=>'search'),
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
			'event' => Language::t(Yii::app()->language,'Backend.System.Models-ExtesnionBase','Event'),
			'class' => Language::t(Yii::app()->language,'Backend.System.Models-ExtesnionBase','Class'),
			'method' => Language::t(Yii::app()->language,'Backend.System.Models-ExtesnionBase','Method'),
			'config' => Language::t(Yii::app()->language,'Backend.Common.Common','Config'),
			'enabled' => Language::t(Yii::app()->language,'Backend.Common.Common','Enabled')
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
		$criteria->compare('event',$this->event,true);
		$criteria->compare('class',$this->class,true);
		$criteria->compare('method',$this->method,true);
		$criteria->compare('config',$this->config,true);
		$criteria->compare('enabled',$this->enabled);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}