<?php
/**
 * Model of a gallery.
 * 
 * @package api
 * @subpackage public
 */
class MT_Gallery extends MT_Common {
	
	const NAME = 'gallery';
	
	public static function getList($fields = NULL, $filter = NULL, $order = NULL, $limit = NULL, $offset = NULL, $query = NULL) {
		$selectMany = parent::mergeFieldsAndSelectMany($fields, array(
			'id' => parent::getTableName().'.id',
			'category',
			'subcategory',
			'name',
			'path' => parent::getTableName().'.path',
			'fullPath',
			'date' => parent::getTableName().'.date'
		));
				
		$query = ORM::for_table(parent::getTableName())
			->left_outer_join(DB_PREFIX.'photo', [
				parent::getTableName().'.id',
				'=',
				'photo.gallery'
			], 'photo')
			->select_many($selectMany)
			->select_expr('COUNT(photo.id)', 'numPhotos')
			->select_expr('MAX(photo.date)', 'updated')
			->where_equal('photo.show', 1)
			->group_by(parent::getTableName().'.id');
		parent::getList(null, $filter, $order, $limit, $offset, $query);
	}
	
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