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

class ModuleService extends FServiceBase
{
	/**
    * create/update a module
    *
    * @param mixed $params
    */
    public function update($params)
    {
    	$module = $this->getModel($params['Module'], 'Module');
        $this->result->processed('module', $module);

        if ($module->Id > 0) $module->setIsNewRecord(false);
        if(!$module->save())
        {
            $this->result->addError('module', 'Invalid data or cannot save module into database');
        }
    }
}
?>
