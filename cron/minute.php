<?php

require_once( dirname(__FILE__) . '/../config.php' );

// check that the log dir exists
if ( !file_exists( SM_DIR . '/log' ) ) {
	if( !mkdir( SM_DIR . '/log' ) ) {
		die('Can not create ' . SM_DIR . '/log' );
	}
}

$filename = SM_DIR . "/log/db." . date('Y-m-d') . ".log";
$f = fopen( $filename, "a" );

$data = array(
	'date' => date('Y-m-d H:i:s'),
	'num' => 0,
	't' => '~',
	'q' => ''
);

$db = new mysqli( DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME );
if (!$db) {
	$data['q'] = "Failed to connect to MySQL: " . mysqli_connect_error();
	$data['num'] = SM_SQL_MAX_CONNECT;
	fputcsv( $f, $data );
} else {

	$sql = 'show full processlist';
	$stmt = $db->prepare( $sql );
	$stmt->execute();
	$stmt->store_result();
	$data['num'] = $stmt->num_rows;
	fputcsv( $f, $data );

	$stmt->bind_result( $Id, $User, $Host, $Db, $Command, $Time, $State, $Info );
	while( $stmt->fetch() ) {
		$info = clean( $Info );
		if ( $Time > 0 && $info != '' ) {
			$data['t'] = $Time;
			$data['q'] = $info;
			fputcsv( $f, $data );
		}
	}
	$stmt->close();
	$db->close();
}

fclose( $f );

function clean( $s ) {
	$s = trim( $s );
	$s = str_replace( "\n", "", $s );
	$s = str_replace( "\r", "", $s );
	return $s;
}


// execute command line tools
exec( '/bin/ps -aef|/bin/grep ' . $proc_name . '|/usr/bin/wc >> ' . SM_DIR . '/log/httpd_`/bin/date +"%Y-%m-%d"`.log' );
exec( '/usr/bin/uptime >> ' . SM_DIR . '/log/cpu_`date +"%Y-%m-%d"`.log' );
exec( '/usr/bin/free -m|grep Mem >> ' . SM_DIR . '/log/mem_`date +"%Y-%m-%d"`.log' );
