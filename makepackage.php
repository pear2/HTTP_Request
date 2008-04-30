<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
require '/Users/bbieber/pyrus/src/PEAR2/Autoload.php';

$a = new PEAR2_Pyrus_Developer_PackageFile_PEAR2SVN(dirname(__FILE__), 'PEAR2_HTTP_Request');


?>
