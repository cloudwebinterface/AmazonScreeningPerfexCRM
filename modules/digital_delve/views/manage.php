<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <h4 class="no-margin pull-left"><?php echo _l('digital_delve_orders'); ?></h4>
                            <?php if (!empty($can_download)) { ?>
                                <?php echo form_open(admin_url('digital_delve/download'), ['class' => 'pull-right', 'id' => 'dd-download-form']); ?>
                                <button type="submit" class="btn btn-info" id="dd-download-btn">
                                    <i class="fa fa-cloud-download"></i>
                                    <?php echo _l('digital_delve_download'); ?>
                                    (max <?php echo (int) $import_limit; ?>)
                                </button>
                                <?php echo form_close(); ?>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="alert alert-warning">
                            <?php echo _l('digital_delve_safety_notice'); ?>
                        </div>

                        <?php if (empty($configured)) { ?>
                            <div class="alert alert-danger">
                                <?php echo _l('digital_delve_not_configured'); ?>
                            </div>
                        <?php } ?>

                        <p class="text-muted">
                            <?php echo _l('digital_delve_total'); ?>: <strong><?php echo (int) $total; ?></strong>
                            <?php if (!empty($last_download_at)) { ?>
                                &nbsp;|&nbsp;
                                <?php echo _l('digital_delve_last_download'); ?>:
                                <?php echo html_escape($last_download_at); ?>
                                (<?php echo (int) $last_download_count; ?> <?php echo _l('digital_delve_new_rows'); ?>)
                            <?php } ?>
                        </p>

                        <div class="table-responsive">
                            <table class="table table-striped table-digital-delve">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('digital_delve_order_id'); ?></th>
                                        <th><?php echo _l('digital_delve_subject'); ?></th>
                                        <th><?php echo _l('digital_delve_dob'); ?></th>
                                        <th><?php echo _l('digital_delve_county_state'); ?></th>
                                        <th><?php echo _l('digital_delve_service'); ?></th>
                                        <th><?php echo _l('digital_delve_account'); ?></th>
                                        <th><?php echo _l('digital_delve_status'); ?></th>
                                        <th><?php echo _l('digital_delve_imported_at'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orders)) { ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <?php echo _l('digital_delve_no_orders'); ?>
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php foreach ($orders as $order) { ?>
                                            <tr>
                                                <td><?php echo html_escape($order['order_detail_order_id']); ?></td>
                                                <td>
                                                    <?php
                                                    echo html_escape(trim(
                                                        $order['last_name'] . ', ' . $order['first_name'] . ' ' . $order['middle_name']
                                                    ));
                                                    ?>
                                                </td>
                                                <td><?php echo html_escape($order['dob']); ?></td>
                                                <td>
                                                    <?php echo html_escape(trim($order['county'] . ', ' . $order['state'], ', ')); ?>
                                                </td>
                                                <td><?php echo html_escape($order['service_code']); ?></td>
                                                <td><?php echo html_escape($order['account_code']); ?></td>
                                                <td><span class="label label-default"><?php echo html_escape($order['status']); ?></span></td>
                                                <td><?php echo html_escape($order['imported_at']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
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
    $('#dd-download-form').on('submit', function (e) {
        if (!window.confirm(<?php echo json_encode(_l('digital_delve_download_confirm')); ?>)) {
            e.preventDefault();
            return false;
        }
        $('#dd-download-btn').prop('disabled', true).text(<?php echo json_encode(_l('digital_delve_downloading')); ?>);
    });
})(jQuery);
</script>
</body>
</html>
