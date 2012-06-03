<?php

class Hydrogen_Show_Manifest_Command extends Hydrogen_Command implements Hydrogen_Command_Interface {

	function run( $args, $switches ) {
		$manifest = Hydrogen::get_manifest( $args[0] );
		print_r( $manifest );
	}

}



