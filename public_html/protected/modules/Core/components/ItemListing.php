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
  
class ItemListing extends FInlineViewWidget {

    public $Order=null;
    
    public $Limit=null;
    
    public $Offset=null;
    
    /**
    * @var string path to ActiveRecord model class
    */
    private $model;
    
    /**
    * @var CModel
    */
    private $searchModel;
    
    public function __construct($owner=null) {
        parent::__construct($owner);
    }

    public function __set($name, $value) {
        if ($name == 'Model') {
            $this->model = $value;
            // create also the search model object
            $this->searchModel = Yii::createComponent($this->model);
            $this->searchModel->setScenario('search');
            $this->searchModel->unsetAttributes();
        } elseif (in_array($name, $this->searchModel->attributeNames())) {
            $this->searchModel->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }
    
    public function run() {
        /**
        * @var CActiveDataProvider
        */
        $criteria = $this->searchModel->search()->getCriteria();
        
        // apply ordering and limited results to the search
        if ($this->Order)
            $criteria->order = $this->Order;
        if ($this->Limit)
            $criteria->limit = $this->Limit;
        if ($this->Offset)
            $criteria->offset = $this->Offset;
        
        $this->render(array('items' => $this->searchModel->findAll($criteria)));
    }
}
?>