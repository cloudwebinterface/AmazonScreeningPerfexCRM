<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php init_tail(); ?>
<style type="text/css" media="screen">
pre {
    max-height: 100vh;
}
.logs-container > div {
    display: inline-block;
    vertical-align: top;
}
.logs-menu {
    width: 100%;
    max-width: 15%;
}
.logs-content {
    width: 100%;
    max-width: 84%;
}

.logs-menu li a {
    display: block;
    border: 1px solid;
    line-height: 1.5;
    padding: 2px 5px;
    margin-bottom: 10px;
    margin-right: 15px;
    text-align: center;
}

.logs-menu {
    width: 100%;
    max-width: 15%;
    padding-top: 60px;
}

.logs-menu li.active a {
    background-color: #008ece;
    color: #fff;
    border-color: #008ece;
}
pre > span {
    display: block;
    border: 1px solid #d0d0d0;
    margin-left: 50px;
    margin-top: 10px;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 5px;
    background-color: #fff;
}
pre > i {
    font-weight: bold;
    background-color: green;
    color: #fff;
    padding: 2px 5px;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">

						<div class="logs-container">
							<div class="logs-menu">
								<ul>
									<li <?php echo (isset($_GET['tab']) ? ($_GET['tab'] == 'error' ? 'class="active"' : '' ) : 'class="active"' ); ?>><a href="/admin/logs?tab=error">Error logs</a></li>
									<li <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'success' ? 'class="active"' : '' ); ?>><a href="/admin/logs?tab=success">Success logs</a></li>
								</ul>	
							</div>
							<div class="logs-content">
								<div id="error" class="logs-main-content" <?php echo (isset($_GET['tab']) ? ($_GET['tab'] == 'error' ? '' : 'style="display: none;"' ) : '' ); ?>>
									<h3>Error logs</h3>
									<pre><?php
									if ( $logs ) {
										foreach ($logs as $time => $log) {
											echo '<i>' . date('F j, Y - g:i a', $time) . '</i> => <span>' . json_encode(json_decode( $log ), JSON_PRETTY_PRINT) . '</span>';
											echo '<br>';
										}
									}
									?></pre>
									
								</div>
								<div id="success" class="logs-main-content" <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'success' ? '' : 'style="display: none;"' ); ?>>
									<h3>Success logs</h3>
									<pre><?php
									if ( $success_logs ) {
										foreach ($success_logs as $time => $log) {
											echo '<i>' . date('F j, Y - g:i a', $time) . '</i> => <span>' . json_encode(json_decode( $log ), JSON_PRETTY_PRINT) . '</span>';
											echo '<br>';
										}
									}
									?></pre>
									
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>