<?php
define('MT_API_PATH', 'src/api');

require 'vendor/autoload.php';
//require 'vendor/slim/slim/Slim/Slim.php';
//require 'vendor/j4mie/idiorm/idiorm.php';

/*
 * Sets database connection via ORM::configure 
 */
include 'config.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true
));

$app->hook("slim.before.router", function() use ($app) {
	$path = $app->request()->getPathInfo();
	if (strpos($path, "news") !== false) {
		require MT_API_PATH.'/news.php';
	}	
	else if (strpos($path, "photographers") !== false) {
		require MT_API_PATH.'/photographers.php';
	}
});

$app->group('/api', function () use ($app) {
	$app->get('/news/', function() { get(); });
	$app->get('/photographers/', function() { MT_Photographer::getList(); });
	$app->post('/photographers/', function() { MT_Photographer::post(); });	
	$app->get('/photographers/:id', function($id) { getItem($id); });
	$app->delete('/photographers/:id', function($id) { deleteItem($id); });
	
});

$app->run();