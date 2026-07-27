<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
   <div class="col-md-12">
      <h2>Load searches data</h2>
      <a href="/api/load_fresh_data" class="btn btn-lg btn-primary">Load searches data</a>
      <span id="load-tables" class="btn btn-lg btn-primary mleft10">Load tables</span>
   </div>
</div>

<h3>Charge tables</h3>
<?php $this->load->helper('api'); ?>
<pre><?php 
	$versions = ab_load_table('dispositions');

	print_r($versions);
?></pre>

<style type="text/css" media="all">
	.load-data-popup {
		display: none;
	    position: fixed;
	    top: 0;
	    left: 0;
	    z-index: 9999;
	    background-color: rgba(255,255,255,0.8);
	    right: 0;
	    bottom: 0;
	    align-items: center;
	    justify-content: center;
	}

	.load-data-popup span {
	    background-image: url(/assets/images/ajax-loader.gif);
	    height: 32px;
	    background-repeat: no-repeat;
	    padding-left: 40px;
	    display: flex;
	    align-items: center;
	    font-size: 16px;
	}
</style>		
<div class="load-data-popup">
	<span>loading data...</span>
	<em>.</em>
</div>
