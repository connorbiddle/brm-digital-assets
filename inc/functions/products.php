<?php


// Does user own product?
function brmda_user_has_bought_product($user, $product_id) {
	return wc_customer_bought_product($user->user_email, $user->ID, $product_id);
}


// Check if asset is in stock and not owned by member
function brmda_asset_should_be_added($product, $member, $last_asset_in_loop) {
	
	
	// Get asset
	$id						= $product->get_id();
	$user_owns_asset		= brmda_user_has_bought_product($member, $id);
	if ($last_asset_in_loop && !$user_owns_asset) return true;
	$asset_out_of_stock		= !$product->is_in_stock();
	$asset_is_rare			= get_field('rare', $id);
	
	// If rare asset, have chance to skip
	if ($asset_is_rare && !$user_owns_asset) return mt_rand(0, 1) === 1;
	
	return !$user_owns_asset && !$asset_out_of_stock;
}


// Is current post a Print product?
function brmda_current_post_is_print() {
	return get_post_type() === 'product' && is_singular() && has_term('print', 'product_cat');
}