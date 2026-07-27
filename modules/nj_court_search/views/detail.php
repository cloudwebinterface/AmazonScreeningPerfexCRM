<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo html_escape(nj_court_search_subject_name($search)); ?>
                            <?php echo nj_court_search_status_badge($search['status']); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><?php echo _l('nj_court_search_first_name'); ?>:</strong> <?php echo html_escape($search['first_name']); ?></p>
                                <p><strong><?php echo _l('nj_court_search_middle_name'); ?>:</strong> <?php echo html_escape($search['middle_name'] ?: '—'); ?></p>
                                <p><strong><?php echo _l('nj_court_search_last_name'); ?>:</strong> <?php echo html_escape($search['last_name']); ?></p>
                                <p><strong><?php echo _l('nj_court_search_suffix'); ?>:</strong> <?php echo html_escape($search['suffix'] ?: '—'); ?></p>
                                <p><strong><?php echo _l('nj_court_search_dob'); ?>:</strong> <?php echo nj_court_search_format_dob($search['dob'], $can_sensitive); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><?php echo _l('nj_court_search_external_job_id'); ?>:</strong> <?php echo html_escape($search['external_job_id'] ?: '—'); ?></p>
                                <p><strong><?php echo _l('nj_court_search_reference_id'); ?>:</strong> <?php echo html_escape($search['reference_id'] ?: '—'); ?></p>
                                <p><strong><?php echo _l('nj_court_search_linked_record'); ?>:</strong> <?php echo nj_court_search_linked_record_html($search); ?></p>
                                <p><strong><?php echo _l('nj_court_search_result_count'); ?>:</strong> <?php echo (int) $search['result_count']; ?></p>
                                <p><strong><?php echo _l('nj_court_search_submitted_by'); ?>:</strong>
                                    <?php
                                    if ($search['submitted_by']) {
                                        echo '<a href="' . admin_url('profile/' . (int) $search['submitted_by']) . '">' . get_staff_full_name($search['submitted_by']) . '</a>';
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_created'); ?>:</strong> <?php echo _dt($search['created_at']); ?></p></div>
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_submitted_at'); ?>:</strong> <?php echo $search['submitted_at'] ? _dt($search['submitted_at']) : '—'; ?></p></div>
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_processing_started'); ?>:</strong> <?php echo $search['processing_started_at'] ? _dt($search['processing_started_at']) : '—'; ?></p></div>
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_completed_at'); ?>:</strong> <?php echo $search['completed_at'] ? _dt($search['completed_at']) : '—'; ?></p></div>
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_last_checked'); ?>:</strong> <?php echo $search['last_checked_at'] ? _dt($search['last_checked_at']) : '—'; ?></p></div>
                            <div class="col-md-4"><p><strong><?php echo _l('nj_court_search_retry_count'); ?>:</strong> <?php echo (int) $search['retry_count']; ?></p></div>
                        </div>

                        <?php if (!empty($search['notes'])) { ?>
                            <hr />
                            <p><strong><?php echo _l('nj_court_search_notes'); ?>:</strong></p>
                            <p><?php echo nl2br(html_escape($search['notes'])); ?></p>
                        <?php } ?>

                        <?php if (!empty($search['error_message'])) { ?>
                            <div class="alert alert-danger mtop15">
                                <strong><?php echo _l('nj_court_search_error'); ?>:</strong>
                                <?php echo html_escape($search['error_message']); ?>
                                <?php if (!empty($search['error_code'])) { ?>
                                    <span class="text-muted">(<?php echo html_escape($search['error_code']); ?>)</span>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <hr />
                        <h4><?php echo _l('nj_court_search_results'); ?></h4>
                        <?php if (empty($results)) { ?>
                            <p class="text-muted"><?php echo _l('nj_court_search_no_results_yet'); ?></p>
                        <?php } elseif (!empty($results['_purged'])) { ?>
                            <div class="alert alert-info">
                                <?php echo _l('nj_court_search_results_purged'); ?>
                                <?php if (!empty($search['result_purged_at'])) { ?>
                                    <br /><small><?php echo _l('nj_court_search_results_purged_at'); ?>:
                                        <?php echo _dt($search['result_purged_at']); ?></small>
                                <?php } ?>
                            </div>
                        <?php } elseif (!empty($results['_masked'])) { ?>
                            <div class="alert alert-warning">
                                <?php echo _l('nj_court_search_results_masked'); ?>
                                (<?php echo (int) $results['result_count']; ?>)
                            </div>
                        <?php } else { ?>
                            <pre class="nj-court-results" style="max-height:420px;overflow:auto;background:#f7f7f7;padding:12px;border:1px solid #e3e3e3;"><?php
                                echo html_escape(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            ?></pre>
                        <?php } ?>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('nj_court_search'); ?>" class="btn btn-default"><?php echo _l('nj_court_search_back_to_list'); ?></a>

                            <?php if ($can_refresh) { ?>
                                <?php echo form_open(admin_url('nj_court_search/refresh/' . (int) $search['id']), ['style' => 'display:inline']); ?>
                                <button type="submit" class="btn btn-default"><?php echo _l('nj_court_search_refresh_status'); ?></button>
                                <?php echo form_close(); ?>
                            <?php } ?>

                            <?php if ($can_retry) { ?>
                                <?php echo form_open(admin_url('nj_court_search/retry/' . (int) $search['id']), ['style' => 'display:inline']); ?>
                                <button type="submit" class="btn btn-warning" onclick="return confirm(<?php echo json_encode(_l('nj_court_search_retry_confirm')); ?>);">
                                    <?php echo _l('nj_court_search_retry'); ?>
                                </button>
                                <?php echo form_close(); ?>
                            <?php } ?>

                            <?php if ($can_cancel) { ?>
                                <?php echo form_open(admin_url('nj_court_search/cancel/' . (int) $search['id']), ['style' => 'display:inline']); ?>
                                <button type="submit" class="btn btn-danger" onclick="return confirm(<?php echo json_encode(_l('nj_court_search_cancel_confirm')); ?>);">
                                    <?php echo _l('nj_court_search_cancel'); ?>
                                </button>
                                <?php echo form_close(); ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('nj_court_search_audit_trail'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <?php if (empty($events)) { ?>
                            <p class="text-muted"><?php echo _l('nj_court_search_no_events'); ?></p>
                        <?php } else { ?>
                            <ul class="list-unstyled">
                                <?php foreach ($events as $event) { ?>
                                    <li class="mbotop10" style="margin-bottom:12px;border-bottom:1px solid #eee;padding-bottom:8px;">
                                        <div><strong><?php echo html_escape($event['event_type']); ?></strong></div>
                                        <div class="text-muted"><?php echo _dt($event['created_at']); ?></div>
                                        <?php if ($event['old_status'] || $event['new_status']) { ?>
                                            <div>
                                                <?php echo html_escape($event['old_status'] ?: '—'); ?>
                                                →
                                                <?php echo html_escape($event['new_status'] ?: '—'); ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($event['staff_id']) { ?>
                                            <div class="text-muted"><?php echo get_staff_full_name($event['staff_id']); ?></div>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
