<?php
require 'vendor/slim/slim/Slim/Slim.php';
require 'vendor/j4mie/idiorm/idiorm.php';

/*
 * Sets database connection via ORM::configure 
 */
include 'config.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->hook("slim.before.router", function() use ($app) {
	$path = $app->request()->getPathInfo();
	if (strpos($path, "news") !== false) {
		require 'entities/news.php';
	}	
	else if (strpos($path, "photographers") !== false) {
		require 'entities/photographers.php';
	}
	else if(strpos($path, "article") !== false) {
		require 'entities/article.php';
	}
});

$app->get('/news', function() { get(); });
$app->get('/photographers', function() { get(); });
$app->get('/photographers/:id', function($id) { getItem($id); });

$app->run();