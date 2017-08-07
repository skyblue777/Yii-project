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


class SettingsService extends FServiceBase
{    
    /**
    * Rebuild all module cache files and page and categogy caches
    */
    public function rebuildCache($params) {
        //Cms::service('Cms/Page/cache', array());
        //FSM::run('Core.Category.cache', array());
        
        foreach(Yii::app()->Modules as $id => $params)
            $this->db2php(array('module' => $id));
        
        return $this->result;
    }
    
    /**
    * Rebuild system cache or cache of a module
    * 
    * @param array $params
    *   - Module string Module name or system (Cms) cache if empty
    * @return ServiceResult
    */
    public function db2php($params){
        $module = $this->getParam($params, 'module', '');
        //For Cms module, save settings as system's settings
        if ($module == 'Core') $module = '';
        //Get module's params in DB
        Yii::import('Core.models.Setting', true);
        $criteria = new CDbCriteria();
        $criteria->addCondition("module = '{$module}'");
        $criteria->order = 'name';
        $params = Setting::model()->findAll($criteria);
        
        $consts = '';
        foreach($params as $param){
            if (!is_numeric($param->value)) $param->value = "'".addslashes($param->value)."'";
            $consts .= "\t//{$param->description}\n\tconst ".str_pad($param->name,48,' ')."= {$param->value};\n";
        }
        $php = 
"<?php
/**
* DONOT modify this file as it's automatically generated based on setting parameters.
**/
class {$module}Settings{
{$consts}
}
?>";
        $filename = $module.'Settings.php';
        $path = yii::app()->getBasePath()."/runtime/cache/";
        
        if (!is_dir($path)){
            $this->result->fail(ServiceResult::ERR_SERVICE_SPECIFIC,'Folder utilities under the module/application folder does not exist.');
            return $this->result;
        }
        
        $f = fopen($path.$filename, 'w+');
        if ($f == false){
            $this->result->fail(ServiceResult::ERR_SERVICE_SPECIFIC,'Cannot create file under utilities folder.');
            return $this->result;
        }
        fwrite($f,$php);
        fclose($f);
        
        return $this->result;
    }
    
    /**
    * Reorder parameters
    * 
    * @param int $prevId: previous item's id
    * @param int $curId: current item's id
    * @param int $nextId: next item's id
    */
    public function reorder($params)
    {
        $tmp = $this->getParam($params, 'curId', null);
        
        $tmp = json_decode($tmp);
        
        if (! empty($tmp))
        {
            $module = $tmp->Module; // We got module name here
            $groupName = $tmp->GroupName; // We got group name here
            $curName = $tmp->Name;
        }
        else
        {
            throw new CHttpException(404, 'Invalid request');
        }
        
        $tmp = $this->getParam($params, 'prevId', null);
        $tmp = json_decode($tmp);
        if (! empty($tmp)){
            $prevName = $tmp->Name;
        }
        else
        {
            $prevName = '';
        }
        
        $tmp = $this->getParam($params, 'nextId', null);
        $tmp = json_decode($tmp);
        if (! empty($tmp)){
            $nextName = $tmp->name;
        }
        else
        {
            $nextName = '';
        }
        
        
        $prevParam = Setting::model()->findByPk(array('name'=>$prevName, 'module'=>$module));
        $curParam = Setting::model()->findByPk(array('name'=>$curName, 'module'=>$module));
        $nextParam = Setting::model()->findByPk(array('name'=>$nextName, 'module'=>$module));
        
        // Move an item to top
        if ($prevName == '')
        {
            // All items < current item move up
            Setting::model()->updateCounters(array('ordering'=>1), "module=:Module AND setting_group=:GroupName AND ordering<:CurOrdering", array(":Module" => $module, ':GroupName' => $groupName, ':CurOrdering' => $curParam->Ordering));
            
            // Current page's ordering = 0
            $curParam->ordering = 0;
            $curParam->save();
        }
        
        // Move an item to bottom
        elseif ($nextName == '')
        {
            // All item > current item move down
            Setting::model()->updateCounters(array('ordering'=>-1), "module=:Module AND setting_group=:GroupName AND ordering>:CurOrdering", array(":Module" => $module, ':GroupName' => $groupName, ':CurOrdering' => $curParam->Ordering));
            
            // Current item ordering = prev ordering
            $curParam->ordering = $prevParam->ordering;
            $curParam->save();
        }
        
        else
        {
            // Move up
            if ($curParam->Ordering > $nextParam->Ordering)
            {
                Setting::model()->updateCounters(array('ordering'=>1), "module=:Module AND setting_group=:GroupName AND ordering>:Begin AND ordering<:End", array(":Module" => $module, ':GroupName' => $groupName, ':Begin' => $prevParam->ordering, ':End' => $curParam->ordering));
                $curParam->ordering = $nextParam->ordering;
                $curParam->save();
            }
            else
            {
                // Move down
                Setting::model()->updateCounters(array('ordering'=>-1), "module=:Module AND setting_group=:GroupName AND ordering>:Begin AND ordering<:End", array(":Module" => $module, ':GroupName' => $groupName, ':Begin' => $curParam->ordering, ':End' => $nextParam->ordering));
                $curParam->ordering = $prevParam->ordering;
                $curParam->save();
            }
            
        }
        
        return $this->result;
    }
    
