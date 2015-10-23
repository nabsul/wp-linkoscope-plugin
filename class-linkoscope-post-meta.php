<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
