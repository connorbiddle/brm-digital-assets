<?php

// Main shortcode
function brmda_shortcode($atts = array()) {
	
	$attributes = shortcode_atts(array('mode' => ''), $atts);
	
	$output1	= '';	// Owned products	(appearing first)
	$output2	= '';	// Unowned products	(appearing last)

	// Get products
	$products = new WP_Query(array(
		'post_type'			=> 'product',
		'product_cat'		=> 'digital-asset',
		'posts_per_page'	=> -1
	));
	
	// Add asset HTML to output
	if ($products->have_posts()):
		while ($products->have_posts()):
			$products->the_post();
			$product_owned = brmda_user_has_bought_product(wp_get_current_user(), get_the_id());
	
			if ($product_owned):
				$output1 .= brmda_generic_product_html(null, null, null, true);
			else:
				$output2 .= brmda_generic_product_html(null, null, null, false);
			endif;
	
		endwhile;
		wp_reset_postdata();
		$output = brmda_product_collection($output1, $output2);
	else:
		// Add 'no results' message to output
		$output = brmda_no_digital_assets_found_html();
	endif;
	
	return $output;
}

add_shortcode('digital_assets', 'brmda_shortcode');


// Latest collection shortcode
function brmda_latest_shortcode($atts = array()) {
	
	$attributes = shortcode_atts(array('mode' => ''), $atts);
	
	$output1	= '';	// Owned products (appearing first)
	$output2	= '';	// Unowned products (appearing last)

	// Get products
	$latest_collection		= get_posts(array('post_type' => 'collection'))[0];
	$collection_repeater	= get_field('relevant_products', $latest_collection->ID);
	
	// Add asset HTML to output
	if ($collection_repeater):
		foreach ($collection_repeater as $key => $row):
			$product		= wc_get_product($row['product']);
			$id				= $product->get_id();
			$product_owned	= brmda_user_has_bought_product(wp_get_current_user(), $id);
	
			if ($product_owned):
				$output1 .= brmda_generic_product_html(get_the_title($id), get_permalink($id), get_the_post_thumbnail($id), true);
			else:
				$output2 .= brmda_generic_product_html(get_the_title($id), get_permalink($id), get_the_post_thumbnail($id), false);
			endif;
	
			$output = brmda_product_collection($output1, $output2);
		endforeach;
	
	else:
		// Add 'no results' message to output
		$output = brmda_no_digital_assets_found_html();
	endif;

	return $output;
}

add_shortcode('digital_assets_latest', 'brmda_latest_shortcode');


// On single digital assets, render relevant buttons
function brmda_single_product_links() {
	$output = "";
	
	$user					= wp_get_current_user();
	$product_id				= get_the_id();
	$product 				= wc_get_product($product_id);
	$product_owned			= brmda_user_has_bought_product($user, $product_id);
	if ($product) $stock	= intval($product->get_stock_quantity());
	$print					= get_field('relevant_print');
	if ($print) $print_url	= get_permalink($print->ID);
	$add_to_cart_url		= '?add-to-cart=' . strval($product_id);
	
	if (!$product) return null;
	$files = $product->get_downloads(); 
	
	if ($product_owned):
		
		foreach( $files as $file ):
			$output .= brmda_download_button_html($file);
		endforeach;
	
		if (isset($print_url)):
			$output .= brmda_purchase_print_button_html($print_url);
		endif;
	elseif (!$product_owned && isset($stock) && $stock > 0 && !get_query_var('simple')):
		$output = brmda_add_to_cart_button($add_to_cart_url);
	endif;
	
	return $output;
}

add_shortcode('digital_assets_single_links', 'brmda_single_product_links');


// Allow ?simple=1 to work
function brmda_query_vars( $qvars ) {
	$qvars[] = 'simple';
	return $qvars;
}
add_filter( 'query_vars', 'brmda_query_vars' );


// Product price
function brmda_product_price() {
	$product				= wc_get_product(get_the_id());
	if (!$product) return null;
	
	$price					= wc_get_price_to_display($product);
	if ($product) $stock	= intval($product->get_stock_quantity());
	
	return isset($stock)
		&& $stock > 0
		&& !get_query_var('simple')
		? '£' . $price
		: null;
}

add_shortcode('digital_assets_price', 'brmda_product_price');


// Render amount of items in cart
function brmda_cart_total() {
	return WC()->cart->get_cart_contents_count();
}

add_shortcode('cart_total', 'brmda_cart_total');


// Get most recently added collection
function brmda_latest_collection() {
	$output = '';
	
	if (have_rows('relevant_products')):
		while (have_rows('relevant_products')):
			the_row();
	
			$asset			= get_sub_field('product');
			$title			= get_the_title($asset->ID);
			$permalink		= get_permalink($asset->ID);
			$thumbnail		= get_the_post_thumbnail($asset->ID);
			$output		   .= brmda_generic_product_html($title, $permalink, $thumbnail, null);
	
		endwhile;
	endif;
	
	$output	   .= brmda_info_image_html();
	
	return $output;
}


add_shortcode('latest_collection', 'brmda_latest_collection');


