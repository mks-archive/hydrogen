<?php
	/*
	 * List of Plugins: http://plugins.svn.wordpress.org/
	 */

class Hydrogen_Cache_Plugin_Command extends Hydrogen_Cache_Command implements Hydrogen_Cache_Command_Interface {
	function run( $args, $switches ) {
		$plugin_name = isset( $args[0] ) ? $args[0] : false;
		if ( ! $plugin_name ) {
			Hydrogen::fail( "No plugin named to get." );
		} else {
			$args['component_name'] = $plugin_name;
			unset( $args[0] );
			$this->cache( $this->default_command_line_args( $args ) );
		}
	}
	function normalize_args( $args ) {
		$args = parent::normalize_args( $args );
		$plugins_install_file = Hydrogen_Config::get_site_dir( 'wp-admin/includes/plugin-install.php' );
		if ( ! file_exists( $plugins_install_file ) ) {
			Hydrogen::fail( "The required include file {$plugins_install_file} was not found." );
		}
		require_once( $plugins_install_file );
		$plugin_info = plugins_api( 'plugin_information', array( 'slug' => $args['component_name'] ) );
		if ( ! isset( $plugin_info->download_link ) || ! isset( $plugin_info->version ) ) {
			Hydrogen::fail( "Error getting plugin [{$args['component_name']}]." );
		}
		$args = h_parse_args( $args, array(
			'component_type' => 'plugin',
			'component_version' => $plugin_info->version,
			'download_link' => $plugin_info->download_link,
			'base_filename' => "{$args['component_name']}-{$plugin_info->version}.zip",
			'cache_dir' => "wordpress.org/plugins",
		));
		return parent::post_normalize_args( $args );
	}
}
