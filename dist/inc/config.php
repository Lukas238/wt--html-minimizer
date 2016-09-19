<?php
/*	CONFIG
****************************/

$server = strtolower(explode("/", parse_url( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", PHP_URL_PATH) )[1]); // stage or dev


switch( $server ){
	case "stage":
		define("DOMAIN", "http://52.67.28.91:9090");
		define("SITE_ROOT", "C:\\xampp\\htdocs\\stage\\tools\\minifier");
		define("SITE_URL", DOMAIN."/stage/tools/minifier");
		break;
	case "dev":
		define("DOMAIN", "http://52.67.28.91:9090");
		define("SITE_ROOT", "C:\\xampp\\htdocs\\dev\\tools\\minifier");
		define("SITE_URL", DOMAIN."/dev/tools/minifier");
		break;
	default: //Localhost
		define("DOMAIN", "http://localhost");
		define("SITE_ROOT", "D:/Work/rsys-html-minifier/src");
		define("SITE_URL", DOMAIN."/rsys-html-minifier/src/");
}

?>