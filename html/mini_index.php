<?php
require_once('../config.php');
?>
<!DOCTYPE html>

<html>

<head>
  <title><?php echo $display_name; ?> - Last 2 hours</title>
</head>

<body>

<h1><?php echo $display_name; ?> - Last 2 hours</h1>
<iframe src="/metrics/db_connections.php?mini=1" frameborder="0" width="350" height="310"></iframe>
<iframe src="/metrics/http_connections.php?mini=1" frameborder="0" width="350" height="310"></iframe>
<iframe src="/metrics/cpu_load.php?mini=1" frameborder="0" width="350" height="310"></iframe>
<iframe src="/metrics/mem_free.php?mini=1" frameborder="0" width="350" height="310"></iframe>

</body>
</html>
