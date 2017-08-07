<?php

/**
 * This is the model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property string $id
 * @property string $title
 * @property string $alias
 * @property string $description
 * @property string $image
 * @property string $parent_id
 * @property integer $is_active
 * @property integer $ordering
 */
class CategoryBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Category the static model class
	 */
	public static function model($className='Category')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, warning_page, show_ad_counter, price_required', 'required'),
			array('is_active, ordering, warning_page, show_ad_counter, price_required, paid_ad_required', 'numerical', 'integerOnly'=>true),
			array('title, alias, image', 'length', 'max'=>255),
			array('parent_id', 'length', 'max'=>20),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, alias, description, image, parent_id, is_active, ordering', 'safe', 'on'=>'search'),
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
			'title' => Language::t(Yii::app()->language,'Backend.Common.Common','Title'),
			'alias' => Language::t(Yii::app()->language,'Backend.Common.Common','Alias'),
			'description' => Language::t(Yii::app()->language,'Backend.Common.Commone','Description'),
			'image' => Language::t(Yii::app()->language,'Backend.System.Category','Image'),
			'parent_id' => Language::t(Yii::app()->language,'Backend.System.Category','Parent'),
			'is_active' => Language::t(Yii::app()->language,'Backend.System.Category','Is active'),
			'ordering' => Language::t(Yii::app()->language,'Backend.Common.Common','Ordering'),
            'warning_page' => Language::t(Yii::app()->language,'Backend.System.Category','Content warning page'),
            'show_ad_counter' => Language::t(Yii::app()->language,'Backend.System.Category','Show ad counter'),
            'price_required' => Language::t(Yii::app()->language,'Backend.System.Category','Price required')
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}