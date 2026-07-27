<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                        
                        <h3 style="margin-top:0;">Invoices <a href="/admin/invoice/new" class="btn btn-primary">Create new invoice</a></h3>

                            <?php table_template(
                                ['-', 'Invoice Number', 'Customer Name / Company', 'Address', 'Search IDs', 'Created Date', 'Due Date', 'Total Bill', 'Actions'],
                                '',
                                'searches display', array(), array('id' => 'invoices-list')
                            );?>
                    
                    </div>  
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>

<script>
	var myTable = $('#invoices-list').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?php echo admin_url('invoice/get_data'); ?>",
            "type": "POST"<?php if ( isset($_GET['haveCases']) && $_GET['haveCases'] == 'yes' ) {
                echo ', 
                "data": function(d) {
                d.haveCases = "yes";
            },';
            } ?>
        },
        "columns": [
            { "data": "id", "orderable": false },
            { "data": "inv_ID", "orderable": false },
            { "data": "cust_name", "orderable": false },
            { "data": "cust_address", "orderable": false },
            { "data": "items", "orderable": false },
            { "data": "created_date", "orderable": false },
            { "data": "due_date", "orderable": false },
            { "data": "total_bill", "orderable": false },
            { "data": "action", "orderable": false }
        ],
        "initComplete": function(settings, json) {
            $('#invoices-list_wrapper').removeClass('table-loading');
        },
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        "columnDefs": [
            {
                "visible": false,
                "searchable": false,
                "targets":   0
            } 
        ],
        bsort: false,
        order: [[ 1, 'desc' ]]
    });
</script>