<?php

class Hydrogen_Package_Command extends Hydrogen_Command {
	var $action = false;
	function __construct() {
		$this->action = strtolower( preg_replace( '#^Hydrogen_([^_]+?)_Package_Command$#', '$1', get_class( $this ) ) );
	}
	function run( $args, $switches ) {
		$package_name = isset( $args[0] ) ? $args[0] : false;
		if ( ! $package_name ) {
			Hydrogen::fail( "No package name specified to build." );
		} else {
			$args['package_name'] = $package_name;
			unset( $args[0] );
			call_user_func_array(
				array( $this, $this->action ),
				array( $package_name, $this->default_command_line_args( $args )
			));

		}
	}

}
