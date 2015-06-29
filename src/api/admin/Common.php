<?php
/**
 * Common model
 * 
 * @package api
 * @subpackage admin
 */
abstract class MT_Admin_Common {

	abstract function action();
	
	/**
	 * 
	 * @param array $body
	 * @return int HTTP status code
	 */
	public static function getList(array $body) {
		try {
			setBody($body);
			return HTTP_STATUS_200_OK;
		} catch (Exception $e) {
			return HTTP_STATUS_400_BAD_REQUEST;
		}
	}
	
	/**
	 * @param string $tableName Table name without prefix
	 * @param string $aggregateFunctionName Aggregate function, e.g. 'MAX', 'AVG'
	 * @param string $fieldName
	 * @retunr string|int
	 */
	protected static function getAggregate($tableName, $aggregateFunctionName, $fieldName) {
		$query = ORM::for_table(DB_PREFIX.$tableName);
		$query->select_expr($aggregateFunctionName.'('.$fieldName.')', 'value');
		try {
			$item = $query->find_one();
			return $item->value;
		} catch (Exception $e) {
			echo $e;
		}		
	}	
	
}