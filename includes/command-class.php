<?php

class Hydrogen_Command {
	var $command_name;
	var $filepath;
	var $class_name;
	function run( $args, $switches ) {
		Hydrogen::fail( "The command class [$class_name] must implement a run(\$args,\$switches) method." );
	}
	/**
	 * Default the $args - Make sure we have the args that most commands expect.
	 *
	 * @static
	 * @param array $args
	 * @return array
	 */
	function default_command_line_args( $args ) {
		return h_parse_args( $args, array(
			'force' => false
		));
	}
}

