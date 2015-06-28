<?php
define('MT_API_PATH', 'src/api');

require 'vendor/autoload.php';

include 'src/config/settings.php';
require 'src/common/functions.php';
require 'src/config/WordPressAuthenticator.php';


ORM::configure($mt_db_configure);

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true
));

$app->add(new \Slim\Middleware\HttpBasicAuthentication([
	"secure" => false,
	"authenticator" => new WordPressAuthenticator()
]));

$app->hook("slim.before.router", function() use ($app) {
	$path = $app->request()->getPathInfo();
	if (strpos($path, "news") !== false) {
		require MT_API_PATH.'/public/News.php';
	}	
	else if (strpos($path, "photographers") !== false) {
		require MT_API_PATH.'/public/Photographers.php';
	}
});

// 'public' group
$app->group('/api/public', function () use ($app) {
	require MT_API_PATH.'/public/Common.php';

	// News
	$app->get('/news/', function() use ($app) {
		$app->response->setStatus(MT_News::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->get('/news/:id', function($id) use ($app) {
		$app->response->setStatus(MT_News::getItem($id, getParam(PARAM_FIELDS)));
	});
	// Photographer
	$app->get('/photographers/', function() use ($app) {
		$app->response->setStatus(MT_Photographer::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->get('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::getItem($id, getParam(PARAM_FIELDS)));
	});
	$app->post('/photographers/', function() use ($app) {
		$app->response->setStatus(MT_Photographer::post(getBody()));
	});
	$app->post('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::postItem($id, getBody()));
	});	
	$app->delete('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::deleteItem($id));
	});	
});

// 'admin' group
$app->group('/api/admin', function () use ($app) {
	require MT_API_PATH.'/admin/Common.php';

	$app->get('/generateNews/', function() use ($app) {
		require MT_API_PATH.'/admin/NewsGeneration.php';
		$app->response->setStatus(MT_Admin_NewsGeneration::getGeneratedNews());
	});
});

$app->run();