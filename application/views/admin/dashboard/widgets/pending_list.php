<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('orders'); ?>">
  <div class="clearfix"></div>
  <div class="panel_s">
   <div class="panel-body">
    <div class="dt-loader hide"></div>
    <?php $this->load->helper('api_helper'); ?>
    <?php $data = ab_get_pending_list(); ?>
    <h3>Pending Searches</h3>
    <?php ob_start(); ?>
    <?php if ($data): ?>
        <?php foreach ($data as $key => $search): ?>
          <tr>
            <td><?php echo $search->search_id; ?></td>
            <td><?php echo $search->search_status == 'P' ? 'Pending' : $search->search_status; ?></td>
            <td><?php echo $search->subject->first_name . ' ' . $search->subject->last_name; ?></td>
            <td><?php echo $search->subject->ssn; ?></td>
            <td><?php echo $search->subject->date_of_birth; ?></td>
            <td><?php echo $search->search_type; ?></td>
            <td><?php echo $search->subject->state . ' - ' . $search->subject->city; ?></td>
          </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php $pending_list = ob_get_clean(); ?>
    <?php $this->load->helper('datatables_helper'); ?>
    <?php table_template(
      ['Search ID', 'Search Status', 'Subject/AKAs', 'SSN', 'DOB', 'Search Type', 'State County'],
      $pending_list
    );?>
    <div class="action clearfix" style="padding-top: 20px">
      <a href="/admin/searches" class="btn btn-primary pull-right">View more</a>
    </div>  
  </div>
</div>
<div class="clearfix"></div>
</div>