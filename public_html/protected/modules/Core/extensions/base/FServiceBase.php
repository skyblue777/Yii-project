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

class FServiceBase extends CComponent
{
    /**
    * Service result object
    * @var FServiceModel
    */
    public $result;
    
    public function __construct() {
        $this->result = new FServiceModel();    
    }
    
    public function init() {}
        
    public function getServicePermissions(){
        /**
        * Service permission array
        * A map of service method => friendly meaningful name + user roles which can execute the method
        * This array should include only method that need to be able to grant/revoke execution priviledge.
        * Public service don't need to be listed.
        * 
        * Sample: array(
        *   'method_name' => array('friendly method name','role1','role2',...),
        *   'method_name_hidden' => array('','role1','role2',...), //This method will not display on Service permission page because friendly method name is empty
        *   ...
        * )
        * 
        * Note that the administrators are able to override these roles using 'Service 
        * permissions' menu. If you want to prevent administrators from overriding privilege
        * of a method, just keep 'friendly method name' empty.
        */
        return array();
    }
    
    public function getResult() {
        return $this->result;
    }
    
    /**
    * Get a param passed into the service given its name 
    * 
    * @param mixed $params Service's param array
    * @param mixed $key name of the parameter to get value of
    * @param mixed $default default value if param is not passed into service
    * @param mixed $excludedFilter 
    * @return mixed
    */
    public function getParam($params, $key, $default = null, $excludedFilter = ''){
        $finput = new FInput($params);
        return $finput->getInput($key, $default,$excludedFilter);
    }
    
    /**
    * Extract the model data from service's parameters
    * 
    * @param mixed $data array or model instance to be parsed into a model
    * @param mixed $class model class
    * @param mixed $excludedFilter
    * @return CModel
    */
    public function getModel(&$data, $class, $excludedFilter  = array()){
        $model = new $class;

        //Data must be an array of model attributes or a model instance
        if (!is_array($data) && !is_a($data, 'CModel')){
            return $model;
        }
        
        $pk = $model->getTableSchema()->primaryKey;
        if (! is_array($pk) && isset($data[$pk]) && !empty($data[$pk]))
            $model = $model->findByPk($data[$pk]);

        //Assign model attributes
        if (is_array($data)){
            $finput = new FInput($data);
            foreach ($data as $attr => $value) {
                $attrFitler = isset($excludedFilter[$attr])?$excludedFilter[$attr]:'';
                $model->$attr = $finput->getInput($attr, null,$attrFitler);
            }
        } else
            $model = $data;
            
        return $model;
    }    
}
?>