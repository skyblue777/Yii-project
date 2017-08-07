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
class FServiceModel extends CModel
{
    /**
    * The data pool to store all processed data
    * 
    * @var array
    */
    public $_data = array();
    
    /**
    * Store the error code that service returns while processing
    * 
    * @var mixed
    */
    public $ErrorCode;
    
    public function __get($name) {
        if ($name === 'data' || $name === 'Data')
            return $this->_data;
        elseif (array_key_exists($name, $this->_data))
            return $this->_data[$name];
        else
            return parent::__get($name);
    }
    
    public function setData($value) {
        $this->_data = $value;
    }
    
    public function attributeNames() {
        return array_keys($this->_data);
    }
    
    /**
    * Associate an error message with a processed data
    * 
    * @param mixed $attribute
    * @param mixed $error
    */
    public function addError($attribute, $error) {
        if (!array_key_exists($attribute, $this->_data))
            $this->_data[$attribute] = null;
            
        parent::addError($attribute, $error);
        FErrorHandler::logError($error);
    }
    
    /**
    * Save a processed data into service result
    * 
    * @param string $key key to access the data
    * @param mixed $data 
    * @param string $errorMessage
    */
    public function processed($key, $data, $errorMessage = null) {
        $this->_data[$key] = $data;
        
        if (!is_null($errorMessage))
            $this->addError($key, $errorMessage);
    }
    
    /**
    * Set error code and error message to service result and 
    * return the result object itself. This function is useful 
    * when called with 'return' to stop the service execution
    * 
    * @param mixed $errorCode Should be a predefined/constant error code
    * @param string $errorMessage
    */
    public function fail($errorCode, $errorMessage) {
        $this->ErrorCode = $errorCode;
        $this->addError('ErrorCode', $errorMessage);
        
        return $this;
    }
    
    /**
    * Return service model data in JSON format
    * @param bool $simplified If set to TRUE, errors are ignored and only data is returned
    */
    public function toJson($simplified = 0){
        if ($simplified){
            $jsonString = json_encode($this->normalizeData($this->_data));
            return "({$jsonString})";
        }
        //Add errors into returned data so that service consumer can use to report error
        $this->_data['errors'] = $this->getErrors();
        foreach ($this->_data as $key => $value)
            if (is_a($value, 'CActiveRecord') && $value->hasErrors())
                $this->_data['errors'][$key] = $value->getErrors();
        
        $jsonString = json_encode($this->normalizeData($this->_data));
        return "({$jsonString})";
    }
    
    /**
    * Return service model data in text format
    * 
    */
    public function toText() {
        ob_start();
        foreach ($this->_data as $value)
            echo $value;
        return ob_get_clean();
    }
    
    public function getActiveErrorMessages($models) {
        $result=array();
        if(!is_array($models))
            $models=array($models);
        foreach($models as $model)
        {
            foreach($model->getErrors() as $attribute=>$errors)
                $result[CHtml::activeId($model,$attribute)]=$errors;
        }
        return function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
    }
    
    /**
    * Normalize a $data variable to be able to convert it into JSON with json_encode()
    * 
    * @param mixed $data
    */
    protected function normalizeData($data){
        if (is_array($data)){
            $ret = array();
            foreach($data as $key => $value){
                if (is_array($value))
                    $ret[$key] = $this->normalizeData($value);
                elseif(is_a($value,'CActiveRecord'))
                    $ret[$key] = $value->attributes;
                else
                    $ret[$key] = $value;
            }
            return $ret;
        }elseif(is_a($data, 'CActiveRecord')){
            return $data->attributes;
        }else
            return $data;
    }
}
?>