<?php

if (!class_exists('PEAR2_HTTP_Request_Exception')) {
    class PEAR2_HTTP_Request_Exception extends Exception {}
}

require_once dirname(__FILE__) . '/HTTP/Request.php';
require_once dirname(__FILE__) . '/HTTP/Request/Exception.php';
require_once dirname(__FILE__) . '/HTTP/Request/Response.php';
require_once dirname(__FILE__) . '/HTTP/Request/Adapter.php';
require_once dirname(__FILE__) . '/HTTP/Request/Adapter/Phpstream.php';
require_once dirname(__FILE__) . '/HTTP/Request/Adapter/Curl.php';

// PEAR Deps, PEAR must be in the include path
require_once 'Net/URL2.php';

