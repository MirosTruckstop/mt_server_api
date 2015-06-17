<?php
define('MT_API_PATH', 'src/api');

require 'vendor/autoload.php';
require 'src/api/Common.php';
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
	/*if (strpos($path, "news") !== false) {
		require MT_API_PATH.'/news.php';
	}	
	else*/ if (strpos($path, "photographers") !== false) {
		require MT_API_PATH.'/Photographers.php';
	}
});

const PARAM_FIELDS = 'fields';
const PARAM_ORDER = 'order';

// 'api' group
$app->group('/api', function () use ($app) {	
	//$app->get('/news/', function() { get(); });
	$app->get('/photographers/', function() use ($app) {
		$fields = $app->request->get(PARAM_FIELDS);
		if (!empty($fields)) {
			$fields = json_decode($fields);
		}
		$order = $app->request->get(PARAM_ORDER);
		if (!empty($order)) {
			$order = json_decode($order);
		}		
		
		$app->response->setStatus(MT_Photographer::getList($fields, $order));
	});
	$app->post('/photographers/', function() use ($app) {MT_Photographer::post(); });	
	$app->get('/photographers/:id', function($id) use ($app) {
		$fields = $app->request->get(PARAM_FIELDS);
		if (!empty($fields)) {
			$fields = json_decode($fields);
		}		
		$app->response->setStatus(MT_Photographer::getItem($id, $fields));
	});
	$app->post('/photographers/:id', function($id) use ($app) {
		$data = $app->request->getBody();
		if (!empty($data)) {
			$data = json_decode($data, TRUE);
		}
		$app->response->setStatus(MT_Photographer::postItem($id, $data));
	});	
	$app->delete('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::deleteItem($id));
	});
	
});

$app->run();