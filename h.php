<?php

	include('includes/hydrogen-loader.php');

	echo "\n";

	$cmdline = Hydrogen::parse_command_line();
	Hydrogen::run_command( $cmdline['command'], $cmdline['args'], $cmdline['switches'] );

	echo "\n\n";

