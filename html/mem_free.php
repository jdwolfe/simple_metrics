<?php
require_once (__DIR__ . '/../config.php');
?>
<!DOCTYPE html>

<html>

<head>
  <title>Memory Metrics</title>
  <script type="text/javascript" src="/metrics/Chart.min.js"></script>
<meta http-equiv="refresh" content="60">

</head>

<body>

<?php

$is_mini = isset($_GET['mini']);
if ( $is_mini ) {
?>
<style>
* { font-size: 100%; }
</style>
<?php
}

$data = array();
$labels = array();

$date = date('Y-m-d');
if ( isset( $_GET['d']) ) {
	$date = date('Y-m-d', strtotime($_GET['d'] ) );
}
$yesterday = date('Y-m-d', strtotime( $date . ' -1 day'));
$tomorrow = date('Y-m-d', strtotime( $date . ' +1 day'));

?>
<div>
<h1>Free Memory for <?php echo $date; ?></h1>
<?php

if ( file_exists( SM_DIR . '/log/mem_' . $date . '.log') ) {
	$f = file( SM_DIR . '/log/mem_' . $date . '.log');

	$count = 0;
	foreach( $f as $line_num => $line ) {
		$data[] = trim( substr( $line, 30, 10 ) );
		$count++;
		if ( $count%60 == 0 ) {
			$labels[] = "'X'";
		} else {
			$labels[] = "''";
		}
	}

	$chart_width = 1500;
	$chart_height = 400;
	// if is_mini
	if ( $is_mini ) {
		$labels = array_slice( $labels, -120, 120 );
		$data = array_slice( $data, -120, 120 );
		$chart_width = 300;
		$chart_height = 250;
	}

	$chart_labels = implode(',', $labels);
	$chart_data = implode(',', $data);

	?>

	<canvas id="canvas1" width="<?php echo $chart_width; ?>" height="<?php echo $chart_height; ?>"></canvas>

	<script>

		var lineChartData = {
			labels : [<?php echo $chart_labels; ?>],
			datasets : [
				{
					fillColor : "rgba(0,255,0,0.5)",
					strokeColor : "rgba(0,204,15,1)",
					pointColor : "rgba(189,85,15,1)",
					pointStrokeColor : "#fff",
					data : [<?php echo $chart_data; ?>]
				}
			]

		}


		lineOptions = {
			animation: false,
			pointDot: false,
		}
		var myLine = new Chart(document.getElementById("canvas1").getContext("2d")).Line(lineChartData, lineOptions);

	</script>
	<div>

	</div>

	<?php
	} else {
			?>
			No data for that day.
			<?php
	}
?>
</div>

<?php if ( !$is_mini ) { ?>
<div>
Check <a href="mem_free.php?d=<?php echo $yesterday; ?>"><?php echo $yesterday; ?></a> or <a href="mem_free.php?d=<?php echo $tomorrow; ?>"><?php echo $tomorrow; ?></a>
</div>
<?php } ?>

</body>
</html>
