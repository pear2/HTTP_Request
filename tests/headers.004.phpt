--TEST--
Test of iterator in PEAR2_HTTP_Request_Headers
--FILE--
<?php
require_once dirname(__FILE__).'/_setup.php';
$in = array(
	'Content-Type'	=> 'text/html',
	'ETag'		=> 'EADAF124D',
	'content-length'=> '10'
	);
$headers = new PEAR2_HTTP_Request_Headers($in);

foreach($headers as $k => $v) {
	echo "$k: $v\n";
}

$headers->iterationStyle = PEAR2_HTTP_Request_Headers::CAMEL_CASE;
foreach($headers as $k => $v) {
	echo "$k: $v\n";
}

$headers->iterationStyle = PEAR2_HTTP_Request_Headers::ORIGINAL_CASE;
foreach($headers as $k => $v) {
	echo "$k: $v\n";
}

?>
--EXPECT--
content-type: text/html
etag: EADAF124D
content-length: 10
ContentType: text/html
ETag: EADAF124D
ContentLength: 10
Content-Type: text/html
ETag: EADAF124D
content-length: 10
