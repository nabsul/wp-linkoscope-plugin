<?php
/**
 * Class LinkoScope_Post_Meta
 * Handles additional fields in the REST API for LinkoScope type posts.
 *
 * Created by Nabeel Sulieman.
 * User: Nabeel
 * Date: 2015-10-23
 * Time: 12:11 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LinkoScope_Post_Meta' ) ) :
	class LinkoScope_Post_Meta{
		public function run(){
			add_action( 'rest_api_init', [$this, 'register_fields'] );
		}

		public function register_fields(){
			$meta_callbacks = array(
				'get_callback'    => [$this, 'get_meta'],
				'update_callback' => [$this, 'set_meta'],
				'schema'          => null,
			);
			register_api_field( 'linkoscope_link','linkoscope_score', $meta_callbacks);
			register_api_field( 'linkoscope_link','linkoscope_likes', $meta_callbacks);


			$author_name_callbacks = array(
				'get_callback'    => [$this, 'get_author_name'],
				'update_callback' => [$this, 'no_op'],
				'schema'          => null,
			);
			register_api_field( 'linkoscope_link','author_name', $author_name_callbacks);

			$comment_count_callbacks = array(
				'get_callback'    => [$this, 'get_comment_count'],
				'update_callback' => [$this, 'no_op'],
				'schema'          => null,
			);
			register_api_field( 'linkoscope_link','comment_count', $comment_count_callbacks);

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

		public function set_meta( $value, $object, $field_name ) {
			return update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
		}

		public function get_meta( $object, $field_name, $request ) {
			$meta = get_post_meta( $object[ 'id' ], $field_name );
			$result = null;
			if (is_array($meta) && count($meta) > 0)
				$result = $meta[0];
			return $result;
		}

		public function get_author_name($object, $field_name, $request){
			$user = get_userdata($object['author']);
			return $user->display_name;
		}

		public function get_comment_count($object, $field_name, $request){
			$comments = get_comment_count($object['id']);
			return $comments['approved'];
		}

		public function no_op($value, $object, $field_name){
		}
	}
endif;

