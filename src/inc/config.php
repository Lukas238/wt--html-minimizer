<?php
/*	CONFIG
****************************/

define('HOSTNAME', $_SERVER['SERVER_NAME']);

switch (HOSTNAME) {
    case '52.67.28.91':
		$server = strtolower(explode("/", parse_url( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", PHP_URL_PATH) )[1]);
		switch ($server){
			case "stage":
				define('ENV', 'stage');
				break;
			default:
				define('ENV', 'dev');
		}
		break;
    default: //Localhost
        define('ENV', 'local');
} 

include_once('config.'. ENV .'.php');

?>