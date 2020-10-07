<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
}

/**
 * Function class autoloader
 * Include class file given by name.
 * 
 * @param string $className Name of class that should get included.
 * @return bool Return true on success or false on failure.
 */
spl_autoload_register(function ($className) {

    $path = ROOT_PATH . 'classes' . DIRECTORY_SEPARATOR;
    $extension = '.php';
    $file = $path . $className . $extension;
    
    if (!file_exists($file)) {
        return false;
    } else {
        require_once $file;
        return true;
    }
});

session::start();