<?php
define('MT_API_PATH', 'src/api');

require 'vendor/autoload.php';
require 'src/config/WordPressAuthenticator.php';
require 'src/api/Common.php';

/*
 * Sets database connection via ORM::configure 
 */
include 'config.php';

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
	/*if (strpos($path, "news") !== false) {
		require MT_API_PATH.'/news.php';
	}	
	else*/ if (strpos($path, "photographers") !== false) {
		require MT_API_PATH.'/Photographers.php';
	}
});

const PARAM_FIELDS = 'fields';
const PARAM_ORDER = 'order';
const PARAM_LIMIT = 'limit';
const PARAM_OFFSET = 'offset';

function getParam($paramName) {
	global $app;
	return json_decode($app->request->get($paramName));
}
function getBody() {
	global $app;
	return json_decode($app->request->getBody(), TRUE);
}

// 'api' group
$app->group('/api', function () use ($app) {	
	//$app->get('/news/', function() { get(); });
	$app->get('/photographers/', function() use ($app) {
		$app->response->setStatus(MT_Photographer::getList(getParam(PARAM_FIELDS), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->post('/photographers/', function() use ($app) {
		$app->response->setStatus(MT_Photographer::post(getBody()));
	});	
	$app->get('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::getItem($id, getParam(PARAM_FIELDS)));
	});
	$app->post('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::postItem($id, getBody()));
	});	
	$app->delete('/photographers/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Photographer::deleteItem($id));
	});
		
});

$app->run();