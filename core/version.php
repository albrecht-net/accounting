<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.34.0-beta';

    // Accounting release date
    $accountingReleaseDate = '2019.07.14';
} else {
    http_response_code(204);
}
?>