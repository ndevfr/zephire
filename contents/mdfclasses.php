<?php
echo "<h1>Modifier les classes</h1>";
if ( estadmin() ) {
	if ( !empty( $_GET[ 'idnv' ] ) ) {
		$niveau = $link->real_escape_string( $_GET[ 'idnv' ] );
	}
	echo "<div class='selecteurs'><p>Niveau : <select name='selniveau' onchange='location.href=\"mdfclasses.php?idnv=\" + this.value'>";
	echo "<option value=''>...</option>";
	$result = $link->query( "SELECT * FROM " . $prefix . "niveaux ORDER BY id DESC" );
	while ( $r = mysqli_fetch_array( $result ) ) {
		$idniveau  = $r[ 'id' ];
		$nomniveau = $r[ 'nom' ];
		if ( $idniveau == $niveau ) {
			echo "<option value='$idniveau' selected>$nomniveau</option>";
		} else {
			echo "<option value='$idniveau'>$nomniveau</option>";
		}
	}
	echo "</select></p></div>";
	if ( !empty( $niveau ) ) {
		if ( ( !empty( $_POST[ 'submit' ] ) ) ) {
			for ( $i = 1; $i <= sizeof( $_POST[ 'nom' ] ); $i++ ) {
				$id   = formatid( $link->real_escape_string( $_POST[ 'id' ][ $i ] ) );
				$exid = $link->real_escape_string( $_POST[ 'exid' ][ $i ] );
				$nom  = $link->real_escape_string( $_POST[ 'nom' ][ $i ] );
				$sql  = "UPDATE " . $prefix . "classes SET nom = '$nom', niveau = '$niveau' WHERE id = '$exid'";
				$link->query( $sql );
				if ( $exid !== $id ) {
					upidclasse( $exid, $id );
				}
			}
			if ( !empty( $_POST[ 'nomnew' ] ) ) {
				$id  = formatid( $link->real_escape_string( $_POST[ 'idnew' ] ) );
				$nom = $link->real_escape_string( $_POST[ 'nomnew' ] );
				$sql = "INSERT INTO " . $prefix . "classes (id, nom, niveau) VALUES ('$id','$nom', '$niveau')";
				$link->query( $sql );
			}
		}
		if ( ( !empty( $_POST[ 'subSuppr' ] ) ) && ( !empty( $_POST[ 'supprcl' ] ) ) ) {
			$supprcl = $link->real_escape_string( $_POST[ 'supprcl' ] );
			updidclasse( $supprcl, "" );
		}
		echo "<form action='mdfclasses.php?idnv=" . $niveau . "' method='POST'><table class='data'><thead><tr><th>Id</th><th>Nom</th></tr></thead><tbody>";
		$result = $link->query( "SELECT * FROM " . $prefix . "classes WHERE niveau = $niveau" );
		$i      = 1;
		while ( $r = mysqli_fetch_array( $result ) ) {
			$nom    = $r[ 'nom' ];
			$id     = $r[ 'id' ];
			$niveau = $r[ 'niveau' ];
			echo "<tr>";
			echo "<td style='width:40px;'><input type='text' class='inputcell' value=\"$id\" style='width:100%' name='id[$i]' /><input type='hidden' value=\"$id\" name='exid[$i]' /></td><td><input type='text' class='inputcell' value=\"$nom\" name='nom[$i]' /></td>";
			echo "</tr>";
			$i++;
		}
		echo "<tr>";
		echo "<td style='width:40px;'><input type='text' class='inputcell' value='' name='idnew' placeholder='...' /></td><td><input type='text' class='inputcell' value='' name='nomnew' placeholder='...' /></td>";
		echo "</tr></tbody></table>";
		echo "<p class='noprint'><input type='submit' value='Valider' name='submit' /></p></form>";
		$result = $link->query( "SELECT * FROM " . $prefix . "classes WHERE niveau = $niveau" );
		echo "<form action='mdfclasses.php?idnv=" . $niveau . "' name='formsuppr' method='POST' class='noprint'><p>Supprimer une classe : <select name='supprcl'><option value='' selected>...</option>";
		while ( $r = mysqli_fetch_array( $result ) ) {
			$idcl  = $r[ 'id' ];
			$nomcl = $r[ 'nom' ];
			echo "<option value='$idcl'>$nomcl</option>";
		}
		echo "</select> <input type='submit' value='Valider' name='subSuppr' /></p></form>";
	} else {
		if ( ( !empty( $_POST[ 'subUpload' ] ) ) && ( !empty( $_FILES[ 'fichiercsv' ][ 'tmp_name' ] ) ) ) {
			$result = $link->query( "SELECT * FROM " . $prefix . "classes WHERE niveau = '$niveau'" );
			while ( $r = mysqli_fetch_array( $result ) ) {
				updidclasse( $r[ 'id' ], "" );
			}
			$fichiercsv = $_FILES[ 'fichiercsv' ][ 'tmp_name' ];
			$fic        = fopen( "$fichiercsv", 'rb' );
			for ( $ligne = fgetcsv( $fic, 1024, ";" ); !feof( $fic ); $ligne = fgetcsv( $fic, 1024, ";" ) ) {
				$symbole = substr( $ligne[ 0 ], 0, 1 );
				if ( $symbole !== "#" ) {
					$niveau = $link->real_escape_string( $ligne[ 0 ] );
					$id     = formatid( $link->real_escape_string( $ligne[ 1 ] ) );
					$nom    = $link->real_escape_string( addslashes( $ligne[ 2 ] ) );
					$sql    = "INSERT INTO " . $prefix . "classes (niveau, id, nom) VALUES ('$niveau', '$id', '$nom')";
					$link->query( $sql );
				}
			}
			fclose( $fic );
		}
		echo "<h2>Importation et exportation</h2>";
		echo "<form action='mdfclasses.php' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
		echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
		echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='T&eacute;l&eacute;charger' onclick='location.href=\"exportcsv.php?cont=classes\"' name='subExport' /></p>";
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>