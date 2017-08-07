<?php

/**
 * This is the model class for table "setting".
 *
 * The followings are the available columns in table 'setting':
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property string $value
 * @property string $description
 * @property string $setting_group
 * @property integer $ordering
 * @property integer $visible
 * @property string $module
 */
class SettingParamBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SettingParam the static model class
	 */
	public static function model($className='SettingParam')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'setting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, value', 'required'),
			array('ordering, visible', 'numerical', 'integerOnly'=>true),
			array('name, label, module', 'length', 'max'=>64),
			array('setting_group', 'length', 'max'=>128),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, label, value, description, setting_group, ordering, visible, module', 'safe', 'on'=>'search'),
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
			'name' => Language::t(Yii::app()->language,'Backend.Common.Common','Name'),
			'label' => Language::t(Yii::app()->language,'Backend.Common.Common','Label'),
			'value' => Language::t(Yii::app()->language,'Backend.Common.Common','Value'),
			'description' => Language::t(Yii::app()->language,'Backend.Common.Common','Description'),
			'setting_group' => Language::t(Yii::app()->language,'Backend.System.Setting','Setting Group'),
			'ordering' => Language::t(Yii::app()->language,'Backend.Common.Common','Ordering'),
			'visible' => Language::t(Yii::app()->language,'Backend.System.Setting','Visible'),
			'module' => Language::t(Yii::app()->language,'Backend.System.Setting','Module'),
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('setting_group',$this->setting_group,true);
		$criteria->compare('ordering',$this->ordering);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('module',$this->module,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}