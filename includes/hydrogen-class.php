<?php

final class Hydrogen {
	private static $commands = array();
	static $wp_load_filepath;
	static function cache( $item_type, $args = array() ) {
		self::get_command( "cache-{$item_type}", $args )->cache( $args );
	}
	static function download( $args ) {
		if ( empty( $args['cached_filepath'] ) ) {
			$args['cached_filepath'] = Hydrogen_Cache_Command::get_cached_filepath( $args );
		}
		if ( empty( $args['force'] ) && file_exists( $args['cached_filepath'] ) ) {
			Hydrogen::msg( "Already in cache: {$args['base_filename']} [{$args['component_type']}]" );
		} else {
			Hydrogen::msg( "Downloading {$args['dir']}/{$args['base_filename']}..." );
			$response = wp_remote_get( $args['download_link'], array(
				'timeout' => 300,
				'stream' => true,
				'filename' => $args['cached_filepath'],
			));
			echo "\n";
			if (  200 != wp_remote_retrieve_response_code( $response )  ) {
				Hydrogen::fail( $response );
			} else {
				Hydrogen::msg( "{$args['base_filename']} downloaded." );
			}
		}
	}
	static function initialize() {
		Hydrogen_Config::load();
		Hydrogen_Cache::initialize();
		self::register_command( 'show-pwd' );
		self::register_command( 'site-vcs' );

		self::register_command( 'show-help' );
		self::register_command( 'show-manifest' );

		self::register_command( 'cache-core' );
		self::register_command( 'cache-theme' );
		self::register_command( 'cache-plugin' );

		self::register_command( 'queue-package' );
		self::register_command( 'build-package' );
		self::register_command( 'deploy-package' );

		self::$wp_load_filepath = Hydrogen_Config::get_site_dir( 'wp-load.php' );
		if ( ! file_exists( self::$wp_load_filepath ) ) {
			self::fail( "'site_dir' is not set correctly in .hydrogen." );
		}
	}
	static function parse_command_line() {
		global $argv;
		if ( ! isset( $argv[1] ) )
			$argv[1] = 'show-help';

		$command = $argv[1];
		if ( ! self::has_command( $command ) ) {
			self::fail( "Command [{$command}] not valid." );
		}

		$args = array();
		for( $i = 2; $i < count( $argv ); $i++ ) {
			if ( '--' == substr( $argv[$i], 0, 2 ) ) {
				$switch = $argv[$i++];
				break;
			} else {
				$args[] = $argv[$i];
			}
		}

		$switches = array();
		for( null; $i < count( $argv ); $i++ ) {
			if ( '--' == substr( $argv[$i], 0, 2 ) ) {
				$switches[] = trim( $switch );
				$switch = '';
			}
			$switch .= " {$argv[$i]}";
		}
		if ( ! empty( $switch ) ) {
			$switches[] = trim( $switch );
		}
		return array(
			'command' => $command,
			'args' => $args,
			'switches' => $switches,
		);
	}
	static function register_command( $command_name, $args = array() ) {
		$classized_name = implode( '_', array_map( 'ucfirst', explode( '-', $command_name ) ) );
		$args = h_parse_args( $args, array(
			'filepath' => HYDROGEN_ROOT_DIR . "/commands/{$command_name}.php",
			'class_name' => "Hydrogen_{$classized_name}_Command",
		));
		if ( ! file_exists( $args['filepath'] ) ) {
			Hydrogen::fail( "The filepath [{$args['filepath']}] for the command {$command_name} is not valid; from [Hydrogen::register_command()]." );
		}
		$args['command_name'] = $command_name;
		self::$commands[$command_name] = $args;
	}
	static function has_command( $command_name ) {
		return isset( self::$commands[$command_name] );
	}
	/**
	 * @static
	 * @param $command_name string
	 * @return Hydrogen_Command
	 */
	static function get_command( $command_name ) {
		$command = false;
		if ( self::has_command( $command_name ) ) {
			$command_args = self::$commands[$command_name];
			require_once( $command_args['filepath'] );
			if ( ! class_exists( $command_args['class_name'] ) ) {
				self::fail( "The class [{$command_args['class_name']}] that implements the command [{$command_name}] was not found." );
			}
			/**
			 * @var $command Hydrogen_Command
			 */
			$command = new $command_args['class_name'];
			$command->class_name = $command_args['class_name'];
			$command->filepath = $command_args['filepath'];
			$command->command_name = $command_name;
		}
		return $command;
	}
	static function run_command( $command_name, $args = array(), $switches = array() ) {
		$command = self::get_command( $command_name );

		if ( ! isset( $args['force'] ) ) {
			// force is oh-so-common
			$args['force'] = in_array( '--force', $switches );
			unset( $switches['--force'] );
		}

		if ( empty( $args[0] ) ) {
			// Make it so commands don't have to validate for the most common case.
			$args[0] = false;
		}

		$command->run( $args, $switches );
	}
	/**
	 * .hpm : Hydrogen Package Manifest
	 */
	static function get_manifest( $manifest_name ) {
		$filepath = Hydrogen_Config::get_manifests_dir( "{$manifest_name}.hpm" );
		if ( ! file_exists( $filepath ) ) {
			Hydrogen::fail( "Package Manifest [{$filepath}] does not exist." );
		}
		$manifest = Hydrogen::load_yaml( $filepath );
		if ( empty( $manifest['core'] ) ) {
			$manifest['core'] = Hydrogen::get_command('cache-core')->parse_args( $args );
		}
		foreach( array( 'theme', 'plugins' ) as $section_name ) {
			if ( ! isset( $manifest[$section_name] ) ) {
				$manifest[$section_name] = false;
			} else {
				foreach( $manifest[$section_name] as $index => $args ) {
					if ( is_string( $args ) ) {
						$args = array( 'component_name' => $args );
					}
					switch ( $section_name ) {
						case 'theme':
							$args = Hydrogen::get_command('cache-theme')->parse_args( $args );
							break;
						case 'plugins':
							$args = Hydrogen::get_command('cache-plugin')->parse_args( $args );
							break;
					}
					$manifest[$section_name][$index] = $args;
				}
			}
		}
		if ( is_array( $manifest['theme'] ) ) {
			// There should only be one theme
			$manifest['theme'] = $manifest['theme'][0];
		}
		return $manifest;
	}
	private static function load_spyc() {
		require_once( HYDROGEN_ROOT_DIR . '/spyc-0.5/spyc.php' );
	}
	static function load_yaml( $filepath ) {
		self::load_spyc();
		return Spyc::YAMLLoad( $filepath );
	}
	static function get_yaml( $values ) {
		self::load_spyc();
		return Spyc::YAMLDump( $values, 2, 0 );
	}
	static function save_yaml( $filepath, $values ) {
		file_put_contents( $filepath, self::get_yaml( $values ) );
	}
	static function msg( $msg ) {
		echo "{$msg}\n";
	}
	static function zip_dir( $full_dir, $local_dir, $zip = false ) {
		if ( '/' != substr( $full_dir, -1, 1 ) ) {
			$full_dir .= '/';
		}
		$result = false;
		if ( $handle = opendir( $full_dir ) ) {
			if ( ! $zip ) {
				$zip = new ZipArchive();
				$zip_filepath = rtrim( $full_dir, '/' ) . '.hpkg';
				if ( true !== ( $zip->open( $zip_filepath, ZIPARCHIVE::CREATE ) ) ) {
					self::fail( "Could not open [{$zip_filepath}] as .ZIP file." );
				}
			}
      while ( false !== ( $entry = readdir( $handle ) ) ) {
      	if ( '.' == $entry || '..' == $entry ) {
      		continue;
				} else {
					$filepath = "{$full_dir}{$entry}";
					$zip_entry = "{$local_dir}/{$entry}";
					if ( is_dir( $filepath ) ) {
						self::zip_dir( $filepath, $zip_entry, $zip );
					} else {
						if ( ! $zip->addFile( realpath( $filepath ), $zip_entry ) ) {
						 Hydrogen::fail( "Could not add file [{$zip_entry}] to [{$zip->filename}]." );
						}
					}
				}
      }
      closedir( $handle );
      if ( isset( $zip_filepath ) ) {
				$zip->close();
			}
			$result = $zip_filepath;
		}
		return $result;
	}
	static function nuke_dir( $dir ) {
		if ( '/' != substr( $dir, -1, 1 ) ) {
			$dir .= '/';
		}
		if ( $handle = opendir( $dir ) ) {
      while ( false !== ( $entry = readdir( $handle ) ) ) {
      	if ( '.' == $entry || '..' == $entry ) {
      		continue;
				} else {
					$entry = "{$dir}{$entry}";
					if ( is_dir( $entry ) ) {
						self::nuke_dir( $entry );
					} else {
						unlink( $entry );
					}
				}
      }
      closedir( $handle );
			rmdir( $dir );
		}
	}
	static function fail( $error_msg ) {
		self::msg( "Hydrogen error: {$error_msg}\n" );
		die(1);
	}
}

