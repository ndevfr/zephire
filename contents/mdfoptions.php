<?php
if ( ( !empty( $_POST[ 'SubOpt' ] ) ) ) {
	$msgacc    = $link->real_escape_string( $_POST[ 'msgacc' ] );
	$lienperso = $link->real_escape_string( $_POST[ 'lienperso' ] );
	$sql       = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'msgacc'" );
	if ( $sql->num_rows == 0 ) {
		$sql = "INSERT INTO " . $prefix . "options (id,valeur) VALUES ('msgacc', '$msgacc')";
		$link->query( $sql );
	} else {
		$sql = "UPDATE " . $prefix . "options SET valeur = '$msgacc' WHERE id = 'msgacc'";
		$link->query( $sql );
	}
	$sql = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'lienperso'" );
	if ( $sql->num_rows == 0 ) {
		$sql = "INSERT INTO " . $prefix . "options (id,valeur) VALUES ('lienperso', '$lienperso')";
		$link->query( $sql );
	} else {
		$sql = "UPDATE " . $prefix . "options SET valeur = '$lienperso' WHERE id = 'lienperso'";
		$link->query( $sql );
	}
}
echo "<h1>Personnalisation</h1>";
if ( estadmin() ) {
	echo "<form action='mdfoptions.php' method='POST'>";
	echo "<p>Je souhaite ajouter le message suivant sur la page d'accueil :</p>";
	$result = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'msgacc'" );
	$r      = mysqli_fetch_array( $result );
	$msgacc = $r[ 'valeur' ];
	echo "<p><textarea name='msgacc' style='width: 100%; border:solid 1px #000000;' rows='6'>$msgacc</textarea></p>";
	echo "<p>Je souhaite ajouter les liens suivants dans le menu Options :</p>";
	$result    = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'lienperso'" );
	$r         = mysqli_fetch_array( $result );
	$lienperso = $r[ 'valeur' ];
	echo "<p><textarea name='lienperso' style='width: 100%; border:solid 1px #000000;' rows='6'>$lienperso</textarea></p>";
	echo "<p class='noprint'><input name='SubOpt' value='Valider' type='submit' /></p></form>";
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>