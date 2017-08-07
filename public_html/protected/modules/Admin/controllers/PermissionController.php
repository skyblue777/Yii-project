<?php
class PermissionController extends BackOfficeController
{
    /**
    * The authManager component shortcut
    * 
    * @var FAuthManager
    */
    protected $auth;
    
    public function __construct($id, $module) {
        parent::__construct($id, $module);
        $this->auth = Yii::app()->authManager;
    }
    
    public function actionIndex(){
        $this->render('index');
    }
    
    public function actionServices(){
        $this->render('index');
    }
    
    public function actionPages(){
        $this->data['pages'] = $this->getPageTree();
        
        $roleItems = Yii::app()->authManager->getAuthItems(FAuthManager::ROLE_ITEM_TYPE);
        $this->data['roles'] = array();
        foreach ($roleItems as $role)
            $this->data['roles'][] = $role->name;
        
        //Save permissions
        if (Yii::app()->request->IsPostRequest) {
            $auth = Yii::app()->authManager;
            foreach($_POST as $role => $assignments)
            {
                if (!is_array($assignments)) continue;
                foreach($assignments as $op => $granted){
                    if ($granted == 1 && $auth->hasItemChild($role, $op) == false){
                        if ($auth->getAuthItem($op) == null)
                            $auth->createService($op, "service");
                        $auth->addItemChild($role, $op);
                    }
                    if ($granted == -1)
                        $auth->removeItemChild($role, $op);
                }
            }
            $this->message = Yii::t('Permission','Page access permissions updated successfully.');
        }
        
        $this->render('pageAccess', $this->data);
    }
    
    public function actionListPages(){
        $pages = $this->getPageTree();
        $this->render('list', array('pages' => $pages));
    }
    
    public function actionEditPage(){
        $name = $this->get('name','');
        $page = $this->auth->getAuthItem($name);
        
        //Save page
        if (Yii::app()->request->IsPostRequest) {
            $result = FSM::run('Core.permission.savePage', $_POST);
            if (!$result->hasErrors()){
                $this->message = Yii::t('Permission', 'Page has been saved successfully.');
                $page = $result->page;
            }
        }
        
        $this->render('editPage', array('page' => $page));
    }
    
    protected function getPageArray() {
        $pages = $this->auth->getAuthItems(FAuthManager::ACTION_ITEM_TYPE);
        
        $arrPages = array();
        foreach ($pages as $page)
            $arrPages[$page->name] = array(
                'description' => $page->description,
                'children' => array(),
            );

        $names = "'".implode("','", array_keys($arrPages))."'";
        $sql = "SELECT * FROM {$this->auth->itemChildTable} WHERE parent IN ({$names}) OR child IN ({$names})";
        $relations = Yii::app()->db->createCommand($sql)->queryAll();
        
        if (!empty($relations))
            foreach($relations as $r) 
                $arrPages[$r['parent']]['children'][] = $r['child'];
                
        return $arrPages;
    }
    
    public function getPageTree() {
        //Find top level pages
        $sql = "SELECT * FROM {$this->auth->itemTable} 
                WHERE type = ".FAuthManager::ACTION_ITEM_TYPE."
                    AND name NOT IN (
                        SELECT child FROM {$this->auth->itemChildTable}, {$this->auth->itemTable}
                        WHERE parent = name AND type = ".FAuthManager::ACTION_ITEM_TYPE."
                    )";
        $topPages = Yii::app()->db->createCommand($sql)->queryAll();
        if (empty($topPages)) return array();
        
        $arrPages = $this->getPageArray();
        $tree = array();
        foreach ($topPages as $page) {
            $tree = $tree + $this->getPageSubTree($page['name'], $arrPages);
        }
        return $tree;
    }
    
    protected function getPageSubTree($name, $pages, $level = 0)
    {
        $pages[$name]['level'] = $level;
        $tree = array($name => $pages[$name]);
        if (isset($pages[$name]['children']) && is_array($pages[$name]['children']) === true) {
            foreach ($pages[$name]['children'] as $subName) {
                $tree = $tree + $this->getPageSubTree($subName, $pages, $level + 1);
            }
        }
        return $tree;
    }
}
?>