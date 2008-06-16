--TEST--
Get a file larger then memory limit
--INI--
memory_limit=1m
--FILE--
<?php
require_once dirname(__FILE__)."/_setup.php";


$request = new PEAR2_HTTP_Request($testServer."2meg.bin");

$temp = tempnam('/tmp','phpt');

$request->requestToFile($temp);


?>
--EXPECT--
