<?php


// Process a Digital Asset order using a Digital Collection product repeater
function brmda_create_order($member, $collection_repeater) {
	// Create order and add products
	$new_order = wc_create_order();
	brmda_populate_order($new_order, $member, $collection_repeater);
	
	// If no items addded, delete the order and return
	if (brmda_order_is_empty($new_order)):
		wp_delete_post($new_order->get_id(), false);
		return;
	endif;
	
	// Configure order and save
	brmda_configure_and_save_order($new_order, $member);
	
	return $new_order;
}


// Select and add random Digital Assets to the given order
function brmda_populate_order($order, $member, $collection_repeater) {
	// Check if member is a Founder
	$is_founder			= user_can($member->ID, 'um_founder-membership');
	$assets_to_add		= $is_founder ? 2 : 1;
	
	// Randomise array order for each member
	shuffle($collection_repeater);
	$amount_of_products_added = 0;
	
	// Loop through repeater and add products to order
	if ($collection_repeater):
		foreach ($collection_repeater as $key => $row):
			// Check if last digital asset in loop
			$is_last_asset = $key === array_key_last($collection_repeater);
	
			// Break once enough products have been added
			if ($amount_of_products_added >= $assets_to_add) break;
			
			// Add products to order after checks
			$product = wc_get_product($row['product']);
			if (!brmda_asset_should_be_added($product, $member, $is_last_asset)) continue;
			$order->add_product($product);
			$amount_of_products_added .= 1;
		endforeach;
	endif;
}


// Set order properties and save with status 'Completed'
function brmda_configure_and_save_order($order, $member) {
	$order->set_customer_id($member->ID);
	$order->apply_coupon('msrqpx');
	$order->calculate_totals();
	$order->set_status('wc-completed');
	$order->save();
}


// Check if order contains no products
function brmda_order_is_empty($order) {
	$item_count = count($order->get_items());
	return $item_count < 1;
}
