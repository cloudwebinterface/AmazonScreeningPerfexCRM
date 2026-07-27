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
                        
                        <h3 style="margin-top:0;text-align: center;margin-bottom: 40px;font-weight: bold;text-transform: uppercase;">Reports</h3>

                        <div class="reports-filter col-md-6 col-md-offset-3">
                        	<form method="get" action="/admin/report">
                        		<input type="hidden" name="set-report" value="1">
								<div class="form-group col-md-6">
									<label for="date-from">From</label>
									<input type="text" name="date-from" class="form-control datepicker" value="<?php echo (isset($_GET['date-from']) ? $_GET['date-from'] : ''); ?>" required>
								</div>
								<div class="form-group col-md-6">
									<label for="date-to">To</label>
									<input type="text" name="date-to" class="form-control datepicker" value="<?php echo (isset($_GET['date-to']) ? $_GET['date-to'] : ''); ?>">
								</div>
								<div class="col-md-12"><button type="submit" class="btn btn-primary">Search</button></div>
								
							</form>
                        </div>
                        <div class="clearfix"></div>

                        <div class="order-activity">
                        	<div class="oa-header">
                        		<?php if ( date('F jS, Y', $date_from) == date('F jS, Y', $date_to) ): ?>
                        			<h3 style="text-align: center;">Order Activity: Today</h3>
                        		<?php else: ?>
                        			<h3 style="text-align: center;">Order Activity: <?php echo date('F jS, Y', $date_from); ?> - <?php echo date('F jS, Y', $date_to); ?></h3>
                        		<?php endif; ?>
                        	</div>
                        	<div class="oa-content">
                        		<table>
                        			<thead>
                        				<tr>
                        					<th width="300">Researcher</th>
                        					<th width="100">Status</th>
                        					<th width="100">Volume</th>
                        				</tr>
                        			</thead>
                        			<tbody>
                        				<tr>
                        					<td>PETER PUMA ENTERPRISES INC</td>
                        					<td>Found</td>
                        					<td><?php echo $found; ?></td>
                        				</tr>
                        				<tr>
                        					<td>PETER PUMA ENTERPRISES INC</td>
                        					<td>Not Found</td>
                        					<td><?php echo $notfound; ?></td>
                        				</tr>
                        				<tr>
                        					<td>PETER PUMA ENTERPRISES INC</td>
                        					<td>Canceled</td>
                        					<td><?php echo $canceled; ?></td>
                        				</tr>
                        				<tr class="volume-total">
                        					<td colspan="2">Total</td>
                        					<td><?php echo $total; ?></td>
                        				</tr>
                        			</tbody>
                        		</table>
                        	</div>
                        </div>

                        <?php table_template([
                        	'Row ID', 
                        	'Search ID', 
                        	'Cases <br>Entered', 
                        	'Subject/AKAs', 
                        	'SSN', 
                        	'DOB', 
                        	'Search Type', 
                        	'City - State', 
                        	'County', 
                        	'Load Date', 
                        	'Sent Date', 
                        	'Status'],
                            '',
                            'searches display', array(), array('id' => 'searches-list')
                        );?>

                        <script type="text/javascript">
                        	$(document).ready(function() {
							    $('#searches-list').DataTable( {
							        "processing": true,
						            "serverSide": true,
						            "searching": false,
						            "ajax": {
						                "url": "<?php echo admin_url('report/get_data'); ?>",
						                "type": "POST",
						                "data": function(d) {
						                    d.dateFrom 	= '<?php echo $date_from; ?>';
						                    d.dateTo  	= '<?php echo $date_to; ?>';
						                }
						            },
							        columns: [
							            { "data": "row_id", "orderable": false },
						                { "data": "search_id", "orderable": false },
						                { "data": "cases_entered", "orderable": false },
						                { "data": "name", "orderable": false },
						                { "data": "ssn", "orderable": false },
						                { "data": "dob", "orderable": false },
						                { "data": "search_type", "orderable": false },
						                { "data": "state", "orderable": false },
						                { "data": "county", "orderable": false },
						                { "data": "load_date", "orderable": false },
						                { "data": "sent_date", "orderable": false },
						                { "data": "status", "orderable": true }
							        ],
						            initComplete: function(settings, json) {
										$('#searches-list_wrapper').removeClass('table-loading');
									},
									"pageLength": 25,
								    "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
								    "columnDefs": [
								    {
								        "visible": false,
								        "searchable": false,
								        "targets":   0
								    } ],
								    bsort: false,
								    //order: [[ 1, 'desc' ]]
								} );
							} );
                        </script>	

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
