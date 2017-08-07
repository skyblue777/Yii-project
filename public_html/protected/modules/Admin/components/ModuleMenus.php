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
class ModuleMenus extends FWidget
{
    public $moduleMenus=array();
    
    public function run() {
        $criteria = new CDbCriteria();
        $criteria->condition = 'has_back_end = \'y\'';
        $criteria->order = 'ordering';
        
        $backEndModules = Module::model()->findAll($criteria);
        $moduleMenus = array();
        foreach ($backEndModules as $key => $beModule) {
            $module = Yii::app()->getModule($beModule->name);
            if (! method_exists($module, 'getMenus')) continue;
            $menus = $module->getMenus();
            if (count($menus))
            {
                foreach ($menus as $menu)
                {
                    if (isset($menu['items']) && is_array($menu['items']) && count($menu['items']))
                    {
                        $title = $beModule->friendly_name;
                        $icon = $beModule->icon;
                        if (isset($menu['title']))
                            $title = $menu['title'];
                        if (isset($menu['icon']))
                            $icon = $menu['icon'];
                        $this->addMenu($title, $menu['items'], $icon);
                    }
                }
            }
        }            
        $this->widget('zii.widgets.CMenu', array(
            'items' => $this->moduleMenus,
            'encodeLabel'=>false
        ));
    }
    
    public function addMenu($title, $items, $icon='')
    {
    	$title=Language::t(Yii::app()->language,'Backend.Common.Menu',$title);
        //Fix submenu label to separate title from description
        foreach ($items as &$menu) {
            $tmp = explode('|',$menu['label']);
            if (count($tmp) < 2) $tmp[]='';
            list($name, $desc) = $tmp;
            $menu['label'] = '<span class="sub-menu-text">
                                  <strong>'.$name.'</strong>
                                  <span>'.$desc.'</span>
                              </span>';
        }
        //If the menu doesn't have icon, use default one
        if ($icon == '')
            $icon = Yii::app()->core->AssetUrl.'/images/module_menu_icon.png';
        
        $this->moduleMenus[] = array(
            'label' => '<img src="'.$icon.'" width="16" height="16" border="0" />
                        <span class="menu-text">'.$title.'</span>
                        <span class="arrow"></span>',
            'url' => '#',
            'items' => $items,
            'linkOptions'=> array(
                'class'=>'top-menu-item'
            ),
        );
    }
}
?>