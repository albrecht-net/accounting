<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.37.0-beta';

    // Accounting release date
    $accountingReleaseDate = '2019.08.03';
} else {
    http_response_code(204);
}
?>