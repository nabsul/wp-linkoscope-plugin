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

if ( ! class_exists( 'LinkoScope_Plugin' ) ) :
	include_once __DIR__ . '/class-linkoscope-post-type.php';
	include_once __DIR__ . '/class-linkoscope-capabilities.php';
	include_once __DIR__ . '/class-linkoscope-comment-meta.php';
	include_once __DIR__ . '/class-linkoscope-post-meta.php';

	class LinkoScope_Plugin {
		public function run() {
			( new LinkoScope_Post_Type() )->run();
			( new LinkoScope_Post_Meta() )->run();
			( new LinkoScope_Comment_Meta() )->run();
			( new LinkoScope_Post_Type() )->run();
			( new LinkoScope_Capabilities() )->run();
		}
	}

	( new LinkoScope_Plugin() )->run();
endif;
