<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {

    // Accounting version tag
    $accountingVersion = 'v2.21.2-beta';

    // Accounting release date
    $accountingReleaseDate = '2019-04-28';
} else {
    http_response_code(204);
}
?>