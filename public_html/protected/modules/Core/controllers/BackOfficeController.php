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

class BackOfficeController extends FController
{

    public function __construct($id, $module = null){
        parent::__construct($id, $module);
        
        Yii::app()->theme = Settings::BO_THEME;
//        $this->layout = '//layouts/'.Settings::DEFAULT_BO_LAYOUT;
        
        Yii::app()->user->loginUrl = array('Admin/default/login');
    }
    
    public function filters(){
        $filters = array(
                'accessControl',
            );
        return $filters;
    }   

    /**
    * Show Permission denied page
    */
    public function actionPermissionDenied() {
        ErrorHandler::logError('Permission denied!<br />- You do not have enough privilege to access the page you requested or<br />- The requested page is accessible but a service on that page cannot be performed on your behalf.');
        Yii::app()->layout = 'permission';
        $this->render('PermissionDenied');
    }
    
    /**
     * Handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('//../modules/Core/views/error', $error);
        }
    }

}
?>