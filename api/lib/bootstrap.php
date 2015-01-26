<?php

ob_start();

$request = $_GET['request'];
require_once (ROOT . DS . 'api' . DS . 'lib' . DS . 'config.php');

/**
 * The magic PHP autoload
 */
function __autoload($className) {
    if (file_exists(ROOT . DS . 'api' . DS . 'lib' . DS .  'class' . DS . strtolower($className) . '.class.php')) {
        require_once(ROOT . DS . 'api' . DS . 'lib' . DS . 'class' . DS .  strtolower($className) . '.class.php');
    } 
    else if (file_exists(ROOT . DS . 'api' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'api' . DS . 'controllers' . DS . strtolower($className) . '.php');
    }
    else if (file_exists(ROOT . DS . 'api' . DS . 'models' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'api' . DS . 'models' . DS . strtolower($className) . '.php');
    }
}

function handleRequest($request) {
    
    $controller = null;
    
    $reqArray = explode("/", $request);
    $controller = $reqArray[0];
    $queryString = array_slice($reqArray, 1);
    
    $controller = ucwords($controller);
    $controller .= 'Controller';
    
    if(class_exists($controller)){
        new $controller($queryString);
    } else {
        throw new Exception('No such Endpoint exists.');
        return;
    }
}

handleRequest($request);

ob_end_flush(); 

?>