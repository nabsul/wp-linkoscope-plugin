<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function linkoscope_has_cap_filter($caps, $cap, $args, WP_User $user){
	$roles = $user->roles;
	$cap = array_filter($cap, function($c){return preg_match('/linkoscope/', $c) == 1;});
	foreach ($cap as $c){
		$caps[$c] = linkoscope_check_capability($c, $roles);
	}

	return $caps;
}

function linkoscope_check_capability($cap, $roles){
	$order = array(
		'administrator' => 0,
		'editor' => 1,
	);

	$roleMap = array(
		'read_private_linkoscope_links' => 'administrator',
		'publish_linkoscope_links' => 'administrator',

		'edit_linkoscope_links' => 'administrator',
		'edit_published_linkoscope_links' => 'administrator',
		'edit_others_linkoscope_links' => 'administrator',

		'delete_published_linkoscope_links' => 'editor',
		'delete_others_linkoscope_links' => 'editor',
	);

	if (!isset($roleMap[$cap])){
		return false;
	}

	foreach ($roles as $role){
		if (!isset($order[$role])){
			return false;
		}

		if ($order[$role] <= $order[$roleMap[$cap]]){
			return true;
		}
	}

	return false;
}
