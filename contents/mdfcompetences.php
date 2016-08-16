<?php
echo "<h1>Modifier les comp&eacute;tences</h1>";
if ( estprof() ) {
	$niveau     = !empty( $_GET[ 'idnv' ] ) ? $_GET[ 'idnv' ] : NULL;
	$niveau     = verifniveau( $link->real_escape_string( $niveau ) );
	$discipline = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
	$discipline = verifdiscipline( $link->real_escape_string( $discipline ) );
	echo "<div class='selecteurs'><p>";
	selectniveau( "\"mdfcompetences.php?idnv=\" + this.value" );
	selectdiscipline( "\"mdfcompetences.php?idnv=$niveau&idds=\" + this.value" );
	echo "</p></div>";
	if ( !empty( $niveau ) && !empty( $discipline ) ) {
		if ( ( !empty( $_POST[ 'submit' ] ) ) ) {
			for ( $i = 1; $i <= sizeof( $_POST[ 'id' ] ); $i++ ) {
				$exid = $link->real_escape_string( $_POST[ 'exid' ][ $i ] );
				$lib  = formatid( $link->real_escape_string( $_POST[ 'id' ][ $i ] ) );
				$id   = $niveau . "." . $discipline . "." . $lib;
				$nom  = $link->real_escape_string( $_POST[ 'nom' ][ $i ] );
				$cat  = $link->real_escape_string( $_POST[ 'ordre' ][ $i ] );
				$soc  = $link->real_escape_string( $_POST[ 'socle' ][ $i ] );
				$sql  = "UPDATE " . $prefix . "competences SET nom = '$nom', cat = '$cat', socle = '$soc', niveau = '$niveau', discipline = '$discipline', libelle = '$lib' WHERE id = '$exid'";
				$link->query( $sql );
				if ( $exid !== $id ) {
					updidcompetence( $exid, $id );
				}
			}
			if ( !empty( $_POST[ 'idnew' ] ) ) {
				$lib = formatid( $link->real_escape_string( $_POST[ 'idnew' ] ) );
				$id  = $niveau . "." . $discipline . "." . $lib;
				$nom = $link->real_escape_string( $_POST[ 'nomnew' ] );
				$cat = $link->real_escape_string( $_POST[ 'ordrenew' ] );
				$soc = $link->real_escape_string( $_POST[ 'soclenew' ] );
				$sql = "INSERT INTO " . $prefix . "competences (id, nom, cat, socle, niveau, discipline, libelle) VALUES ('$id', '$nom', '$cat', '$soc', '$niveau', '$discipline', '$lib')";
				$link->query( $sql );
			}
		}
		if ( ( !empty( $_POST[ 'subSuppr' ] ) ) && ( !empty( $_POST[ 'supprcp' ] ) ) ) {
			$supprcp = $link->real_escape_string( $_POST[ 'supprcp' ] );
			updidcompetence( $supprcp, "" );
		}
		if ( ( !empty( $_POST[ 'subUpload' ] ) ) && ( !empty( $_FILES[ 'fichiercsv' ][ 'tmp_name' ] ) ) ) {
			$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE niveau = '$niveau' AND discipline = '$discipline'" );
			while ( $r = mysqli_fetch_array( $result ) ) {
				updidcompetence( $r[ 'id' ], "" );
			}
			$fichiercsv = $_FILES[ 'fichiercsv' ][ 'tmp_name' ];
			$fic        = fopen( "$fichiercsv", 'rb' );
			for ( $ligne = fgetcsv( $fic, 1024, ";" ); !feof( $fic ); $ligne = fgetcsv( $fic, 1024, ";" ) ) {
				$symbole = substr( $ligne[ 0 ], 0, 1 );
				if ( $symbole !== "#" ) {
					$cat = $link->real_escape_string( $ligne[ 0 ] );
					$lib = formatid( $link->real_escape_string( $ligne[ 1 ] ) );
					$id  = $niveau . "." . $discipline . "." . $lib;
					$soc = $link->real_escape_string( $ligne[ 2 ] );
					$nom = $link->real_escape_string( $ligne[ 3 ] );
					$sql = "INSERT INTO " . $prefix . "competences (id, nom, cat, socle, niveau, discipline, libelle) VALUES ('$id', '$nom', $cat, '$soc', '$niveau', '$discipline', '$lib')";
					$link->query( $sql );
				}
			}
			fclose( $fic );
		}
		echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' method='POST'><table class='data large'><thead><tr><th style='width:40px;'>Id</th><th>Nom</th><th style='width:20px;'>So</th><th style='width:20px;'>#</th></tr></thead><tbody>";
		$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE niveau = '$niveau' AND discipline = '$discipline' ORDER BY cat ASC, id ASC" );
		$i      = 1;
		while ( $r = mysqli_fetch_array( $result ) ) {
			$exid  = $r[ 'id' ];
			$id    = substr( $r[ 'id' ], strlen( $niveau ) + strlen( $discipline ) + 2 );
			$socle = $r[ 'socle' ];
			$nom   = stripslashes( $r[ 'nom' ] );
			$ordre = $r[ 'cat' ];
			echo "<tr>";
			echo "<td style='width:40px;'><input type='hidden' value=\"$exid\" name='exid[$i]' /><input type='text' class='inputcell' value=\"$id\" name='id[$i]' /></td>";
			echo "<td><input type='text' class='inputcell' value=\"$nom\" name='nom[$i]' /></td>";
			echo "<td style='width:20px;'><input type='text' class='inputcell' value=\"$socle\" name='socle[$i]' /></td>";
			echo "<td style='width:20px;'><input type='text' class='inputcell' value=\"$ordre\" name='ordre[$i]' /></td>";
			echo "</tr>";
			$i++;
		}
		echo "<tr>";
		echo "<td style='width:40px;'><input type='text' class='inputcell' value='' name='idnew' placeholder='...' /></td>";
		echo "<td><input type='text' class='inputcell' value='' name='nomnew' placeholder='...' /></td>";
		echo "<td style='width:20px;'><input type='text' class='inputcell' value='' name='soclenew' placeholder='...' /></td>";
		echo "<td style='width:20px;'><input type='text' class='inputcell' value='' name='ordrenew' placeholder='...' /></td>";
		echo "</tr></tbody></table>";
		echo "<p class='noprint'><input type='submit' value='Valider' name='submit' /></p></form>";
		$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE niveau = '$niveau' AND discipline = '$discipline' ORDER BY cat ASC, id ASC" );
		echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' name='formsuppr' method='POST' class='noprint'><p>Supprimer une comp&eacute;tence : <select name='supprcp'><option value='' selected>...</option>";
		while ( $r = mysqli_fetch_array( $result ) ) {
			$idcp  = $r[ 'id' ];
			$nomcp = stripslashes( $r[ 'nom' ] );
			echo "<option value='$idcp'>" . substr( $idcp, strlen( $niveau ) + strlen( $discipline ) + 2 ) . " : $nomcp</option>";
		}
		echo "</select> <input type='submit' value='Valider' name='subSuppr' /></p></form>";
		echo "<h2>Importation et exportation</h2>";
		echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
		echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
		echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='T&eacute;l&eacute;charger' onclick='location.href=\"exportcsv.php?cont=competences&amp;idnv=" . $niveau . "&idds=" . $discipline . "\"' name='subExport' /></p>";
	} else {
		echo "<p>Vous n'avez pas choisi soit le niveau, soit la discipline.</p>";
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>