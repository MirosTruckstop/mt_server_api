<?php
/**
 * Model of a category.
 * 
 * @package api
 * @subpackage public
 */
class MT_Category extends MT_Common {
	
	const NAME = 'category';
	
	public static function post(array $data) {
		require_once MT_API_PATH.'/model/File.php';
		$data['path'] = MT_Admin_Model_File::nameToPath($data['name']);
		$result = parent::post($data);
		if ($result == HTTP_STATUS_201_CREATED) {
			MT_Admin_Model_File::createDirectory($data['path']);
		}
		return $result;
	}
}