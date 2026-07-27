<?php
$header = '
<style>
	table.invoice-info { border: 1px solid #999; }
	table.invoice-info > tr > td{ line-height:1.3; }
</style>
<table class="header" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="4"><div style="font-size:12px;font-weight:bold;color:#555">'.$company_name.'</div></th>
        <th colspan="1"><div style="font-size:18px;font-weight:bold;color:#555;line-height:2;">INVOICE</div></th>
    </tr>
    <tr>
        <td colspan="4"><div style="font-size:8px;color:#333">'.$company_address.'<br>'.$company_address2.'</div></td>
        <td colspan="1"><table border="1" cellpadding="2" class="invoice-info"><tr><td style="font-size:8px;color:#333">ID #:</td><td style="font-size:8px;color:#333">'.$prefix.$inv_ID.'</td></tr><tr><td style="font-size:8px;color:#333">Date: </td><td style="font-size:8px;color:#333">'.$created_date.'</td></tr><tr><td style="font-size:8px;color:#333">Due Date: </td><td style="font-size:8px;color:#333">'.$due_date.'</td></tr></table></td>
    </tr>
</table>';

$bill_to = '
<table border="0" cellspacing="0" cellpadding="0" width="200">
	<tr><th style="background-color:#567185;color:#fff;font-size:8px;line-height:1.5;text-align:center">BILL TO</th></tr>
	<tr><td style="font-size:10px;color:#333;line-height:1.8;font-weight:bold">'.$cust_name.'</td></tr>
	<tr><td style="font-size:8px;color:#333;line-height:1.3">'.$cust_company.'</td></tr>
	<tr><td style="font-size:8px;color:#333;line-height:1.3">'.$cust_address.'</td></tr>
	<tr><td style="font-size:8px;color:#333;line-height:1.3">'.$cust_city.' '.$cust_state.' '.$cust_postal_code.'</td></tr>
</table>';

$item_rows = '';
if ( $items ) {
	foreach ($items as $key => $item) {
		$item_rows .= '<tr><td colspan="4" style="font-size:8px;color:#333;line-height:1.5;border-bottom-width:1px;border-bottom-color:#eee;">'.$item->description.'</td><td colspan="1" style="font-size:8px;color:#333;line-height:1.5;border-bottom-width:1px;border-bottom-color:#eee;text-align:center">$'.$item->price.'</td></tr>';
	}
}



$html_items = '<table cellpadding="2"  class="items">
<tr><th colspan="4" style="background-color:#567185;color:#fff;font-size:8px;line-height:1.5;text-align:center">Search ID / Name</th>
<th colspan="1" style="background-color:#567185;color:#fff;font-size:8px;line-height:1.5;text-align:center">Amount</th></tr>
'.$item_rows.'
<tr><td colspan="4" style="font-size:12px;color:#000;line-height:1.5;text-align:right">Total</td><td colspan="1" style="font-size:12px;color:#000;line-height:1.5;text-align:center;font-weight:bold">$'.$total_bill.'</td></tr>
</table>
';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, "A4", true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_name);
$pdf->SetTitle('INVOICE ' . $prefix.$inv_ID . '_' . $time);
$pdf->SetSubject('Invoice generator');
$pdf->SetKeywords('invoice');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->AddPage();

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

// output the HTML content
$pdf->writeHTML($header, true, false, true, false, '');
$pdf->ln(5);
$pdf->writeHTML($bill_to, true, false, true, false, '');
$pdf->ln(3);
$pdf->writeHTML($html_items, true, false, true, false, '');

//Close and output PDF document
$pdf->Output('invoice_'.$prefix.$inv_ID.'_'.$time.'.pdf', 'I');
