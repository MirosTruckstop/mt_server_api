<?php
/**
 * Model of a photographer.
 * 
 * @package public
 * @subpackage model
 */
class MT_Photographer extends MT_Common {
	
	const NAME = 'photographer';
	
	public static function post($data) {
		$data['date'] = time();
		return parent::post($data);
	}


	
	/**
	 * TODO move to common
	 */
/*	public static function deleteItem($id) {
		// Only delete photographers with no photo
		if (MT_Photo::getNumPhotos($this->id) == 0) {
			return parent::deleteItem($id);
		}
		return FALSE;	
	}*/
}