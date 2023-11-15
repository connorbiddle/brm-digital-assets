<?php

// --------------------------------------- FRONT END ----------------------------------------

// Product collection HTML
function brmda_product_collection($output1, $output2) {
	return '<div class="brm-products">' . $output1 . $output2 . '</div>';
}

// Product in collection HTML
function brmda_generic_product_html($title = null, $permalink = null, $thumbnail = null, $owned = null) {
	// Establish type of product to render
	if ($owned === true)	$product_type = 'owned';
	if ($owned === false)	$product_type = 'unowned';
	if ($owned === null)	$product_type = 'basic';
	
	// Prepare data for interpolation
	$permalink		= $permalink		? $permalink		: get_permalink();
	$thumbnail		= $thumbnail		? $thumbnail		: get_the_post_thumbnail();
	$title 			= $title			? $title			: get_the_title();
	
	// Basic product sends user to simpler single product page
	if ($product_type === 'basic') $permalink .= '?simple=1';
	
	// Output
	return "
		<figure class='brm-product " . $product_type . "'>
			<a href='" . $permalink . "'>
				" . $thumbnail . "
			</a>
			<figcaption class='brm-caption'>" . $title . "</figcaption>
		</figure>
	";
}


// Empty digital assets message
function brmda_no_digital_assets_found_html() {
	return '<h2>No digital assets found!</h2>';
}


// Info image HTML - comes after all products
function brmda_info_image_html() {
	$src = plugin_dir_url(__FILE__) .'../img/info.png';
	
	return "
		<figure class='brm-product basic info'>
			<img src='" . $src . "' />
			<figcaption class='brm-info-caption'>
				Please click on any image to read their stories
			</figcaption>
		</figure>
	";
}


// Download Digital Asset button
function brmda_download_button_html($file) {
	return '
		<a class="brm-button" href="' . $file['file'] . '" download target="_blank">
			<i class="fa fa-download"></i> Download Collectable
		</a>
	';
}


// Purchase related Print button
function brmda_purchase_print_button_html($print_url) {
	return "<a class='brm-button' href='" . $print_url . "'>Purchase Print</a>";
}


// Add to Cart button
function brmda_add_to_cart_button($add_to_cart_url) {
	if (has_term(45, 'product_cat')) {
		$text = 'Add Collectable To Cart';
	} else if (has_term(47, 'product_cat')) {
		$text = 'Add Print To Cart';
	}

	
	return "<a class='brm-button' href='" . $add_to_cart_url . "'>" . $text . "</a>";
}

