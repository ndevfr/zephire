<?php
if ( $modech == 0 ) {
	$notes = donnenota( $classe, $discipline );
	$libs = donnelib( $classe, $discipline, $modech );
	$descs = donnedesc( $classe, $discipline );
	echo "<div id='explication'>";
	echo "<table class='data large'><tr><td colspan=" . ( sizeof( $codes ) - 1 ) . "><strong>Explication du code</strong></td></tr>";
	foreach ( $codes as $cd ) {
		if ( $cd !== "" ) {
			echo "<tr><td>" . $libs[ $cd ] . "</td><td style='padding-left:5px;padding-right:5px;'>" . enimage( $cd, $classe, $discipline, $modech ) . "</td><td style='text-align:left;text-align:left;width:100%;'>" . $descs[ $cd ];
			if ( $notech ) {
				echo " J'obtiens " . $notes[ $cd ] . " % des points.";
			}
			echo "</td></tr>";
		}
	}
	echo "</table></div>";
} elseif ( $modech == 1 ) {
	$ceinturesnv = $ceintures;
	unset( $ceinturesnv[ 0 ] );
	echo "<div id='explication'><table class='data large'>";
	echo "<tr><td colspan=2><strong>Explication du code</strong></td></tr>";
	$result   = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE id = '$chapitre'" );
	$infoch   = mysqli_fetch_array( $result );
	$arrceint = explode( '&', $infoch[ 'ceintures' ] );
	foreach ( $arrceint as $ceint ) {
		$tabceint = explode( '|', $ceint );
		$idcomp   = $tabceint[ 0 ];
		if ( !empty( $idcomp ) ) {
			$tceint = array();
			for ( $i = 1; $i < sizeof( $ceintures ); $i++ ) {
				if ( !empty( $tabceint[ $i ] ) ) {
					$tceint[] = $tabceint[ $i ];
				} else {
					$tceint[] = "";
				}
			}
			$descceintures[ $idcomp ] = $tceint;
		}
	}
	for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
		if ( !empty( $descceintures[ $comp[ $i ][ 'id' ] ] ) ) {
			$comp[ $i ][ 'ceintures' ] = $descceintures[ $comp[ $i ][ 'id' ] ];
		} else {
			$comp[ $i ][ 'ceintures' ] = array(
				"",
				"",
				"",
				"",
				"",
				"",
				"" 
			);
		}
	}
	for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
		echo "<tr><td colspan=2 style='text-align:left;background-color:#CCCCCC;'><strong><em>" . $comp[ $i ][ 'id' ] . " : " . $comp[ $i ][ 'nom' ] . "</em></strong></td></tr>";
		$j = 0;
		if ( $modech == 1 ) {
			$ceinturesform = recupceintures( $chapitre, $comp[ $i ][ 'id' ] );
			unset( $ceinturesform[ 0 ] );
		} else {
			$ceinturesform = $ceinturesnv;
		}
		foreach ( $ceinturesnv as $cnt ) {
			if ( in_array( $cnt, $ceinturesform ) ) {
				if ( !empty( $comp[ $i ][ 'ceintures' ][ $j ] ) ) {
					$txt = nl2br( $comp[ $i ][ 'ceintures' ][ $j ] );
				} else {
					$txt = "<em>Rep&egrave;re(s) de progressivit&eacute; non d&eacute;fini.</em>";
				}
				echo "<tr><td style='padding-left:5px;padding-right:5px;'>" . enimage( $cnt, $classe, $discipline, 1 ) . "</td><td style='text-align:left;width:100%;'>" . $txt . "</td></tr>";
			}
			$j++;
		}
	}
	echo "</table></div>";
}
?>