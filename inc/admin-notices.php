<?php


// Check if not on specified admin screens
function brmda_not_on_screens(...$slugs) {
	$screen = get_current_screen()->id;
	return !in_array($screen, $slugs);
}


// Add admin notice to Digital Collections screen
function brmda_collections_admin_notice() {
	if (brmda_not_on_screens('edit-collection', 'collection')) return;
	$src = plugin_dir_url(__FILE__) .'../img/logo.png'; ?>

    <div class="notice is-dismissible brm-notice">
		<img src="<?php echo $src; ?>" class="brm-notice-logo" />
		<div class="brm-notice-content">
			<p>
				Go to <strong>Tools > Distribute Assets</strong> to trigger the monthly collectable giveaway.
			</p>
		</div>
    </div>
<?php }

add_action( 'admin_notices', 'brmda_collections_admin_notice' );


// Add admin notice to Reports and single Report screens
function brmda_reports_admin_notice() {
	if (brmda_not_on_screens('edit-report', 'report')) return;
	$src = plugin_dir_url(__FILE__) .'../img/logo.png'; ?>

    <div class="notice is-dismissible brm-notice">
		<img src="<?php echo $src; ?>" class="brm-notice-logo" />
		<div class="brm-notice-content">
			<p><strong>Reports</strong> are generated automatically when a giveaway is run. They list the recipients and remaining stock of relevant assets.</p>
			<p>
				They are set to Private by default and are therefore only accessible to BRM Paddock Pass administrators. They are 
				<strong>not intended to be created or edited manually</strong>.
			</p>
			
		</div>
    </div>
<?php }

add_action( 'admin_notices', 'brmda_reports_admin_notice' );

