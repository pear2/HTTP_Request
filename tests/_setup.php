<?php
$testServer = false; // set to the url where testFiles has been copied
$testServer = "http://bluga.net/projects/PEAR2_HTTP_Request/test/";

// for tests were including all the HTTP_Request files
// this is a hack to make tests run from svn for now
require_once dirname(__FILE__).'/../src/HTTP/Request/allfiles.php';
