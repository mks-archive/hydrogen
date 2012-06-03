<?php

class Hydrogen_Cache_Core_Command extends Hydrogen_Cache_Command implements Hydrogen_Cache_Command_Interface {

	function run( $args, $switches ) {
		$this->cache( $this->default_command_line_args( $args ) );
	}
	function normalize_args( $args ) {
		$args = parent::normalize_args( $args );
		$response = wp_remote_request('http://api.wordpress.org/core/version-check/1.6/');
		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			Hydrogen::fail( "Something went wrong." );
		}
		$body = unserialize($response['body']);
		$download_link = $body['offers'][0]['download'];
		$version = $body['offers'][0]['current'];
		$args = h_parse_args( $args, array(
			'component_name' => 'wordpress-core',
			'component_type' => 'core',
			'component_version' => $version,
			'download_link' => $download_link,
			'base_filename' => "wordpress-{$version}.zip",
			'cache_dir'  => "wordpress.org/core",
		));
		return parent::post_normalize_args( $args );
	}
}



