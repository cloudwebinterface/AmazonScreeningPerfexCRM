<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php init_tail(); ?>
<style type="text/css"><?php include FCPATH . 'assets/css/history.css'; ?></style>
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

                        $display_field = array(
                            'search_id' => array( 'search_value' ),
                            'name'      => array( 'first_name', 'middle_name', 'last_name' ),
                            'sent_date' => array( 'sent_date_from', 'sent_date_to' )
                        );
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
                                    <option value="sent_date" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'sent_date' ? 'selected' : ''); ?>>Sent Date</option>
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
                            <div class="search-input by-search_id" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'search_id' ? 'style="display:block;"' : 'style="display:none;"'); ?>>
                                <label>Search value</label>
                                <input type="text" name="search_value" value="<?php echo (isset($fd['search_value']) ? $fd['search_value'] : ''); ?>" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'disabled' : ''); ?>>
                            </div>
                            <div class="search-input by-sent_date" <?php echo ( isset($fd['search_by']) && $fd['search_by'] == 'sent_date' ? 'style="display:block;"' : 'style="display:none;"' ); ?>>
                                <label>From</label>
                                <input type="text" name="sent_date_from" class="datepicker" value="<?php echo (isset($fd['sent_date_from']) ? $fd['sent_date_from'] : ''); ?>" <?php echo ( isset( $fd['search_by'] ) && isset($display_field[$fd['search_by']]) && in_array( 'sent_date_from', $display_field[$fd['search_by']] ) ? '' : 'disabled'); ?>>
                            </div>
                            <div class="search-input by-sent_date" <?php echo ( isset($fd['search_by']) && $fd['search_by'] == 'sent_date' ? 'style="display:block;"' : 'style="display:none;"' ); ?>>
                                <label>To</label>
                                <input type="text" name="sent_date_to" class="datepicker" value="<?php echo (isset($fd['sent_date_to']) ? $fd['sent_date_to'] : ''); ?>" <?php echo ( isset( $fd['search_by'] ) && isset($display_field[$fd['search_by']]) && in_array( 'sent_date_from', $display_field[$fd['search_by']] ) ? '' : 'disabled'); ?>>
                            </div>

                            <input type="submit" value="Search" class="btn btn-primary">
                            <?php if ( isset($_GET['search']) && isset($_GET['fdata']) && $_GET['fdata'] != '' ): ?>
                                <a href="/admin/history" class="btn btn-secondary">Reset search result</a>
                            <?php endif; ?>
                        </div>
                        <?php echo form_close(); ?>

                        <?php table_template(
                            ['Row ID', 'Search ID', 'Cases <br>Entered', 'Subject/AKAs', 'SSN', 'DOB', 'Search Type', 'City - State', 'County', 'Load Date', 'Sent Date', 'Status'],
                            '',
                            'searches display', array(), array('id' => 'searches-list')
                        );?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $('.search-filter select').on('change', function() {
        var sValue = $(this).val();
        var display_field = <?php echo json_encode($display_field); ?>;

        $('.search-input').hide().find('input').prop("disabled", true);

        if ( sValue == '' ) {
            $('.search-input.by-search_id').show().find('input').prop("disabled", false);
        } else {
            $('.search-input.by-'+sValue).show();
            display_field[sValue].forEach(function(index, el) {
                $('.search-input input[name="'+index+'"]').show().prop("disabled", false);
            });
        }
    });

    $('#have-cases').on('change', function(e) {
        e.preventDefault();
        $(window).off('beforeunload');
        var checkStatus = $(this).prop('checked');
        if ( checkStatus === true ) {
            window.location.href = '/admin/history?haveCases=yes';
        } else {
            window.location.href = '/admin/history';
        }
    });

    <?php if ( isset($_GET['search']) && isset($_GET['fdata']) && $_GET['fdata'] != '' ): ?>
        var fData = <?php echo base64_decode($_GET['fdata']); ?>;

        var myTable = $('#searches-list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "<?php echo admin_url('history/get_data'); ?>",
                "data": function(d) {
                    d.cSearch = fData;
                },
                "type": "POST"
            },
            "columns": [
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
                { "data": "status", "orderable": false }
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
                "url": "<?php echo admin_url('history/get_data'); ?>",
                "type": "POST"<?php if ( isset($_GET['haveCases']) && $_GET['haveCases'] == 'yes' ) {
                    echo ', 
                    "data": function(d) {
                    d.haveCases = "yes";
                },';
                } ?>
            },
            "columns": [
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
                { "data": "status", "orderable": false }

                /*{ "data": "row_id", "orderable": false },
                { "data": "search_id", "orderable": false },
                { "data": "cases_entered", "orderable": false },
                { "data": "name", "orderable": false },
                { "data": "ssn", "orderable": false },
                { "data": "dob", "orderable": false },
                { "data": "search_type", "orderable": false },
                { "data": "state", "orderable": false },
                { "data": "load_date", "orderable": false },
                { "data": "sent_date", "orderable": false },
                { "data": "client_notes", "visible": false },
                { "data": "internal_notes", "visible": false },
                { "data": "aka_names", "visible": false },
                { "data": "sid", "visible": false }*/
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