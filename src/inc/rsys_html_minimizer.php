<?php
/*
*	HTML MINIMIZE FOR RESPONSYS
*
*	v1.0.1
*	By Lucas Dasso <dassolucas@wudnerman.com>
*	08/2016
********************/

define("NEWLINE", "@@");

function rsys_minimize_html($input_code){

	
	// Compress HTML in single line
	$input_code = preg_replace( "/\r|\n/", NEWLINE ." ", $input_code );

	// Removes tab character
	$input_code = preg_replace( "/\t/", "", $input_code );
	
	//Removes CSS comments (from CSS and HTML). Ex.: /* This is a CSS comment */
	$input_code = preg_replace( "/\/\*[^*]*\*\//", "", $input_code );
	
	/*
	*	Update old module ID comment to the special comment notation
	* for backguar compatibility
	*
	*/
	//$input_code = preg_replace("/<!--( (?:HERO|PRIMARY|TERTIARY|BANNER|FOOTNOTE)(?: OFFER|):[^>]* -->)/m", "<!--**$1", $input_code );//After
	
	
	/*
	*	Remove HTML comments but keep RESPONSYS functions
	*
	*	Exceptions:
	*	- IE conditional comments
	*	- Special comments notation (ex.: <!--** KEEP THIS TEXT -->)
	*/
	$input_code = preg_replace_callback('/<!--[^**\\[<>].*?(?<!!)-->/', function($m) { //Loop HTML comments, but exclude IE conditional commetns
		
		$lines = explode(NEWLINE, $m[0]);
		
		$return_var = '';
		foreach( $lines as $line){
			if( preg_match_all('/(\$[^\s$]+[^\$]+\$)/m', $line, $rsys_function)){ //Search for any RSYS function
				$rsys_function = implode($rsys_function[0],'');//Keep RSYS functions
				$return_var .= $rsys_function;
			}
			
		}
		return $return_var;
		
	}, $input_code);
	$input_code = preg_replace( "/".NEWLINE."/", "", $input_code );

	// Remove white spaces between any table family tags
	$input_code = preg_replace( "/((html|body|head|meta|style|table|tr|td)[^>]*>)[\s]*/i", "$1", $input_code );//After
	$input_code = preg_replace( "/[\s]*(<\/(html|body|head|meta|style|table|tr|td)>)/i", "$1", $input_code );// Before
	
	// Removes empty alt attributes from img tag
	$input_code = preg_replace("/(<img[^>]*)alt=?=\"\"|''([^>]*>)/i", "$1$2", $input_code);
	
	//Removes spaces between attributes in the tags
	$input_code = preg_replace_callback('~<[^img](.*?)>~i', function($m) { //Loop tags
		$return_var = preg_replace('/([a-z]*="[^"]*")[\s]*/mi', '$1', $m[0]); // Remove space.
		return $return_var;
	}, $input_code);
	
	// Convert multiple consecutive  white spaces into a single white space
	$input_code = preg_replace( "/[\s]{2,}/", " ", $input_code );
	
	return $input_code;
}
?>