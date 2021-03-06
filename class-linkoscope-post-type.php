<?php
/**
 * Class LinkoScope_Post_Type
 * Registers the new post type for LinkoScope.
 *
 * Created by Nabeel Sulieman.
 * User: Nabeel
 * Date: 2015-10-23
 * Time: 12:11 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LinkoScope_Post_Type' ) ) :
	class LinkoScope_Post_Type {
		public function run() {
			add_action( 'init', [ $this, 'post_type_init' ] );
			add_filter( 'rest_query_vars', [ $this, 'add_rest_query_field' ] );
		}

		public function post_type_init() {
			$supports = array(
				'title',
				'editor',
				'author',
				'content',
				'excerpt',
				'comments',
				'page-attributes',
				'custom-fields',
			);

			$typeArgs = array(
				'label' => __( 'Linkoscope Links' ),
				'public' => true, // if false, it won't show in WP-API
				'show_in_rest' => true, // this has to be set for it to have a route
				'rest_base' => 'linkolink', // api root: `/wp/v2/linkolink`
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'supports' => $supports,
				'capability_type' => 'linkoscope_link',
				'map_meta_cap' => false,
				'capabilities' => array(
					'create_posts' => 'create_linkoscope_links'
				),
			);

			register_post_type( 'linkoscope_link', $typeArgs );
		}

		public function add_rest_query_field( $vars ) {
			$vars[] = 'meta_key';
			return $vars;
		}
	}
endif;
