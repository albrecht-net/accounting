<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.28.0-beta';

    // Accounting release date
    $accountingReleaseDate = '2019-05-28';
} else {
    http_response_code(204);
}
?>