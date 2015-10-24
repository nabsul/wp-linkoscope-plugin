<?php
/**
 * Class LinkoScope_Capabilities
 * Sets the capabilities and permissions for LinkoScope users.
 *
 * Created by Nabeel Sulieman.
 * User: Nabeel
 * Date: 2015-10-23
 * Time: 12:11 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LinkoScope_Capabilities' ) ) :
	class LinkoScope_Capabilities {
		public function run() {
			add_action( 'activate_linkoscope/linkoscope.php', [ $this, 'add_caps' ] );
			add_action( 'deactivate_linkoscope/linkoscope.php', [ $this, 'delete_caps' ] );
		}

		public function add_caps() {
			foreach ( $this->caps as $roleName => $caps ) {
				$role = get_role( $roleName );
				foreach ( $caps as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

		public function delete_caps() {
			foreach ( $this->caps as $roleName => $caps ) {
				$role = get_role( $roleName );
				foreach ( $role->capabilities as $cap => $is_set ) {
					if ( preg_match( '/linkoscope\_link/', $cap ) == 1 ) {
						$role->remove_cap( $cap );
					}
				}
			}
		}

		private $caps = array(
			'administrator' => array(
				'read_linkoscope_link',
				'create_linkoscope_links',
				'publish_linkoscope_links',
				'delete_linkoscope_link',
				'edit_linkoscope_link',
				'edit_linkoscope_links',
				'edit_others_linkoscope_links',
			),
			'editor' => array(
				'read_linkoscope_link',
				'create_linkoscope_links',
				'publish_linkoscope_links',
				'edit_linkoscope_link',
				'edit_linkoscope_links',
				'edit_others_linkoscope_links',
			),
			'author' => array(
				'read_linkoscope_link',
				'create_linkoscope_links',
				'publish_linkoscope_links',
				'delete_linkoscope_link',
				'edit_linkoscope_link',
			),
			'contributor' => array(
				'read_linkoscope_link',
			),
			'subscriber' => array(
				'read_linkoscope_link',
			),
		);
	}
endif;

