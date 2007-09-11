<?php

if (!defined('PEAR2_HTTP_Request_PATH')) {
    define('PEAR2_HTTP_Request_PATH', dirname(__FILE__));
}

require PEAR2_HTTP_Request_PATH . '/../Request.php';
require PEAR2_HTTP_Request_PATH . '/Exception.php';
require PEAR2_HTTP_Request_PATH . '/Response.php';
require PEAR2_HTTP_Request_PATH . '/Adapter.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Phpstream.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Curl.php';

// PEAR Deps, PEAR must be in the include path
require_once 'Net/URL2.php';

