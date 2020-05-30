<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.39.1-beta';

    // Accounting release date
    $accountingReleaseDate = '2020.05.31';
} else {
    http_response_code(204);
}
?>