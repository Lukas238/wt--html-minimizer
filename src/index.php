<?php
session_start();

include_once(dirname(__FILE__) .'/inc/functions.php');


$action = false;
if (isset($_POST['action']) && $_POST['action'] != ""){
	$action = $_POST['action'];
}


// Minifice and download zip file

if( $action == "batch_tar" && isset($_FILES)){
	
	$ext = pathinfo($_FILES['frm_tar']['name'], PATHINFO_EXTENSION);
	
	if( $ext == "tar"){
		$zip_file_server = minimize_batch_tar();	
		/*
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($_SESSION["download_zip"]));
		header("Content-Length: " . filesize($zip_file_server));
		readfile($zip_file_server);
		*/		
		$feedback[] = array('success', 'Minification successful. <a href="'. $_SESSION["download_zip"] .'">Download minimized .zip</a>.');
	}else{
		$feedback[] = array('warning', 'Only files with <strong>.tar</strong> extension are allowed.');
	}
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>MMP HTML Minimizer Tool</title>
	
	<!-- STYLES -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<style>
		html, body{
			margin: 0;
			padding: 0;
			height: 100%;
		}
		/* HELPERS */
		.feedback .msg{
			padding: 15px;
		}
		
		/* STYLES */
		#results{
			margin-top: 1.5em;
		}
		body.loading{
			position: relative;
		}
		body.loading:before{
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			display: block;
			width: 100%;
			height: 100%;
			background-color: rgba(255, 255, 0, .3);
			z-index: 10;
		}
		body.loading:after{
			content: "Minimizing...";
			position: absolute;
			top: 50%;
			left: 0;
			display: block;
			width: 100%;
			height: 24px;
			margin-top: -12px;
			line-height: 24px;
			text-align: center;
			z-index: 11;
		}
		#tab-direct-input textarea{
			width: 100%;
			max-width: 100%;
		}
	</style>
</head>
<body class="container-fluid">


	<div id="wrapper" class="row">
		
		<?php feedback('col-sm-8 col-sm-offset-2'); ?>
	
		<header id="header" class="col-sm-8 col-sm-offset-2">
			<h1>MMP HTML Minimizer Tool</h1>
		</header>
		<main id="main" class="col-sm-8 col-sm-offset-2">
			<div id="content">
			
			
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
							if( isset($_SESSION["download_zip"]) ){
							?>
							<a href="<?php echo $_SESSION["download_zip"]; ?>" class="btn btn-success">Download minimized result</a>
							<a href="index.php" class="btn btn-primary">Reset</a>
							<?php
							}else{
							?>
							<div class="form-group col-sm-8">
								<input type="file" id="frm-tar" name="frm_tar" class="">
								<input type="hidden" name="action" value="batch_tar">
							</div>
							<button id="btn-batch" class="btn btn-primary" type="submit" name="submit">Upload and minimize</button>
							<?php
							}
							?>
							
							<hr>
							
							<p>Upload the archives .tar file exported by the CMS.</p>
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
			'btnClass': 'btn btn-secondary'
		});
		
		$('#btn-minimize').on('click', function(){
			
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
<?php
	session_unset(); 
	session_destroy(); 
?>