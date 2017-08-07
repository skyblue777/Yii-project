<?php

/**
 * This is the model class for table "module".
 *
 * The followings are the available columns in table 'module':
 * @property integer $id
 * @property string $name
 * @property string $friendly_name
 * @property string $description
 * @property string $version
 * @property integer $has_back_end
 * @property integer $ordering
 * @property string $icon
 */
class ModuleBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Module the static model class
	 */
	public static function model($className='Module')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'module';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, has_back_end', 'required'),
			array('has_back_end, ordering', 'numerical', 'integerOnly'=>true),
			array('name, version', 'length', 'max'=>64),
			array('friendly_name, icon', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, friendly_name, description, version, has_back_end, ordering, icon', 'safe', 'on'=>'search'),
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
			'friendly_name' => Language::t(Yii::app()->language,'Backend.System.Module','Friendly Name'),
			'description' => Language::t(Yii::app()->language,'Backend.Common.Common','Description'),
			'version' => Language::t(Yii::app()->language,'Backend.System.Module','Version'),
			'has_back_end' => Language::t(Yii::app()->language,'Backend.System.Module','Has Back End'),
			'ordering' => Language::t(Yii::app()->language,'Backend.Common.Common','Ordering'),
			'icon' => Language::t(Yii::app()->language,'Backend.System.Module','Icon'),
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
		$criteria->compare('friendly_name',$this->friendly_name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('has_back_end',$this->has_back_end);
		$criteria->compare('ordering',$this->ordering);
		$criteria->compare('icon',$this->icon,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}