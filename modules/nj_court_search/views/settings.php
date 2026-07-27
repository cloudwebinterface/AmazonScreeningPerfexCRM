<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo html_escape($title); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('nj_court_search/settings')); ?>

                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="enabled" id="enabled" value="1" <?php echo $settings['enabled'] == '1' ? 'checked' : ''; ?>>
                            <label for="enabled"><?php echo _l('nj_court_search_enable_integration'); ?></label>
                        </div>

                        <?php echo render_input('api_base_url', 'nj_court_search_api_base_url', $settings['api_base_url']); ?>
                        <p class="text-muted"><?php echo _l('nj_court_search_api_base_url_help'); ?></p>

                        <div class="form-group">
                            <label for="api_key"><?php echo _l('nj_court_search_api_key'); ?></label>
                            <input type="password" class="form-control" name="api_key" id="api_key"
                                   value="<?php echo html_escape($settings['api_key_mask']); ?>"
                                   placeholder="<?php echo $settings['api_key_mask'] ? _l('nj_court_search_secret_leave_blank') : ''; ?>"
                                   autocomplete="new-password" />
                            <p class="text-muted"><?php echo _l('nj_court_search_secret_help'); ?></p>
                        </div>

                        <?php echo render_input('api_timeout', 'nj_court_search_api_timeout', $settings['api_timeout'], 'number'); ?>
                        <?php echo render_input('poll_interval', 'nj_court_search_poll_interval', $settings['poll_interval'], 'number'); ?>
                        <?php echo render_input('poll_batch_size', 'nj_court_search_poll_batch_size', $settings['poll_batch_size'], 'number'); ?>

                        <div class="form-group">
                            <label for="webhook_secret"><?php echo _l('nj_court_search_webhook_secret'); ?></label>
                            <input type="password" class="form-control" name="webhook_secret" id="webhook_secret"
                                   value="<?php echo html_escape($settings['webhook_secret_mask']); ?>"
                                   placeholder="<?php echo $settings['webhook_secret_mask'] ? _l('nj_court_search_secret_leave_blank') : ''; ?>"
                                   autocomplete="new-password" />
                        </div>

                        <?php echo render_input('webhook_tolerance', 'nj_court_search_webhook_tolerance', $settings['webhook_tolerance'], 'number'); ?>

                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="webhook_enabled" id="webhook_enabled" value="1" <?php echo $settings['webhook_enabled'] == '1' ? 'checked' : ''; ?>>
                            <label for="webhook_enabled"><?php echo _l('nj_court_search_enable_webhooks'); ?></label>
                        </div>

                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="cron_polling_enabled" id="cron_polling_enabled" value="1" <?php echo $settings['cron_polling_enabled'] == '1' ? 'checked' : ''; ?>>
                            <label for="cron_polling_enabled"><?php echo _l('nj_court_search_enable_cron_polling'); ?></label>
                        </div>

                        <?php echo render_input('result_retention_days', 'nj_court_search_result_retention_days', $settings['result_retention_days'], 'number'); ?>
                        <p class="text-muted"><?php echo _l('nj_court_search_result_retention_help'); ?></p>
                        <?php echo render_input('retention_batch_size', 'nj_court_search_retention_batch_size', $settings['retention_batch_size'], 'number'); ?>
                        <p class="text-muted"><?php echo _l('nj_court_search_retention_batch_size_help'); ?></p>

                        <?php echo render_textarea('role_restrictions', 'nj_court_search_role_restrictions', $settings['role_restrictions']); ?>
                        <p class="text-muted"><?php echo _l('nj_court_search_role_restrictions_help'); ?></p>

                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" name="purge_on_uninstall" id="purge_on_uninstall" value="1" <?php echo $settings['purge_on_uninstall'] == '1' ? 'checked' : ''; ?>>
                            <label for="purge_on_uninstall"><?php echo _l('nj_court_search_purge_on_uninstall'); ?></label>
                        </div>
                        <p class="text-muted"><?php echo _l('nj_court_search_purge_on_uninstall_help'); ?></p>

                        <?php if (!empty($settings['mock_allowed'])) { ?>
                            <hr />
                            <h4><?php echo _l('nj_court_search_mock_heading'); ?></h4>
                            <div class="alert alert-warning">
                                <?php echo _l('nj_court_search_mock_warning'); ?>
                            </div>
                            <div class="checkbox checkbox-warning">
                                <input type="checkbox" name="mock_mode" id="mock_mode" value="1" <?php echo $settings['mock_mode'] == '1' ? 'checked' : ''; ?>>
                                <label for="mock_mode"><?php echo _l('nj_court_search_mock_mode'); ?></label>
                            </div>
                            <div class="form-group">
                                <label for="mock_scenario"><?php echo _l('nj_court_search_mock_scenario'); ?></label>
                                <select name="mock_scenario" id="mock_scenario" class="selectpicker" data-width="100%">
                                    <?php
                                    $scenarios = [
                                        'success_flow', 'processing', 'completed', 'no_results',
                                        'failed', 'submission_failure', 'timeout', 'malformed',
                                    ];
                                    foreach ($scenarios as $sc) {
                                        $sel = ($settings['mock_scenario'] === $sc) ? 'selected' : '';
                                        echo '<option value="' . html_escape($sc) . '" ' . $sel . '>' . html_escape($sc) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>

                        <div class="alert alert-info mtop15">
                            <strong><?php echo _l('nj_court_search_webhook_url'); ?>:</strong>
                            <code><?php echo html_escape($settings['webhook_url']); ?></code>
                            <br />
                            <small><?php echo _l('nj_court_search_csrf_exclude_help'); ?></small>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="button" class="btn btn-default" id="nj-court-test-connection">
                                <?php echo _l('nj_court_search_test_connection'); ?>
                            </button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                        <?php echo form_close(); ?>

                        <div id="nj-court-test-result" class="mtop15" style="display:none;"></div>
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
    $('#nj-court-test-connection').on('click', function () {
        var $btn = $(this);
        var $out = $('#nj-court-test-result');
        $btn.prop('disabled', true);
        $out.hide().removeClass('alert-success alert-danger');

        $.post(admin_url + 'nj_court_search/test_connection', {
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        }).done(function (resp) {
            try { if (typeof resp === 'string') { resp = JSON.parse(resp); } } catch (e) {}
            $out.addClass(resp.success ? 'alert alert-success' : 'alert alert-danger')
                .text(resp.message || (resp.success ? 'OK' : 'Failed'))
                .show();
        }).fail(function () {
            $out.addClass('alert alert-danger').text(<?php echo json_encode(_l('nj_court_search_test_connection_failed')); ?>).show();
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    // Clear masked placeholders on focus so user can enter a new secret
    $('#api_key, #webhook_secret').on('focus', function () {
        if (($(this).val() || '').indexOf('•') !== -1) {
            $(this).val('');
        }
    });
})(jQuery);
</script>
</body>
</html>
