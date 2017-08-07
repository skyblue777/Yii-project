<?php

/**
 * This is the model class for table "article_comment".
 *
 * The followings are the available columns in table 'article_comment':
 * @property integer $id
 * @property integer $article_id
 * @property integer $user_id
 * @property string $name
 * @property string $email
 * @property string $url
 * @property string $comment
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class ArticleCommentBase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ArticleComment the static model class
	 */
	public static function model($className='ArticleComment')
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'article_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('article_id, name, email, comment', 'required'),
			array('article_id, user_id, status', 'numerical', 'integerOnly'=>true),
			array('name, email', 'length', 'max'=>128),
			array('url', 'length', 'max'=>256),
			array('create_time, update_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, article_id, user_id, name, email, url, comment, status, create_time, update_time', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'article_id' => 'Article',
			'user_id' => 'User',
			'name' => 'Name',
			'email' => 'Email',
			'url' => 'Url',
			'comment' => 'Comment',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
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
		$criteria->compare('article_id',$this->article_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}