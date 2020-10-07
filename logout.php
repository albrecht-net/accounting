<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__, 1) . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

session_destroy();
header("Location: login.php?loggedout=1" . (!empty($_GET) ? '&' . $_SERVER['QUERY_STRING'] : ''));
?>