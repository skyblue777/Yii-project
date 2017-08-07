<?php

/**
 * This is the model class for table "lookup".
 */
Yii::import('Core.models.base.LookupBase');
class Lookup extends LookupBase
{
    private static $_items=array();
 
    /**
    * Get an array of code => name of a type that can be used for populating a drop-down list
    * 
    * @param mixed $type
    * @return Lookup
    */
    public static function items($type)
    {
        if(!isset(self::$_items[$type]))
            self::loadItems($type);
        return self::$_items[$type];
    }
 
    /**
    * Get name of a code
    * 
    * @param mixed $type
    * @param mixed $code
    * @return Lookup
    */
    public static function item($type,$code)
    {
        if(!isset(self::$_items[$type]))
            self::loadItems($type);
        return isset(self::$_items[$type][$code]) ? self::$_items[$type][$code] : false;
    }
 
    private static function loadItems($type)
    {
        self::$_items[$type]=array();
        $models=self::model()->findAll(array(
            'condition'=>'type=:type',
            'params'=>array(':type'=>$type),
            'order'=>'position',
        ));
        foreach($models as $model)
            self::$_items[$type][$model->code]=$model->name;
    }
}