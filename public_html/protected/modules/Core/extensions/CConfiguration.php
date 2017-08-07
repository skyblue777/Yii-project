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


class CConfiguration extends CApplicationComponent {
    public $configPath;
    public $cacheID='cache';
    
    public function init()
    {
        parent::init();
        if($this->configPath===null)
            $this->configPath=Yii::app()->getRuntimePath();
        @mkdir($this->configPath,0777,true);
        if(!is_dir($this->configPath) || !is_writable($this->configPath))
            throw new CException(Yii::t('yii','Unable to create application state file "{file}". Make sure the directory containing the file exists and is writable by the Web server process.',
                array('{file}'=>$this->configPath)));
        
        //overide config
        $this->applyModuleConfig();
    }
    
    /**
    * save config to file
    * 
    * @param string $id
    * @param array $config
    * 
    * @return boolean
    */
    public function saveConfig($id, $config) {
        $configFile = $this->getFilename($id);
        /*if (file_exists($configFile) === true)
            throw new CException("File $configFile exists.");*/
        return @file_put_contents($configFile, serialize($config),LOCK_EX);
    }
    
    /**
    * generate file name
    * 
    * @param string $id
    * 
    * @return string path config file
    */
    protected function getFilename($id) {
        $id = preg_replace('/[^a-zA-Z0-9_\-]/i', '_', $id);
        $filename = md5('CConfiguration#'.$id);
        return $this->configPath.DIRECTORY_SEPARATOR.$filename;
    }
    
    public function hasConfigFile($id) {
        $configFile = $this->getFilename($id);
        if (file_exists($configFile)) return true;
        return false;
    }
    
    public function removeConfigFile($id) {
        $configFile = $this->getFilename($id);
        if (file_exists($configFile)) {
            @unlink($configFile);
        }
    }
    
    /**
    * load config
    * 
    * @param string $id
    * @return mixed
    */
    public function loadConfig($id) {
        $configFile = $this->getFilename($id);
        if (!file_exists($configFile)) {
            Yii::log('cache file of module\'s config not created.', CLogger::LEVEL_WARNING, 'application.extensions.core.Configuration');
            return array();
        }
        if($this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
        {
            $cacheKey='Configuration.'.$configFile;
            if(($value=$cache->get($cacheKey))!==false)
                return unserialize($value);
            else if(($content=@file_get_contents($configFile))!==false)
            {
                $cache->set($cacheKey,$content,0,new CFileCacheDependency($configFile));
                return unserialize($content);
            }
            else
                return null;
        }
        else if(($content=@file_get_contents($configFile))!==false)
            return unserialize($content);
        else
            return null;
    }
    
    public function applyModuleConfig() {
        $config = $this->loadConfig('modules');
        Yii::app()->configure($config);
    }
    
    public function applyParams() {
        $config = $this->loadConfig('params');
        Yii::app()->configure($config);
    }
}