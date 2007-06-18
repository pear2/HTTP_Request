<?php
if (!defined('BLUGA_ROOT')) {
	define('BLUGA_ROOT',realpath(dirname(__FILE__).'/../../'));
}

require_once BLUGA_ROOT.'/Http/Request.php';
require_once BLUGA_ROOT.'/Http/Request/Exception.php';
require_once BLUGA_ROOT.'/Http/Request/Response.php';
require_once BLUGA_ROOT.'/Http/Request/Adapter.php';
require_once BLUGA_ROOT.'/Http/Request/Adapter/Phpstream.php';
require_once BLUGA_ROOT.'/Http/Request/Adapter/Curl.php';

// PEAR Deps, PEAR must be in the include path
require_once 'Net/URL2.php';
