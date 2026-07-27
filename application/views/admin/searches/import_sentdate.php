<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style><?php include FCPATH . 'assets/css/search-detail.css'; ?></style>
<?php init_tail(); ?>
<div id="wrapper">

    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <div class="clearfix"></div>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    	
                    	<div class="dt-loader hide"></div>
                        
                        <h3 style="margin-top:0;text-align: center;margin-bottom: 0px;font-weight: bold;text-transform: uppercase;">Import Sent Date</h3>
                        <p style="text-align: center;margin-bottom: 40px;">From logs to searches table</p>

                        <?php if ( $from != '' && $to != '' ): ?>
                        	test
                        <?php else: ?>
	                        <div class="reports-filter col-md-6 col-md-offset-3">
	                        	<?php echo form_open('admin/report/import_sent_date_process'); ?>
									<div class="form-group col-md-6">
										<label for="date-from">From</label>
										<input type="text" name="date-from" class="form-control datepicker" value="<?php echo (isset($_GET['date-from']) ? $_GET['date-from'] : ''); ?>" required>
									</div>
									<div class="form-group col-md-6">
										<label for="date-to">To</label>
										<input type="text" name="date-to" class="form-control datepicker" value="<?php echo (isset($_GET['date-to']) ? $_GET['date-to'] : ''); ?>" required>
									</div>
									<div class="col-md-12"><button type="submit" class="btn btn-primary">Search</button></div>
									
								<?php echo form_close(); ?>
	                        </div>
	                    <?php endif; ?>

                        <div class="clearfix"></div>

                    </div>  
                </div>
            </div>

        </div>
    </div>
</div>
<style type="text/css">
	.reports-filter {
	    border: 1px solid #ccc;
	    padding: 15px 20px;
	    border-radius: 4px;
	    margin-bottom: 30px;
	}
	#searches-list_wrapper {
	    display: inline-block;
	    width: 100%;
	}
	.reports-action {
	    padding: 10px 0px 30px 0;
	}
	table.dataTable thead .sorting::after {
	  opacity: 1;
	  display: flex;
	  align-items: center;
	}
	.oa-content table {
		margin: 25px auto 50px;
	}
	.oa-content th {
		border-left: 1px solid #aaa;
		border-bottom: 1px solid #aaa;
		border-top: 1px solid #aaa;
		text-align: center;
		padding: 5px 10px;
		text-transform: uppercase;
		font-weight: bold;
		font-family: 'open sans', sans-serif;
		font-size: 14px;
	}
	.oa-content th:last-child {
		border-right: 1px solid #aaa;
	}
	.oa-content td {
		border-bottom: 1px solid #aaa;
		border-left: 1px solid #aaa;
		padding: 2px 10px;
		font-size: 12px;
	}
	.oa-content td:last-child {
		border-right: 1px solid #aaa;
		background-color: #eee;
		font-weight: bold;
		text-align: center;
	}
	.oa-content td:not(:first-child) {
		text-align: center;
		text-transform: uppercase;
	}
	.oa-content .volume-total td:first-child {
		text-align: right;
	}
</style>
</body>
</html>
