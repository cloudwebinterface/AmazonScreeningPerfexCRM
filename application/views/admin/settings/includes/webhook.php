<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
   <div class="col-md-12">
		<h2>Register webhook</h2>
		<div class="reg-webhook-form">
			<div class="form-group">
				<label class="control-label">New</label>
				<input type="text" id="settings[webhook_new]" name="settings[webhook_new]" class="form-control" value="<?php echo get_option('webhook_new'); ?>">
			</div>
			<div class="form-group">
				<label class="control-label">Modified</label>
				<input type="text" id="settings[webhook_modified]" name="settings[webhook_modified]" class="form-control" value="<?php echo get_option('webhook_modified'); ?>">
			</div>
			<div class="form-group">
				<label class="control-label">Canceled</label>
				<input type="text" id="settings[webhook_canceled]" name="settings[webhook_canceled]" class="form-control" value="<?php echo get_option('webhook_canceled'); ?>">
			</div>
		</div>
		response: <?php echo get_option('register_webhook_response'); ?>
		<br>
		url: <?php echo site_url(); ?><?php echo get_option('webhook_new'); ?>
		<br>
		received: <?php echo get_option('received_webhooks_new'); ?>
		<br>
		received post: <?php echo get_option('received_webhooks_new_post'); ?>
		<br>
		Token: <?php
   $this->load->helper('api_helper');
   echo generate_api_token(); ?>
   </div>
   
</div>

