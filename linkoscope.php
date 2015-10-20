<?php
/**
 * Plugin Name: LinkoScope REST API Extension
 * Description: Adds a post type and REST endpoint for LinkoScope links, used by the LinkoScope WebApp.
 * Author: Nabeel Sulieman
 * Version: 0.1
 * Plugin URI: https://github.com/nabsul/wp-linkoscope-plugin
 * License: MIT
 */

function linkoscope_post_type_init () {
	$supports = array('title',
		'editor',
		'author',
		'content',
		'excerpt',
		'comments',
		'page-attributes',
		'custom-fields',
	);

	$typeArgs = array(
		'label'               => __('Linkoscope Links'),
		'public'              => true, // if false, it won't show in WP-API
		'show_in_rest'        => true, // this has to be set for it to have a route
		'rest_base'           => 'linkolink', // api root: `/wp/v2/linkolink`
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => false,
		'supports'            => $supports,
		'capability_type'     => 'linkoscope_link',
		'map_meta_cap'        => true,
	);

	register_post_type( 'linkoscope_link', $typeArgs );
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

function linkoscope_has_cap_filter($caps, $cap, $args, WP_User $user){
	$roles = $user->roles;
	$cap = array_filter($cap, function($c){return preg_match('/linkoscope/', $c) == 1;});
	foreach ($cap as $c){
		$caps[$c] = linkoscope_check_capability($c, $roles);
	}

	return $caps;
}

add_filter('user_has_cap', 'linkoscope_has_cap_filter', 10, 4);

function linkoscope_add_rest_query($vars)
{
	$vars[] = 'meta_key';
	return $vars;
}

function linkoscope_set_meta( $value, $object, $field_name ) {
	return update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
}

function linkoscope_get_meta( $object, $field_name, $request ) {
	if ($field_name == 'author_name'){
		$user = get_userdata($object['author']);
		return $user->display_name;
	}

	if ($field_name == 'comment_count'){
		$comments = get_comment_count($object['id']);
		return $comments['approved'];
	}

	$meta = get_post_meta( $object[ 'id' ], $field_name );
	$result = null;
	if (is_array($meta) && count($meta) > 0)
		$result = $meta[0];
	return $result;
}

function linkoscope_set_comment_meta( $value, $object, $field_name ) {
	if ($field_name == 'author_name') return;
	return update_comment_meta( $object->comment_ID, $field_name, strip_tags( $value ) );
}

function linkoscope_get_comment_meta( $object, $field_name, $request ) {
	$meta = get_comment_meta( $object[ 'id' ], $field_name );
	$result = null;
	if (is_array($meta) && count($meta) > 0)
		$result = $meta[0];
	return $result;
}

function linkoscope_register_fields(){
	$callbacks = array(
		'get_callback'    => 'linkoscope_get_meta',
		'update_callback' => 'linkoscope_set_meta',
		'schema'          => null,
	);

	register_api_field( 'linkoscope_link','linkoscope_score', $callbacks);
	register_api_field( 'linkoscope_link','linkoscope_likes', $callbacks);
	register_api_field( 'linkoscope_link','author_name', $callbacks);
	register_api_field( 'linkoscope_link','comment_count', $callbacks);

	$callbacks = array(
		'get_callback'    => 'linkoscope_get_comment_meta',
		'update_callback' => 'linkoscope_set_comment_meta',
		'schema'          => null,
	);

	register_api_field( 'comment', 'linkoscope_likes', $callbacks);

	$raw = array(
		'schema' => array(
			'type' => 'string',
			'raw' => array (
				'type'        => 'string',
				'description' => 'Title for the object, as it exists in the database.',
				'context' => array( 'view', 'edit' ) ),
		)
	);

	$int = array(
		'schema' => array(
			'type' => 'int',
			'description'  => 'Votes on a comment.',
			'type'         => 'integer',
		),
	);

	register_api_field( 'linkoscope_link','title', $raw);
	register_api_field( 'linkoscope_link','content', $raw);
	register_api_field( 'comment','content', $raw);
	register_api_field( 'comment', 'karma', $int);
}

add_action('init', 'linkoscope_post_type_init');

add_action( 'rest_api_init', 'linkoscope_register_fields' );

add_filter('rest_query_vars', 'linkoscope_add_rest_query');

