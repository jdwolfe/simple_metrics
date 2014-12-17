<?php
require_once('../functions.php');
?>
<!DOCTYPE html>

<html>
<head>
	<title><?php echo $display_name; ?> Metrics</title>
</head>
<body>
<h1><?php echo $display_name; ?> Metrics</h1>
<iframe src="/metrics/db_connections.php" height="600" width="1500"></iframe>
<iframe src="/metrics/http_connections.php" height="600" width="1500"></iframe>
<iframe src="/metrics/cpu_load.php" height="600" width="1500"></iframe>
<iframe src="/metrics/mem_free.php" height="600" width="1500"></iframe>
</body>
</html>
