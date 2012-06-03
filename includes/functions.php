<?php

function h_parse_args( $args, $defaults = array() ) {
	foreach( $defaults as $key => $value ) {
		if ( empty( $args[$key] ) ) {
			$args[$key] = $value;
		}
	}
	return $args;
}
