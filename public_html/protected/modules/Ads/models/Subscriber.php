<?php

class Subscriber extends CActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'subscriber';
    }
    
    public function rules() {
        return array (
          array ('url,email','required') 
        );
    }
}
?>
