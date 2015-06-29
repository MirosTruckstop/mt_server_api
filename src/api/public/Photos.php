<?php
/**
 * Model of a photo.
 * 
 * @package api
 * @subpackage public
 */
class MT_Photo extends MT_Common {
	
	const NAME = 'photo';
	
	public static function getList($fields = NULL, $filter = NULL, $order = NULL, $limit = NULL, $offset = NULL, $query = NULL) {
		$query = ORM::for_table(DB_PREFIX.'photo')
			->inner_join(DB_PREFIX.'photographer', array(
				DB_PREFIX.'photographer.id',
				'=',
				DB_PREFIX.'photo.photographer'
			))
			->select_many(array(
				'id' => DB_PREFIX.'photo.id',
				'description',
				'path',
				'date' => DB_PREFIX.'photo.date',
				'photographerId' => DB_PREFIX.'photographer.id',					
				'photographerName' => DB_PREFIX.'photographer.name'
			))
			->where_equal('show', 1);
		return parent::getList($fields, $filter, $order, $limit, $offset, $query);
	}
}