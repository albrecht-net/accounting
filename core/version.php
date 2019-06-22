<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.31.1-beta';

    // Accounting release date
    $accountingReleaseDate = '2019-06-22';
} else {
    http_response_code(204);
}
?>