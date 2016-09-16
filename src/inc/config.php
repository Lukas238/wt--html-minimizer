<?php
/*	CONFIG
****************************/
switch ( $_SERVER['SERVER_NAME'] ) {
    case '52.67.28.91':
		define('ENV', 'stage');
		break;
    default: //Localhost
        define('ENV', 'local');
} 
include_once('config.'. ENV .'.php');
?>