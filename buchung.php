<?php
header('Location: entry.php' . (count($_GET) >= 1 ? '?' . http_build_query($_GET) : ''), TRUE, 301);
exit();
?>