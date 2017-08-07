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
* Class SettingParams, provide metadata for setting parameters of the CMS
* The statis function params() return an array of metadata item, each define
* meta data for a paramter and has this structure as default:
* 
* array(
*   //a CHtml method or path to a custom widget that render HTML input control
*   //for the param.
*   'widget' => 'textField', 
*   'widget' => array('class' => widget_path, 'nameParam' => property_used_as_name, 'valueParam' => property_used_as_value),
* 
*   //array of rules, each in format 'yii_validator' => params_array()
*   //i.e, for pagesize we can define a rule as 'numerical' => array('min' => 5, 'max' =>50)
*   'rules' => array(),
* 
*   //assoc array serve as widget params or parameters for XHtml::xxx()
*   //whereas xxx is define by 'widget' key. If you use CHtml method for
*   //'widget' then note that there are 2 common paramters the method requires
*   //name and value. Name is usually the first param while value can be the 2nd
*   //3rd or 4th,... You must place params in the correct order as defined by 
*   //CHtml::xxx() signature
*   'params' => array('name' => $this->Name, 'value' => $this->Value),
* 
*   //ServiceID of the reader function. This service recieve param's value and
*   //modify it before the param is renderred
*   'reader' => '',
* 
*   //SericeID of the writer function. This service recieve params's value and
*   //modify it before the param is actually saved
*   'wrtier' => ''
* )
* 
* This metadata array works together with the settings table in database. While
* your system is under development, you do not need to create metadata for a param.
* By default, the setting page will display a param without metadata as a textbox.
* Note that without metadata, you loose the ability to define constraints/rules on
* the param's value.
* 
* The same file SettingParams.php should be placed in <module>.models of each module
* which want to provide users ability to customize its features with setting paramters.
* For more information, see Setting model class and SettingsController class. Extending
* these classes with xyzSetting and xyzSettingsController whereas xyz is module's name
* will allow users to customize your module via a GUI.
* 
* IMPORTANT:
* All CMS's setting params are cached in application.utilities.Settings class as 
* constants. 
*/
class SettingParams extends CComponent
{
    public static function params(){
        return array(
            'ADMIN_EMAIL' => array(
                'rules' => array('email' => null)
            ),
            'ARTICLE_LEADING_WORDS' => array(
                'rules' => array('numerical' => array('min' => 30,'max' => 200))
            ),
            'BO_PAGE_SIZE' => array(
                'rules' => array('numerical' => array('min' => 5, 'max' => 50))
            ),
            'BO_THEME' => array(
            ),
            'CATEGORIES_POPUP' => array(
                'rules' => array('numerical' => array('min' => 5, 'max' => 20))
            ),
            'DEFAULT_BO_LAYOUT' => array(
            ),
            'DEFAULT_LAYOUT' => array(
            ),
            'DEFAULT_PAGE_ID' => array(
            ),
            'PAGER_HEADER' => array(
            ),
            'PAGER_NEXT_PAGE_LABEL' => array(
            ),
            'PAGER_PREV_PAGE_LABEL' => array(
            ),
            'PAGE_SIZE' => array(
                'rules' => array('numerical' => array('min' => 5, 'max' => 20))
            ),
            'SITE_COPYRIGHT' => array(
            ),
            'SITE_NAME' => array(
            ),
            'THEME' => array(
            ),
            'URL_EXT' => array(
            ),
            'LOGO_WIDTH' => array(
                'rules' => array('numerical' => array('min' => 20, 'max' => 500))
            ),
            'LOGO_HEIGHT' => array(
                'rules' => array('numerical' => array('min' => 20, 'max' => 500))
            ),
            'MAIL_METHOD' => array(
                'widget' => 'dropDownList',
                'params' => array('name' => 'MAIL_METHOD', 'value' => '', 
                                    'data' => array('smtp' => 'SMTP', 'mail' => 'mail() function', 'sendmail' => 'sendmail')),
                
            ),
            'SMTP_PORT' => array(
                'rules' => array('numerical' => array())
            ),
            'SMTP_PASSWORD' => array(
                'widget' => 'passwordField',
            ),
            'SMTP_SECURE' => array(
                'widget' => 'dropDownList',
                'params' => array('name' => 'SMTP_SECURE', 'value' => '', 
                                    'data' => array('' => 'No secure connection', 'ssl' => 'SSL', 'tls' => 'TLS')),
                
            ),
            'VERSION' => array(
            ),
            'TIMEZONE' => array(
                'widget'=>'dropDownList',
                'params'=>array('name'=>'TIMEZONE', 'value'=>Yii::app()->getTimeZone(),'data'=>Utility::getFriendlyTimezoneList())
            ),
            'DATETIME_FORMAT' => array(
            ),
            'DATE_FORMAT' => array(
            ),
            'TIME_FORMAT' => array(
            ),
        );
    }
}
?>
