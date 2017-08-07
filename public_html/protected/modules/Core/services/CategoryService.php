<?php

class CategoryService extends FServiceBase
{    
    const CACHE_KEY = 'CORE_CATEGORIES';
    
    /**
    * Get a Category model given its ID
    * 
    * @param int id Category ID
    * @return FServiceModel
    */
    public function get($params){
        $model = Category::model()->findByPk($this->getParam($params, 'id',0));
        if (! $model)
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid ID.'));
        $this->result->processed('model', $model);
        return $this->result;
    }
    
    public function save($params) {
        /**
        * @var CModel
        */
        $model = $this->getModel($params['Category'],'Category');
        $model->title = trim(strip_tags($params['Category']['title']));
        $this->result->processed('model', $model);
        
        if (! $model->validate())
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        elseif ($this->getParam($params, 'validateOnly',0) == TRUE)
            return $this->result;
        elseif (! $model->save())
            $this->result->fail(ERROR_HANDLING_DB, Language::t(Yii::app()->language,'Frontend.Ads.Message','Error while saving submitted data into database.'));
            
        //flag update cache
        FCache::invalidateCache(self::CACHE_KEY);
        
        return $this->result;
    }

    public function delete($params) {
        $ids = $this->getParam($params, 'ids', array());
        if ($ids == 0) {
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid ID.'));
        }
      
        if (!is_array($ids)) $ids = array($ids);
        Yii::import('Ads.models.Annonce');
        Yii::import('Article.models.Article');
        foreach($ids as $id) {
            $model = Category::model()->findByPk($id);
            if (is_object($model)) {
                if ($model->countChildren) {
                    $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Category has some child categories so it cannot be deleted.'));
                    return $this->result;
                }
                if (Annonce::model()->count('category_id=:cat_id',array(':cat_id'=>$model->id)) > 0)
                {
                    $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Category has some ads so it cannot be deleted.'));
                    return $this->result;   
                }
                if (Article::model()->count('category_id=:cat_id',array(':cat_id'=>$model->id)) > 0)
                {
                    $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Sorry! Category has some articles so it cannot be deleted.'));
                    return $this->result;   
                }
            } else {
                $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
                return $this->result;
            }
            try {
                $model->delete();
                
                //flag update cache
                FCache::invalidateCache(self::CACHE_KEY);
            } catch (CDbException $ex) {
                $this->result->fail(ERROR_HANDLING_DB, $ex->getMessage());
            }
        }
        return $this->result;
    }

    public function sort($params)
    {
        $ids = $this->getParam($params, 'items', array());
        if (count($ids)) {
            foreach ($ids as $id => $parentId) {
                $orders = array();
                if ($parentId == 'root') {
                    $orders = array_keys($ids, $parentId);
                    $parentId = AdsSettings::ADS_ROOT_CATEGORY;
                }
                else {
                    $parentId = (int) $parentId;
                    $orders = array_keys($ids, $parentId);
                }
                $order = 0;
                if (count($orders))
                    $order = (int) array_search($id, $orders);
                $order++;
                Category::model()->updateByPk($id, array(
                    'parent_id'=>$parentId,
                    'ordering'=>$order,
                ));
                
                //flag update cache
                FCache::invalidateCache(self::CACHE_KEY);
            }
        }
        return $this->result;
    }
    
    public function status($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $value = $this->getParam($params, 'value', 0);
        $model = Category::model()->findByPk($id);
        if (is_object($model)) {
            $model->is_active = $value;
            if (!$model->save(false, array('is_active')))
                $this->result->fail(ERROR_HANDLING_DB, Language::t(Yii::app()->language,'Frontend.Ads.Message','Error while saving submitted data into database.'));
            
            FCache::invalidateCache(self::CACHE_KEY);
        } else
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        return $this->result;
    }
    
    public function changePriceRequired($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $value = $this->getParam($params, 'value', 0);
        $model = Category::model()->findByPk($id);
        if (!is_null($model)) {
            $model->price_required = $value;
            if (!$model->save(false, array('price_required')))
                $this->result->fail(ERROR_HANDLING_DB, Language::t(Yii::app()->language,'Frontend.Ads.Message','Error while saving submitted data into database.'));
            
            FCache::invalidateCache(self::CACHE_KEY);
        } else
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        return $this->result;
    }
    
