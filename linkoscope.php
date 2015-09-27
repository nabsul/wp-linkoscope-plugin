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

	$capabilities = array(
		'edit_post' => 'edit_others_posts',
		'read_post',
		'delete_post',
		'edit_posts',
		'edit_others_posts',
		'publish_posts',
		'read_private_posts'
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
		//'capabilities'        => $capabilities,
	);
	register_post_type( 'linkoscope_link', $typeArgs );

	$roles = array('editor', 'author', 'contributor', 'subscriber');
	$caps = array(
		'edit_others_linkoscope_links',
		'edit_published_linkoscope_links',
		'edit_published_linkoscope_links',
		'edit_linkoscope_links',
		'publish_linkoscope_links',
	);

	foreach($roles as $role) {
		foreach ($caps as $cap) {
			get_role($role)->add_cap($cap);
		}
	}
}

function linkoscope_add_rest_query($vars)
{
	$vars[] = 'meta_key';
	return $vars;
}

function linkoscope_set_meta( $value, $object, $field_name ) {
	$ret = update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
	return $ret;
}

function linkoscope_get_meta( $object, $field_name, $request ) {
	return get_post_meta( $object[ 'id' ], $field_name );
}

function linkoscope_register_fields(){
	$callbacks = array(
		'get_callback'    => 'linkoscope_get_meta',
		'update_callback' => 'linkoscope_set_meta',
		'schema'          => null,
	);

	register_api_field( 'linkoscope_link','linkoscope_score', $callbacks);
	register_api_field( 'linkoscope_link','linkoscope_likes', $callbacks);


	$raw = array(
		'schema' => array(
			'type' => 'string',
			'raw' => array (
				'type'        => 'string',
				'description' => 'Title for the object, as it exists in the database.',
				'context' => array( 'view', 'edit' ) ),
		)
	);

	register_api_field( 'linkoscope_link','title', $raw);
	register_api_field( 'linkoscope_link','content', $raw);
}

add_action('init', 'linkoscope_post_type_init');

add_action( 'rest_api_init', 'linkoscope_register_fields' );

add_filter('rest_query_vars', 'linkoscope_add_rest_query');

