<?php
include_once(realpath( __DIR__ . "\inc\config.php"));

wl_add_security();


include_once(__DIR__ .'/inc/functions.php');

$action = false;
if (isset($_POST['action']) && $_POST['action'] != ""){
	$action = $_POST['action'];
}


// Minifice and download zip file

if( $action == "batch_tar" && isset($_FILES)){
	
	$ext = pathinfo($_FILES['frm_tar']['name'], PATHINFO_EXTENSION);
	
	if( $ext == "tar" || $ext == "zip"){
		
		$minimize_results = minimize_batch_tar();
		
		$minimize_results["files_path"];
		
		//print_r($minimize_results["files_path"]);// DEBUG
			
		wl_add_feedback([
			'type'		=>	'success',
			'message'	=>	'Minification successful. '.$minimize_results['ratio'].'% compressed.'
		]);
	}else{
		wl_add_feedback([
			'type'		=>	'warning',
			'message'	=>	'Only files with <strong>.tar</strong> extension are allowed.'
		]);
	}
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>MMP HTML Minimizer Tool</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link type="text/plain" rel="author" href="humans.txt">

	<!-- STYLES -->
	<link rel="stylesheet" href="https://bootswatch.com/3/paper/bootstrap.min.css">
	<?php wl_admin_css(); ?>
	<link rel="stylesheet" href="<?php echo DOMAIN; ?>/admin/css/wl_login.css">
	<style>
		#main{
			padding-top: 65px;
		}
		
		textarea{
			max-width: 100%;
		}
		@media screen and (max-width: 480px){
			.btn-mobile{
				width: 100%;
				height: 44px;
			}
		}
	</style>
	
</head>
<body class="container-fluid">
	
	<?php include_once("inc/analyticstracking.php") ?>

	<div id="wrapper">
		
		<header id="header">
			<?php
				echo wl_main_menu([
					'title'			=>	'HTML Minifier',
					'logo'			=>	'images/logo.png',
					'logo_title'	=>	'Where we pressure cook the HTML!'
				]);
			?>
		</header>
		
		
		<main id="main" class="row">
			<?php wl_feedback(['styles' => 'col-sm-8 col-sm-offset-2']); ?>

			<div id="content" class="col-sm-8 col-sm-offset-2">
			
			
				<form id="form" class="form" action="index.php" method="POST" enctype="multipart/form-data">
				
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tab-batch" aria-controls="home" role="tab" data-toggle="tab">Batch</a></li>
						<li role="presentation"><a href="#tab-direct-input" aria-controls="profile" role="tab" data-toggle="tab">Direct Input</a></li>
						<li role="presentation" class="pull-right"><a href="#tab-help" aria-controls="messages" role="tab" data-toggle="tab">Help</a></li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="tab-batch">
							<h3>Batch minimizer</h3>
							
							
							<?php
							if( isset($minimize_results["files_path"]) ){
							?>
								<h4>Download minimized zip files</h4>
								<ul>
									<?php
									foreach($minimize_results["files_path"] as $key => $file_data ){
										
										$file_name = EXPORT_FILE_NAME . $key . ".zip";
									?>
									<li><a href="<?php echo $file_data; ?>" download="<?php echo $file_name; ?>"><?php echo $file_name; ?></a></li>
									<?php					
									}
									?>
								</ul>
							<?php					
								
							?>
							
							
							<a href="index.php" class="btn btn-primary">Reset</a>
							<?php
							}else{
							?>
							<div class="form-group col-sm-8">
								<input type="file" id="frm-tar" name="frm_tar" class="">
								<input type="hidden" name="action" value="batch_tar">
								<p class="text-muted">Limit the number of files to <?php echo FILES_PER_ZIP;?> files in each zip file.</p>
							</div>
							<div class="form-group col-sm-4">
								<button id="btn-batch" class="btn btn-primary btn-mobile" type="submit" name="submit">Upload and minimize</button>
							</div>
							<div class="form-group col-sm-12">
								<p>Valid files:</p>
								<ul>
									<li>.tar files exported by Responsys</li>
									<li>.zip files with .htm or .html files inside.</li>
								</ul>
							</div>
							
							<?php
							}
							?>
							
							<hr>
							
							
						</div>
						<div role="tabpanel" class="tab-pane" id="tab-direct-input">
						
							<h3>Direct input minimizer</h3>
							<div class="form-group">
								<textarea id="frm-source" cols="90" rows="10"></textarea>
							</div>
							<button id="btn-minimize" class="btn btn-primary" type="button">Minimize</button>
							
							<div id="results" class="visible">
								<h4>Results</h4>
								<textarea id="frm-output" cols="90" rows="10"></textarea>
							</div>
							
						</div>
						<div role="tabpanel" class="tab-pane" id="tab-help">
							<h3>What this tool does?</h3>
							
							<ol>
								<li>Compress all HTML code in a single line.</li>
								<li>Removes all tab characters.</li>
								<li>Remove all HTML comments and content, with some exceptions:
									<ul>
										<li>RSYS function inside comments.</li>
										<li>IE conditional comment.</li>
										<li><em>Special comment</em>, starting with two asteriscs.
										<br />
										Ex.:&lt;!--** Keep this comment! --&gt;</li>
										<li>Empty comments, since they are used to target Outlook.
										<br />
										Ex.:&lt;!-- --&gt;</li>
									</ul>
								</li>
								<li>Remove white spaces before and after the folowing tags: &lt;html&gt;, &lt;body&gt;, &lt;head&gt;, &lt;meta&gt;, &lt;style&gt;, &lt;table&gt;, &lt;tr&gt;, and &lt;td&gt;.</li>
								<li>Removes spaces between attributes in the tags, except in the &lt;img&gt; tag (RSYS breaks image attribute src there is no spaces before or after it).</li>
								<li>Convert multiple consecutive white spaces into a single white space.</li>
							</ol>
						
						</div>
					</div>
				
					
					
				
				</form><!-- /#form -->
				
				
				
				
			</div><!-- /#content -->
		</main>
	</div>
	
	
	
	<!-- SCRIPTS -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="//rawgit.com/Lukas238/better-input-file/master/src/betterInputFileButton.js"></script>
	<script>
		$('input:file').betterInputFile({
			'btnClass': 'btn btn-secondary',
			'placeholder': '  No file selected'
		});
		
		$('#btn-minimize').on('click', function(){
			
			$('#frm-output').val('');
			var $data = $('#frm-source').val();
			
			$.ajax({
				url:"inc/minimize.php",
				type:"post",
				dataType:"html",
				data:{"html_code": $data},
				success:function(data){
					$('#frm-output').val(data);
				}
			  });
			
		});
	
	</script>

	
</body>
</html>