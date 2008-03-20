<?php

if (!defined('PEAR2_HTTP_Request_PATH')) {
    define('PEAR2_HTTP_Request_PATH', dirname(__FILE__));
}

// hack for development
require PEAR2_HTTP_Request_PATH . '/../../../../../Exception/trunk/src/Exception.php';

require PEAR2_HTTP_Request_PATH . '/../Request.php';
require PEAR2_HTTP_Request_PATH . '/Exception.php';
require PEAR2_HTTP_Request_PATH . '/Response.php';
require PEAR2_HTTP_Request_PATH . '/Adapter.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Phpstream.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Phpsocket.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Curl.php';
require PEAR2_HTTP_Request_PATH . '/Adapter/Http.php';

// PEAR Deps, PEAR must be in the include path
require_once 'Net/URL2.php';

