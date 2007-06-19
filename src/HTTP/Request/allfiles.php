<?php

if (!class_exists('PEAR2_HTTP_Request_Exception')) {
    class PEAR2_HTTP_Request_Exception extends Exception {}
}

if (!defined('PEAR2_HTTP_Request_PATH')) {
    define('PEAR2_HTTP_Request_PATH', dirname(__FILE__));
}

require PEAR2_HTTP_Request_PATH . '/HTTP/Request.php';
require PEAR2_HTTP_Request_PATH . '/HTTP/Request/Exception.php';
require PEAR2_HTTP_Request_PATH . '/HTTP/Request/Response.php';
require PEAR2_HTTP_Request_PATH . '/HTTP/Request/Adapter.php';
require PEAR2_HTTP_Request_PATH . '/HTTP/Request/Adapter/Phpstream.php';
require PEAR2_HTTP_Request_PATH . '/HTTP/Request/Adapter/Curl.php';

// PEAR Deps, PEAR must be in the include path
require_once 'Net/URL2.php';

