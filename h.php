<?php
	/*
	 * Hydrogen - A WordPress-specific command line tool for configuration management, site provisioning and site deployment/redeployment.
	 *
	 * GitHub: https://github.com/getsunrise/hydrogen
	 * Copyright: (c) 2012 Mike Schinkel and NewClarity LLC
	 * License: GPLv2+
	 *
	 */
	include('includes/hydrogen-loader.php');

	echo "\n";

	$cmdline = Hydrogen::parse_command_line();
	Hydrogen::run_command( $cmdline['command'], $cmdline['args'], $cmdline['switches'] );

	echo "\n\n";

