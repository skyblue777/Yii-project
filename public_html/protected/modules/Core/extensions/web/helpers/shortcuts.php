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
  
  
/**
* shortcut to create url
* 
* @param mixed $route
* @param mixed $params
* @param mixed $ampersand
*/
function url($route, $params=array ( ), $ampersand='&'){
    return Yii::app()->controller->createUrl($route, $params, $ampersand);
}

/**
* Create an service URL which use service controller to call an API given its serviceId
* 
* @param string $serviceId
* @param string $type possible values ajax / ajax.text / ajax.full / widget / command
* @param array $params
* @param string $ampersand
*/
function serviceUrl($serviceId, $type='ajax', $params = array( ), $ampersand='&') {
    $params['SID'] = $serviceId;
    // receive simplified result in case of AJAX. There is no error info in result.
    if ($type == 'ajax.full')
        $type = 'ajax';
    elseif ($type == 'ajax.text') {
        $type = 'ajax';
        $params['FORMAT'] = 'text';
    }elseif ($type == 'ajax') {
        $type = 'ajax';
        $params['SIMPLIFIED'] = 1;
    }
        
    return url('/Core/service/'.$type, $params, $ampersand);
}

/**
* site base url
*/
function baseUrl() {
    return Yii::app()->request->getBaseUrl(true);
}
/**
* theme base url
*/
function themeUrl(){
    if (Yii::app()->theme)
        return Yii::app()->theme->baseUrl;
    else
        return Yii::app()->baseUrl;
}

/**
* Application base path
*/
function basePath() {
    return Yii::app()->basePath;
}

/**
* Application runtime path
*/
function runtimePath() {
    return Yii::app()->basePath.'/runtime';
}

/**
* Application cache path
*/
function cachePath() {
    return Yii::app()->basePath.'/runtime/cache';
}

/**
* Application clientScript object
* @return CClientScript
*/
function cs(){
    return Yii::app()->clientScript;
}

/**
* current logged in user
* @return CWebUser
*/
function user() {
    return Yii::app()->user;
}

/**
* Check if a setting class has a defined constant
* 
* @param mixed $class
* @param mixed $param
* @return mixed parameter value or false
*/
function hasParam($setting) {
    list($class, $param) = explode('::',$setting);
    if (class_exists($class, false)) {
        $class = new ReflectionClass($class);
        return $class->getConstant($param);
    }
    return false;
}
?>