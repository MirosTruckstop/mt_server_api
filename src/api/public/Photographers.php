<?php
/**
 * Model of a photographer.
 * 
 * @package api
 * @subpackage public
 */
class MT_Photographer extends MT_Common {
	
	const NAME = 'photographer';
	
	public static function post(array $data) {
		$data['date'] = time();
		return parent::post($data);
	}

	public static function getList($fields = NULL, $filter = NULL, $order = NULL, $limit = NULL, $offset = NULL, $query = NULL) {
		$selectMany = parent::mergeFieldsAndSelectMany($fields, array(
			'id' => parent::getTableName().'.id',
			'name' => parent::getTableName().'.name',
			'date' => parent::getTableName().'.date',
		));
		
		$query = ORM::for_table(parent::getTableName())
			// TODO: get all photographers, also without photos
/*			->raw_join('LEFT JOIN '.DB_PREFIX.'photo', array(
				'photo.photographer',
				'=',
				parent::getTableName().'.id',
			), 'photo')*/
			->left_outer_join(DB_PREFIX.'photo', array(
				parent::getTableName().'.id',
				'=',
				'photo.photographer'
			), 'photo')
			->select_many($selectMany)
			->select_expr('COUNT(photo.path)', 'numPhotos')
			->where_equal('photo.show', 1)
			->group_by(parent::getTableName().'.id');
		return parent::getList(null, $filter, $order, $limit, $offset, $query);
	} 
	
	public static function deleteItem($id, $delete = NULL) {
		$numPhotos = ORM::for_table(DB_PREFIX.'photo')
			->where_equal(self::NAME, $id)
			->count();
		return parent::deleteItem($id, $numPhotos == 0);
	}
}