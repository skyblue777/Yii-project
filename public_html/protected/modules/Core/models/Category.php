<?php

/**
 * This is the model class for table "category".
 */
Yii::import('Core.models.base.CategoryBase');
class Category extends CategoryBase
{
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return CMap::mergeArray(parent::relations(), array(
            'parent'=>array(self::BELONGS_TO, 'Category', 'parent_id'),
            'children'=>array(self::HAS_MANY, 'Category', 'parent_id', 'order'=>'ordering'),
            'countChildren'=>array(self::STAT, 'Category', 'parent_id'),
        ));
    }

    /**
     * This method is invoked before saving a record (after validation, if any).
     * The default implementation raises the {@link onBeforeSave} event.
     * You may override this method to do any preparation work for record saving.
     * Use {@link isNewRecord} to determine whether the saving is
     * for inserting or updating record.
     * Make sure you call the parent implementation so that the event is raised properly.
     * @return boolean whether the saving should be executed. Defaults to true.
     */
    protected function beforeSave()
    {
        $this->alias = Utility::createAlias(new Category(), $this->title);
        return parent::beforeSave();
    }
}