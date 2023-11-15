<?php

// Create a report from an array of orders
function brmda_create_report($collection, $orders) {
	if (!$orders || count($orders) < 1) return null;
	
	$report = array();
	$products_added = array();
	
	foreach($orders as $order):
		$items				= $order->get_items();
		$customer_email		= $order->get_billing_email();
	
		// Loop through order items - if ID not in $unique_products, add it
		foreach ($items as $item):
			$id		= $item->get_product_id();
			$name	= $item->get_name();
	
			// If this product is already in the array...
			if (in_array($id, $products_added)):
				
				// Add order customer to that array item's 'recipients'
				array_push($report[$id]['recipients'], $customer_email);
				continue;
	
			else:
				// Add product to array with ID, name and recipients
				$products_added[] = $id;
				$report[$id] = array(
					'product_id'	=> $id,
					'product_name'	=> $name,
					'recipients'	=> array($customer_email)
				);
			endif;
	

		endforeach;
	
	endforeach;
	
	return brmda_save_report_post($collection, $report);
}


// Save Report post to WordPress database
function brmda_save_report_post($collection, $report) {
	$content = '';
	
	foreach($report as $id => $item):
		$name				= $item['product_name'];
		$recipients			= $item['recipients'];
		$number				= count($recipients);
		$stock				= wc_get_product($id)->get_stock_quantity();

		// Format list of recipients
		$recipients_str = '';
	
		foreach ($recipients as $recipient):
			$recipients_str .= "<li>$recipient</li>";
		endforeach;
		
		$content .= "
			<div class='report-section'>
				<p><strong>$name</strong> ($number allocated - $stock remaining)</p>
				<ul class='recipients'>$recipients_str</ul>
			</div>
		";
	endforeach;
	
	$report_id = wp_insert_post(array(
		'post_title'	=> $collection->post_title . ' (distributed on ' . date("d/m/y") . ')',
		'post_content'	=> $content,
		'post_status'	=> 'private',
		'post_type'		=> 'report'
	));
	
	return $report_id;
}
