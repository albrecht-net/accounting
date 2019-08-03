<?php
if (__FILE__ != $_SERVER['SCRIPT_FILENAME']) {
    // For display
    $fmtD = numfmt_create($config['lang'], NumberFormatter::DECIMAL );
    numfmt_set_attribute($fmtD, NumberFormatter::FRACTION_DIGITS, 2);

    // For value
    $fmtV = numfmt_create($config['lang'], NumberFormatter::DECIMAL );
    numfmt_set_attribute($fmtV, NumberFormatter::FRACTION_DIGITS, 2);
    numfmt_set_attribute($fmtV, NumberFormatter::GROUPING_USED, FALSE);
} else {
    http_response_code(204);
}
?>