<?php

/**
 * This is the model class for table "annonce".
 *
 * The followings are the available columns in table 'annonce':
 * @property integer $id
 * @property integer $category_id
 * @property string $type
 * @property string $title
 * @property integer $price
 * @property string $opt_price
 * @property string $description
 * @property string $email
 * @property string $area
 * @property string $zipcode
 * @property double $lat
 * @property double $lng
 * @property string $photos
 * @property string $video
 * @property integer $viewed
 * @property integer $replied
 * @property string $code
 * @property string $txn_id
 * @property string $public
 * @property string $featured
 * @property string $feature_status
 * @property integer $feature_days
 * @property double $feature_total
 * @property string $feature_mdp
 * @property string $feature_txn
 * @property string $date
 * @property string $evt
 * @property string $create_time
 * @property string $update_time
 * @property integer $send
 * @property string $homepage
 * @property string $homepage_status
 * @property integer $homepage_days
 * @property double $homepage_total
 * @property string $homepage_mdp
 * @property string $homepage_txn
 */
class AnnonceBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Annonce the static model class
	 */
	public static function model($className='Annonce')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'annonce';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id, type, title, price, opt_price, description, email, txn_id, feature_mdp, feature_txn, date, create_time, update_time, homepage_mdp, homepage_txn', 'required'),
			array('category_id, price, viewed, replied, feature_days, send, homepage_days', 'numerical', 'integerOnly'=>true),
			array('lat, lng, feature_total, homepage_total', 'numerical'),
			array('type, opt_price, public, featured, feature_status, evt, homepage, homepage_status', 'length', 'max'=>1),
			array('title', 'length', 'max'=>150),
			array('email', 'length', 'max'=>100),
			array('area', 'length', 'max'=>200),
			array('zipcode', 'length', 'max'=>10),
			array('code', 'length', 'max'=>50),
			array('feature_mdp, homepage_mdp', 'length', 'max'=>5),
			array('photos, video', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, category_id, type, title, price, opt_price, email, area, zipcode, lat, lng, photos, video, viewed, replied, code, public, featured, feature_status, feature_days, feature_total, feature_mdp, feature_txn, date, evt, create_time, update_time, send, homepage, homepage_status, homepage_days, homepage_total, homepage_mdp, homepage_txn', 'safe', 'on'=>'search'),
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
			'category_id' => Language::t(Yii::app()->language,'Backend.Common.Common','Category'),
			'type' => Language::t(Yii::app()->language,'Backend.Common.Common','Type'),
			'title' => Language::t(Yii::app()->language,'Backend.Common.Common','Title'),
			'price' => Language::t(Yii::app()->language,'Backend.Common.Common','Price'),
			'opt_price' => Language::t(Yii::app()->language,'Backend.Ads.Common','Opt Price'),
			'description' => Language::t(Yii::app()->language,'Backend.Common.Common','Description'),
			'email' => Language::t(Yii::app()->language,'Backend.Common.Common','Email'),
			'area' =>	Language::t(Yii::app()->language,'Backend.Ads.Common','Location'),
			'zipcode' => Language::t(Yii::app()->language,'Backend.Ads.Common','Zip Code'),
			'lat' => Language::t(Yii::app()->language,'Backend.Ads.Common','Lat'),
			'lng' => Language::t(Yii::app()->language,'Backend.Ads.Common','Lng'),
			'photos' => Language::t(Yii::app()->language,'Backend.Common.Common','Photos'),
			'video' => Language::t(Yii::app()->language,'Backend.Common','Video'),
			'viewed' =>  Language::t(Yii::app()->language,'Backend.Ads.Common','Viewed'),
			'replied' => Language::t(Yii::app()->language,'Backend.Ads.Common','Replied'),
			'code' => Language::t(Yii::app()->language,'Backend.Ads.Common','Code'),
      		'txn_id' => Language::t(Yii::app()->language,'Backend.Ads.Common','Paypal Transaction ID'),
			'public' => Language::t(Yii::app()->language,'Backend.Ads.Common','Public'),
			'featured' => Language::t(Yii::app()->language,'Backend.Ads.Common','Featured'),
			'feature_status' => Language::t(Yii::app()->language,'Backend.Ads.Common','Feature Status'),
			'feature_days' => Language::t(Yii::app()->language,'Backend.Ads.Common','Feature Days'),
			'feature_total' => Language::t(Yii::app()->language,'Backend.Ads.Common','Feature Total'),
			'feature_mdp' => Language::t(Yii::app()->language,'Backend.Ads.Common','Feature Mdp'),
			'feature_txn' => Language::t(Yii::app()->language,'Backend.Ads.Common','Feature Txn'),
			'date' => Language::t(Yii::app()->language,'Backend.Common.Common','Date'),
			'evt' => Language::t(Yii::app()->language,'Backend.Ads.Common','Evt'),
			'create_time' => Language::t(Yii::app()->language,'Backend.Common.Common','Create Time'),
			'update_time' => Language::t(Yii::app()->language,'Backend.Common.Common','Update Time'),
			'send' => Language::t(Yii::app()->language,'Backend.Common.Common','Send'),
			'homepage' => Language::t(Yii::app()->language,'Backend.Common','Homepage'),
			'homepage_status' => Language::t(Yii::app()->language,'Backend.Ads.Common','Homepage Status'),
			'homepage_days' => Language::t(Yii::app()->language,'Backend.Ads.Common','Homepage Days'),
			'homepage_total' => Language::t(Yii::app()->language,'Backend.Ads.Common','Homepage Total'),
			'homepage_mdp' => Language::t(Yii::app()->language,'Backend.Ads.Common','Homepage Mdp'),
			'homepage_mdp' => Language::t(Yii::app()->language,'Backend.Ads.Common','Homepage Txn'),
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
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('opt_price',$this->opt_price,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);
		$criteria->compare('photos',$this->photos,true);
		$criteria->compare('video',$this->video,true);
		$criteria->compare('viewed',$this->viewed);
		$criteria->compare('replied',$this->replied);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('public',$this->public,true);
		$criteria->compare('featured',$this->featured,true);
		$criteria->compare('feature_status',$this->feature_status,true);
		$criteria->compare('feature_days',$this->feature_days);
		$criteria->compare('feature_total',$this->feature_total);
		$criteria->compare('feature_mdp',$this->feature_mdp,true);
		$criteria->compare('feature_txn',$this->feature_txn,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('evt',$this->evt,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('send',$this->send);
		$criteria->compare('homepage',$this->homepage,true);
		$criteria->compare('homepage_status',$this->homepage_status,true);
		$criteria->compare('homepage_days',$this->homepage_days);
		$criteria->compare('homepage_total',$this->homepage_total);
		$criteria->compare('homepage_mdp',$this->homepage_mdp,true);
		$criteria->compare('homepage_txn',$this->homepage_txn,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}