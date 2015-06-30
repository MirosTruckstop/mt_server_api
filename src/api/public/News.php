<?php
/**
 * Model of a news.
 * 
 * @package api
 * @subpackage public
 */
class MT_News extends MT_Common {
	
	const NAME = 'news';
	
	public static function post(array $data) {
		$data['date'] = time();
		return parent::post($data);
	}
	
	public static function deleteItem($id, $delete) {
		return parent::deleteItem($id, TRUE);
	}	
}