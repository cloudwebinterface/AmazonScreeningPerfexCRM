<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php init_tail(); ?>
<style type="text/css">
body.pulling {overflow: hidden;}
.pulling-data-progress {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999;
    background-color: rgba(255,255,255,0.8);
    width: 100%;
    display: none;
    align-items: center;
    justify-content: center;
}
.progress-content {
    font-size: 16px;
    font-weight: 500;
}
.update-data-wrapper {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #fff;
    z-index: 9;
}
#searches-list_processing {
    background-color: rgba(255,255,255,0.9) !important;
    top: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 9999;
    margin: 0 !important;
    width: 100%;
    font-size: 26px;
}
.submit-success {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.submit-success_content {
    background-color: #fff;
    padding: 30px 35px;
    border-radius: 6px;
}
.search-filter {
    border: 1px solid #ddd;
    width: 400px;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 30px;
    float: right;
}
.search-filter label {
    width: 100px;
}
.search-filter select, .search-filter [type="text"] {
    width: 260px;
    border-color: #ccc !important;
    height: 25px;
    font-size: 13px;
}
.search-filter > div {
    margin-bottom: 5px;
}
.search-filter .btn {
    float: right;
    margin-right: 5px;
}

.search-filter .by-name {
    display: none;
}
.filter-by-cases {
    margin-bottom: 10px;
}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php $this->load->helper('datatables_helper'); ?>
                        
                        <?php echo form_open('submit/search_history_form', array('id' => 'custom-search', 'method' => 'post')); ?>
                        <?php
                        $fd = array();
                        if ( isset($_GET['fdata']) && $_GET['fdata'] != '' ) {
                            $decode = base64_decode($_GET['fdata']);
                            $fd = json_decode( $decode, true );
                            
                        }
                        ?>
                        <div class="filter-by-cases">
                        	<label><input type="checkbox" name="have_cases" id="have-cases" <?php echo (isset($_GET['haveCases']) && $_GET['haveCases'] == 'yes' ? 'checked' : ''); ?>> Have at least one case</label>
                        </div>
                        <div class="search-filter">
                            <div class="search-by">
                                <label>Search by</label>
                                <select name="search_by">
                                    <option value=""></option>
                                    <option value="search_id" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'search_id' ? 'selected' : ''); ?>>Search ID</option>
                                    <option value="name" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'selected' : ''); ?>>Name</option>
                                </select>
                            </div>
                            <div class="search-input by-name" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'style="display:block;"' : 'style="display:none;"'); ?>>
                                <label>First name</label>
                                <input type="text" name="first_name" value="<?php echo (isset($fd['first_name']) ? $fd['first_name'] : ''); ?>" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? '' : 'disabled'); ?>>
                            </div>
                            <div class="search-input by-name" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'style="display:block;"' : 'style="display:none;"'); ?>>
                                <label>Middle name</label>
                                <input type="text" name="middle_name" value="<?php echo (isset($fd['middle_name']) ? $fd['middle_name'] : ''); ?>" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? '' : 'disabled'); ?>>
                            </div>
                            <div class="search-input by-name" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'style="display:block;"' : 'style="display:none;"'); ?>>
                                <label>Last name</label>
                                <input type="text" name="last_name" value="<?php echo (isset($fd['last_name']) ? $fd['last_name'] : ''); ?>" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? '' : 'disabled'); ?>>
                            </div>
                            <div class="search-input by-val" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'style="display:none;"' : 'style="display:block;"'); ?>>
                                <label>Search value</label>
                                <input type="text" name="search_value" value="<?php echo (isset($fd['search_value']) ? $fd['search_value'] : ''); ?>" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'disabled' : ''); ?>>
                            </div>
                            <input type="submit" value="Search" class="btn btn-primary">
                            <?php if ( isset($_GET['search']) && isset($_GET['fdata']) && $_GET['fdata'] != '' ): ?>
                                <a href="/admin/history/history_old" class="btn btn-secondary">Reset search result</a>
                            <?php endif; ?>
                        </div>
                        <?php echo form_close(); ?>

                        <?php table_template(
                            ['Row ID', 'Search ID', 'Cases <br>Entered', 'Subject/AKAs', 'SSN', 'DOB', 'Search Type', 'State County'],
                            '',
                            'searches display', array(), array('id' => 'searches-list')
                        );?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ( isset($_GET['submit_status']) && $_GET['submit_status'] == 'success' ): ?>
<div class="submit-success">
    <div class="submit-success_content">
        Search ID <strong><?php echo $_GET['sid'];?></strong> has been successfully submitted.
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){

        $('.submit-success').on('click', function() {
            $('.submit-success').remove();
        });

        setTimeout(function(){
            $('.submit-success').fadeOut();
        }, 4000);
        
    });
</script>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function() {

    $('.search-filter select').on('change', function() {
        var sValue = $(this).val();

        if ( sValue == 'name' ) {
            $('.search-input.by-name').show().find('input').prop("disabled", false);
            $('.search-input.by-val').hide().find('input').prop("disabled", true);
        } else {
            $('.search-input.by-name').hide().find('input').prop("disabled", true);
            $('.search-input.by-val').show().find('input').prop("disabled", false);
        }
    });

    $('#have-cases').on('change', function(e) {
    	e.preventDefault();
    	$(window).off('beforeunload');
    	var checkStatus = $(this).prop('checked');
    	if ( checkStatus === true ) {
    		window.location.href = '/admin/history/history_old?haveCases=yes';
    	} else {
    		window.location.href = '/admin/history/history_old';
    	}
    });

    <?php if ( isset($_GET['search']) && isset($_GET['fdata']) && $_GET['fdata'] != '' ): ?>
        var fData = <?php echo base64_decode($_GET['fdata']); ?>;

        var myTable = $('#searches-list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "<?php echo admin_url('history/get_old_data'); ?>",
                "data": function(d) {
                    d.cSearch = fData;
                },
                "type": "POST"
            },
            "columns": [
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

    <?php else: ?>
        var myTable = $('#searches-list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "<?php echo admin_url('history/get_old_data'); ?>",
                "type": "POST"<?php if ( isset($_GET['haveCases']) && $_GET['haveCases'] == 'yes' ) {
                	echo ', 
                	"data": function(d) {
                    d.haveCases = "yes";
                },';
                } ?>
            },
            "columns": [
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
    <?php endif; ?>

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
});
</script>
</body>
</html>