<?php

// Enqueue front end styles
function brmda_enqueue_scripts() {
	wp_enqueue_style( 'brmda-front-styles', plugin_dir_url(__FILE__) .'../css/brmda-front.css' );
}

add_action('wp_enqueue_scripts', 'brmda_enqueue_scripts');


// Enqueue back end styles
function brmda_enqueue_admin_scripts() {
	wp_enqueue_style( 'brmda-admin-styles', plugin_dir_url(__FILE__) .'../css/brmda-back.css' );
}

add_action('admin_enqueue_scripts', 'brmda_enqueue_admin_scripts');
