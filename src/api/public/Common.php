<?php
/**
 * Common model
 * 
 * @package api
 * @subpackage public
 */
abstract class MT_Common {
	
	/**
	 * All allowed filter types, i.e. the first element of the paramter $filter.
	 * 
	 * @type array
	 */
	const SUPPORTED_FILTER_TYPES = ['=', 'LIKE', '<', '<=', '>', '>='];
	
	/**
	 * 
	 * @return string
	 */
	private static function getTableName() {
		return DB_PREFIX.static::NAME;
	}

	/**
	 * @todo Array for $order supported?
	 * 
	 * @param string|array|null $fields
	 * @param array|null $filter [<filter type>, <field name>, <value>]
	 * @param string|null $order
	 * @param int|null $limit 
	 * @param int|null $offset 
	 * @return int|null HTTP status code
	 */
	public static function getList($fields = NULL, $filter = NULL, $order = NULL, $limit = NULL, $offset = NULL) {
		$query = ORM::for_table(self::getTableName());
		if ($fields) {
			$query->select_many($fields);
		}
		if (!empty($filter)) {
			if (count($filter) == 3 && in_array($filter[0], self::SUPPORTED_FILTER_TYPES)) {
				$query->where_raw('(`'.$filter[1].'` '.$filter[0].' ?)', $filter[2]);				
			} else {
				return HTTP_STATUS_400_BAD_REQUEST;
			}
		}
		if (!empty($order)) {
			$query->order_by_asc($order);
		}
		// Get and set the total number of element before $limit and $offset
		// parameters.
		$queryClone = clone $query;
		setHeader(HEADER_X_TOTAL_COUNT, $queryClone->count());

		if (!empty($limit)) {
			$query->limit($limit);
		}
		if (!empty($offset)) {
			$query->offset($offset);
		}

		try {
			setBody($query->find_array());
			return HTTP_STATUS_200_OK;
		} catch (Exception $e) {
			return HTTP_STATUS_400_BAD_REQUEST;
		}
	}
	
	/**
	 * 
	 * @param string $aggregateFunctionName Aggregate function, e.g. 'MAX', 'AVG'
	 * @param string $fieldName
	 * @retunr string|int
	 */
	protected static function getAggregate($aggregateFunctionName, $fieldName) {
		$query = ORM::for_table(self::getTableName());
		$query->select_expr($aggregateFunctionName.'('.$fieldName.')', 'value');
		try {
			$item = $query->find_one();
			return $item->value;
		} catch (Exception $e) {
			echo $e;
		}		
	}

	/**
	 * 
	 * @param array $data
	 * @return int HTTP status code
	 */
	public static function post(array $data) {
		try {
			$item = ORM::for_table(self::getTableName())->create();
			$item->set($data)->save();
			return HTTP_STATUS_201_CREATED;
		} catch (Exception $e) {
			return HTTP_STATUS_400_BAD_REQUEST;
		}
	}
	
	
	/**
	 * 
	 * @param int $id
	 * @param string|array|null $fields
	 */
	public static function getItem($id, $fields = NULL) {
		$query = ORM::for_table(self::getTableName());
		$query->where_id_is($id);
		if (!empty($fields)) {
			$query->select_many($fields);
		}

		try {
			$item = $query->find_one();
			if ($item) {
				setBody($item->as_array());
				return HTTP_STATUS_200_OK;				
			} else {
				return HTTP_STATUS_204_NO_CONTENT;
			}
		} catch (Exception $e) {
			return HTTP_STATUS_400_BAD_REQUEST;
		}
	}
	
	/**
	 * 
	 * @param int $id
	 * @param array $data 
	 * @return int HTTP status code
	 */
	public static function postItem($id, array $data) {
		try {
			$item = ORM::for_table(self::getTableName())->find_one($id);
			if ($item) {
				$item->set($data)->save();
				return HTTP_STATUS_202_ACCEPTED;
			} else {
				return HTTP_STATUS_204_NO_CONTENT;
			}
		} catch (Exception $e) {
			return HTTP_STATUS_400_BAD_REQUEST;
		}
	}
	
	/**
	 * 
	 * @param int $id
	 * @return string HTTP status code
	 */
	public static function deleteItem($id) {
		$item = ORM::for_table(self::getTableName())->find_one($id);
		if ($item) {
			$item->delete();
			return HTTP_STATUS_202_ACCEPTED;
		}
		return HTTP_STATUS_204_NO_CONTENT;
	}
	
}