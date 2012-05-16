<?php
if(!defined('WP_UNINSTALL_PLUGIN')) {
	echo "Hi there! Nice try. Come again.";
	exit;
}

// remove options
delete_option('path_access');
delete_option('path_access_roles');

// remove cap
$editable_roles = get_editable_roles();
foreach ( $editable_roles as $role => $details ) :
	$role = esc_attr($role);
	$thisRole = get_role($role);
	$thisRole->remove_cap('path_access');
endforeach;
