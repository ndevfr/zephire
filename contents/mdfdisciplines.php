<?php
echo "<h1>Modifier les disciplines</h1>";
if ( estadmin() ) {
	if ( ( !empty( $_POST[ 'submit' ] ) ) ) {
		if ( !empty( $_POST[ 'exid' ] ) ) {
			for ( $i = 1; $i <= sizeof( $_POST[ 'nom' ] ); $i++ ) {
				$exid = $link->real_escape_string( $_POST[ 'exid' ][ $i ] );
				$id   = formatid( $link->real_escape_string( $_POST[ 'id' ][ $i ] ) );
				$nom  = $link->real_escape_string( $_POST[ 'nom' ][ $i ] );
				if ( !empty( $_POST[ 'active' ][ $i ] ) ) {
					$active = 1;
				} else {
					$active = 0;
				}
				$sql = "UPDATE " . $prefix . "disciplines SET nom = '$nom', active = '$active' WHERE id = '$exid'";
				$link->query( $sql );
				if ( $exid !== $id ) {
					updidcdiscipline( $exid, $id );
				}
			}
		}
		if ( !empty( $_POST[ 'idnew' ] ) ) {
			$id  = formatid( $link->real_escape_string( $_POST[ 'idnew' ] ) );
			$nom = $link->real_escape_string( $_POST[ 'nomnew' ] );
			if ( !empty( $_POST[ 'activenew' ] ) ) {
				$active = 1;
			} else {
				$active = 0;
			}
			$sql = "INSERT INTO " . $prefix . "disciplines (id, nom, active) VALUES ('$id', '$nom', '$active')";
			$link->query( $sql );
		}
	}
	if ( ( !empty( $_POST[ 'subSuppr' ] ) ) && ( !empty( $_POST[ 'supprdis' ] ) ) ) {
		$supprdis = $link->real_escape_string( $_POST[ 'supprdis' ] );
		updidcdiscipline( $supprdis, "" );
	}
	if ( ( !empty( $_POST[ 'subUpload' ] ) ) && ( !empty( $_FILES[ 'fichiercsv' ][ 'tmp_name' ] ) ) ) {
		$result = $link->query( "SELECT * FROM " . $prefix . "disciplines" );
		while ( $r = mysqli_fetch_array( $result ) ) {
			updiddiscipline( $r[ 'id' ], "" );
		}
		$fichiercsv = $_FILES[ 'fichiercsv' ][ 'tmp_name' ];
		$fic        = fopen( "$fichiercsv", 'rb' );
		while ( $ligne = fgetcsv( $fic, 1024, ";", '"') ) {			
			$symbole = substr( $ligne[ 0 ], 0, 1 );
			if ( $symbole !== "#" ) {
				$id     = formatid( $link->real_escape_string( $ligne[ 0 ] ) );
				$nom    = $link->real_escape_string( addslashes( $ligne[ 1 ] ) );
				$active = $link->real_escape_string( addslashes( $ligne[ 2 ] ) );
				$sql    = "INSERT INTO " . $prefix . "disciplines (id, nom, active) VALUES ('$id', '$nom', '$active')";
				$link->query( $sql );
			}
		}
		fclose( $fic );
	}
	echo "<form action='mdfdisciplines.php' method='POST'><table class='data'><thead><tr><th>Id</th><th>Nom</th><th>Active ?</th></tr></thead><tbody>";
	$result = $link->query( "SELECT * FROM " . $prefix . "disciplines ORDER BY id ASC" );
	$i      = 1;
	while ( $r = mysqli_fetch_array( $result ) ) {
		$nom    = $r[ 'nom' ];
		$exid   = $r[ 'id' ];
		$id     = $r[ 'id' ];
		$active = $r[ 'active' ];
		echo "<tr>";
		echo "<td style='width:40px;'><input type='hidden' value=\"$exid\" name='exid[$i]' /><input type='text' class='inputcell' value=\"$id\" name='id[$i]' /></td>";
		echo "<td><input type='text' class='inputcell' value=\"$nom\" name='nom[$i]' /></td>";
		echo "<td><input type='checkbox'";
		if ( $active == 1 ) {
			echo " checked='true'";
		}
		echo " name='active[$i]' value='1' /></td>";
		echo "</tr>";
		$i++;
	}
	echo "<tr>";
	echo "<td style='width:10px;'><input type='text' class='inputcell' value='' name='idnew' placeholder='...' /></td>";
	echo "<td><input type='text' class='inputcell' value='' name='nomnew' placeholder='...' /></td>";
	echo "<td><input type='checkbox' name='activenew' /></td>";
	echo "</tr></tbody></table>";
	echo "<p class='noprint'><input type='submit' value='Valider' name='submit' /></p></form>";
	$result = $link->query( "SELECT * FROM " . $prefix . "disciplines ORDER BY id ASC" );
	echo "<form action='mdfdisciplines.php' name='formsuppr' method='POST' class='noprint'><p>Supprimer une discipline : <select name='supprdis'><option value='' selected>...</option>";
	while ( $r = mysqli_fetch_array( $result ) ) {
		$idcl  = $r[ 'id' ];
		$nomcl = $r[ 'nom' ];
		echo "<option value='$idcl'>$nomcl</option>";
	}
	echo "</select> <input type='submit' value='Valider' name='subSuppr' /></p></form>";
	echo "<h2>Importation et exportation</h2>";
	echo "<form action='mdfdisciplines.php' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
	echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
	echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='T&eacute;l&eacute;charger' onclick='location.href=\"exportcsv.php?cont=disciplines\"' name='subExport' /></p>";
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>