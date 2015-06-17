<?php

class MT_Photographer {
	
	static $table = 'wp_mt_photographer';
			
	function getList() {
		$news = ORM::for_table(self::$table)
				->select_many('id', 'name')
				->find_many();
		if (!empty($news)) {
			$news_arr = Array();
			foreach ($news as $item) {
				array_push($news_arr, $item->as_array());
			}
		} //else {
	//		$result_arr = array('status' => 'error', 'msg' => 'no news found');
	//	}
		echo json_encode($news_arr);
	}
	
	function post() {
		
	}

	function getItem($id) {
		$news = ORM::for_table(self::$table)->where_id_is($id)->find_one();		
		echo json_encode($news->as_array());
	}
	
	function deleteItem() {
		
	}
}