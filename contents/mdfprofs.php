<?php
echo "<h1>Modifier les professeurs</h1>";
if ( estadmin() ) {
	if ( ( !empty( $_POST[ 'subSuppr' ] ) ) && ( !empty( $_POST[ 'supprpr' ] ) ) ) {
		$supprpr = $link->real_escape_string( $_POST[ 'supprpr' ] );
		$sql     = "DELETE FROM " . $prefix . "profs WHERE id = $supprpr";
		$link->query( $sql );
	}
	if ( !empty( $_POST[ 'submit' ] ) ) {
		for ( $i = 1; $i <= sizeof( $_POST[ 'id' ] ); $i++ ) {
			$idpr     = $link->real_escape_string( $_POST[ 'id' ][ $i ] );
			$nompr    = $link->real_escape_string( $_POST[ 'nom' ][ $i ] );
			$prenompr = $link->real_escape_string( $_POST[ 'prenom' ][ $i ] );
			if ( !empty( $_POST[ 'classespr' ][ $i ] ) ) {
				$classespr = $link->real_escape_string( implode( ",", $_POST[ 'classespr' ][ $i ] ) );
			} else {
				$classespr = "";
			}
			if ( !empty( $_POST[ 'disciplinespr' ][ $i ] ) ) {
				$disciplinespr = $link->real_escape_string( implode( ",", $_POST[ 'disciplinespr' ][ $i ] ) );
			} else {
				$disciplinespr = "";
			}
			$usernamepr = $link->real_escape_string( $_POST[ 'username' ][ $i ] );
			$passwordpr = $_POST[ 'password' ][ $i ];
			if ( !empty( $_POST[ 'admin' ][ $i ] ) ) {
				$adminpr = $link->real_escape_string( $_POST[ 'admin' ][ $i ] );
			} else {
				$adminpr = 0;
			}
			if ( !empty( $passwordpr ) ) {
				$passwordpr = md5( $link->real_escape_string( $passwordpr ) );
				$sql        = "UPDATE " . $prefix . "profs SET nom = '$nompr', prenom = '$prenompr', classes = '$classespr', disciplines = '$disciplinespr', username = '$usernamepr', password = '$passwordpr', admin = $adminpr WHERE id = $idpr";
			} else {
				$sql = "UPDATE " . $prefix . "profs SET nom = '$nompr', prenom = '$prenompr', classes = '$classespr', disciplines = '$disciplinespr', username = '$usernamepr', admin = $adminpr WHERE id = $idpr";
			}
			$link->query( $sql );
		}
		if ( !empty( $_POST[ 'nomnew' ] ) ) {
			$nompr    = $link->real_escape_string( $_POST[ 'nomnew' ] );
			$prenompr = $link->real_escape_string( $_POST[ 'prenomnew' ] );
			$sexepr   = $link->real_escape_string( $_POST[ 'sexenew' ] );
			if ( sizeof( $_POST[ 'classesnew' ] ) > 0 ) {
				$classespr = $link->real_escape_string( implode( ",", $_POST[ 'classesnew' ] ) );
			} else {
				$classespr = "";
			}
			if ( sizeof( $_POST[ 'disciplinesnew' ] ) > 0 ) {
				$disciplinespr = $link->real_escape_string( implode( ",", $_POST[ 'disciplinesnew' ] ) );
			} else {
				$disciplinespr = "";
			}
			$usernamepr = $link->real_escape_string( $_POST[ 'usernamenew' ] );
			$passwordpr = $_POST[ 'passwordnew' ];
			if ( empty( $passwordpr ) ) {
				for ( $p = 0; $p <= 3; $p++ ) {
					$passwordrpr .= rand( 0, 9 );
				}
			}
			$passwordpr = $link->real_escape_string( encrypt( $passwordpr ) );
			$sql        = "INSERT INTO " . $prefix . "profs (nom, prenom, classes, disciplines, username, password, admin) VALUES ('$nompr', '$prenompr', '$classespr', '$disciplinespr','$usernamepr', '$passwordpr', $adminpr)";
			$link->query( $sql );
		}
	}
	if ( ( !empty( $_POST[ 'subUpload' ] ) ) && ( !empty( $_FILES[ 'fichiercsv' ][ 'tmp_name' ] ) ) ) {
		$sql = "DELETE FROM " . $prefix . "profs";
		$link->query( $sql );
		$fichiercsv = $_FILES[ 'fichiercsv' ][ 'tmp_name' ];
		$fic        = fopen( "$fichiercsv", 'rb' );
		for ( $ligne = fgetcsv( $fic, 1024, ";" ); !feof( $fic ); $ligne = fgetcsv( $fic, 1024, ";" ) ) {
			$symbole = substr( $ligne[ 0 ], 0, 1 );
			if ( $symbole !== "#" ) {
				if ( empty( $ligne[ 4 ] ) ) {
					for ( $p = 0; $p <= 3; $p++ ) {
						$ligne[ 4 ] .= rand( 0, 9 );
					}
				}
				$ligne[ 0 ] = $link->real_escape_string( $ligne[ 0 ] );
				$ligne[ 1 ] = $link->real_escape_string( $ligne[ 1 ] );
				$ligne[ 2 ] = $link->real_escape_string( $ligne[ 2 ] );
				$ligne[ 3 ] = $link->real_escape_string( $ligne[ 3 ] );
				$ligne[ 4 ] = $link->real_escape_string( $ligne[ 4 ] );
				$ligne[ 5 ] = $link->real_escape_string( $ligne[ 5 ] );
				$ligne[ 6 ] = $link->real_escape_string( $ligne[ 6 ] );
				$sql        = "INSERT INTO " . $prefix . "profs (nom, prenom, classes, disciplines, username, password, admin) VALUES ('" . $ligne[ 0 ] . "', '" . $ligne[ 1 ] . "', '" . $ligne[ 2 ] . "', '" . $ligne[ 3 ] . "', '" . $ligne[ 4 ] . "', " . $ligne[ 5 ] . "', " . $ligne[ 6 ] . ")";
				$link->query( $sql );
			}
		}
		fclose( $fic );
	}
	echo "<form action='mdfprofs.php' method='POST'><table class='data'>";
	echo "<thead><tr><th>Nom - Pr&eacute;nom</th><th>Classes</th><th>Disciplines</th><th>Identifiant</th><th>Mot de passe</th><th>Admin</th></tr></thead><tbody>";
	$result = $link->query( "SELECT * FROM " . $prefix . "profs ORDER BY nom" );
	$k      = 1;
	while ( $r = mysqli_fetch_array( $result ) ) {
		$idpr          = $r[ 'id' ];
		$nompr         = $r[ 'nom' ];
		$prenompr      = $r[ 'prenom' ];
		$classespr     = explode( ",", $r[ 'classes' ] );
		$disciplinespr = explode( ",", $r[ 'disciplines' ] );
		$usernamepr    = $r[ 'username' ];
		$passwordpr    = decrypt( $r[ 'password' ] );
		$adminpr       = $r[ 'admin' ];
		echo "<tr>";
		echo "<td><input type='hidden' name='id[$k]' value='$idpr' /><input type='text' name='nom[$k]' value='$nompr' /><br />";
		echo "<input type='text' name='prenom[$k]' value='$prenompr' /></td>";
		echo "<td><select name='classespr[$k][]' type='select' multiple='multiple' class='multselect' style='width:200px;' size='2'>";
		$sqlclasses = $link->query( "SELECT * FROM " . $prefix . "classes ORDER BY niveau DESC, nom ASC" );
		while ( $c = mysqli_fetch_array( $sqlclasses ) ) {
			$cid  = $c[ 'id' ];
			$cnom = $c[ 'nom' ];
			echo "<option value='$cid'";
			if ( in_array( $cid, $classespr ) ) {
				echo " selected='selected'";
			}
			echo ">$cnom</option>";
		}
		echo "</select></td>";
		echo "<td><select name='disciplinespr[$k][]' type='select' multiple='multiple' class='multselect' style='width:200px;' size='2'>";
		$sqlclasses = $link->query( "SELECT * FROM " . $prefix . "disciplines ORDER BY nom ASC" );
		while ( $c = mysqli_fetch_array( $sqlclasses ) ) {
			$cid  = $c[ 'id' ];
			$cnom = $c[ 'nom' ];
			echo "<option value='$cid'";
			if ( in_array( $cid, $disciplinespr ) ) {
				echo " selected='selected'";
			}
			echo ">$cnom</option>";
		}
		echo "</select></td>";
		echo "<td><input type='text' name='username[$k]' value='$usernamepr' placeholder='Identifiant' /></td>";
		echo "<td><input type='password' name='password[$k]' value='$passwordpr' placeholder='Changer le mot de passe' /></td>";
		echo "<td><input type='checkbox' name='admin[$k]' value='1' ";
		if ( $adminpr == 1 ) {
			echo "checked";
		}
		echo "/></td>";
		echo "</tr>";
		$k++;
	}
	echo "<tr>";
	echo "<td><input type='text' name='nomnew' placeholder='Nom' /><br /><input type='text' name='prenomnew' placeholder='Prénom' /></td>";
	echo "<td><select name='classesnew[]' type='select' multiple='multiple' class='multselect' style='width:200px;' size='2'><option value=''>Aucune</option>";
	$sqlclasses = $link->query( "SELECT * FROM " . $prefix . "classes ORDER BY niveau DESC, nom ASC" );
	while ( $c = mysqli_fetch_array( $sqlclasses ) ) {
		$cid  = $c[ 'id' ];
		$cnom = $c[ 'nom' ];
		echo "<option value='$cid'>$cnom</option>";
	}
	echo "</select></td>";
	echo "<td><select name='disciplinesnew[]' type='select' multiple='multiple' class='multselect' style='width:200px;' size='2'>";
	$sqlclasses = $link->query( "SELECT * FROM " . $prefix . "disciplines ORDER BY nom ASC" );
	while ( $c = mysqli_fetch_array( $sqlclasses ) ) {
		$cid  = $c[ 'id' ];
		$cnom = $c[ 'nom' ];
		echo "<option value='$cid'>$cnom</option>";
	}
	echo "</select></td>";
	echo "<td><input type='text' name='usernamenew' placeholder='Identifiant' /></td>";
	echo "<td><input type='password' name='passwordnew' placeholder='Mot de passe initial' /></td>";
	echo "<td><input type='checkbox' name='adminnew' value='1' /></td>";
	echo "</tr>";
	echo "</tbody></table>";
	echo "<p><input type='submit' name='submit' value='Valider' /></p></form>";
	$result = $link->query( "SELECT * FROM " . $prefix . "profs ORDER BY nom" );
	echo "<form action='mdfprofs.php' name='formsuppr' method='POST'><p>Supprimer un professeur : <select name='supprpr'><option value='' selected>...</option>";
	while ( $r = mysqli_fetch_array( $result ) ) {
		$idpr     = $r[ 'id' ];
		$nompr    = $r[ 'nom' ];
		$prenompr = $r[ 'prenom' ];
		echo "<option value='$idpr'>$nompr $prenompr</option>";
	}
	echo "</select> <input type='submit' value='Valider' name='subSuppr' /></p></form>";
	echo "<h2>Importation et exportation</h2>";
	echo "<form action='mdfprofs.php' name='formupload' enctype='multipart/form-data' method='POST'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
	echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
	echo "<p>Exporter un fichier CSV : <input type='submit' value='T&eacute;l&eacute;charger' onclick='location.href=\"exportcsv.php?cont=profs\"' name='subExport' /></p>";
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute;.</p>";
}
?>