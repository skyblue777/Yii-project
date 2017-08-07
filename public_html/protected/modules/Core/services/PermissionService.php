<?php
class PermissionService extends FServiceBase
{
    /**
    * @var FAuthManager
    */
    protected $auth;
    
    public function __construct() {
        parent::__construct();
        $this->auth = Yii::app()->authManager;
    }
    
    /**
    * Save page item
    * 
    * @param string name
    * @param string parent
    * @param string description
    * @return CAuthItem page
    */
    public function savePage($params) {
        $page = $this->getParam($params, 'AuthItem');

        if (empty($page['name']))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Permission', 'Page name is required.'));
            
        if (empty($page['description']))
            return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Permission', 'Page description is required.'));
        
        $parent = null;
        if (!empty($page['parent']))
            if (($parent = $this->auth->getAuthItem($page['parent'])) == null)
                return $this->result->fail(ERROR_INVALID_DATA, Yii::t('Permission', 'Parent page does not exist.'));
            
        $item = $this->auth->getAuthItem($page['name']);
        if ($item == null) {
            $item = $this->auth->createAction($page['name'], $page['description']);
        } else {
            $item->description = $page['description'];
            $this->auth->saveAuthItem($item, $page['name']);
        }
        //Delete relationship with the old parent
        $sql = "DELETE FROM  {$this->auth->itemChildTable}
                USING {$this->auth->itemTable}, {$this->auth->itemChildTable}
                WHERE child = '{$page['name']}' 
                AND parent = name AND type = ".FAuthManager::ACTION_ITEM_TYPE;
        Yii::app()->db->createCommand($sql)->execute();
        //Add the new one
        if ($parent != null)        
            $this->auth->addItemChild($parent->name, $page['name']);
        
        $this->result->processed('page', $item);
    }
}
?>