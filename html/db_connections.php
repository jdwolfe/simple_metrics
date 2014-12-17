<?php
require_once (__DIR__ . '/../config.php');
?>
<!DOCTYPE html>

<html>

<head>
  <title>DB Metrics</title>
  <script type="text/javascript" src="/metrics/Chart.min.js"></script>
<meta http-equiv="refresh" content="60">
</head>

<body>

<?php
$is_mini = isset ($_GET['mini']);
if ($is_mini) {
	?>
	<style>
	* { font-size: 100%; }
	</style>
		<?php
}

$data = array();
$labels = array();
$long_queries = array();

$long_queries_count = 0;

$date = date('Y-m-d');
if (isset ($_GET['d'])) {
	$date = date('Y-m-d', strtotime($_GET['d']));
}
$yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
$tomorrow = date('Y-m-d', strtotime($date . ' +1 day'));

?>
<div>
<h1>DB Connections for <?php echo $date; ?></h1>
<?php
if ( file_exists( SM_DIR . '/log/db.' . $date . '.log' ) ) {
	$f = fopen( SM_DIR . '/log/db.' . $date . '.log', 'r' );
	$hour = '';
	$min = '';

	while (($csv = fgetcsv($f)) !== FALSE) {
		if ('~' == $csv[2]) {
			// set previous long_queries_count
			if ('' != $hour) {
				$long_queries[] = $long_queries_count;
			}
			$long_queries_count = 0;
			$hour = substr($csv[0], 11, 2);
			$min = substr($csv[0], 14, 2);
			if ('00' == $min) {
				$labels[] = "'" . $hour . "'";
			}
			else {
				$labels[] = "''";
			}
			$data[] = $csv[1];
		}
		else {
			$long_queries_count++;
		}
	}
	fclose($f);
	// set last long_queries_count;
	$long_queries[] = $long_queries_count;

	$chart_width = 1500;
	$chart_height = 400;
	// if is_mini
	if ($is_mini) {
		$labels = array_slice($labels, - 120, 120);
		$data = array_slice($data, - 120, 120);
		$long_queries = array_slice($long_queries, - 120, 120);
		$chart_width = 300;
		$chart_height = 250;
	}

	$chart_labels = implode(',', $labels);
	$chart_data = implode(',', $data);
	$chart_long_queries = implode(',', $long_queries);

	?>

		<canvas id="canvas1" width="<?php echo $chart_width; ?>" height="<?php echo $chart_height; ?>"></canvas>

		<script>

			var lineChartData = {
					labels : [<?php echo $chart_labels; ?>],
					datasets : [
							{
									fillColor : "rgba(255,255,255,0.5)",
									strokeColor : "rgba(0,204,15,1)",
									pointColor : "rgba(189,85,15,1)",
									pointStrokeColor : "#fff",
									data : [<?php echo $chart_data; ?>]
							},
							{
									fillColor : "rgba(214,96,17	 ,0.5)",
									strokeColor : "rgba(255,85,15,1)",
									pointColor : "rgba(189,85,15,1)",
									pointStrokeColor : "#fff",
									data : [<?php echo $chart_long_queries; ?>]
							}
					]

			}


			lineOptions = {
					animation: false,
					pointDot: false,
			}
		var myLine = new Chart(document.getElementById("canvas1").getContext("2d")).Line(lineChartData, lineOptions);

		</script>
		<?php
		if (!$is_mini) {
			?>
			<div>
			Green = Number of queries.<br>
					Red = Queries taking longer than 1 second.
			</div>
		<?php } ?>


		<?php
		}
		else {
			?>
			No data for that day.
			<?php
		}
		?>
</div>

<?php
if ( !$is_mini ) {
	?>
	<div>
	Check <a href="db_connections.php?d=<?php echo $yesterday; ?>"><?php echo $yesterday; ?></a> or <a href="db_connections.php?d=<?php echo $tomorrow; ?>"><?php echo $tomorrow; ?></a>
	</div>
	<?php
}
?>

</body>
</html>
