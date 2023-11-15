<?php

/**
 * Plugin Name:       	BRM Digital Assets
 * Description:       	Adds BRM Paddock Pass' custom digital asset functionality.
 * Version:           	2.0.0
 * Requires PHP:      	5.6
 * Author:            	Chameleon Web Services
 * Author URI:        	https://chameleon.co.uk/
 */


// Defines
define('BRMDA_DIR_PATH', plugin_dir_path( __FILE__ ));


// Includes
require_once(BRMDA_DIR_PATH . 'inc/enqueue.php');
require_once(BRMDA_DIR_PATH . 'inc/functions/members.php');
require_once(BRMDA_DIR_PATH . 'inc/functions/products.php');
require_once(BRMDA_DIR_PATH . 'inc/functions/orders.php');
require_once(BRMDA_DIR_PATH . 'inc/functions/reports.php');
require_once(BRMDA_DIR_PATH . 'inc/admin-notices.php');
require_once(BRMDA_DIR_PATH . 'inc/html-templates.php');
require_once(BRMDA_DIR_PATH . 'inc/shortcodes.php');
require_once(BRMDA_DIR_PATH . 'inc/create-print-product.php');
require_once(BRMDA_DIR_PATH . 'inc/options-page.php');


// Is logged-in user either an Editor or Administrator?
function brmda_current_user_is_admin() {
	return current_user_can('editor') || current_user_can('administrator');
}


// Redirect from single Print when Digital Asset not owned
function brmda_redirect_from_print() {
	// Don't redirect if logged in as admin, or if not on a print page
	if (!brmda_current_post_is_print() || brmda_current_user_is_admin()) return;
	
	// If not owned, redirect
	$user		= wp_get_current_user();
	$asset_id	= get_field('relevant_digital_asset')->ID;
	
	if (!brmda_user_has_bought_product($user, $asset_id)) wp_safe_redirect('/');
}

add_action('wp', 'brmda_redirect_from_print');
