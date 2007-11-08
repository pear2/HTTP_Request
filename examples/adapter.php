<?php
//require_once 'PEAR2/HTTP/Request/allfiles.php';

// to run from svn checkout
require_once '../src/HTTP/Request/allfiles.php';

$url = 'http://webthumb.bluga.net/home';

$adapters = array(
	'Phpstream' => true,
	'Phpsocket' => false,
	'Peclhttp' => false,
	);

foreach($adapters as $adapter => $status) {
	if (!$status) {
		continue;
	}

	$class = 'PEAR2_HTTP_Request_Adapter_'.$adapter;
	$request = new PEAR2_HTTP_Request($url,new $class);
	$response = $request->sendRequest();

	echo "$adapter adapter\n";
	var_dump($response->code);
	var_dump($response->headers);
	var_dump($response->cookies);
	var_dump(strlen($response->body));
}
