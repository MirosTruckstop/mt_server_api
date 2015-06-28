<?php
// Response header parameter names
const HEADER_X_TOTAL_COUNT = 'X-Total-Count';
// Request parameter names
const PARAM_FIELDS = 'fields';
const PARAM_FILTER = 'filter';
const PARAM_ORDER = 'order';
const PARAM_LIMIT = 'limit';
const PARAM_OFFSET = 'offset';

/**
 * Gets, decodes and returns the parameter of the request.
 * 
 * @global object $app
 * @param string $param
 * @return misc|null
 */
function getParam($param) {
	global $app;
	return json_decode($app->request->get($param));
}
/**
 * Gets, decodes and returns the body of the request.
 * 
 * @global object $app
 * @return mixed|null
 */
function getBody() {
	global $app;
	return json_decode($app->request->getBody(), TRUE);
}
/**
 * Sets a header parameter, i.e. a name value pair, of the response.
 * 
 * @global object $app
 * @param string $param
 * @param string|integer $value
 */
function setHeader($param, $value) {
	global $app;
	$app->response->headers->set($param, $value);
}
/**
 * Sets the body of the reponse, i.e. encodes and outputs the given value.
 * 
 * @global integer $mt_json_encode_options
 * @param mixed $value
 */
function setBody($value) {
	global $mt_json_encode_options;
	echo json_encode($value, $mt_json_encode_options);
}