    /**
    * Delete a parameter by id
    * 
    * @param int $paramId: Parameter id
    */
    public function delete($params)
    {
        $tmp = $this->getParam($params, 'paramId', null);
        $tmp = json_decode($tmp);
        
        if (! empty($tmp))
        {
            $module = $tmp->module; // We got module name here
            $groupName = $tmp->setting_group; // We got group name here
            $name = $tmp->name;
        }
        else
        {
            $this->result->addError('parameter', Yii::t('Page', 'PARAMETER_INVALID_INPUT'));
            return $this->result;
        }
        
        $param = Setting::model()->findByPk(array('name'=>$name, 'module'=>$module));
        if (is_null($param))
        {
            $this->result->addError('parameter', Yii::t('Page', 'PARAMETER_INVALID_INPUT'));
        }
        
        if (! $param->delete())
        {
            $this->result->addError('parameter', $this->normalizeModelErrors($param->Errors));
        }
        else
        {
            // Move up params
            Setting::model()->updateCounters(array('ordering'=>-1), "module=:Module AND setting_group=:GroupName AND ordering>:Begin", array(":Module" => $module, ':GroupName' => $groupName, ':Begin' => $param->ordering));
            $this->db2php(array());
        }
        return $this->result;
    }
    
    /**
    * create a new page
    * 
    * @param mixed $params
    */
    public function create($params)
    {
        $param = $this->getModel($params['Setting'], 'Setting');
        
        if ($param->module == 'System'){
            $param->module = '';
        }
        
        // Get max ordering in a group
        $sql = "
                SELECT MAX(ordering)
                FROM setting
                WHERE module=:Module AND setting_group=:GroupName
                ";
        $con = Yii::app()->db;
        $command = $con->createCommand($sql);
        $maxOrdering = $command->queryScalar(array(':Module'=>$param->module, ':GroupName'=>$param->setting_group));
        $param->ordering = $maxOrdering+1;
        
        $temp = Setting::model()->findByPk(array('name'=>$param->Name, 'module'=>$param->module));
        if (! is_null($temp))
        {
            $this->result->addError('parameter', Yii::t('Parameter', 'PARAMETER_EXISTS'));
            return $this->result;
        }
        
        if ($this->result->hasErrors())
        {
            return $this->result;
        }
        
        if (! $param->save())
        {
            $this->result->addError('parameter', $this->normalizeModelErrors($param->Errors));
            //$this->result->fail(ServiceResult::ERR_INVALID_DATA, $this->normalizeModelErrors($param->Errors));
        }
        else
        {
            $this->db2php(array());
        }
        
        return $this->result;
    }
    
    public function update($params)
    {
        $param = $this->getModel($params['Setting'], 'Setting');
        
        if ($param->module == 'System'){
            $param->module = '';
        }
        
        // Get old Name & Module
        $oldName = $this->getParam($params, 'OldName');
        $oldModule = $this->getParam($params, 'OldModule');
        if ($oldModule == 'System') $oldModule = '';
        
        if ($oldName == $param->name && $oldModule == $param->module)
        {
            // User didn't change primary key -> update as usual
            foreach($param->attributes as $key=>$attr)
            {
                if ($key !== 'name' && $key !== 'module')
                {
                    $updatedFields[$key] = $param->$key;
                }
            }
            $result = Setting::model()->updateByPk(array('name' => $oldName, 'module' => $oldModule), $updatedFields);
            if (! $result)
            {
                $this->result->addError('parameter', $param->getErrors());
            }
            else
            {
                $this->db2php(array());
            }
        }
        else // User has changed primary key (Name or Module)
        {
            // Check if this new primary key exists or not
            $temp = Setting::model()->findByPk(array('name'=>$param->name, 'module'=>$param->module));
            
            if (! empty($temp))
            {
                $this->result->addError('parameter', Yii::t('Parameter', 'PARAMETER_EXISTS'));
                return $this->result;
            }
            // else
            // Delete old parameter
            Setting::model()->deleteByPk(array('name' => $oldName, 'module' => $oldModule));
            $param->ordering = $this->getParam($params, 'OldOrdering');
            if (! $param->save())
            {
                $this->result->addError('parameter', $this->normalizeModelErrors($param->Errors));
            }
            else
            {
                $this->db2php(array());
            }
        }
        
        return $this->result;
    }
    
    /**
    * Set application locale. The locale information will be saved into browser's cookies
    * 
    * @param array $params
    *   - string locale Yii's locale ID format (langeId_regioanId, i.e. en_us)
    */
    public function setLocale($params){
        $locale = $this->getParam($params, 'locale', 'en_us');
        //Set cookies
        $localeCookie = new CHttpCookie('Locale', $locale);
        $localeCookie->expire = time() + 15*24*3600;
        Yii::app()->request->cookies['Locale'] = $localeCookie;

        //Set application langauge
        Yii::app()->setLanguage($locale);
        return $this->result;
    }
}
?>
