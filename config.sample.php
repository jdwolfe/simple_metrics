<?php

DEFINE( 'SM_DIR', __DIR__ );
DEFINE( 'DATABASE_HOST', 'localhost' );
DEFINE( 'DATABASE_NAME', 'database' );
DEFINE( 'DATABASE_USER', 'username' );
DEFINE( 'DATABASE_PASS', 'password' );
DEFINE( 'SM_CPU_COUNT', 2 ); // cat /proc/cpuinfo

/*
 Name of Web server process that would show up when using the ps command
*/
$proc_name = 'apache'; // usally either apache or httpd
$display_name = 'Server Metrics';