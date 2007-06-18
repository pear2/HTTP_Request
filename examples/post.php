<?php
require_once '../../Bluga/Http/Request/AllFiles.php';

$request = new Bluga_Http_Request();
$request->url = "http://bluga.net/post.php";
$request->body = array('test1'=>'value1','test2'=>'value2');

$response = $request->sendRequest();

var_dump($response->code);
var_dump($response->headers);
var_dump($response->body);
