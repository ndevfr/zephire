<?php
session_set_cookie_params( 0 );
session_name( 'AuthZephire' );
session_start();
foreach ( $_REQUEST as $key => $val ) {
	if ( !is_array( $val ) ) {
		$val              = preg_replace( "/[^_A-Za-z0-9-\.&=]/i", '', $val );
		$_REQUEST[ $key ] = $val;
	}
}
include( $dir . "include.php" );
include( $dir . "page.php" );
if ( ( $inclpage !== "exportcsv.php" ) && ( $inclpage !== "backup.php" ) && ( $inclpage !== "api.php" ) ) {
	include( $dir . "header.php" );
}
include( $dir . $inclpage );
if ( ( $inclpage !== "exportcsv.php" ) && ( $inclpage !== "backup.php" ) && ( $inclpage !== "api.php" ) ) {
	include( $dir . "footer.php" );
}
?>