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


class FController extends CController
{
    /**
    * Message to be displayed to user
    * 
    * @var string
    */
    protected $message;
    
    /**
    * Current view that being renderred
    * 
    * @var mixed
    */
    public $CurrentViewFile;
    
    /**
    * Array of view data
    * 
    * @var array
    */
    public $data = array();
    
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();
    
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();

    
    public function init(){
        parent::init();
        if (! @class_exists('Settings')){
            Yii::trace('Settings file is not found! File is generated into runtime/cache folder. Make sure it is writable. ');
            FSM::run('Core.Settings.db2php', array('module'=>'Core'));
        }
        // register FAjax library
        if (! Yii::app()->request->getIsAjaxRequest()) {
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScript('set-baseurl-for-js',"var baseUrl = '".Yii::app()->request->baseUrl."'",CClientScript::POS_BEGIN);
            Yii::app()->clientScript->registerScriptFile(Yii::app()->core->AssetUrl.'/scripts/jquery/fajax.js', CClientScript::POS_BEGIN);
	}
    }
    
    public function post($name, $default = null, $filter = ''){
        $input = new FInput($_POST);
        return $input->getInput($name, $default, $filter);
    }
    
    public function get($name, $default = null, $filter = ''){
        $input = new FInput($_GET);
        return $input->getInput($name, $default, $filter);
    }
    
    public function cookie($name, $default = null, $filter = ''){
        $input = new FInput($_COOKIE);
        return $input->getInput($name, $default, $filter);
    }
    
    
    public function renderFile($viewFile,$data=null,$return=false)
    {
        //Track the current view file so the InlineViewWidget knows which view it belong to
        $this->CurrentViewFile = $viewFile;
        return parent::renderFile($viewFile, $data, $return);
    }    

    /**
    * Get message of current page and also previous page
    * 
    * Message from previous page will be prepended
    */
    public function getMessage(){ 
        $message = Yii::app()->Session['Message'];
        $message .= "<br/>".$this->message;
        unset(Yii::app()->Session['Message']);
        return $message;
    }
    /**
    * Set message for the current page
    * 
    * @param mixed $value
    */
    public function setMessage($value){ $this->message = $value;}
    
    /**
    * Redirect page but save errors and message to session if needed
    * 
    * @param mixed $url
    * @param mixed $terminate
    */
    public function redirect($url, $terminate = true){
        $errorHtml = $this->getUserErrors();
        if ($errorHtml != '')
            Yii::app()->Session['ErrorMessage'] = $errorHtml;
        if ($this->message != '')
            Yii::app()->Session['Message'] = $this->message;
        parent::redirect($url, $terminate);
    }
    
    /**
    * Get user error messages and translate into HTML content
    * @return string HTML error content
    */
    public function getUserErrors(){
        $errors = Yii::app()->getErrorHandler()->getUserExceptions();
        $c = count($errors);
        if ($c == 0) return '';
        
        
        $html = "<ul>\n";
        for ($i = 0; $i < $c; $i++){
            $e = $errors[$i];
            $html .= "<li>".$e->getErrorMessage()."</li>\n";
        }    
        $html .= "</ul>\n";
        return $html;
    }
    
    /**
    * Get error for displaying to user
    * 
    * Error message in session (from previous page) will be prepended
    */
    public function getErrors(){
        //Get error message from previous page (which may redirect user to this page)
        $html = trim(Yii::app()->Session['ErrorMessage']);
        //Append error message in this page after processing
        $html .= "<br/>".$this->getUserErrors();
        //Error is surely displayed to user, we can clean session
        unset(Yii::app()->Session['ErrorMessage']);
        return $html;
    }

    /**
    * Define an array of options that guest users can access.
    * Derived controllers if define accessControll filter can 
    * override this method to provide guest users access to 
    * other feature of the site
    */
    public function getGuestAllowedActions() {
        return array(
            'login', 'forgotPassword', 'error'
        );
    }
    
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => $this->getGuestAllowedActions(),
                'users' => array('*')
            ),
            array(
                'allow',
                //Use our RBAC to allow admin dynamicall grant/revoke access to Admin Panel (AP)
                //TODO: Yii will throw error HTTP 403 if user logged in and does not meet this rule
                //while we prefer log him out and send him back to BO login page
                'expression' => array($this, 'isActionAccessible'),
            ),
            array(
                'deny',
                //Finally, everyone is denied, event logged in users
                'users' => array('*')
            ),
        );
    }     
    
    /**
    * Get route for Auth item name
    * Subclassess can override this function in special cases (include query string...)
    * @return string route
    */
    public function getRouteForAuthItem()
    {
        $route = $this->getRoute();
        return Yii::app()->authManager->urlRoute2AuthItem($route);
    }
    
    /**
    * Check if an action is accessible by user
    * 
    * @param mixed $action
    */
    public function isActionAccessible(){
        if (Yii::app()->user->isGuest) return false;
    
        $authItemName = $this->getRouteForAuthItem();
        return Yii::app()->user->checkAccess($authItemName);            
    }
    
    /**
    * Return TRUE if a request ask for a BackEndController
    * otherwise return false;
    * @return bool
    */
    public function isBackEnd(){
        if (Yii::app()->controller instanceof BackOfficeController)
            return true;
        else
            return false;        
    }
    
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $error=Yii::app()->errorHandler->error;
        if(Yii::app()->request->isAjaxRequest)
            echo $error['message'];
        else
        {
            //$this->layout = '//layouts/error';
            if (!empty(Yii::app()->theme)) {
                $errorView = Yii::app()->theme->SystemViewPath.'/error'.$error['code'].'.php';
                if (file_exists($errorView)) {
                    // custom error page
                    $this->render('error'.$error['code'], array('error'=>$error)); 
                    return;
                }
            }
            // default error page
            $this->render('//error', array('error'=>$error));
        }
    }
}
?>