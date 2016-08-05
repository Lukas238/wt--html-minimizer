<?php


/*	INCLUDES
***********************/
include_once(dirname(__FILE__) .'/rsys_html_minimizer.php');


/* CONSTANTS
**********************/
define("DOMAIN", "http://localhost");

define("SITE_ROOT", "D:/Work/rsys-html-minifier/src");// LOCAL
//define("SITE_ROOT", "D:/Websites/wumndermanlab/www/dev/mmpot/minimizer");// DEV

define("SITE_URL", DOMAIN."/rsys-html-minifier/src");// LOCAL
//define("SITE_URL", "http://wundermanlab.com.ar/mmpot/dev/minimize");// DEV

define("UPLOAD_FOLDER", SITE_ROOT . "/temp");
define("DOWNLOAD_FOLDER", SITE_URL . "/temp");


/*	GLOBALS
**********************/
$feedback = [];


/*	FUNCTIONS
**********************/

/*	Output the html of the feedback messages */
function feedback($styles){
	global $feedback;
	
	$output = "";
	foreach( $feedback as $feed){
		$output .= '<p class="msg bg-'.$feed[0].'">'.$feed[1].'</p>';
	}
	echo '<div class="feedback '.$styles.'">' . $output . '</div>';
}


/* Unarchive tar files */
function unarchive_tar($file, $extract_folder){
	// unarchive from the tar
	$phar = new PharData($file);
	$phar->extractTo($extract_folder); 
	return true;
}


/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false, $limit_files = 100) {
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
		$zip_filesnames = array();
		foreach($valid_files as $file) {
			
			if( $i % $limit_files == 0 ){
				$zip = new ZipArchive();
				if($zip->open($destination."_".$file_num.".zip",$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
					return false;
				}				
			}
			
			$zip->addFile($file,basename($file)); //Add current file
			
			
			
			if( $i % $limit_files == ($limit_files -1) || count($valid_files)-1 == $i ){
				//close the zip -- done!
				$zip->close();
				
				$zip_filesnames[] = $destination."_".$file_num.".zip";
				$file_num++;
			}
			
			$i++;;
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//print_r(glob(dirname($files[0])."/*.*"));
		//echo dirname($files[0]);
		array_map('unlink', glob(dirname($files[0])."/*.htm") );
		rmdir(dirname($files[0]));
		
		
		//check to make sure the file exists
		return $zip_filesnames;
		
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

	$files_list=[];
	//Loop files
	$filesize_new = $filesize_org = 0;
	foreach (glob($files_folder ."/*.htm") as $filename) {
		
		
		$filesize_org += filesize($filename);
		clearstatcache();
		
		$file_contents = file_get_contents($filename);
		$file_contents = rsys_minimize_html($file_contents);
		if( file_put_contents($filename, $file_contents) ){
			$files_list[] = $filename;
			
			$filesize_new += filesize($filename);
		};
	}
	
	
	$zip_files_path= create_zip($files_list, $extract_folder .'/'. $folder_name, true, 100);
	$zip_files_url = [];
	foreach($zip_files_path as $file){
		$zip_files_url[] = $download_folder .'/'. basename($file);
	}
	
	
	return array(
		"files_path" => $zip_files_path,
		"files_url" => $zip_files_url,
		"files_list" => $files_list,
		"size" => $filesize_new,
		"size_org" => $filesize_org,
		"ratio" => number_format(100 - $filesize_new * 100 / $filesize_org, 2)
	);
}

?>