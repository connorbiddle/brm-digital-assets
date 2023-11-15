<?php

// Register options page
function brmda_options_page() {
	$page = add_management_page(
		'Distribute Assets',
		'Distribute Assets',
		'manage_options',
		'brm-distribute-assets',
		'brmda_options_page_html',
		0.00143
	);
}

add_action('admin_menu', 'brmda_options_page');


// Options page HTML
function brmda_options_page_html() {
	if (brmda_submitting_options_page()) brmda_distribute_digital_assets($_POST);  ?>

	<div class="wrap">
		<h1>BRM Paddock Pass - Distribute Digital Assets</h1>
		<p>Select a Digital Collection below to automatically distribute its Digital Assets between members of the site.</p>
		<p>Founder members will receive two assets. All other members will receive one.</p>
		<p>A report will be created and saved detailing who received what.</p> <br />
		
		<form action="" method="post">
			<h2 style="font-weight: normal">Collection to distribute:</h2>
			<select id="chosen-collection" name="chosen-collection">
				<?php brmda_collection_options_html(); ?>
			</select>
			<?php submit_button('Distribute Assets'); ?>
		</form>
	</div>
<?php }


// Render HTML <option> elements (for use inside <select>)
function brmda_collection_options_html() {
	$collections = new WP_Query(array('post_type' => 'collection'));
											  
	if ($collections->have_posts()):
		while ($collections->have_posts()):
			$collections->the_post(); ?>

			<option value="<?php the_id(); ?>">
				<?php the_title(); ?>
			</option> <?php

		endwhile;
	endif;
	
	wp_reset_postdata();
}


// On form submission, distribute assets and create a report
function brmda_distribute_digital_assets($post) {
	// Get Collection and its products
	$collection_id			= $post['chosen-collection'];
	$collection				= get_post($collection_id);
	$collection_repeater	= get_field('relevant_products', $collection_id);
	
	// Loop through members and create orders
	$members				= brmda_get_members();
	$orders					= array();
	
	foreach ($members as $member):
		$order = brmda_create_order($member, $collection_repeater);
		if ($order) $orders[] = $order;
	endforeach;

	// Create report and output admin notice
	if (!empty($orders)):
		$report = brmda_create_report($collection, $orders); ?>

		<div class="notice notice-success is-dismissible">
			<p>Distributed collection: <?php echo $collection->post_title; ?></p>
			<p><a href="<?php echo get_permalink($report) ?>">View report</a></p>
		</div> <?php
	else: ?>
		<div class="notice notice-info is-dismissible">
			<p>Nothing was distributed from: <?php echo $collection->post_title; ?></p>
			<p>Possible reasons are that all members already own all assets, or there are no assets left in stock.</p>
		</div> <?php
	endif;
	
}


// Check if options page has been submitted
function brmda_submitting_options_page() {
	return array_key_exists('submit', $_POST) && $_POST['submit'] === 'Distribute Assets';
}

