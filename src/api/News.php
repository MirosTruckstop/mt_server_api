<?php
/**
 * Model of a news.
 * 
 * @package public
 * @subpackage model
 */
class MT_News extends MT_Common {
	
	const NAME = 'news';
	
	public static function post($data) {
		$data['date'] = time();
		return parent::post($data);
	}
}