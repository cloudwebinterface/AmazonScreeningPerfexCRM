<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="/favicon.ico">

		<title><?php echo $title; ?></title>

		<link href="<?php echo site_url( 'assets/css/bs-overides.min.css' ); ?>" rel="stylesheet">
		<link href="<?php echo site_url( 'assets/css/style.min.css' ); ?>" rel="stylesheet">
		<link href="<?php echo site_url( 'assets/css/custom.css' ); ?>" rel="stylesheet">
		<style type="text/css">
			
		</style>
	</head>
	<body>
		<div id="header">
			<div id="logo">
				<a href="<?php echo admin_url(); ?>" class="logo img-responsive">
				<img src="<?php echo site_url( 'uploads/company/logo.png' ); ?>" class="img-responsive" alt="AmazonScreening">
				</a>   
			</div>
			<nav>
				<div class="small-logo">
					<span class="text-primary">
						<a href="<?php echo admin_url(); ?>" class="logo img-responsive">
							<img src="<?php echo site_url( 'uploads/company/logo.png' ); ?>" class="img-responsive" alt="AmazonScreening">
						</a>         
					</span>
				</div>
			</nav>
		</div>
		<div class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s">
						<div class="panel-body">
							<div class="load">
								<h2 style="text-align: center;">Please wait, we are loading fresh data</h2>
								<img src="<?php echo site_url('assets/images/ajax-loader.gif') ?>" alt="loading data" style="margin: 0 auto; display: block;">
							</div>	
						</div>
					</div>
				</div>		
			</div>	
		</div>
	</body>
</html>
