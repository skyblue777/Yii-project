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
 
class CategoryDropDownList extends CInputWidget
{
    const CACHE_KEY='Core.components.CategoryTree.categories';
    
    public $categories = null;
    public $exclude = array();
    /* 
     * Level -1 means find all;
     * Level 0 means find only the children DIRECTLY under the current category;
     * level N means find those categories that are within N levels.
     */
    public $prefix = '--';
    public $root_id = 0;
    public $level = -1;
    public $select = 0;
    public $cacheID = 'cache';
    public $enableCache=false;
    public $expire=0;
    public $dependency=null;
    
    public function init()
    {
        if (is_null($this->categories))
        {
            if($this->enableCache && $this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
            {
                $key = self::CACHE_KEY.'#level:'.((string) $this->level).(is_array($this->exclude) && count($this->exclude) ? ',exclude:'.implode($this->exclude) : '');
                if(($data=$cache->get($key))!==false)
                    $this->categories = $data;
            }
            
            if (is_null($this->categories))
            {
                $this->categories = array();
                $criteria=new CDbCriteria;
                $criteria->compare('parent_id', $this->root_id);
                $criteria->order = 'ordering';
                $models = Category::model()->findAll($criteria);
                $this->findChildrenRecursive($models);
                if ($this->enableCache && $cache)
                    $cache->set($key, $this->categories, $this->expire, $this->dependency);
            }
        }
        parent::init();
    }
    
    public function run()
    {
        if (is_object($this->model))
            echo CHtml::activeDropDownList($this->model, $this->attribute, $this->categories, $this->htmlOptions);
        else
            echo CHtml::dropDownList($this->name, $this->select, $this->categories, $this->htmlOptions);
    }
    
    protected function findChildrenRecursive($models, $count=0)
    {
        foreach ($models as $index => $model)
        {
            if (count($this->exclude) && in_array($model->id, $this->exclude)) continue;
            if ($this->level !== -1 && $count > $this->level) continue;
            
            $this->categories[$model->id] = str_repeat($this->prefix, $count).$model->title;
            $children = $model->children;
            if (is_array($children) && count($children))
                $this->findChildrenRecursive($children, $count+1);
        }
    }
}