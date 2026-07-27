<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php init_tail(); ?>
<style type="text/css"><?php include FCPATH . 'assets/css/summary.css'; ?></style>
<div id="wrapper">

    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <div class="clearfix"></div>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="dt-loader hide"></div>
                        
                        <h3 style="margin-top:0;">Case Summary <a href="/submit/all_searches" class="btn btn-primary submit-all-cases" onclick="return confirm('Are you sure want to submit all entered cases?');">Submit all entered cases</a></h3>
                        <?php echo form_open('admin/summary/bulk_submit'); ?>
                            <?php table_template(
                                ['-', 'Row ID', 'Search ID', 'Cases <br>Entered', 'Subject/AKAs', 'SSN', 'DOB', 'Search Type', 'State County'],
                                '',
                                'searches display', array(), array('id' => 'searches-list')
                            );?>
                            <div class="search-action text-center hide">
                                <button type="submit" class="btn button btn-primary">Submit Selected Cases</button>
                            </div>  
                    	<?php echo form_close(); ?>
                    </div>  
                </div>
            </div>

        </div>
    </div>
</div>

<script>
var myTable = $('#searches-list').DataTable({
    "processing": true,
    "serverSide": true,
    "searching": false,
    "ajax": {
        "url": "<?php echo admin_url('summary/get_data'); ?>",
        "type": "POST"
    },
    "columns": [
        { 
            "data": "NF",
            "searchable": false,
            "orderable": false
        },
        { "data": "row_id", "orderable": false },
        { "data": "search_id" },
        { "data": "cases_entered", "orderable": false },
        { "data": "name", "orderable": false },
        { "data": "ssn", "orderable": false },
        { "data": "dob", "orderable": false },
        { "data": "search_type", "orderable": false },
        { "data": "state", "orderable": false }
    ],
    "initComplete": function(settings, json) {
        $('#searches-list_wrapper').removeClass('table-loading');
        if (json.recordsTotal !== 0) {
        	$('a.submit-all-cases').fadeIn();
        }
    },
    "pageLength": 25,
    "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
    "columnDefs": [ {
        "orderable": false,
        "className": 'select-checkbox',
        "targets":   0
    },
    {
        "visible": false,
        "searchable": false,
        "targets":   1
    } ],
    bsort: false,
    order: [[ 1, 'desc' ]]
});

myTable.on( 'click', 'td.select-checkbox input', function(e) {
    if($(this).prop("checked") == true){
        $(this).addClass('selected');
    } else if($(this).prop("checked") == false){
        $(this).removeClass('selected');
    }

    if ( $('td.select-checkbox input.selected').length > 0 ) {
        $('.search-action').fadeIn().removeClass('hide');
    } else {
        $('.search-action').fadeOut().addClass('hide');
    }
});
</script>
</body>
</html>
