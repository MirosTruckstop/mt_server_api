<?php
/**
 * Common model
 * 
 * @package public
 * @subpackage model
 */
abstract class MT_Common {

	const DB_PREFIX = 'wp_mt_';
	
	const STATUS_200_OK = 200;
	const STATUS_201_CREATED = 201;
	const STATUS_202_ACCEPTED = 202;
	const STATUS_204_NO_CONTENT = 204;
	
	const STATUS_400_BAD_REQUEST = 400;
	const STATUS_404_NOT_FOUND = 404;
	
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
		return self::DB_PREFIX.static::NAME;
	}

	/**
	 * @todo Array for $order supported?
	 * 
	 * @param string|array $fields
	 * @param array $filter [<filter type>, <field name>, <value>]
	 * @param string $order
	 * @param int $limit 
	 * @param int $offset 
	 * @return int HTTP status code
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
				return self::STATUS_400_BAD_REQUEST;
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
			return self::STATUS_200_OK;
		} catch (Exception $e) {
			return self::STATUS_400_BAD_REQUEST;
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
			return self::STATUS_201_CREATED;
		} catch (Exception $e) {
			return self::STATUS_400_BAD_REQUEST;
		}
	}
	
	
	/**
	 * 
	 * @param int $id
	 * @param string|array $fields
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
				return self::STATUS_200_OK;				
			} else {
				return self::STATUS_204_NO_CONTENT;
			}
		} catch (Exception $e) {
			return self::STATUS_400_BAD_REQUEST;
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
				return self::STATUS_202_ACCEPTED;
			} else {
				return self::STATUS_204_NO_CONTENT;
			}
		} catch (Exception $e) {
			return self::STATUS_400_BAD_REQUEST;
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
			return self::STATUS_202_ACCEPTED;
		}
		return self::STATUS_204_NO_CONTENT;
	}
	
}