<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php init_tail(); ?>
<style type="text/css"><?php include FCPATH . 'assets/css/searches.css'; ?></style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php $this->load->helper('datatables_helper'); ?>
                        <div class="searches-page-header">
                            <div>
                                <h2 class="searches-page-title">Pending Searches</h2>
                                <p class="searches-page-sub">Review, note, and submit screening cases</p>
                            </div>
                            <?php if ( isset($total_rows) && $total_rows > 0 ): ?>
                                <div class="export-action">
                                    <a href="/admin/searches/export" class="btn btn-primary">Export to CSV</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php echo form_open('submit/search_form', array('id' => 'custom-search', 'method' => 'post')); ?>
                        <?php
                        $fd = array();
                        if ( isset($_GET['fdata']) && $_GET['fdata'] != '' ) {
                            $decode = base64_decode($_GET['fdata']);
                            $fd = json_decode( $decode, true );
                            
                        }
                        ?>
                        <div class="search-filter">
                            <div class="search-by">
                                <label>Search by</label>
                                <select name="search_by">
                                    <option value=""></option>
                                    <option value="search_id" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'search_id' ? 'selected' : ''); ?>>Search ID</option>
                                    <option value="name" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'name' ? 'selected' : ''); ?>>Name</option>
                                    <option value="state" <?php echo (isset($fd['search_by']) && $fd['search_by'] == 'state' ? 'selected' : ''); ?>>State</option>
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
                                <a href="/admin/searches" class="btn btn-secondary">Reset search result</a>
                            <?php endif; ?>
                        </div>
                        <?php echo form_close(); ?>

                        <?php if ( $duplicate_searches ): ?>
                            <div class="notification-search">
                                <div class="page-title">
                                    <h2>multiple entries with same entry found: <button class="btn btn-primary" id="load-more-duplicate-searches">Show</button></h2>
                                    <table class="duplicate-searches-found">
                                        <thead>
                                            <tr>
                                                <th colspan="" rowspan="" headers="" scope="">SSN</th>
                                                <th colspan="" rowspan="" headers="" scope="">Name</th>
                                                <th colspan="" rowspan="" headers="" scope="">Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ( $duplicate_searches as $key => $item ): ?>
                                            <tr>
                                                <td><?php echo $item['ssn']; ?></td>
                                                <td><?php echo $item['name']; ?></td>
                                                <td><?php echo $item['count']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open('admin/searches/update_search'); ?>
                            <div class="search-action text-center hide top-area">
                                <button type="submit" class="btn button btn-primary">Submit No Records</button>
                            </div>
                            <?php table_template(
                                [ '', 'Row ID', 'Search ID', 'Cases <br>Entered', 'Subject/AKAs', 'SSN', 'DOB', 'Search Type', 'City - State', 'County', 'Load Date', 'Sent Date', 'Client Notes', 'Internal Notes', 'AKA Names', 'sid'],
                                '',
                                'searches display', array(), array('id' => 'searches-list')
                            );?>
                            <div class="search-action text-center hide">
								<input type="hidden" name="submit-mode" value="no-record">
								<button type="submit" class="btn button btn-primary">Submit No Records</button>
							</div>
                        <?php echo form_close(); ?>
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

<?php if ( isset($_GET['error']) && $_GET['error'] != '' ): ?>
    <?php $error_id = $_GET['error']; ?>
    <?php $error = isset($errors[$error_id]) ? json_decode($errors[$error_id]) : ''; ?>
    <?php if ( $error != '' && isset($error->failed_updates) ): ?>
        <div class="submit-success">
            <div class="submit-success_content">
                <?php foreach($error->failed_updates as $idx => $error_value): ?>
                    <?php echo $error_value->error_message; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
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

