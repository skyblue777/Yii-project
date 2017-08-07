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
  
class Query extends FInlineViewWidget
{
    /**
    * @var string SQL query
    */
    public $Sql='';
    /**
    * @var string name of the db connection used to query data
    */
    public $DB='db';
    /**
    * @var array query parameters
    */
    public $Params=array();
    
    /**
    * @var string path to ActiveRecord model class
    */
    public $Model;
    
    /**
    * @var string condition used for the criteria of the findAll()
    */
    public $Where=null;
    
    public $Order=null;
    
    public $Limit=null;
    
    public $Offset=null;
    
    public function run(){
        if ($this->Sql == '') {
            //Import the model
            Yii::import($this->Model);
            
            //Create the criteria object
            $criteria = new CDbCriteria();
            $criteria->params = $this->Params;
            if ($this->Where)
                $criteria->condition = $this->Where;
            if ($this->Order)
                $criteria->order = $this->Order;
            if ($this->Limit)
                $criteria->limit = $this->Limit;
            if ($this->Offset)
                $criteria->offset = $this->Offset;
            
            //Build findAll PHP statement
            $tmp = explode('.',$this->Model);
            $class = end($tmp);
            $php = "\$result = {$class}::model()->findAll(\$criteria);";
            
            eval($php);
        } else {
            //We don't want to harm the database with a dangerous query!
            if (strtolower(substr($this->Sql,0,6)) !== 'select') {
                $result = array();
            } else {
                $db = Yii::app()->getComponent($this->DB);
                $cmd = $db->createCommand($this->Sql);
                $result = $cmd->queryAll(true, $this->Params);
            }
        }
        
        
        $this->render(array('result'=>$result));
    }
}
?>