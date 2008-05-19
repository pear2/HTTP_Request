<?php
$testServer = false; // set to the url where testFiles has been copied
$testServer = "http://bluga.net/projects/PEAR2_HTTP_Request/test/";

// for tests were including all the HTTP_Request files
// this is a hack to make tests run from svn for now

$autoload = dirname(__FILE__).'/../../sandbox/Loader/src/PEAR2/Loader/PEAR2Svn.php';
if (file_exists($autoload)) {
	require_once dirname(__FILE__).'/../../sandbox/Loader/src/PEAR2/Loader/PEAR2Svn.php';
}
