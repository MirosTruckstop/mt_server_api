<?php
/**
 * Common model
 * 
 * @package api
 * @subpackage admin
 */
abstract class MT_Admin_Common {

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
	
}