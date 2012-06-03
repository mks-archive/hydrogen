<?php
	/*
	 * List of Themes: http://themes.svn.wordpress.org/
	 */

class Hydrogen_Cache_Theme_Command extends Hydrogen_Cache_Command implements Hydrogen_Cache_Command_Interface {
	function run( $args, $switches ) {
		$theme_name = isset( $args[0] ) ? $args[0] : false;
		if ( ! $theme_name ) {
			Hydrogen::fail( "No theme named to get." );
		} else {
			$args['component_name'] = $theme_name;
			unset( $args[0] );
			$this->cache( $this->default_command_line_args( $args ) );
		}
	}
	function normalize_args( $args ) {
		$args = parent::normalize_args( $args );
		$themes_info_file = Hydrogen_Config::get_site_dir( 'wp-admin/includes/theme.php' );
		if ( ! file_exists( $themes_info_file ) ) {
			Hydrogen::fail( "The required include file {$themes_info_file} was not found." );
		}
		require_once( $themes_info_file );
		$theme_info = themes_api( 'theme_information', array( 'slug' => $args['component_name'] ) );
		if ( ! isset( $theme_info->download_link ) || ! isset( $theme_info->version ) ) {
			Hydrogen::fail( "Error getting theme [{$args['component_name']}]." );
		}
		$args = h_parse_args( $args, array(
			'component_type' => 'theme',
			'component_version' => $theme_info->version,
			'download_link' => $theme_info->download_link,
			'base_filename' => "{$args['component_name']}-{$theme_info->version}.zip",
			'cache_dir' => "wordpress.org/themes",
		));
		return parent::post_normalize_args( $args );
	}
}
