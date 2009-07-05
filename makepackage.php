<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
require_once dirname(__FILE__).'/../autoload.php';

$a = new pear2\Pyrus\PEAR2SVN(dirname(__FILE__), 'PEAR2_HTTP_Request');

$package = new pear2\Pyrus\Package('package.xml');
$outfile = $package->name.'-'.$package->version['release'];
$a = new pear2\Pyrus\Creator(
	new pear2\Pyrus\Tar($outfile.'.tar', 'none'),
	//new pear2\Pyrus\Phar($outfile.'.tgz', false, Phar::TAR, Phar::GZ),
	dirname(__FILE__).'/../Exception/src',
	dirname(__FILE__).'/../Autoload/src',
	dirname(__FILE__).'/../MultiErrors/src'
);
$a->render($package);
?>
