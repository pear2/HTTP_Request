<?php
$url = $testServer.'test003.png';

$http = new PEAR2_HTTP_Request($url,$adapter);
$http->verb = 'GET';
$response = $http->sendRequest();

// compare md5sum of the body
if ('3e4c6173971d58992d2f87f7045619f8' === md5($response->body)) {
	echo "good\n";
}
else {
	echo $response->body;
}
