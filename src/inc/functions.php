<?php


/*	INCLUDES
***********************/
include_once( __DIR__ . DIRECTORY_SEPARATOR . 'config.php');
include_once( __DIR__ . DIRECTORY_SEPARATOR . 'rsys_html_minimizer.php');


/* CONSTANTS
**********************/
define("UPLOAD_FOLDER", SITE_ROOT . DIRECTORY_SEPARATOR . "tmp");
define("DOWNLOAD_URL", SITE_URL . "/tmp");
define("EXPORT_FILE_NAME", "export_");
define("FILES_PER_ZIP", 100);


/*	GLOBALS
**********************/


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
function create_zip($files = array(),$destination = '',$overwrite = false, $limit_files = FILES_PER_ZIP) {
	
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
		
		//add the files
		$i = 0;
		$file_num = 1;
		$zip_parts = array();
		foreach($valid_files as $file) {
			
			$zip_part_name = $destination."_".$file_num.".zip";
			if( $i % $limit_files == 0 ){
				$zip = new ZipArchive();
				if( $zip->open($zip_part_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true) {
					return false;
				}				
			}
			
			$zip->addFile($file,basename($file)); //Add current file
			
			if( $i % $limit_files == ($limit_files -1) || count($valid_files)-1 == $i ){
				$zip->close();
				
				ob_start();
				echo file_get_contents( $zip_part_name );
				$content = ob_get_contents();
				ob_end_clean();
				
				$zip_parts[] = "data:application/zip;base64," . base64_encode($content);
				
				unlink($zip_part_name);
				$file_num++;
			}
			
			$i++;;
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//print_r(glob(dirname($files[0])."/*.*"));
		//echo dirname($files[0]);
		
		array_map('unlink', glob(dirname($files[0]). DIRECTORY_SEPARATOR ."*.htm") );
		rmdir(dirname($files[0]));
	
		
		//check to make sure the file exists
		return $zip_parts;
		
	}
	else
	{
		echo "Fail";
		return false;
	}
}



/*	Minimize batch html files, from the exported tar file from the CMS
*/
function minimize_batch_tar(){

	$temp_file = $_FILES['frm_tar']['tmp_name'];
	$extract_folder = UPLOAD_FOLDER . DIRECTORY_SEPARATOR . basename($temp_file, ".tmp");
	$download_url = DOWNLOAD_URL . "/" . basename($temp_file, ".tmp");
	$folder_name = basename($_FILES['frm_tar']['name'], ".tar");
	$files_folder = $extract_folder . DIRECTORY_SEPARATOR . $folder_name;

	
	//Extract the temp zip file content to a new temp folder
	unarchive_tar($temp_file, $extract_folder);
	

	$files_list=[];
	//Loop files
	$filesize_new = $filesize_org = 0;
	foreach (glob($files_folder . DIRECTORY_SEPARATOR . "*.htm") as $filename) {		
		$filesize_org += filesize($filename);
		clearstatcache();
		
		$file_contents = file_get_contents($filename);
		$file_contents = rsys_minimize_html($file_contents);
		if( file_put_contents($filename, $file_contents) ){
			$files_list[] = $filename;
			
			$filesize_new += filesize($filename);
		};
	}
	
	$zip_files_path= create_zip($files_list, $extract_folder . DIRECTORY_SEPARATOR . $folder_name, true, 100);
	
	rmdir($extract_folder);
	
	return array(
		"files_path" => $zip_files_path,
		"files_list" => $files_list,
		"size" => $filesize_new,
		"size_org" => $filesize_org,
		"ratio" => number_format(100 - $filesize_new * 100 / $filesize_org, 2)
	);
}

?>