<?php

class Hydrogen_Site_Vcs_Command {
	function run( $args, $switches ) {
		$full_dir = in_array( '--curdir', $switches ) ? trim( shell_exec( 'pwd' ), "\n" ). '/' : ABSPATH;
		echo "$full_dir\n";
		chdir( $full_dir );
		$repos = $this->site_vcs( $full_dir );
		//print_r ( $repos );
	}

	function site_vcs( $full_dir = false, &$repos = array() ) {
		$result = false;
		if ( $handle = opendir( $full_dir ) ) {
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( '.' == $entry || '..' == $entry ) {
					continue;
				} else {
					$filepath = "{$full_dir}{$entry}";
					if ( false !== strpos( '.hg/.git/.svn', $entry ) ) {
						$repos[] = array(
							'site_vcs' => ltrim( $entry, '.' ),
							'filepath' => $full_dir,
							);
						$repo_num = count($repos)-1;
						switch ( $entry ) {
							case '.hg':
								$repos[$repo_num]['subrepo'] = file_exists( $hgsub = "{$full_dir}/.hgsub" );
								$results = shell_exec( "cd {$full_dir}\nhg in 2>&1" );
								if ( $count = substr_count( $results, "\nchangeset:" ) ) {
									echo "{$count} changsets: {$full_dir}\n";
								}
								break;
							case '.git':
								break;
							case '.svn':
								break;
						}
					} else if ( is_dir( $filepath ) ) {
						$this->site_vcs( "{$filepath}/", $repos );
					}
				}
			}
			closedir( $handle );
		}
		return $repos;
	}

}
