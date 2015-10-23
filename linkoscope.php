<?php
/**
 * Plugin Name: LinkoScope REST API Extension
 * Description: Adds a post type and REST endpoint for LinkoScope links, used by the LinkoScope WebApp.
 * Author: Nabeel Sulieman
 * Version: 0.1
 * Plugin URI: https://github.com/nabsul/wp-linkoscope-plugin
 * License: MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include __DIR__ . '/class-linkoscope-post-type.php';
add_action('init', 'linkoscope_post_type_init');
add_filter('rest_query_vars', 'linkoscope_add_rest_query');

include __DIR__ . '/class-linkoscope-capabilities.php';
add_filter('user_has_cap', 'linkoscope_has_cap_filter', 10, 4);

include __DIR__ . '/class-linkoscope-comment-meta.php';
include __DIR__ . '/class-linkoscope-post-meta.php';
add_action( 'rest_api_init', 'linkoscope_register_fields' );
