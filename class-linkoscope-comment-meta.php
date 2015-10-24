<?php
/**
 * Class LinkoScope_Comment_Meta
 * Handles additional fields in the REST API for comments.
 *
 * Created by Nabeel Sulieman.
 * User: Nabeel
 * Date: 2015-10-23
 * Time: 12:11 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LinkoScope_Comment_Meta' ) ) :
	class LinkoScope_Comment_Meta{
		public function run(){
			add_action( 'rest_api_init', [$this, 'register_fields'] );
		}

		public function register_fields(){
			$callbacks = array(
				'get_callback'    => [$this, 'get_meta'],
				'update_callback' => [$this, 'set_meta'],
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
			register_api_field( 'comment','content', $raw);

			$int = array(
				'schema' => array(
					'type' => 'int',
					'description'  => 'Votes on a comment.',
					'type'         => 'integer',
				),
			);
			register_api_field( 'comment', 'karma', $int);
		}

		public function set_meta( $value, $object, $field_name ) {
			if ($field_name == 'author_name') return;
			return update_comment_meta( $object->comment_ID, $field_name, strip_tags( $value ) );
		}

		public function get_meta( $object, $field_name, $request ) {
			$meta = get_comment_meta( $object[ 'id' ], $field_name );
			$result = null;
			if (is_array($meta) && count($meta) > 0)
				$result = $meta[0];
			return $result;
		}
	}
endif;


