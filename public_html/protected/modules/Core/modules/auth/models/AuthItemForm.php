<?php
/**
* Authorization item form class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.5
*/
class AuthItemForm extends CFormModel
{
	public $name;
	public $description;
	public $type;
	public $bizRule;
	public $data;

	/**
	* Declares the validation rules.
	*/
	public function rules()
	{
		return array(
			array('name, description', 'required'),
			array('name', 'nameIsAvailable', 'on'=>'create'),
			array('name', 'newNameIsAvailable', 'on'=>'update'),
			array('name', 'isSuperuser', 'on'=>'update'),
		   	array('bizRule, data', 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	*/
	public function attributeLabels()
	{
		return array(
			'name'			=> Language::t(Yii::app()->language,'Backend.System.AuthItem','Name'),
			'description'	=> Language::t(Yii::app()->language,'Backend.System.AuthItem','Description'),
			'bizRule'		=> Language::t(Yii::app()->language,'Backend.System.AuthItem','Business rule'),
			'data'			=> Language::t(Yii::app()->language,'Backend.System.AuthItem','Data'),
		);
	}

	/**
	* Makes sure that the name is available.
	* This is the 'nameIsAvailable' validator as declared in rules().
	*/
	public function nameIsAvailable($attribute, $params)
	{
		// Make sure that an authorization item with the name does not already exist
		if( Rights::getAuthorizer()->authManager->getAuthItem($this->name)!==null )
			$this->addError('name', Yii::t('AuthModule.core', 'An item with this name already exists.', array(':name'=>$this->name)));
	}

	/**
	* Makes sure that the new name is available if the name been has changed.
	* This is the 'newNameIsAvailable' validator as declared in rules().
	*/
	public function newNameIsAvailable($attribute, $params)
	{
		if( strtolower($_GET['name'])!==strtolower($this->name) )
			$this->nameIsAvailable($attribute, $params);
	}

	/**
	* Makes sure that the superuser roles name is not changed.
	* This is the 'isSuperuser' validator as declared in rules().
	*/
	public function isSuperuser($attribute, $params)
	{
		if( strtolower($_GET['name'])!==strtolower($this->name) && strtolower($_GET['name'])===strtolower(Rights::getConfig('superuserName')) )
			$this->addError('name', Yii::t('AuthModule.core', 'Name of the superuser cannot be changed.'));
	}
}

