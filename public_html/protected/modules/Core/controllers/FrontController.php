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
  
 class FrontController extends FController
 {
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    public $keyWordsMetaTagContent = '';
    public $descriptionMetaTagContent = '';
    public $adTitle = '';
    
    public function init(){
        parent::init();
        //Set front theme
        Yii::app()->theme = Settings::THEME;
        Yii::app()->layout = Settings::DEFAULT_LAYOUT;
        //set meta tags
        $this->descriptionMetaTagContent = Settings::DEFAULT_META_DESCRIPTION;
        //use CPradoViewRenderer
        if (Yii::app()->core->FrontendRenderer){
            $rendererClass = Yii::app()->core->FrontendRenderer;
            Yii::app()->setComponent('viewRenderer', new $rendererClass);
        }
    }
    
    /*public function filters() {
        return array(
            'accessControl',
        );
    }*/
 }
?>