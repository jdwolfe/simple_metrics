<?php
require_once (__DIR__ . '/config.php');

function do_header( $title = '' ) {
	if ( $title == '' ) {
		global $display_name;
		$title = $display_name . ' - Last 2 hours';
	}
	?>
	<!DOCTYPE html>
	<html>

	<head>
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="/metrics/Chart.min.js"></script>
		<meta http-equiv="refresh" content="60">
	</head>

	<body>
	<?php
}
function do_footer() {
	?>
	</body>
	</html>
	<?php
}


class SimpleMetrics {

	private $date = '';
	private $yesterday = '';
	private $tomorrow = '';
	private $full_height = 400;
	private $full_width = 1500;
	private $mini_height = 150;
	private $mini_width = 300;

	public function __construct() {

		$this->date = date('Y-m-d');
		if (isset ($_GET['d'])) {
			$this->date = date('Y-m-d', strtotime($_GET['d']));
		}
		$this->yesterday = date('Y-m-d', strtotime($this->date . ' -1 day'));
		$this->tomorrow = date('Y-m-d', strtotime($this->date . ' +1 day'));
 	}

	private function get_date() {
		return $this->date;
	}

	public function show_db_chart( $date = '', $is_mini = TRUE ) {
		$date = '' == $date ? $this->get_date() : $date;

		$data = array();
		$labels = array();
		$long_queries = array();
		$long_queries_count = 0;

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

			$chart_width = $this->full_width;
			$chart_height = $this->full_height;
			// if is_mini
			if ($is_mini) {
				$labels = array_slice($labels, - 120, 120);
				$data = array_slice($data, - 120, 120);
				$long_queries = array_slice($long_queries, - 120, 120);
				$chart_width = $this->mini_width;
				$chart_height = $this->mini_height;
			}

			$chart_labels = implode(',', $labels);
			$chart_data = implode(',', $data);
			$chart_long_queries = implode(',', $long_queries);

			?>
			<h3>DB Connections</h3>
			<canvas id="canvas-db" width="<?php echo $chart_width; ?>" height="<?php echo $chart_height; ?>"></canvas>
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
				var myLine = new Chart(document.getElementById("canvas-db").getContext("2d")).Line(lineChartData, lineOptions);
    		</script>
			<?php
		} else {
			?>
			No data for that day.
			<?php
		}
	}

	public function show_http_chart( $date = '', $is_mini = TRUE ) {
		$date = '' == $date ? $this->get_date() : $date;

		if ( file_exists( SM_DIR . '/log/httpd_' . $date . '.log' ) ) {
			$f = file( SM_DIR . '/log/httpd_' . $date . '.log' );

			$count = 0;
			foreach( $f as $line_num => $line ) {
					$data[] = trim( substr( $line, 0, 7 ) );
					$count++;
					if ( $count%60 == 0 ) {
							$labels[] = "'X'";
					} else {
							$labels[] = "''";
					}
			}

			$chart_width = $this->full_width;
			$chart_height = $this->full_height;
			// if is_mini
			if ( $is_mini ) {
				$labels = array_slice( $labels, -120, 120 );
				$data = array_slice( $data, -120, 120 );
				$chart_width = $this->mini_width;
				$chart_height = $this->mini_height;
			}

			$chart_labels = implode(',', $labels);
			$chart_data = implode(',', $data);

			?>

			<h3>HTTP Connections</h3>
			<canvas id="canvas-http" width="<?php echo $chart_width; ?>" height="<?php echo $chart_height; ?>"></canvas>
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
								}
						]
				}

				lineOptions = {
						animation: false,
						pointDot: false,
				}
				var myLine = new Chart(document.getElementById("canvas-http").getContext("2d")).Line(lineChartData, lineOptions);
			</script>
			<?php
			} else {
				?>
				No data for that day.
				<?php
			}
	}

	public function show_cpu_chart( $date = '', $is_mini = TRUE ) {
		$date = '' == $date ? $this->get_date() : $date;

		$load = array();
		$max_load = array();
		$labels = array();
		if ( file_exists( SM_DIR . '/log/cpu_' . $date . '.log' ) ) {
			$f = file( SM_DIR . '/log/cpu_' . $date . '.log' );

			$hour = '';
			$min = '';
			foreach( $f as $line_num => $line ) {
				$hour = trim( substr( $line, 1, 2 ) );
				$min = trim( substr( $line, 4, 2 ) );
				$parts = explode(',', $line);
				$part_num = stripos( $line, 'day' ) > 0 ? 3 : 2; // if it is up less than 1 day
				$parts2 = explode(' ', trim($parts[ $part_num ]));
				$load[] = trim($parts2[2]);
				$max_load[] = SM_CPU_COUNT;

				if ( '00' == $min ) {
					$labels[] = "'".$hour."'";
				} else {
					$labels[] = "''";
				}
			}

			$chart_width = $this->full_width;
			$chart_height = $this->full_height;
			// if is_mini
			if ( $is_mini ) {
				$labels = array_slice( $labels, -120, 120 );
				$load = array_slice( $load, -120, 120 );
				$max_load = array_slice( $max_load, -120, 120 );
				$chart_width = $this->mini_width;
				$chart_height = $this->mini_height;
			}

			$chart_labels = implode(',', $labels);
			$chart_data1 = implode(',', $load);
			$chart_max = implode(',', $max_load);

			?>
			<h3>CPU Load</h3>
			<canvas id="canvas-cpu" width="<?php echo $chart_width; ?>" height="<?php echo $chart_height; ?>"></canvas>
			<script>
				var lineChartData = {
					labels : [<?php echo $chart_labels; ?>],
					datasets : [
						{
							fillColor : "rgba(97,255,92,0.5)",
							strokeColor : "rgba(0,204,15,1)",
							pointColor : "rgba(189,85,15,1)",
							pointStrokeColor : "#fff",
							data : [<?php echo $chart_data1; ?>]
						},
						{
							fillColor : "rgba(97,255,92,0.5)",
							strokeColor : "rgba(255,0,0,1)",
							pointColor : "rgba(255,0,0,1)",
							pointStrokeColor : "#f00",
							data : [<?php echo $chart_max; ?>]
						}

					]

				}
				lineOptions = {
						animation: false,
						pointDot: false,
				}
				var myLine = new Chart(document.getElementById("canvas-cpu").getContext("2d")).Line(lineChartData, lineOptions);
			</script>
		<?php
		} else {
			?>
			No data for that day.
			<?php
		}
	}

	public function show_memory_chart( $date = '', $is_mini = TRUE ) {
		$data = array();
		$labels = array();
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

			$chart_width = $this->full_width;
			$chart_height = $this->full_height;
			// if is_mini
			if ( $is_mini ) {
				$labels = array_slice( $labels, -120, 120 );
				$data = array_slice( $data, -120, 120 );
				$chart_width = $this->mini_width;
				$chart_height = $this->mini_height;
			}

			$chart_labels = implode(',', $labels);
			$chart_data = implode(',', $data);

			?>
			<h3>Available Memory</h3>
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
			<?php
		} else {
			?>
			No data for that day.
			<?php
		}
	}

}