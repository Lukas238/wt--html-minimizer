<?php
//session_start();


/*	INCLUDES
***********************/
include_once(dirname(__FILE__) .'/rsys_html_minimizer.php');


/* CONSTANTS
**********************/
define("SITE_ROOT", "D:/Work/I+D/minimize/tool");// LOCAL
//define("SITE_ROOT", "D:/Websites/wumndermanlab/www/dev/mmpot/minimizer");// DEV

define("SITE_URL", "http://localhost/I+D/minimize/tool");// LOCAL
//define("SITE_URL", "http://wundermanlab.com.ar/mmpot/dev/minimize");// DEV

define("UPLOAD_FOLDER", SITE_ROOT . "/temp");
define("DOWNLOAD_FOLDER", SITE_URL . "/temp");



/*	FUNCTIONS
**********************/

/* Unarchive tar files */
function unarchive_tar($file, $extract_folder){
	// unarchive from the tar
	$phar = new PharData($file);
	$phar->extractTo($extract_folder); 
	return true;
}


/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,basename($file));
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}



/*	Minimize batch html files, from the exported tar file from the CMS
*/
function minimize_batch_tar(){

	$temp_file = $_FILES['frm_tar']['tmp_name'];
	$extract_folder = UPLOAD_FOLDER ."/". basename($temp_file, ".tmp");
	$download_folder = DOWNLOAD_FOLDER ."/". basename($temp_file, ".tmp");
	$folder_name = basename($_FILES['frm_tar']['name'], ".tar");
	$files_folder = $extract_folder . "/". $folder_name;

	//Extract the temp zip file content to a new temp folder
	unarchive_tar($temp_file, $extract_folder);

	$files_to_zip=[];
	//Loop files
	foreach (glob($files_folder ."/*.htm") as $filename) {
		
		$file_contents = file_get_contents($filename);
		$file_contents = rsys_minimize_html($file_contents);
		if( file_put_contents($filename, $file_contents) ){
			$files_to_zip[] = $filename;
		};
	}
	
	$zip_file_server = $extract_folder .'/'. $folder_name . '.zip';
	$result = create_zip($files_to_zip, $zip_file_server, true);

	$_SESSION["download_zip"] = $download_folder .'/'. $folder_name . '.zip';
	
	return $zip_file_server;
}

?>