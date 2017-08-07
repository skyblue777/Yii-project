<?php

/**
 * This is the model class for table "ann_favorites".
 *
 * The followings are the available columns in table 'ann_favorites':
 * @property integer $user_id
 * @property integer $annonce_id
 */
class AdFavoritesBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AdFavorites the static model class
	 */
	public static function model($className='AdFavorites')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ann_favorites';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, annonce_id', 'required'),
			array('user_id, annonce_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, annonce_id', 'safe', 'on'=>'search'),
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
			'user_id' => Language::t(Yii::app()->language,'Backend.Common.Common','User'),
			'annonce_id' => Language::t(Yii::app()->language,'Backend.Ads.AdFavorites','Annonce'),
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('annonce_id',$this->annonce_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}