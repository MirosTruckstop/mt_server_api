<?php

function get() {
	$news = ORM::for_table('wp_mt_news')
			->find_many();
	if (!empty($news)) {
		$news_arr = Array();
		foreach ($news as $item) {
			array_push($news_arr, $item->as_array());
		}
	}
	echo json_encode($news_arr);
}