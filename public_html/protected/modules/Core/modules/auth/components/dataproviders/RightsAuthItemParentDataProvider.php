<?php
/**
* Rights authorization item parent data provider class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9.10
*/
class RightsAuthItemParentDataProvider extends RightsAuthItemDataProvider
{
	/**
	* Constructs the data provider.
	* @param array configuration (name=>value) to be applied as the initial property values of this class.
	* @return RightsAuthItemDataProvider
	*/
	public function __construct($owner, $config=array())
	{
		$this->owner = $owner;
		$this->setId($owner->name);

		foreach($config as $key=>$value)
			$this->$key=$value;
	}

	/**
	* Fetches the data from the persistent data storage.
	* @return array list of data items
	*/
	public function fetchData()
	{
		$this->items = Rights::getAuthorizer()->getAuthItemParents($this->owner->name, null, true);
		return parent::fetchData();
	}
}
