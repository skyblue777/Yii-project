<?php

/**
 * This is the model class for table "cache".
 */

require_once dirname(__FILE__).'/base/CacheBase.php';
class Cache extends CacheBase
{
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return CMap::mergeArray(parent::rules(), array(
            array('name', 'unique'),
        ));
    }
}