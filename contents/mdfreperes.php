<?php
$classe      = !empty( $_GET[ 'idcl' ] ) ? $_GET[ 'idcl' ] : NULL;
$classe      = verifclasse( $link->real_escape_string( $classe ) );
$discipline  = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
$discipline  = verifdiscipline( $link->real_escape_string( $discipline ) );
$chapitre    = !empty( $_GET[ 'idch' ] ) ? $_GET[ 'idch' ] : NULL;
$chapitre    = verifchapitre( $link->real_escape_string( $chapitre ), $classe, $discipline );
$idchapitre  = substr( $chapitre, strlen( $classe ) + strlen( $discipline ) + 2 );
$ceinturesnv = $ceintures;
unset( $ceinturesnv[ 0 ] );
if ( !empty( $classe ) ) {
	$result = $link->query( "SELECT * FROM " . $prefix . "classes WHERE id = '$classe'" );
	$r      = mysqli_fetch_array( $result );
	$niveau = $r[ 'niveau' ];
	$nomcl  = $r[ 'nom' ];
}
if ( !empty( $chapitre ) ) {
	$infoch  = infoch( $chapitre );
	$titrech = $infoch[ 'nom' ];
	$compch  = $infoch[ 'competences' ];
} else {
	$chapitre = "";
}
echo "<h1>$nomcl : $titrech</h1>";
echo "<h2>$nompage</h2>";
if ( ( !empty( $classe ) ) && ( !empty( $discipline ) ) && ( !empty( $chapitre ) ) ) {
	$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC" );
	$i      = 0;
	while ( $r = mysqli_fetch_array( $result ) ) {
		$comp[ $i ][ 'id' ]      = substr( $r[ 'id' ], strlen( $niveau ) + strlen( $discipline ) + 2 );
		$comp[ $i ][ 'cat' ]     = $r[ 'cat' ];
		$comp[ $i ][ 'nom' ]     = stripslashes( $r[ 'nom' ] );
		$comp[ $i ][ 'socle' ]   = $r[ 'socle' ];
		$comp[ $i ][ 'libelle' ] = $comp[ $i ][ 'id' ];
		$i++;
	}
	if ( !empty( $_POST[ 'submit' ] ) ) {
		$subdescceint = "";
		for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
			if ( !empty( $subdescceint ) ) {
				$subdescceint .= "&";
			}
			$cpceint = array();
			foreach ( $ceinturesnv as $cd ) {
				$cpceint[ "$cd" ] = $link->real_escape_string( $_POST[ 'descceint' ][ $i ][ "$cd" ] );
			}
			$cpceint = implode( "|", $cpceint );
			$subdescceint .= $comp[ $i ][ 'id' ] . "|" . $cpceint;
		}
		$sql = "UPDATE " . $prefix . "chapitres SET ceintures = \"$subdescceint\" WHERE id = '$chapitre'";
		$link->query( $sql );
	}
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
	echo "<form action='mdfreperes.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $idchapitre . "' method='POST'><p>Pour chaque compétence, indiquer les repères de progressivité de chaque ceinture.</p>";
	for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
		echo "<p><strong>" . $comp[ $i ][ 'id' ] . " : " . $comp[ $i ][ 'nom' ] . "</strong></p>";
		echo "<table>";
		$j = 0;
		foreach ( $ceinturesnv as $cnt ) {
			echo "<tr><td>" . enimage( $cnt, $classe, $discipline, 1 ) . "</td><td style='width:100%;'><textarea name='descceint[$i][$cnt]' style='width:99%;height:50px;border:0px;'>" . $comp[ $i ][ 'ceintures' ][ $j ] . "</textarea></td></tr>";
			$j++;
		}
		echo "</table>";
	}
	echo "<p><input name='submit' type='submit' value='Modifier' /> <input name='retour' type='button' value='Retour' onclick='window.location.href=\"mdfprogressions.php?idcl=$classe&idds=$discipline\"' /></p></form>";
}
?>