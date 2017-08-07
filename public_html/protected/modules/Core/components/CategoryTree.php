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
 
class CategoryTree extends CInputWidget
{
    const CACHE_KEY='Core.components.CategoryTree.categories';
    
    /**
    * root category ID 
    * @var int
    */
    public $root_id = 0;
        
    /**
    * categories to be rendered as tree
    * 
    * @var array See the array structure of Core.Category.tree for more details
    */
    public $categories = null;
    
    /**
    * URL format used for each category. In the URL, :id and :alias are replace by the category's id and alias
    * 
    * @var mixed
    */
    public $url;
    
//    public $exclude = array();
//    /* 
//     * Level -1 means find all;
//     * Level 0 means find only the children DIRECTLY under the current category;
//     * level N means find those categories that are within N levels.
//     */
//    public $prefix = '--';
//    public $level = -1;
//    public $select = 0;
//    public $cacheID = 'cache';
//    public $enableCache=false;
//    public $expire=0;
//    public $dependency=null;
    
    public function init()
    {
        if (is_null($this->categories))
        {
            $this->categories = FSM::_run('Core.Category.tree', array(
                'id' => $this->root_id,
                'status'=>1,
                'no_root'=>1
            ))->tree;
        }
        
        if (!empty($this->url)) 
            $this->url = urldecode($this->url);
            
        parent::init();
    }
    
    public function run()
    {
        echo '<ul>';
        foreach($this->categories as $id => $cat)
        {
            if ($cat['parent'] == $this->root_id)
                $this->renderCategoryRecursive($id);
        }
        echo '</ul>';
    }
    
    protected function renderCategoryRecursive($id) {
        echo '<li>';
        if (!empty($this->url) && (strpos($this->url, ':id') !== false || strpos($this->url, ':id') !== false))
        {
            $url = preg_replace(array('/:id/','/:alias/'), array($id, $this->categories[$id]['alias']),$this->url);
            echo CHtml::link($this->categories[$id]['title'], $url);
        }
        else
            echo $this->categories[$id]['title'];
        if (!empty($this->categories[$id]['children']))
        {
            echo '<ul>';
            foreach($this->categories[$id]['children'] as $id)
                $this->renderCategoryRecursive($id);
            echo '</ul>';
        }
        else
        {
            unset($this->categories[$id]);
            echo '</li>';
        }
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