    public function changeShowBanner($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $value = $this->getParam($params, 'value', 0);
        $model = Category::model()->findByPk($id);
        if (!is_null($model)) {
            $updatedCatIds = array($model->id);
            // find all child cats of this cat
            $childIds = $this->findAllChildCatIds($model->id);
            $updatedCatIds = CMap::mergeArray($updatedCatIds,$childIds);
            
            $cri = new CDbCriteria();
            $cri->addInCondition('id',$updatedCatIds);
            Category::model()->updateAll(array('show_banner'=>$value),$cri);
            
            FCache::invalidateCache(self::CACHE_KEY);
        } else
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        return $this->result;
    }
    
    public function changePaidAdRequired($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $value = $this->getParam($params, 'value', 0);
        $model = Category::model()->findByPk($id);
        if (!is_null($model)) {
            $updatedCatIds = array($model->id);
            // find all child cats of this cat
            $childIds = $this->findAllChildCatIds($model->id);
            $updatedCatIds = CMap::mergeArray($updatedCatIds,$childIds);
            
            $cri = new CDbCriteria();
            $cri->addInCondition('id',$updatedCatIds);
            Category::model()->updateAll(array('paid_ad_required'=>$value),$cri);
            
            FCache::invalidateCache(self::CACHE_KEY);
        } else
            $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Submitted data is missing or invalid.'));
        return $this->result;
    }
    
    protected function findAllChildCatIds($cat_id)
    {
        static $childCatIds;
        if (!isset($childCatIds))
            $childCatIds = array();
        $con = Yii::app()->db;
        $childs = $con->createCommand("SELECT id FROM category WHERE parent_id = {$cat_id}")->queryAll(TRUE);
        foreach($childs as $child)
        {
            $childCatIds[] = $child['id'];
            $this->findAllChildCatIds($child['id']);    
        }
        
        return $childCatIds;    
    }
    
    public function checkIsAdsCategory($params)
    {
        $cat_id = $this->getParam($params, 'cat_id', 0);
        if ($cat_id == AdsSettings::ADS_ROOT_CATEGORY)
        {
            $this->result->processed('isAdsCat',1);
            $this->result->processed('isAdsTopCat',1);
            return $this->result;    
        }
        
        $cat = Category::model()->findByPk($cat_id);
        if (is_null($cat))
        {
            $this->result->processed('isAdsCat',0);
            return $this->result;
        }
        
        $parent = $cat->parent;
        while(!is_null($parent))
        {
            if ($parent->parent_id == 0 && $parent->id == AdsSettings::ADS_ROOT_CATEGORY)
            {
                $this->result->processed('isAdsCat',1);
                $this->result->processed('isAdsTopCat',0);
                return $this->result;
            }
            $parent = $parent->parent;
        }   
        
        $this->result->processed('isAdsCat',0);
        return $this->result;
    }

    /**
    * Get category tree
    * 
    * @param int $id root category ID
    * @param int $status if set to 1 will get only active categories
    * @param int $no_root if set to 1 will exclude the root category, default is 0
    * @return array
    */
    public function tree($params = null)
    {
        $id = $this->getParam($params, 'id', 0);
        $status = $this->getParam($params, 'status', null);//null => select all, 1=> only select public category
        $no_root = $this->getParam($params, 'no_root', 0);
        
        $key='Core.services.'.__CLASS__.'::'.__FUNCTION__.'.'.$id;
        if(($data=FCache::get($key))!==false) {
            $tree = unserialize($data);
            $this->result->processed('tree', $tree);
            return $this->result;
        }
        
        //Get top level categories
        $criteria=new CDbCriteria;
        if ($no_root)
            $criteria->compare('parent_id', $id);
        else
            $criteria->compare('id', $id);
        if (isset($status))
            $criteria->compare('is_active', $status);
        $criteria->order = 'ordering';
        $models = Category::model()->findAll($criteria);
        $tree = $this->subTree($models, $status);
        
        FCache::set($key, serialize($tree), FCache::duration(self::CACHE_KEY), FCache::dependency(self::CACHE_KEY));
        
        $this->result->processed('tree', $tree);
        return $this->result;
    }
    
