<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
require_once dirname(__FILE__).'/../autoload.php';

$a = new PEAR2_Pyrus_Developer_PackageFile_PEAR2SVN(dirname(__FILE__), 'PEAR2_HTTP_Request');

$package = new PEAR2_Pyrus_Package('package.xml');
$outfile = $package->name.'-'.$package->version['release'];
$a = new PEAR2_Pyrus_Package_Creator(
	new PEAR2_Pyrus_Developer_Creator_Tar($outfile.'.tar', 'none'),
	//new PEAR2_Pyrus_Developer_Creator_Phar($outfile.'.tgz', false, Phar::TAR, Phar::GZ),
	dirname(__FILE__).'/../Exception/src',
	dirname(__FILE__).'/../Autoload/src',
	dirname(__FILE__).'/../MultiErrors/src'
);
$a->render($package);
?>
