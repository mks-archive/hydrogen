<?php

class Hydrogen_Queue_Package_Command extends Hydrogen_Package_Command implements Hydrogen_Command_Interface {

	function queue( $package_name, $args = array() ) {
		$queued_package_dir = Hydrogen_Config::get_queue_dir( $package_name );
		if ( is_dir( $queued_package_dir ) ) {
			if ( ! $args['force'] ) {
				Hydrogen::fail( "Package queue directory already exists. Use --force.\n\n\t[file = {$queued_package_dir}] " );
			} else {
				Hydrogen::nuke_dir( $queued_package_dir );
			}
		}
		mkdir( $queued_package_dir );
		$manifest = Hydrogen::get_manifest( $package_name );

		if ( isset( $manifest['core'] ) ) {
			Hydrogen::cache( 'core', $core = $manifest['core'] );
			if ( ! is_dir( $core_dir = "{$queued_package_dir}/core" ) ) {
				mkdir( $core_dir );
			}
			copy( $core['cached_filepath'], "{$core_dir}/{$core['base_filename']}" );
		}
		if ( isset( $manifest['theme'] ) ) {
			Hydrogen::cache( 'theme', $theme = $manifest['theme'] );
			if ( ! is_dir( $theme_dir = "{$queued_package_dir}/themes" ) ) {
				mkdir( $theme_dir );
			}
			copy( $theme['cached_filepath'], "{$theme_dir}/{$theme['base_filename']}" );
		}
		if ( isset( $manifest['plugins'] ) ) {
			if ( ! is_dir( $plugin_dir = "{$queued_package_dir}/plugins" ) ) {
				mkdir( $plugin_dir );
			}
			foreach( $manifest['plugins'] as $plugin ) {
				Hydrogen::cache( 'plugin', $plugin );
				copy( $plugin['cached_filepath'], "{$plugin_dir}/{$plugin['base_filename']}" );
			}
		}
	}
}



