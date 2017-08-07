<?php

/**
 * This is the model class for table "article".
 */

Yii::import('User.models.User');
require_once dirname(__FILE__).'/base/ArticleBase.php';
class Article extends ArticleBase
{
    const STATUS_ACTIVE = 1, STATUS_INACTIVE = 2;
    
    public $author_name;
    
    public function relations(){
        return CMap::mergeArray(
            parent::relations(),
            array(
                'category' => array(self::BELONGS_TO, 'Category', 'category_id', 'select' => 'title'),
                'author' => array(self::BELONGS_TO, 'User', 'author_id', 'select' => 'username,email'),
            )
        );
    }
    
    
    public function behaviors()
    {
        return array(
            'timestamp'=>array(
                'class'=>'Core.extensions.db.ar.TimestampBehavior',
            ),
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
        $criteria->compare('t.lang',$this->lang);
        $criteria->compare('t.category_id',$this->category_id);
        $criteria->compare('t.title',$this->title,true);
        $criteria->compare('t.alias',$this->alias,true);
        $criteria->compare('leading_text',$this->leading_text,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('photo',$this->photo,true);
        $criteria->compare('tags',$this->tags,true);
        $criteria->compare('t.status',$this->status);
        $criteria->compare('t.create_time',$this->create_time);
        $criteria->compare('t.update_time',$this->update_time);
        $criteria->compare('t.author_id',$this->author_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
    }
    
}