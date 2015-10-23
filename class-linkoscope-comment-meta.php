<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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

