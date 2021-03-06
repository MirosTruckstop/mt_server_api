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
	'secure' => false,
	'authenticator' => new WordPressAuthenticator()
]));

$app->hook('slim.before.router', function() use ($app) {
	$path = $app->request()->getPathInfo();
	if (strpos($path, 'photos') !== false) {
		require MT_API_PATH.'/public/Photos.php';
	}
	else if (strpos($path, 'galleries') !== false) {
		require MT_API_PATH.'/public/Gallery.php';
	}
	else if (strpos($path, 'news') !== false) {
		require MT_API_PATH.'/public/News.php';
	}
	else if (strpos($path, 'photographers') !== false) {
		require MT_API_PATH.'/public/Photographers.php';
	}
	else if (strpos($path, 'subcategory') !== false) {
		require MT_API_PATH.'/public/Subcategory.php';
	}	
	else if (strpos($path, 'category') !== false) {
		require MT_API_PATH.'/public/Category.php';
	}
});

// 'public' group
$app->group('/api/public', function () use ($app) {
	require MT_API_PATH.'/public/Common.php';
	// Photo
	$app->get('/photos', function() use ($app) {
		$app->response->setStatus(MT_Photo::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	// Gallery
	$app->get('/galleries/', function() use ($app) {
		$app->response->setStatus(MT_Gallery::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->get('/galleries/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Gallery::getItem($id, getParam(PARAM_FIELDS)));
	});
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
	// Category
	$app->get('/category/', function() use ($app) {
		$app->response->setStatus(MT_Category::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->get('/category/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Category::getItem($id, getParam(PARAM_FIELDS)));
	});
	$app->post('/category/', function() use ($app) {
		$app->response->setStatus(MT_Category::post(getBody()));
	});
	$app->post('/category/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Category::postItem($id, getBody()));
	});	
	// Subcategory
	$app->get('/subcategory/', function() use ($app) {
		$app->response->setStatus(MT_Subcategory::getList(getParam(PARAM_FIELDS), getParam(PARAM_FILTER), getParam(PARAM_ORDER), getParam(PARAM_LIMIT), getParam(PARAM_OFFSET)));
	});
	$app->get('/subcategory/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Subcategory::getItem($id, getParam(PARAM_FIELDS)));
	});
	$app->post('/subcategory/', function() use ($app) {
		$app->response->setStatus(MT_Subcategory::post(getBody()));
	});
	$app->post('/subcategory/:id', function($id) use ($app) {
		$app->response->setStatus(MT_Subcategory::postItem($id, getBody()));
	});
});

// 'admin' group
$app->group('/api/admin', function () use ($app) {
	require MT_API_PATH.'/admin/Common.php';

	$app->get('/generateNews/', function() use ($app) {
		require MT_API_PATH.'/admin/NewsGeneration.php';
		$app->response->setStatus((new MT_Admin_NewsGeneration)->action());
	});
});

$app->run();