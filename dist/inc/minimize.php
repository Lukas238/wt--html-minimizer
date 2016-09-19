<?php
include_once(dirname(__FILE__) .'/functions.php');

if(!isset($_POST['html_code']) || $_POST['html_code']==""){
	die();
}


$input_code = $_POST['html_code'];
echo rsys_minimize_html($input_code);
	
?>