--TEST--
PEAR2_HTTP_Request_Adapter_Http - Test of an HTTP Get request of a binary file
uses test003.png
--FILE--
<?php
require_once dirname(__FILE__).'/../_setup.php';

$adapter = new PEAR2_HTTP_Request_Adapter_Http(); // http extension

// this is a shared test with only the adapter being differ
require_once dirname(__FILE__).'/../shared/get.002.php';
--EXPECT--
good
