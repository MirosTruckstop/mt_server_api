<?php

function get() {
	$news = ORM::for_table('wp_mt_photographer')
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

function getItem($id) {
	$news = ORM::for_table('wp_mt_photographer')->where_id_is($id)->find_one();		
	echo json_encode($news->as_array());
}