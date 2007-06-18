<?php
require_once '../../Bluga/Http/Request/AllFiles.php';

$request = new Bluga_Http_Request("http://bluga.net/webthumb/");
$response = $request->sendRequest();

var_dump($response->code);
var_dump($response->headers);
var_dump(strlen($response->body));
