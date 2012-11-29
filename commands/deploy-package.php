<?php
/**
 * http://docs.cpanel.net/twiki/bin/view/SoftwareDevelopmentKit/ApiRef/MysqlRef
 * http://docs.cpanel.net/twiki/bin/view/SoftwareDevelopmentKit/ApiMysql
 * http://docs.cpanel.net/twiki/bin/view/SoftwareDevelopmentKit/ApiFileman#Fileman::fileop
 */
class Hydrogen_Deploy_Package_Command extends Hydrogen_Package_Command implements Hydrogen_Command_Interface {

	function deploy( $package_name, $args = array() ) {
		Hydrogen::msg( "We'll started writing the deploy code soon..." );
	}
}


