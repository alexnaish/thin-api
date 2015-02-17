<?php
session_start();
// Base Application Settings
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

require_once (ROOT . DS . 'api' . DS . 'lib' . DS . 'bootstrap.php');

?>