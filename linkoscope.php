<?php
/**
 * Plugin Name: Linkoscope Post Type
 * Version 0.1
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
		'capability_type'     => 'post',
	);
	register_post_type( 'linkoscope_link', $typeArgs );
}

function linkoscope_add_rest_query($vars)
{
	$vars[] = 'meta_key';
	return $vars;
}

add_action('init', 'linkoscope_post_type_init');

add_filter('rest_query_vars', 'linkoscope_add_rest_query');