<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
    }
    
    require_once ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'init.php';

    // For display
    $fmtD = numfmt_create(config::get('defaultLang'), NumberFormatter::DECIMAL );
    numfmt_set_attribute($fmtD, NumberFormatter::FRACTION_DIGITS, 2);

    // For value
    $fmtV = numfmt_create(config::get('defaultLang'), NumberFormatter::DECIMAL );
    numfmt_set_attribute($fmtV, NumberFormatter::FRACTION_DIGITS, 2);
    numfmt_set_attribute($fmtV, NumberFormatter::GROUPING_USED, FALSE);
} else {
    http_response_code(204);
}
?>