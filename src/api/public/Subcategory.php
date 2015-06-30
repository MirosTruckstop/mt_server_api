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
		
		// TODO: may get attribute function?
		$category = ORM::for_table(DB_PREFIX.'category')
				->select('path')
				->where_id_is($data['category'])
				->find_one();
		if ($category) {
			$result = parent::post($data);
			if ($result == HTTP_STATUS_201_CREATED) {
				MT_Admin_Model_File::createDirectory($category->path.'/'.$data['path']);
			}
			return $result;
		}
		return HTTP_STATUS_400_BAD_REQUEST;
	}
}