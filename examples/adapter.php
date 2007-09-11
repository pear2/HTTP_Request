<?php
//require_once 'PEAR2/HTTP/Request/allfiles.php';

// to run from svn checkout
require_once '../src/HTTP/Request/allfiles.php';

// make a request using the phpstream adapter
/*
$request = new PEAR2_HTTP_Request('http://pear.php.net/',new PEAR2_HTTP_Request_Adapter_Phpstream);
$response = $request->sendRequest();

var_dump($response->code);
var_dump($response->headers);
var_dump(strlen($response->body));
*/

// make a request using the phpsocket adapter
$request = new PEAR2_HTTP_Request('http://pear.php.net/',new PEAR2_HTTP_Request_Adapter_Phpsocket);
$response = $request->sendRequest();

var_dump($response->code);
var_dump($response->headers);
var_dump(strlen($response->body));
