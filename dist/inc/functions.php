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
function unarchive($file, $extract_folder, $file_ext){
	//Extract the temp  compressed file content to a new temp folder
	switch( $file_ext ){
		case 'tar':
			$phar = new PharData($file);
			$phar->extractTo($extract_folder); 
			return true;
			break;
			
		default: // ZIP
			$zip = new ZipArchive;
			if ($zip->open($file) === TRUE) {
				$zip->extractTo($extract_folder);
				$zip->close();
				return true;
			} else {
				return false;
			}
	}	
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
			
			$i++;
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//print_r(glob(dirname($files[0])."/*.*"));
		//echo dirname($files[0]);
		
		$glog_string = dirname($files[0]). DIRECTORY_SEPARATOR ."*.{htm,html}";
		array_map('unlink', glob($glog_string, GLOB_BRACE) );
		rmdir(dirname($files[0]));
	
		
		//check to make sure the file exists
		return $zip_parts;
		
	}else{
		return false;
	}
}



/*	Minimize batch html files, from the exported tar file from the CMS
*/
function minimize_batch_tar(){

	$temp_file      = $_FILES['frm_tar']['tmp_name'];
	$extract_folder = UPLOAD_FOLDER . DIRECTORY_SEPARATOR . basename($temp_file, ".tmp");
	$download_url   = DOWNLOAD_URL . "/" . basename($temp_file, ".tmp");
	
	$path_info      = pathinfo($_FILES['frm_tar']['name']);
	$folder_name    = $path_info['filename'];
	$file_ext       = $path_info['extension'];
	$files_folder   = $extract_folder . ($file_ext == 'tar' ?  DIRECTORY_SEPARATOR . $folder_name : '') ;
	
	
	unarchive($temp_file, $files_folder, $file_ext);
	

	$files_list=[];
	//Loop files
	$filesize_new = $filesize_org = 0;
	$glog_string = $files_folder . DIRECTORY_SEPARATOR . "*.{htm,html}";
		
	foreach (glob($glog_string, GLOB_BRACE) as $filename) {	
		$filesize_org += filesize($filename);
		clearstatcache();
		
		$file_contents = file_get_contents($filename);
		$file_contents = rsys_minimize_html($file_contents);

		if( file_put_contents($filename, $file_contents) ){
			$files_list[] = $filename;
			
			$filesize_new += filesize($filename);
		};
	}
		
	$zip_files_path= create_zip($files_list, $files_folder . DIRECTORY_SEPARATOR . $folder_name, true, 100);
	
	if( $file_ext  != 'zip' ){
		rmdir($files_folder);
	}

	$ratio_val = 0;
	if( $filesize_org >0){
		$ratio_val = 100 - $filesize_new * 100 / $filesize_org;
	}
	return array(
		"files_path" => $zip_files_path,
		"files_list" => $files_list,
		"size" => $filesize_new,
		"size_org" => $filesize_org,
		"ratio" => number_format($ratio_val, 2)
	);
}

?>