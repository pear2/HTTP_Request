<?php
require_once 'PEAR2/HTTP/Request/allfiles.php';

$request = new PEAR2_HTTP_Request('http://bluga.net/webthumb/');
$response = $request->sendRequest();

var_dump($response->code);
var_dump($response->headers);
var_dump(strlen($response->body));
