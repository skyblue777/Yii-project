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


class FWidget extends CWidget
{
    protected $isAjaxRequest = FALSE;
    
    public function __construct($owner=null) {
        parent::__construct($owner);
        /**
        * Check if the ServiceController is creating the widget to support AJAX widget
        * as the $owner could be a controller or a widget, we can not base on it
        */
        $controller = Yii::app()->controller;
        if ($controller instanceof ServiceController && $controller->action->Id == 'widget')
            $this->isAjaxRequest = TRUE;
    }
    public function __set($name, $value) {
        try {
          parent::__set($name, $value);
        } catch(Exception $ex) {
            if (! $this->isAjaxRequest)
                throw $ex;
            else {
            
            }
        }
    }
}