<?php if ( isset($_GET['internal_notes']) && $_GET['internal_notes'] != '' ): ?>
    <?php $s_id = $_GET['sid']; ?>
    <div class="submit-success">
        <div class="submit-success_content note-success">
            <span class="message">An internal note has been submitted to Search ID <strong><?php echo $s_id; ?></strong>.</span>
            <span class="action"><a href="https://amazonescreening.justfortesting.xyz/admin/search/<?php echo $s_id; ?>">Go to internal notes</a></span>
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
    $('.menu-item-searches').addClass('active');

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

    function format(d, r) {
        // `d` is the original data object for the row
        var akas = '-';

        if ( d.aka_names != '' ) {
            akas = '<h3>Aliases:</h3>'+d.aka_names;
        }

        var publicView = '<div class="child-row-card">'+
        '<div class="note-button-area">'+
        '<button type="button" class="add-notes" data-sid="'+d.sid+'" data-row="'+d.row_id+'">Add notes</button>'+
        '<?php $form = form_open('admin/notes/add', array('class' => 'add_notes_form')); $modified_form = preg_replace('/\s+/', ' ', trim($form)); echo $modified_form; ?>'+
        '<div style="display:none"><input type="hidden" name="sid" value="'+d.sid+'"><input type="hidden" name="row" value="'+d.row_id+'"></div>'+
        '<div class="inp-group"><textarea name="note" placeholder="Write an internal note..."></textarea></div>'+
        '<div class="inp-group"><button type="submit">Submit note</button><button type="button" class="cancel-note">Cancel</button></div>'+
        '<?php echo form_close(); ?>'+
        '</div>'+
        '<div class="aliases-area">'+akas+'</div>'+
        '<table cellspacing="0" border="0" class="datatable-inner-table note-content">'+
            '<tr>'+
                '<td>Client notes</td>'+
                '<td>'+d.client_notes+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td>Internal notes</td>'+
                '<td>'+d.internal_notes+'</td>'+
            '</tr>'+
        '</table>'+
        '</div>';

        return publicView;

    }

    <?php if ( isset($_GET['search']) && isset($_GET['fdata']) && $_GET['fdata'] != '' ): ?>
        var fData = <?php echo base64_decode($_GET['fdata']); ?>;

        var myTable = $('#searches-list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "<?php echo admin_url('searches/get_data'); ?>",
                "data": function(d) {
                    d.cSearch = fData;
                },
                "type": "POST"
            },
            "columns": [
                { "data": "NF", "orderable": false },
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
                { "data": "client_notes", "visible": false },
                { "data": "internal_notes", "visible": false },
                { "data": "aka_names", "visible": false },
                { "data": "sid", "visible": false }
            ],
            "initComplete": function(settings, json) {
                $('#searches-list_wrapper').removeClass('table-loading');
            },
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, 250, 500, 1000, 1500, 2000], [10, 25, 50, 100, 250, 500, 1000, 1500, 2000]],
            "columnDefs": [ 
                {
                    "orderable": false,
                    "sortable": false,
                    "searchable": false,
                    "className": 'select-checkbox',
                    "targets":   0
                },
                {
                    "visible": false,
                    "searchable": false,
                    "targets":   1
                }
            ],
            bsort: false,
            order: [[ 4, 'desc' ]],
            rowCallback: function (r, d) {

                var tr = $(r);
                var row = myTable.row( tr );
                var childClasses = 'child';

                if ( d.cases_entered != '' ) {
                    tr.addClass('has-case');
                    childClasses = 'child child_has-case';
                }

                row.child( format(row.data(), r), childClasses ).show();
                tr.addClass('shown').attr('data-sid', d.sid);

            }
        });

    <?php else: ?>
        var myTable = $('#searches-list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "<?php echo admin_url('searches/get_data'); ?>",
                "type": "POST"
            },
            "columns": [
                { "data": "NF", "orderable": false },
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
                { "data": "client_notes", "visible": false },
                { "data": "internal_notes", "visible": false },
                { "data": "aka_names", "visible": false },
                { "data": "sid", "visible": false }
            ],
            "initComplete": function(settings, json) {
                $('#searches-list_wrapper').removeClass('table-loading');
            },
            "pageLength": 50,
            "lengthMenu": [[25, 50, 100, 250, 500, 1000, 1500, 2000], [25, 50, 100, 250, 500, 1000, 1500, 2000]],
            "columnDefs": [ 
                {
                    "orderable": false,
                    "sortable": false,
                    "searchable": false,
                    "className": 'select-checkbox',
                    "targets":   0
                },
                {
                    "visible": false,
                    "searchable": false,
                    "targets":   1
                }
            ],
            bsort: false,
            order: [[ 4, 'desc' ]],
            rowCallback: function (r, d) {

            	var tr = $(r);
		        var row = myTable.row( tr );
                var childClasses = 'child';

                if ( d.cases_entered != '' ) {
                    tr.addClass('has-case');
                    childClasses = 'child child_has-case';
                }

		        row.child( format(row.data(), r), childClasses ).show();
		        tr.addClass('shown').attr('data-sid', d.sid);

            }
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

    $(document).on('click', '.add-notes', function(e) {
        $(this).next('.add_notes_form').fadeIn();
        $(this).hide();
    });

    $(document).on('click', '.cancel-note', function(e) {
        e.preventDefault();
        var closestArea = $(this).closest('.note-button-area');
        closestArea.find('.add-notes').fadeIn();
        closestArea.find('.add_notes_form').hide().trigger("reset");

    });

    // show hide duplicate searches
    if ( jQuery('#load-more-duplicate-searches').length > 0 ) {
        jQuery('#load-more-duplicate-searches').on('click', function(e) {
            e.preventDefault();

            if ( jQuery(this).hasClass('active') ) {
                jQuery(this).text('Show').removeClass('active');
                jQuery('table.duplicate-searches-found').removeClass('active');
            } else {
                jQuery(this).text('Hide').addClass('active');
                jQuery('table.duplicate-searches-found').addClass('active');
            }

        });
    }

});
</script>
</body>
</html>