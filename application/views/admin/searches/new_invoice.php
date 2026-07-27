<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style><?php include FCPATH . 'assets/css/search-detail.css'; ?></style>
<?php init_tail(); ?>
<div id="wrapper">

    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <div class="clearfix"></div>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body col-md-12">
                        <div class="page-title">
                            <h2>New Invoice</h2>
                        </div>

                        <div class="invoice-template">
                        	<?php echo form_open('admin/invoice/submit', array('id' => 'invoiceForm')); ?>
                            <div class="header-section">
                                <div class="left-section">
                                    <div class="company-info company"><?php echo get_option('invoice_company_name'); ?></div>
                                    <div class="company-info address"><?php echo get_option('invoice_company_address'); ?></div>
                                    <div class="company-info city"><?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('company_state'); ?> <?php echo get_option('invoice_company_postal_code'); ?></div>

                                    <div class="bill-to-title">BILL TO</div>
                                    <div class="input-column">
                                        <div class="input-field">
                                            <label><small class="req text-danger">*</small> Name:</label>
                                            <input type="text" name="client_name" id="client_name" class="form-field" required>
                                        </div>
                                        <div class="input-field">
                                            <label>Company Name:</label>
                                            <input type="text" name="client_company_name" id="client_company_name" class="form-field">
                                        </div>
                                        <div class="input-field">
                                            <label>Street Address:</label>
                                            <input type="text" name="client_address" id="client_address" class="form-field">
                                        </div>
                                        <div class="input-field">
                                            <label>City:</label>
                                            <input type="text" name="client_city" id="client_city" class="form-field">
                                        </div>
                                        <div class="input-field">
                                            <label>State:</label>
                                            <select name="client_state" id="client_state" class="form-field">
                                                <option value="">SELECT</option>
                                                <?php foreach ($states as $key => $state) {
                                                    echo '<option value="'.$key.'" '.(edit_case($edit_case, 'state') == $key ? 'selected' : '').'>'.strtoupper($state).'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="input-field">
                                            <label>Zip Code:</label>
                                            <input type="text" name="client_postal_code" id="client_postal_code" class="form-field">
                                        </div>
                                        <div class="input-field">
                                            <label>Phone:</label>
                                            <input type="text" name="client_phone" id="client_phone" class="form-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="right-section">
                                    <div class="invoice-title">INVOICE</div>
                                    <div class="input-field">
                                        <label>Invoice ID #:</label>
                                        <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-field" placeholder="prefix">
                                        <input type="number" name="invoice_id" id="invoice_id" class="form-field" placeholder="Invoice number">
                                    </div>
                                    <div class="input-field">
                                        <label>Date:</label>
                                        <input type="text" name="invoice_date" id="invoice_date" class="form-field">
                                    </div>
                                    <div class="input-field">
                                        <label>Due Date:</label>
                                        <input type="text" name="invoice_due_date" id="invoice_due_date" class="form-field">
                                    </div>
                                </div>
                            </div>

                            <div class="product-list-section">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Search ID / Name</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="invoice-item" id="invitem-0" data-id="0">
                                            <td>
                                                <div class="input-field">
                                                    <input type="text" name="item[0][search_id]" class="form-field invoice-search-id" placeholder="e.g '11324428 / John Doe'">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-field">
                                                    <input type="number" name="item[0][amount]" class="form-field invoice-item-amount" placeholder="e.g '20'">
                                                </div>
                                                <span class="remove-item"><i class="fa fa-close"></i></span>
                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                    	<tr>
                                            <td colspan="2"><button type="button" class="btn btn-secondary" id="add-more-items">+</button></td>
                                        </tr>
                                    </tfoot>
                                </table>        
                            </div>

                            <div class="section-footer">
                            	<button type="submit" name="submit_type" value="save_invoice" class="btn btn-primary">Save</button>
                            </div>

                            <?php echo form_close(); ?>
                        </div>
                    
                    </div>  
                </div>
            </div>

        </div>
    </div>
</div>

<style type="text/css" media="screen">      
.invoice-template {
    border: 1px solid #ddd;
    width: 900px;
    margin-top: 30px;
    margin-left: auto;
    margin-right: auto;
    padding: 45px 50px;
    margin-bottom: 50px;
}

.header-section {
    display: inline-block;
    width: 100%;
}

.left-section {
    width: 300px;
    float: left;
}

.right-section {
    width: 300px;
    float: right;
}

.invoice-title {
    text-align: center;
    font-weight: bold;
    font-size: 30px;
    margin-bottom: 20px;
}

.invoice-template .company-info.company {
    font-weight: bold;
    margin-bottom: 10px;
}

.bill-to-title {
    margin-top: 30px;
    background-color: #567185;
    color: #fff;
    padding: 2px 5px;
    font-family: 'Open Sans', sans-serif;
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 10px;
}

.product-list-section table {
    width: 100%;
}

.product-list-section table th {
    background-color: #567185;
    color: #fff;
    padding: 2px 10px;
}

.product-list-section table th:last-child {
    width: 140px;
    text-align: center;
}

.product-list-section table td {
    border-bottom: 1px solid #ccc;
    padding: 4px 10px;
}

.product-list-section table td:last-child {
    text-align: center;
    border-left: 1px solid #ccc;
    position: relative;
}

.product-list-section table td .input-field {
    margin-bottom: 0;
}

.product-list-section table td .input-field .form-field {
    width: 100%;
}

.product-list-section table tfoot td {
    border: none;
}

.product-list-section table tfoot td:last-child {
    border: none;
}

.product-list-section table td .input-field .form-field.invoice-item-amount {
    text-align: center;
}

.product-list-section table tbody td .input-field .form-field::placeholder {
    color: #c5c498;
}
.section-footer {
    margin-top: 60px;
    text-align: right;
}
span.remove-item {
    color: red;
    cursor: pointer;
    font-family: 'Open Sans';
    position: absolute;
    right: -24px;
    top: 5px;
    font-size: 15px;
}

tr:first-child span.remove-item {
    display: none;
}
.input-field #invoice_prefix {
    max-width: 50px;
    text-align: center;
}

.input-field #invoice_id {
    max-width: 97px;
}
</style>
<script>
	jQuery(document).ready(function($) {
		$('#add-more-items').on('click', function(e) {
			e.preventDefault();
			var lastId = $('tbody .invoice-item').last().attr('data-id');
			var id = parseFloat(lastId)+1;
			$('#invitem-0').clone().appendTo('.product-list-section tbody').closest('tr').attr({
				'id': 'invitem-'+id,
				'data-id': id
			});
			$('#invitem-'+id+' .invoice-search-id').attr('name', 'item['+id+'][search_id]').val('');
			$('#invitem-'+id+' .invoice-item-amount').attr('name', 'item['+id+'][amount]').val('');
		});

		$(document).on('click', 'span.remove-item', function() {
			$(this).closest('tr').remove();
		});
	});
</script>
</body>
</html>
