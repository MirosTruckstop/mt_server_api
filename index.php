<?php
define('MT_API_PATH', 'src/api');

require 'vendor/autoload.php';
require 'src/config/WordPressAuthenticator.php';
require 'src/api/Common.php';

/*
 * Sets database connection via ORM::configure 
 */
include 'src/config/database.php';

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
		require MT_API_PATH.'/News.php';
	}	
	else if (strpos($path, "photographers") !== false) {
		require MT_API_PATH.'/Photographers.php';
	}
});

// Response header parameters
const HEADER_X_TOTAL_COUNT = 'X-Total-Count';
// Request parameters
const PARAM_FIELDS = 'fields';
const PARAM_FILTER = 'filter';
const PARAM_ORDER = 'order';
const PARAM_LIMIT = 'limit';
const PARAM_OFFSET = 'offset';

function getParam($param) {
	global $app;
	return json_decode($app->request->get($param));
}
function getBody() {
	global $app;
	return json_decode($app->request->getBody(), TRUE);
}
function setHeader($param, $value) {
	global $app;
	$app->response->headers->set($param, $value);
}
function setBody($value) {
	echo json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// 'api' group
$app->group('/api', function () use ($app) {
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

$app->run();