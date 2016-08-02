<?php
session_start();

include_once(dirname(__FILE__) .'/inc/functions.php');


$action = false;
if (isset($_POST['action']) && $_POST['action'] != ""){
	$action = $_POST['action'];
}


// Minifice and download zip file
if( $action == "batch_tar" && isset($_FILES)){
	$zip_file_server = minimize_batch_tar();	
	
	header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=" . basename($_SESSION["download_zip"]));
    header("Content-Length: " . filesize($zip_file_server));
    readfile($zip_file_server);
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
	</style>
</head>
<body class="container-fluid">


	<div id="wrapper" class="row">
		<header id="header" class="col-sm-8 col-sm-offset-2">
			<h1>MMP HTML Minimizer Tool</h1>
		</header>
		<main id="main" class="col-sm-8 col-sm-offset-2">
			<div id="content">
			
			
				<form id="form" class="form" action="index.php" method="POST" enctype="multipart/form-data">
					<h3>Batch minimizer for tar files</h3>
					<div class="form-group">
						<input type="file" id="frm-tar" name="frm_tar">
						<input type="hidden" name="action" value="batch_tar">
					</div>
					<button id="btn-batch" class="btn btn-primary" type="submit" name="submit">Batch minimize</button>
					
					<?php
					if($action=="batch_tar"){
					?>
					<a href="<?php echo $_SESSION["download_zip"]; ?>">Download minimized result</a>
					<?php
					}
					?>
					
					
					<hr>
					<h3>HTML code minimize</h3>
					<div class="form-group">
						<textarea id="frm-source" cols="90" rows="10"></textarea>
					</div>
					<button id="btn-minimize" class="btn btn-primary" type="button">Minimize</button>
					
				
				</form><!-- /#form -->
				
				<div id="results" class="visible">
					<h4>Results</h4>
					<textarea id="frm-output" cols="90" rows="10"></textarea>
				</div>
				
				
			</div><!-- /#content -->
		</main>
	</div>
	
	
	
	<!-- SCRIPTS -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script>
		
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