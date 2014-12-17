<?php
require_once('../functions.php');
do_header();
$sm = new SimpleMetrics;

?>
<h2><?php echo $display_name; ?> - Last 2 hours</h2>
<div class='mini-chart'>
	<?php
	$sm->show_db_chart( date('Y-m-d') );
	?>
</div>

<div class='mini-chart'>
	<?php
	$sm->show_http_chart( date('Y-m-d') );
	?>
</div>

<div class='mini-chart'>
	<?php
	$sm->show_cpu_chart( date('Y-m-d') );
	?>
</div>

<div class='mini-chart'>
	<?php
	$sm->show_memory_chart( date('Y-m-d') );
	?>
</div>

<?php

do_footer();