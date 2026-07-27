<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.list-group-item .lbl {
    font-weight: bold;
    display: inline-block;
    width: 200px;
    color: #000;
    text-align: left;
}
.list-group-item .ctn {
    display: inline-block;
    max-width: 600px;
    width: 100%;
    vertical-align: top;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
                        <h2>Add notes</h2>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="lbl">Search ID:</span>
                                <span class="ctn"><?php echo isset($data->search_id) ? $data->search_id : ''; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="lbl">Name:</span>
                                <span class="ctn">
                                    <?php echo isset($data->subject->first_name) ? $data->subject->first_name : ''; ?> 
                                    <?php echo isset($data->subject->middle_name) ? $data->subject->middle_name : ''; ?>
                                    <?php echo isset($data->subject->last_name) ? $data->subject->last_name : ''; ?>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="lbl">AKA Names:</span>
                                <span class="ctn">
                                    <?php if ( isset($data->subject->aka_names) ): ?>
                                        <?php foreach($data->subject->aka_names as $key => $aka): ?>
                                            <ul>
                                                <li>
                                                    <?php echo isset($aka->first_name) ? $aka->first_name : ''; ?> 
                                                    <?php echo isset($aka->middle_name) ? $aka->middle_name : ''; ?>
                                                    <?php echo isset($aka->last_name) ? $aka->last_name : ''; ?>
                                                </li>
                                            </ul>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <span class="lbl">Client Notes:</span>
                                <span class="ctn"><?php echo isset($data->client_notes) ? nl2br($data->client_notes) : ''; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="lbl">Result Notes:</span>
                                <span class="ctn"><?php echo isset($data->result_notes) ? nl2br($data->result_notes) : ''; ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="lbl">Internal Notes:</span>
                                <span class="ctn"><?php echo isset($data->internal_notes) ? nl2br($data->internal_notes) : ''; ?></span>
                            </li>
                        </ul>
                        <pre><?php print_r($data); ?></pre>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>