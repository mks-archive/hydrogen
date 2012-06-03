<?php

class Hydrogen_Config {
	static $config;
	static $config_dir;
	static $aliases = array();
	static $hosts = array();
	static function load() {
		$dir = HYDROGEN_ROOT_DIR;
		self::$config = Hydrogen::load_yaml( $hydrogen_file = "{$dir}/.hydrogen" );
		if ( empty( self::$config['config_dir'] ) ) {
			Hydrogen::fail( "There is not [config_dir] entry in the core .hydrogen file: {$hydrogen_file}" );
		} else {
			self::$config_dir = self::$config['config_dir'];
			unset( self::$config['config_dir'] );
		}

		if ( isset( self::$config['aliases'] ) ) {
			self::$aliases = self::$config['aliases'];
			unset( self::$config['aliases'] );
		}
		if ( isset( self::$config['hosts'] ) ) {
			self::$hosts = self::$config['hosts'];
			unset( self::$config['hosts'] );
		}

		$config_file = self::get_config_dir(".hydrogen");
		if ( ! file_exists( $config_file ) ) {
			Hydrogen::fail( "The expected .hydrogen config file was not found: {$hydrogen_file}" );
		}
		self::merge_config( $config_file );
		self::normalize_hosts();
		return;
	}
	static function normalize_hosts() {
		foreach( self::$hosts as $host_key => $host ) {
			if ( empty( self::$hosts[$host_key]['type'] ) ) {
				self::$hosts[$host_key]['type'] = 'cpanel';
			} else {
				self::$hosts[$host_key]['type'] = strtolower( self::$hosts[$host_key]['type'] );
			}
		}
	}
	static function merge_config( $new_config ) {
		$new_yaml = Hydrogen::load_yaml( $new_config );
		if ( isset( $new_yaml['aliases'] ) ) {
			self::$aliases = array_merge( self::$aliases, $new_yaml['aliases'] );
			unset( $new_yaml['aliases'] );
		}
		if ( isset( $new_yaml['hosts'] ) ) {
			self::$hosts = array_merge( self::$hosts, $new_yaml['hosts'] );
			unset( $new_yaml['hosts'] );
		}
		self::$config = $new_yaml;
		return;
	}
	static function get_values() {
		$dir = HYDROGEN_ROOT_DIR;
		return Hydrogen::load_yaml( "{$dir}/.hydrogen" );
	}
	static function put_values( $values ) {
		$dir = HYDROGEN_ROOT_DIR;
		Hydrogen::save_yaml( "{$dir}/.hydrogen", $values );
	}
	static function get_dir( $type, $file = '' ) {
		$key = "{$type}_dir";
		$dir = isset( self::$config[$key] ) ? self::$config[$key] : HYDROGEN_ROOT_DIR . "/{$type}";
		if ( ! file_exists( $dir ) ) {
			mkdir( $dir );
		}
		return "{$dir}/{$file}";
	}
	static function get_cache_dir( $file = '' ) {
		return self::get_dir( 'cache', $file );
	}
	static function get_site_dir( $file = '' ) {
		return self::get_dir( 'site', $file );
	}
	static function get_build_dir( $file = '' ) {
		return self::get_dir( 'build', $file );
	}
	static function get_queue_dir( $file = '' ) {
		return self::get_dir( 'queue', $file );
	}
	static function get_manifests_dir( $file = '' ) {
		return self::get_dir( 'manifests', $file );
	}
	static function get_config_dir( $file = '' ) {
		return rtrim( self::$config_dir, '/' ) . ( empty( $file ) ? '' : "/{$file}" );
	}
}
