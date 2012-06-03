<?php

class Hydrogen_Build_Package_Command extends Hydrogen_Package_Command implements Hydrogen_Command_Interface {

	function build( $package_name, $args = array() ) {
		$queued_package_dir = Hydrogen_Config::get_queue_dir( $package_name );
		if ( file_exists( $package_file = "{$queued_package_dir}.hpkg" ) && ! $args['force'] ) {
			Hydrogen::fail( "Package already exists. Use --force.\n\n\t[file = {$package_file}] " );
		}

		Hydrogen::msg( "Building Hydrogen package: {$package_name}.hpkg" );
		Hydrogen::get_command( 'queue-package' )->queue( $package_name, $args );
		$filepath = $this->zip_package( $package_name, $args );
		Hydrogen::msg( "Package built: {$filepath}" );
	}

	function zip_package( $package_name, $args ) {
		$queued_package_dir = Hydrogen_Config::get_queue_dir( $package_name );
		$filepath = Hydrogen::zip_dir( $queued_package_dir, $package_name );
		Hydrogen::nuke_dir( $queued_package_dir );
		return $filepath;
	}
}


