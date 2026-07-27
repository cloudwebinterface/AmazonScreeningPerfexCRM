<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (nj_court_search_staff_can('create')) { ?>
                                <a href="<?php echo admin_url('nj_court_search/create'); ?>" class="btn btn-info pull-left display-block">
                                    <?php echo _l('nj_court_search_new_search'); ?>
                                </a>
                            <?php } ?>
                            <?php if (nj_court_search_staff_can('manage_settings')) { ?>
                                <a href="<?php echo admin_url('nj_court_search/settings'); ?>" class="btn btn-default mleft5 pull-left">
                                    <?php echo _l('nj_court_search_settings'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="row mbot15">
                            <div class="col-md-3">
                                <label for="filter_status"><?php echo _l('nj_court_search_status'); ?></label>
                                <select id="filter_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>">
                                    <option value=""><?php echo _l('all'); ?></option>
                                    <?php foreach ($statuses as $st) { ?>
                                        <option value="<?php echo html_escape($st); ?>"><?php echo _l('nj_court_search_status_' . $st); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('filter_from', 'nj_court_search_date_from'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('filter_to', 'nj_court_search_date_to'); ?>
                            </div>
                        </div>

                        <?php
                        render_datatable([
                            _l('nj_court_search_created'),
                            _l('nj_court_search_subject'),
                            _l('nj_court_search_dob'),
                            _l('nj_court_search_linked_record'),
                            _l('nj_court_search_reference_id'),
                            _l('nj_court_search_status'),
                            _l('nj_court_search_result_count'),
                            _l('nj_court_search_submitted_by'),
                            _l('nj_court_search_last_updated'),
                        ], 'nj-court-searches');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
(function ($) {
    "use strict";
    var fnServerParams = {
        status: '[name="filter_status"], #filter_status',
        from: '[name="filter_from"]',
        to: '[name="filter_to"]'
    };
    initDataTable('.table-nj-court-searches', window.location.href, undefined, undefined, fnServerParams, [0, 'desc']);

    $('#filter_status, #filter_from, #filter_to').on('change', function () {
        $('.table-nj-court-searches').DataTable().ajax.reload();
    });
})(jQuery);
</script>
</body>
</html>
