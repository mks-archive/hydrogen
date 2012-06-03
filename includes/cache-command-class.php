<?php

class Hydrogen_Cache_Command extends Hydrogen_Command {
	function cache( $args = array() ) {
		Hydrogen::download( $this->parse_args( $args ) );
	}
	/**
	 * Normalize $args
	 *
	 * @static
	 * @param array $args
	 * @return array
	 */
	function normalize_args( $args ) {
		$shortcuts = array(
			'source' 	=> 'download_source',
			'version' => 'component_version',
			'slug' 		=> 'component_name',
			'name' 		=> 'component_name',
		);
		foreach( $shortcuts as $shortcut => $longcut ) {
			if ( ! isset( $args[$longcut] ) ) {
				$args[$longcut] = $args[$shortcut];
			} else {
				unset( $args[$shortcut] );
			}
		}
		return $args;
	}
	function default_args( $args ) {
		if ( empty( $args['download_source'] ) ) {
			$args['download_source'] = 'wordpress.org';
		}
		return $args;
	}
	function parse_args( $args ) {
		$args = $this->normalize_args( $this->default_args( $args ) );
		return $args;
	}
	function post_normalize_args( $args = array() ) {
		$args = h_parse_args( $args, array(
			'cached_filepath' => self::get_cached_filepath( $args ),
		));
		return $args;
	}
	static function get_cached_filepath( $args ) {
		return Hydrogen_Config::get_cache_dir( $args['cache_dir'] ) . "/{$args['base_filename']}";
	}
}
