<?php
include_once( __DIR__ . DIRECTORY_SEPARATOR .  'functions.php');

if(!isset($_POST['html_code']) || $_POST['html_code']==""){
	die();
}

$input_code = $_POST['html_code'];
echo rsys_minimize_html($input_code);	
?>