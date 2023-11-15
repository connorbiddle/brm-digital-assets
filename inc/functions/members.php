<?php

// Get all WordPress users with BRM Paddock Pass user roles
function brmda_get_members() {
	// 	Not working
	// 	$brm_roles = array
	// 		('um_v16-membership', 'um_founder-membership', 'um_p578-member', 'um_ambassador');

	// 	$members = get_users(array('role_in' => $brm_roles));
	
	$users		= get_users();
	$members	= array();
	
	foreach ($users as $user):
		if (user_can($user->ID, 'um_v16-membership') ||
			user_can($user->ID, 'um_founder-membership') ||
			user_can($user->ID, 'um_p578-member') ||
			user_can($user->ID, 'um_ambassador')):
			array_push($members, $user);
		endif;
	endforeach;
	
	return $members;
}