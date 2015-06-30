<?php
/**
 * Model of a gallery.
 * 
 * @package api
 * @subpackage public
 */
class MT_Gallery extends MT_Common {
	
	const NAME = 'gallery';
	
	public static function post(array $data) {
		require_once MT_API_PATH.'/model/File.php';

		$subcategoryPath = '';
		
		// If a subcategory is given
		if(!empty($data['subcategory'])) {
			$subcategoryCategory = parent::getAttribute('subcategory', $data['subcategory'], 'category');
			$subcategoryPath = parent::getAttribute('subcategory', $data['subcategory'], 'path').'/';
			
			// Check if the given subcategory and category ID fit
			if ($data['category'] != $subcategoryCategory) {
				setBodyErrorMessage('Kategorie mit Pfad "'.$subcategoryPath.'" ist keine Unterkategorie von Kategorie mit ID "'.$data['category'].'"');
				return HTTP_STATUS_400_BAD_REQUEST;
			}
		}		
		$data['date'] = time();
		$data['path'] = MT_Admin_Model_File::nameToPath($data['name']);
		$data['fullPath'] = parent::getAttribute('category', $data['category'], 'path').'/'.$subcategoryPath.$data['path'].'/';		
		
		$result = parent::post($data);
		if ($result == HTTP_STATUS_201_CREATED) {
			MT_Admin_Model_File::createDirectory($data['fullPath']);
		}
		return $result;
	}
}