    /**
    * Create subtree, internally use by Category service
    * 
    * @param Category $models
    * @param int $level
    * 
    * @return array
    */
    protected function subTree($models, $status=null, $level = 0)
    {
        static $data;
        if (!isset($data))
            $data = array();
        foreach ($models as $index => $model) {
            if (isset($status) && $model->is_active != $status) continue;
            $children = $model->children;
            $data[$model->id] = array(
                'title' => $model->title,
                'alias' => $model->alias, 
                'level' => $level,
                'parent' => $model->parent_id,
                'children' => array(),
            );
            if (is_array($children) && count($children)) {
                $data[$model->id]['children'] = CHtml::listData($children, 'id', 'id');
                $this->subTree($children, $status, $level+1);
            }
        }
        return $data;
    }
    
    
    /**
    * Get path of category Ids from root to the current category
    * 
    * @param mixed $params
    *   - int id
    * @return FServiceModel
    */
    public function path($params)
    {
        $id = $this->getParam($params, 'id', 0);
        
        if ($id == 0)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid category ID'));

        if($this->getExpire() && (($cache=Yii::app()->cache)!==null))
        {
            $key='Core.services.'.__CLASS__.'::'.__FUNCTION__.'.'.$id;
            if(($data=$cache->get($key))!==false) {
                $path = unserialize($data);
                $this->result->processed('path', $path);
                return $this->result;
            }
        }
            
        $criteria=new CDbCriteria;
        $criteria->compare('id', $id);
        $model = Category::model()->find($criteria);
        
        $path = array($id);
        if (is_object($model)) {
            $parent = $model->parent;
            while (is_object($parent)) {
                array_push($path, $parent->id);
                $parent = $parent->parent;
            }
        }
        
        if(isset($cache))
        {
            $cache->set($key,serialize($path), $this->getExpire(), $this->getCacheDependency());
        }
        
        $this->result->processed('path', $path);
        return $this->result;
    }

    /**
    * Find categories which are childrend of a category given its Id
    * Required param:
    *   - int id: Id of parent category
    * Optional param:
    *   - bool includeStat: Whether to include statistics (total child categories and total articles)
    *   - bool skipDescription: Whether to return description data. Use this to speed up AJAX request
    *
    * @param mixed $params
    * @return FServiceModel
    */
    public function findByParentId($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $withStat = $this->getParam($params, 'includeStat', 0);
        $skipDescription = $this->getParam($params, 'skipDescription', 0);
        
        if ($id == 0)
            return $this->result->fail(ERROR_INVALID_DATA, Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid category ID'));
            
        $criteria=new CDbCriteria;
        $criteria->compare('parent_id', $id);
        $criteria->order = 'ordering';
        
        if ($skipDescription) {
            $attributes = Category::model()->getAttributes();
            if (array_key_exists('description', $attributes))
                unset($attributes['description']);
            $attributes = array_keys($attributes);
            $criteria->select = $attributes;
        }
        
        if ($withStat)
            $criteria->with = 'countChildren';
        
        $models = Category::model()->findAll($criteria);
        
        $this->result->processed('categories', $models);
        return $this->result;
    }
    
    /**
    * Get Id of the current category and all of its descendant Ids
    * 
    * @param mixed $params
    *   - int id
    * @return array ids: list of IDs
    */
    public function getIdAndChildren($params)
    {
        $id = $this->getParam($params, 'id', 0);
        $status = $this->getParam($params, 'status', null);
        
        if ($id == 0)
            return $this->result->fail(ERROR_INVALID_DATA,  Language::t(Yii::app()->language,'Frontend.Ads.Message','Invalid category ID'));
        
        $ids = array($id);
        
        $data = $this->buildSubTree(array('id'=>$id, 'status'=>$status))->tree;
        if (is_array($data) && count($data))
            $ids = CMap::mergeArray($ids, array_keys($data));
            
        $this->result->processed('ids', $ids);
        return $this->result;
    }
    
    /**
    * Create an alias
    *
    * @param mixed $params
    * @return String alias
    */
    public function createAlias($title)
    {
        return Utility::createAlias(new Category(), $title);
    }
}
