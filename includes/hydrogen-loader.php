<?php
define( 'IN_HYDROGEN', true );
define( 'HYDROGEN_INCLUDES_DIR', dirname( __FILE__ ) );
define( 'HYDROGEN_ROOT_DIR', dirname( HYDROGEN_INCLUDES_DIR ) );
include( HYDROGEN_INCLUDES_DIR . '/functions.php' );
include( HYDROGEN_INCLUDES_DIR . '/hydrogen-class.php' );
include( HYDROGEN_INCLUDES_DIR . '/config-class.php' );
include(HYDROGEN_INCLUDES_DIR . '/command-interface.php');
include(HYDROGEN_INCLUDES_DIR . '/command-class.php');
include( HYDROGEN_INCLUDES_DIR . '/cache-command-interface.php' );
include( HYDROGEN_INCLUDES_DIR . '/cache-command-class.php' );
include( HYDROGEN_INCLUDES_DIR . '/cache-class.php' );
include( HYDROGEN_INCLUDES_DIR . '/package-command-class.php' );
include( HYDROGEN_INCLUDES_DIR . '/superglobals.php' );

Hydrogen::initialize();
include( Hydrogen::$wp_load_filepath );

