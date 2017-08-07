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
class FSM extends CComponent
{
    /**
    * Array of service instances
    */
    private static $_initializedServices = array();
    
    /**
    * Get instance of the service to be executed
    * 
    * @param mixed $serviceId
    * @return array($serviceIntance, $methodName)
    */
    public static function getService($serviceId) {
        $tmp = explode('.', $serviceId);
        if (count($tmp) != 3) throw new Exception("Invalid service ID format {$serviceId}. The correct service ID format is module.service.action");

        $srvKey = "{$tmp[0]}.{$tmp[1]}";
        //Service instance is not created, make a new one
        $class = ucfirst($tmp[1]).'Service';
        
        if (!isset(self::$_initializedServices[$srvKey])) {
            //Try to import class file
            Yii::import($tmp[0].'.services.'.$class, true);
            if(!class_exists($class))
                throw new Exception("{$class} service is not found while trying to call service ID {$serviceId}");
            
            /**
            * Initialize the service
            *  - Import models
            *  - Save service permission data
            */
            $service = new $class;
            if (!is_a($service, 'FServiceBase'))
                throw new Exception("{$class} service is not extended from FServiceBase");
                
            Yii::import($tmp[0].'.models.*');
            self::$_initializedServices[$srvKey] = $service->ServicePermissions;
        } else {
            $service = new $class;
        }

        return array($service, $tmp[2]);
    }
    
    /**
    * Execute a module service without checking for permission
    * This method improve the performance by assuming that you are sure
    * the caller has enough privilege. Becareful when use this method to
    * call a service.
    *
    * @param string $serviceId in format Module.ServiceClass.method
    * @param array $data
    * @param bool $bypassAuthCheck bypass authentication checking
    * @return FServiceModel
    */
    public static function _run($serviceId, $data = array(), $bypassAuthCheck = TRUE) {
        list($service, $method) = self::getService($serviceId);     
        if (!is_callable(array($service, $method)))
            throw new Exception("Function {$method} is not found in {$class} service");
        //Check user permission
        if (! $bypassAuthCheck)
            if (self::isExecutable($serviceId, $data) !== TRUE) {
                FErrorHandler::logError("User does not have enough privilege to execute {$serviceId} service.");
                return null;
            }
        
        //Execute pre service extensions
        $exts = Extension::model()->findAll('event = :Event AND enabled = 0', array(':Event' => 'pre_'.strtolower(str_replace('.','_',$serviceId))));
        foreach($exts as $ext){
            $result = $ext->execute($data);
            if ($result->hasErrors())
                return $result;
        }
        //Run the service
        try {
            $service->init();
            $service->$method($data);
            $serviceResult = $service->result;
        } catch (FException $ex) {
            FErrorHandler::logError($ex->getMessage());
            return null;
        }
        //Execute post service extensions
        if (! $serviceResult->hasErrors()){
            $exts = Extension::model()->findAll('event = :Event AND enabled != 0', array(':Event' => 'post_'.strtolower(str_replace('.','_',$serviceId))));
            foreach($exts as $ext){
                $serviceResult = $ext->execute($data, $serviceResult);
                if ($serviceResult->hasErrors())
                    return $serviceResult;
            }
        }
        
        return $serviceResult;
    }
    
    /**
    * Execute a module service
    *
    * @param string $serviceId in format Module.ServiceClass.method
    * @param array $data
    * @return FServiceModel
    */
    public static function run($serviceId, $data = array()){
        return self::_run($serviceId, $data, FALSE);
    }
    
    public static function isExecutable($serviceId, &$data) {
        $executable = false;
        
        list($module,$serviceClass,$method) = explode('.', $serviceId);
        list($service, $method) = self::getService($serviceId);
        
        /**
        * Get service class's permission array
        * And previleged methods (those who require user to have some previleges to execute)
        */
        $permissions = $service->getServicePermissions();
        $privilegedServices = array_keys($permissions);
        if(in_array($method, $privilegedServices)){
            //The requested service is a privileged service, first check if it's not overridable by admin
            $authItem = "{$module}.{$serviceClass}Service.{$method}";
            if ($permissions[$method][0] == '' || Yii::app()->authManager->getAuthItem($authItem) === null) {
                //Overriding is not allowed OR no override is mode, just checking the listed roles is enough
                for($i = 1; $i <count($permissions[$method]); $i++) {
                    $role = $permissions[$method][$i];
                    if (Yii::app()->user->checkAccess($role, $data)) {
                        $executable = TRUE;
                        break;
                    }
                }
            } else {
                //Admin can override privileges, check in our RBAC database
                if(Yii::app()->user->checkAccess($authItem, $data))
                    $executable = TRUE;
            }
        } else {
            //The requested service is not a privileged service, free to execute
            $executable = TRUE;
        } 
        
        return $executable;
    }
}
?>