<?php
class Hydrogen_Show_Help_Command extends Hydrogen_Command implements Hydrogen_Command_Interface {
	function run( $args, $switches ) {
		$help =<<<HELP
This is the help file for Hydrogen.
Clearly we've got some work to do here.
HELP;
		echo $help;
	}
}
