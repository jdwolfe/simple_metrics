<?php

require_once( '../config.php' );

$filename = SM_DIR . "/log/db." . date('Y-m-d') . ".log";
$f = fopen( $filename, "a" );

$data = array(
	'date' => date('Y-m-d H:i:s'),
	'num' => 0,
	't' => '~',
	'q' => ''
);

$db = mysql_connect( DATABASE_HOST, DATABASE_USER, DATABASE_PASS );
if (!$db) {
	$data['q'] = "Failed to connect to MySQL: " . mysql_error();
	fputcsv($f, $data);
	fclose($f);
	exit;
}

$sql = 'show full processlist';
$result = mysql_query($sql);
$data['num'] = mysql_num_rows( $result );
fputcsv( $f, $data );

while( $row = mysql_fetch_object( $result ) ) {
	$info = clean( $row->Info );
	if ( $row->Time > 0 && $info != '' ) {
		$data['t'] = $row->Time;
		$data['q'] = $info;
		fputcsv( $f, $data );
	}
}
fclose( $f );

function clean( $s ) {
	$s = trim( $s );
	$s = str_replace( "\n", "", $s );
	$s = str_replace( "\r", "", $s );
	return $s;
}


// execute command line tools
exec( '/bin/ps -aef|/bin/grep apache2|/usr/bin/wc >> ' . SM_DIR . '/log/httpd_`/bin/date +"%Y-%m-%d"`.log' );
exec( '/usr/bin/uptime >> ' . SM_DIR . '/log/cpu_`date +"%Y-%m-%d"`.log' );
exec( '/usr/bin/free -m|grep Mem >> ' . SM_DIR . '/log/mem_`date +"%Y-%m-%d"`.log' );