<?php

// Autoload Classes
$classes = glob( __DIR__ . '/classes/*/*.php', GLOB_NOSORT );
if ( ! empty( $classes ) ) {
	foreach ( $classes as $class ) {
		include_once( $class );
	}
}