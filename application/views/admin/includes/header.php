<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css" media="screen">
.load-fresh-data {
    padding: 13px 20px;
}
.load-fresh-data > a {
    height: auto !important;
    line-height: 2 !important;
}
</style>
<div id="header">
   <?php /*<div class="hide-menu"><i class="fa fa-bars"></i></div>*/ ?>
   <div id="logo">
      <?php get_company_logo(get_admin_uri().'/') ?>
   </div>
   <nav>
      <div class="small-logo">
         <span class="text-primary">
            <?php get_company_logo(get_admin_uri().'/') ?>
         </span>
      </div>
      <div class="mobile-menu">
         <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
            <i class="fa fa-chevron-down"></i>
         </button>
         <ul class="mobile-icon-menu">
            <?php
               // To prevent not loading the timers twice
            if(is_mobile()){ ?>
               <li class="dropdown notifications-wrapper header-notifications">
                  <?php $this->load->view('admin/includes/notifications'); ?>
               </li>
            <?php } ?>
         </ul>
         <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
            <ul class="nav navbar-nav">
               <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
               <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
               <?php if(is_staff_member()){ ?>
                  <li class="header-newsfeed">
                   <a href="#" class="open_newsfeed mobile">
                     <?php echo _l('whats_on_your_mind'); ?>
                  </a>
               </li>
            <?php } ?>
            <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
         </ul>
      </div>
   </div>
   
   <ul class="nav navbar-nav navbar-right">

	<li class="load-fresh-data">
	 	<a href="/api/load_fresh_data" class="btn btn-success">Load fresh data</a>
	</li>
      
    <?php hooks()->do_action('after_render_top_search'); ?>
    <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo get_staff_full_name(); ?>" data-placement="bottom">
      <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
         <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-small','pull-left')); ?>
      </a>
      <ul class="dropdown-menu animated fadeIn">
         <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
         <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
         <li class="header-logout">
            <a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
         </li>
      </ul>
   </li>
   <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
      <?php $this->load->view('admin/includes/notifications'); ?>
   </li>
</ul>
</nav>
</div>

