<?php
session_start();
session_destroy();
header("Location: login.php?loggedout=1" . (!empty($_GET) ? '&' . $_SERVER['QUERY_STRING'] : ''));
?>