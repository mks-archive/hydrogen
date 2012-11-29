<?php

class Hydrogen_Show_Pwd_Command {
	function run( $args, $switches ) {
		echo shell_exec( "pwd" );
	}
}

