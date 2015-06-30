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
		$query = ORM::for_table(parent::getTableName())
			->inner_join(DB_PREFIX.'photographer', array(
				'photographer.id',
				'=',
				parent::getTableName().'.photographer'
			), 'photographer')
			->select_many(array(
				'id' => parent::getTableName().'.id',
				'description',
				'path',
				'date' => parent::getTableName().'.date',
				'photographerId' => 'photographer.id',					
				'photographerName' => 'photographer.name'
			))
			->where_equal('show', 1);
		return parent::getList($fields, $filter, $order, $limit, $offset, $query);
	}
}