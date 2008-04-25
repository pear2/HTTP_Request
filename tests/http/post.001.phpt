--TEST--
PEAR2_HTTP_Request_Adapter_Http - Test of an HTTP Post request 
uses test001.html
--FILE--
<?php
require_once dirname(__FILE__).'/../_setup.php';

$adapter = new PEAR2_HTTP_Request_Adapter_Http();

// this is a shared test with only the adapter being differ
require_once dirname(__FILE__).'/../shared/post.001.php';
--EXPECT--
string(5) "Test
"
bool(true)
