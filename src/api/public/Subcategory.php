<?php
/**
 * Model of a subcategory.
 * 
 * @package api
 * @subpackage public
 */
class MT_Subcategory extends MT_Common {
	
	const NAME = 'subcategory';
	
	public static function post(array $data) {
		require_once MT_API_PATH.'/model/File.php';
		$data['path'] = MT_Admin_Model_File::nameToPath($data['name']);
		
		$categoryPath = parent::getAttribute('category', $data['category'], 'path');
		if ($categoryPath) {
			$result = parent::post($data);
			if ($result == HTTP_STATUS_201_CREATED) {
				MT_Admin_Model_File::createDirectory($categoryPath.'/'.$data['path']);
			}
			return $result;
		}
		return HTTP_STATUS_400_BAD_REQUEST;
	}
}