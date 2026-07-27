<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$form = isset($form) && is_array($form) ? $form : [];
$fv = function ($key, $default = '') use ($form) {
    return isset($form[$key]) ? $form[$key] : $default;
};
$leadLabel    = $fv('lead_label', $fv('lead_id') ? (_l('lead') . ' #' . (int) $fv('lead_id')) : '');
$clientLabel  = $fv('client_label', $fv('client_id') ? (_l('client') . ' #' . (int) $fv('client_id')) : '');
$contactLabel = $fv('contact_label', $fv('contact_id') ? (_l('contact') . ' #' . (int) $fv('contact_id')) : '');
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo html_escape($title); ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('nj_court_search/create'), ['id' => 'nj-court-search-form', 'autocomplete' => 'off']); ?>
                        <input type="hidden" name="idempotency_key" value="<?php echo html_escape($idempotency_key); ?>" />

                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('first_name', 'nj_court_search_first_name', $fv('first_name'), 'text', ['required' => true, 'autofocus' => true]); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('middle_name', 'nj_court_search_middle_name', $fv('middle_name')); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('last_name', 'nj_court_search_last_name', $fv('last_name'), 'text', ['required' => true]); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('suffix', 'nj_court_search_suffix', $fv('suffix')); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_date_input('dob', 'nj_court_search_dob', $fv('dob'), ['required' => true]); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('reference_id', 'nj_court_search_reference_id', $fv('reference_id')); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lead_id"><?php echo _l('lead'); ?></label>
                                    <select id="lead_id" name="lead_id" data-live-search="true" data-width="100%"
                                            class="ajax-search"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if ($fv('lead_id')) { ?>
                                            <option value="<?php echo (int) $fv('lead_id'); ?>" selected>
                                                <?php echo html_escape($leadLabel); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <p class="text-muted"><?php echo _l('nj_court_search_lead_help'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id"><?php echo _l('client'); ?></label>
                                    <select id="client_id" name="client_id" data-live-search="true" data-width="100%"
                                            class="ajax-search"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if ($fv('client_id')) { ?>
                                            <option value="<?php echo (int) $fv('client_id'); ?>" selected>
                                                <?php echo html_escape($clientLabel); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_id"><?php echo _l('contact'); ?></label>
                                    <select id="contact_id" name="contact_id" data-live-search="true" data-width="100%"
                                            class="ajax-search"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if ($fv('contact_id')) { ?>
                                            <option value="<?php echo (int) $fv('contact_id'); ?>" selected>
                                                <?php echo html_escape($contactLabel); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <p class="text-muted"><?php echo _l('nj_court_search_contact_help'); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php echo render_textarea('notes', 'nj_court_search_notes', $fv('notes')); ?>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('nj_court_search'); ?>" class="btn btn-default"><?php echo _l('go_back'); ?></a>
                            <button type="submit" class="btn btn-info" id="nj-court-submit-btn"><?php echo _l('nj_court_search_submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
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

    var pickerUrl = {
        lead: admin_url + 'nj_court_search/ajax_search_leads',
        customer: admin_url + 'nj_court_search/ajax_search_customers',
        contact: admin_url + 'nj_court_search/ajax_search_contacts'
    };

    init_ajax_search('lead', '#lead_id.ajax-search', undefined, pickerUrl.lead);
    init_ajax_search('customer', '#client_id.ajax-search', undefined, pickerUrl.customer);
    init_ajax_search('contact', '#contact_id.ajax-search', {
        contact_userid: function () {
            return $('#client_id').val() || '';
        }
    }, pickerUrl.contact);

    function clearContactPicker() {
        var $contact = $('#contact_id');
        $contact.val('').html('').selectpicker('refresh');
        if ($contact.data('AjaxBootstrapSelect')) {
            // Preserve empty state after customer change/clear
            $contact.selectpicker('val', '');
        }
    }

    $('#client_id').on('changed.bs.select', function () {
        clearContactPicker();
    });
    $('body').on('selected.cleared.ajax.bootstrap.select', '#client_id', function () {
        clearContactPicker();
    });

    var submitted = false;
    $('#nj-court-search-form').on('submit', function () {
        var first = $.trim($('#first_name').val() || '');
        var last = $.trim($('#last_name').val() || '');
        var dob = $.trim($('#dob').val() || '');
        if (!first || !last || !dob) {
            alert_float('danger', <?php echo json_encode(_l('nj_court_search_client_validation')); ?>);
            return false;
        }
        if (submitted) {
            return false;
        }
        submitted = true;
        $('#nj-court-submit-btn').prop('disabled', true).text(<?php echo json_encode(_l('nj_court_search_submitting')); ?>);
        return true;
    });
})(jQuery);
</script>
</body>
</html>
