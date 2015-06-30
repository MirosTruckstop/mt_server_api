<?php
// HTTP status codes
const HTTP_STATUS_200_OK = 200;
const HTTP_STATUS_201_CREATED = 201;
const HTTP_STATUS_202_ACCEPTED = 202;
const HTTP_STATUS_204_NO_CONTENT = 204;	
const HTTP_STATUS_400_BAD_REQUEST = 400;
const HTTP_STATUS_403_FORBIDDEN = 403;
const HTTP_STATUS_404_NOT_FOUND = 404;
// Response header parameter names
const HEADER_CONTENT_TYPE = 'Content-Type';
const HEADER_X_TOTAL_COUNT = 'X-Total-Count';
// Response header parameter values
const HEADER_CONTENT_TYPE_JSON = 'application/json';
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
 * @return mixed|null
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
 * @param string|int $value
 */
function setHeader($param, $value) {
	global $app;
	$app->response->headers->set($param, $value);
}
/**
 * Sets the body of the reponse, i.e. encodes and outputs the given value.
 * 
 * @global int $mt_json_encode_options
 * @param mixed $value
 */
function setBody($value) {
	setHeader(HEADER_CONTENT_TYPE, HEADER_CONTENT_TYPE_JSON);

	global $mt_json_encode_options;
	echo json_encode($value, $mt_json_encode_options);
}
/**
 * 
 * @param string $message
 */
function setBodyErrorMessage($message) {
	setBody([
		'error' => $message
	]);
}