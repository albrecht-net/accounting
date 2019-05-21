<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.26.0-beta';

    // Accounting release date
    $accountingReleaseDate = '2019-05-21';
} else {
    http_response_code(204);
}
?>