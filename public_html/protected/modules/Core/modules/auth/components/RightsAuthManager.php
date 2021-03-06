<?php
class RightsAuthManager extends CDbAuthManager
{
	public $itemWeightTable = 'AuthItemWeight';

	/**
	* Returns the authorization items of the specific type and user.
	* @param integer the item type (0: operation, 1: task, 2: role). Defaults to null,
	* meaning returning all items regardless of their type.
	* @param mixed the user ID. Defaults to null, meaning returning all items even if
	* they are not assigned to a user.
	* @param boolean whether to sort the results according to item weights.
	* Sort is not supported when type is provided.
	* @return array the authorization items of the specific type.
	*/
	public function getAuthItems($type=null, $userId=null, $sort=false)
	{
		if( $sort===true )
		{
			if( $type===null && $userId===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->itemWeightTable} t2 ON name=itemname
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
			}
			else if( $userId===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->itemWeightTable} t2 ON name=itemname
					WHERE t1.type=:type
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
			}
			else if( $type===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->itemWeightTable} t3 ON name=t3.itemname
					WHERE userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':userid', $userId);
			}
			else
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1,
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->itemWeightTable} t3 ON name=t3.itemname
					WHERE t1.type=:type AND userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
				$command->bindValue(':userid', $userId);
			}

			$items = array();
			foreach($command->queryAll() as $row)
				$items[ $row['name'] ] = new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], unserialize($row['data']));
		}
		else
		{
			$items = parent::getAuthItems($type, $userId);
		}

		return $items;
	}

	/**
	* Returns the specified authorization items sorted by weights.
	* @param array the names of the authorization items to get.
	* @return array the authorization items.
	*/
	public function getSortedAuthItems($names)
	{
		$items = array();

		if( $names!==array() )
		{
			$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
				FROM {$this->itemTable} t1
				LEFT JOIN {$this->itemWeightTable} t2 ON name=itemname
				WHERE name IN ('".implode("','",$names)."')
				ORDER BY t1.type DESC, weight ASC";
			$command=$this->db->createCommand($sql);

			foreach($command->queryAll() as $row)
				$items[ $row['name'] ]=new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], unserialize($row['data']));
		}

		return $items;
	}

	/**
	* Updates the authorization item weights.
	* @param array the result returned from jui-sortable.
	*/
	public function updateItemWeights($result)
	{
		foreach( $result as $weight=>$itemname )
		{
			// Check if the item already has a weight
			$sql = "SELECT COUNT(*) FROM {$this->itemWeightTable}
				WHERE itemname=:itemname";
			$command = $this->db->createCommand($sql);
			$command->bindValue(':itemname', $itemname);

			if( $command->queryScalar()>0 )
			{
				$sql = "UPDATE {$this->itemWeightTable}
					SET weight=:weight
					WHERE itemname=:itemname";
				$command = $this->db->createCommand($sql);
				$command->bindValue(':weight', $weight);
				$command->bindValue(':itemname', $itemname);
				$command->execute();
			}
			// Item does not have a weight, insert it
			else
			{
				if( ($item = $this->getAuthItem($itemname))!==null )
				{
					$sql = "INSERT INTO {$this->itemWeightTable} (itemname, type, weight)
						VALUES (:itemname, :type, :weight)";
					$command = $this->db->createCommand($sql);
					$command->bindValue(':itemname', $itemname);
					$command->bindValue(':type', $item->getType());
					$command->bindValue(':weight', $weight);
					$command->execute();
				}
			}
		}
	}
}
