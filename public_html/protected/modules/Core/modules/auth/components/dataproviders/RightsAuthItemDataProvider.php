<?php
/**
* Rights authorization item data provider class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9.10
*/
class RightsAuthItemDataProvider extends CDataProvider
{
	/**
	* @var string the AuthItemModel class name. The {@link getData()} method
	* will return a list of objects of this class.
	*/
	public $modelClass = 'AuthItemModel';

	public $type;
	public $userId;
	public $owner;
	public $items;
	public $sortable;

	/**
	* Constructs the data provider.
	* @param string the data provider identifier.
	* @param integer the item type(s). (0: operation, 1: task, 2: role)
	* @param array configuration (name=>value) to be applied as the initial property values of this class.
	* @return RightsAuthItemDataProvider
	*/
	public function __construct($id, $type, $config=array())
	{
		$this->type = $type;
		$this->setId($this->modelClass);

		foreach($config as $key=>$value)
			$this->$key=$value;
	}

	/**
	* Fetches the data from the persistent data storage.
	* @return array list of data items
	*/
	public function fetchData()
	{
		if( $this->sortable!==null )
			$this->processSortable();

		if( $this->items===null )
			$this->items = Rights::getAuthorizer()->getAuthItems($this->type, $this->userId, $this->owner, true);

		$data = array();
		foreach( $this->items as $name=>$item )
		{
			$model = new $this->modelClass;
			$model->name = $item->name;
			$model->description = $item->description;
			$model->type = $item->type;
			$model->bizRule = $item->bizRule;
			$model->data = $item->data;
			$model->userId = $this->userId;
			$model->owner = $this->owner;
			$model->childCount = $this->countChildren($item->name);
			$data[] = $model;
		}

		return $data;
	}

	/**
	* Fetches the data item keys from the persistent data storage.
	* @return array list of data item keys.
	*/
	public function fetchKeys()
	{
		$keys = array();
		foreach( $this->getData() as $name=>$item )
			$keys[] = $name;

		return $keys;
	}
    

	/**
	* Applies jQuery UI sortable on the target element.
	*/
	protected function processSortable()
	{
		if( $this->sortable!==null )
		{
			if( isset($this->sortable['id'])===true && isset($this->sortable['element'])===true && isset($this->sortable['url'])===true )
			{
				// Register the script to bind the sortable plugin to the role table
				Yii::app()->getClientScript()->registerScript($this->sortable['id'],
					"jQuery('".$this->sortable['element']."').rightsSortableTable({
						url:'".$this->sortable['url']."'
					});"
				);
			}
		}
	}

	/**
	* Calculates the total number of data items.
	* @return integer the total number of data items.
	*/
	protected function calculateTotalItemCount()
	{
		return count($this->getData());
	}
    
    /**
    * Count number of child roles of a given role
    * @param mixed $roleName
    */
    protected function countChildren($roleName) {
        $sql = "SELECT count(child) 
               FROM ".Yii::app()->authManager->itemTable.", ".Yii::app()->authManager->itemChildTable."
               WHERE parent='{$roleName}' AND child=name AND type=".FAuthManager::ROLE_ITEM_TYPE;
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }
}
