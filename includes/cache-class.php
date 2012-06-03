<?php

class Hydrogen_Cache {
	static function initialize() {
		$dir = HYDROGEN_ROOT_DIR;
		if ( ! file_exists( $cache_dir = Hydrogen_Config::get_cache_dir() ) ) {
			mkdir( $cache_dir );
		}
		if ( ! file_exists( $cache_dir = "{$cache_dir}/wordpress.org" ) ) {
			mkdir( $cache_dir );
		}
		foreach( array( 'core', 'plugins', 'themes' ) as $subdir ) {
			if ( ! file_exists( $subdir = "{$cache_dir}/{$subdir}" ) ) {
				mkdir( $subdir );
			}
		}
	}
}
