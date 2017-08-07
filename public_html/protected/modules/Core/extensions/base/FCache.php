<?php
/**
-------------------------
GNU GPL COPYRIGHT NOTICES
-------------------------
This file is part of FlexicaCMS.

FlexicaCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FlexicaCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FlexicaCMS.  If not, see <http://www.gnu.org/licenses/>.*/

/**
 * $Id$
 *
 * @author FlexicaCMS team <contact@flexicacms.com>
 * @link http://www.flexicacms.com/
 * @copyright Copyright &copy; 2009-2010 Gia Han Online Solutions Ltd.
 * @license http://www.flexicacms.com/license.html
 */
 
class FCache extends CComponent
{
    /**
    * @var CCache
    */
    protected static $cache;

    protected static function getCache() {
        if (self::$cache == null)
            self::$cache = Yii::app()->getComponent('cache');
            
        return self::$cache;
    }   
    
    /**
    * Retrieves a value from cache with a specified key.
    * 
    * @param string $id
    * @return mixed
    */
    public static function get($id) {
        if (self::getCache() == null) return false;
        return self::getCache()->get($id);
    } 
    
    /**
    * Stores a value identified by a key into cache.
    * 
    * @param string $id
    * @param mixed $value
    * @param mixed $expire time to live of the cache. 
    * If a string is specify then it is considered as a name of a Cache Dependency Item
    * so the duration and dependency object of that item will be used
    * 
    * @param ICacheDependency $dependency
    * @return boolean
    */
    public static function set($id, $value, $expire = 0, $dependency = null) {
        if (is_string($expire) && !empty($expire)) {
            //TODO: need change
            $dependency = self::dependency($name);
            $expire = self::duration($expire);
        }
        return self::getCache()->set($id, $value, $expire, $dependency);
    }
    
    /**
    * Cretae a Cache Dependency Item for tracking cache validation
    * 
    * @param mixed $name
    * @param mixed $description
    */
    protected static function createDependencyItem($name, $description = '') {
        if (empty($name))
            throw new Exception(Yii::t('Admin.Cache','Data is missing or invalid.'));
            
        if (!Cache::model()->countByAttributes(array('name'=>$name))) {
            $model = new Cache;
            $model->name = $name;
            $model->description = $description;
            $model->last_update = date('Y-m-d H:i:s');
            if (!$model->save()) {
                throw new Exception(Yii::t('Core.Cache','Error while saving Cache Dependency item.'));
            }
        }
    }
    
    /**
    * Get Cache Dependency Item by name
    * 
    * @param mixed $name
    * @return Cache
    */
    protected static function getDependencyItem($name) {
        $model = Cache::model()->findByAttributes(array('name'=>$name));
//        if (! $model)
//            throw new Exception(Yii::t('Admin.Cache','Invalid name.'));
        return $model;
    }
    
    /**
    * Create a CDbCacheDependencyObject
    * 
    * @param mixed $name
    * @return CDbCacheDependency
    */
    public static function dependency($name, $description = '')
    {
        if (empty($name))
            throw new Exception(Yii::t('Core.Cache','A name is required for creating the dependency object.'));
        
        self::createDependencyItem($name, $description);
        
        $sql = "SELECT last_update FROM cache WHERE name='".$name."'";
        return new CDbCacheDependency($sql);
    }
    
    public static function duration($name) {
        if (($item = self::getDependencyItem($name)) != null)
            $duration = $item->duration;
        else
            $duration = -1;
        
        //use global expire when duration = -1
        if ($duration < 0) {
            if (defined('Settings::CACHE_EXPIRE') && Settings::CACHE_EXPIRE >= 0)
                $duration = Settings::CACHE_EXPIRE;
            else
                $duration = 0;
        }
        
        return $duration;
    }
    
    /**
    * Make Cache Dependency Item become invalidated
    * 
    * @param mixed $name can be a single name or array
    */
    public static function invalidateCache($name) {
        if (! is_array($name)) $name = array($name);
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('name', $name);
        
        Cache::model()->updateAll(
            array(
                'expired'=>date('Y-m-d H:i:s')
            ),
            $criteria
        );            
    }
}