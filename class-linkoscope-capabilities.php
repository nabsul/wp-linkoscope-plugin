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
			add_filter( 'user_has_cap', [ $this, 'cap_filter' ], 10, 4 );
		}

		public function cap_filter( $caps, $cap, $args, WP_User $user ) {
			$roles = $user->roles;
			$cap = array_filter( $cap, function ( $c ) {
				return preg_match( '/linkoscope/', $c ) == 1;
			} );
			foreach ( $cap as $c ) {
				$caps[$c] = $this->check_capability( $c, $roles );
			}

			return $caps;
		}

		private function check_capability( $cap, $roles ) {
			$order = array(
				'administrator' => 0,
				'editor' => 1,
			);

			$roleMap = array(
				'read_private_linkoscope_links' => 'administrator',
				'publish_linkoscope_links' => 'administrator',

				'edit_linkoscope_links' => 'administrator',
				'edit_published_linkoscope_links' => 'administrator',
				'edit_others_linkoscope_links' => 'administrator',

				'delete_published_linkoscope_links' => 'editor',
				'delete_others_linkoscope_links' => 'editor',
			);

			if ( ! isset( $roleMap[$cap] ) ) {
				return false;
			}

			foreach ( $roles as $role ) {
				if ( ! isset( $order[$role] ) ) {
					return false;
				}

				if ( $order[$role] <= $order[$roleMap[$cap]] ) {
					return true;
				}
			}

			return false;
		}
	}
endif;

