<?php
$package->dependencies['required']->php = '5.3.0';
$compatible->dependencies['required']->php = '5.3.0';

unset($package->files['tests/testFiles/2meg.bin']);
unset($compatible->files['test/pear2.php.net/PEAR2_HTTP_Request/testFiles/testFiles/2meg.bin']);
