<?php

// On Digital Asset product creation, also create matching Print product
function brmda_on_create_digital_asset( $new_status, $old_status, $post ) {
	// Exit if not creating a Digital Asset
    if (!brmda_creating_new_digital_asset($new_status, $old_status, $post)) return;
	
	// Create Print from Asset
	$asset = wc_get_product($post->ID);
	brmda_create_print_from_asset($asset);
}

add_action( 'transition_post_status', 'brmda_on_create_digital_asset', 10, 3 );


// Create a matching Print product from a Digital Asset
function brmda_create_print_from_asset($asset) {
	$print_id = wp_insert_post(array(
		'post_title'	=> $asset->get_name() . ' (Print)',
		'post_type' 	=> 'product',
		'post_status' 	=> 'publish',
		'post_content'	=> $asset->get_description()
	));

	wp_set_object_terms($print_id, 'print', 'product_cat', true);
	
	return $print_id;
}


// Check if creating a new Digital Asset or not
function brmda_creating_new_digital_asset($new_status, $old_status, $post) {
	return  'product' === $post->post_type && 
			'publish' === $new_status &&
			'publish' !== $old_status &&
			has_term('digital-asset', 'product_cat', $post->ID);
}



