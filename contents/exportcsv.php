<?php
$cont = $_GET[ 'cont' ];
if ( $cont == "eleves" ) {
	$classe    = $link->real_escape_string( $_GET[ 'idcl' ] );
	$result    = $link->query( "SELECT * FROM " . $prefix . "classes WHERE id = '$classe'" );
	$r         = mysqli_fetch_array( $result );
	$nomclasse = $r[ 'nom' ];
	header( "Content-type:text/csv; charset=UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Eleves-$nomclasse.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "eleves WHERE classe = '$classe' ORDER BY nom ASC, prenom ASC" );
	echo "#Nom;Prenom;Sexe;Date de naissance;Regime;Options;Commentaires;Identifiant;MotDePasse_En_Clair\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		$r[ 'options' ] = str_replace( "\r\n", "", $r[ 'options' ] );
		$r[ 'options' ] = str_replace( "\n", "", $r[ 'options' ] );
		echo stripslashes( $r[ 'nom' ] ) . ";" . stripslashes( $r[ 'prenom' ] ) . ";" . $r[ 'sexe' ] . ";" . $r[ 'datenaissance' ] . ";" . $r[ 'regime' ] . ";" . stripslashes( $r[ 'options' ] ) . ";" . stripslashes( $r[ 'commentaires' ] ) . ";" . $r[ 'username' ] . ";" . decrypt( $r[ 'password' ] ) . "\n";
	}
} elseif ( $cont == "profs" ) {
	header( "Content-type:text/csv; charset=UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Professeurs.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "profs ORDER BY nom ASC, prenom ASC" );
	echo "#Nom;Prenom;Classes;Discipline;Identifiant;MotDePasse_Hashe_Avec_Md5;Administrateur\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo stripslashes( $r[ 'nom' ] ) . ";" . stripslashes( $r[ 'prenom' ] ) . ";" . stripslashes( $r[ 'classes' ] ) . ";" . stripslashes( $r[ 'disciplines' ] ) . ";" . $r[ 'username' ] . ";" . $r[ 'password' ] . ";" . $r[ 'admin' ] . "\n";
	}
} elseif ( $cont == "competences" ) {
	$niveau        = $link->real_escape_string( $_GET[ 'idnv' ] );
	$discipline    = $link->real_escape_string( $_GET[ 'idds' ] );
	$result        = $link->query( "SELECT * FROM " . $prefix . "disciplines WHERE id = '$discipline'" );
	$r             = mysqli_fetch_array( $result );
	$nomdiscipline = $r[ 'nom' ];
	header( "Content-type:text/csv; charset=UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Competences-Niv$niveau-$nomdiscipline.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE niveau = '$niveau' AND discipline = '$discipline' ORDER BY cat ASC, id ASC" );
	echo "#Categorie;Id;Nom;Socle\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo $r[ 'cat' ] . ";" . substr( $r[ 'id' ], strlen( $niveau ) + strlen( $discipline ) + 2 ) . ";" . $r[ 'socle' ] . ";" . stripslashes( $r[ 'nom' ] ) . "\n";
	}
} elseif ( $cont == "progressions" ) {
	$classe        = $link->real_escape_string( $_GET[ 'idcl' ] );
	$discipline    = $link->real_escape_string( $_GET[ 'idds' ] );
	$result        = $link->query( "SELECT * FROM " . $prefix . "disciplines WHERE id = '$discipline'" );
	$r             = mysqli_fetch_array( $result );
	$nomdiscipline = $r[ 'nom' ];
	$result        = $link->query( "SELECT * FROM " . $prefix . "classes WHERE id = '$classe'" );
	$r             = mysqli_fetch_array( $result );
	$nomclasse     = $r[ 'nom' ];
	header( "Content-type:text/csv; charset=UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Progressions-$nomdiscipline-$nomclasse.csv\"" );
	echo "#Libelles;Descriptions;Notations;Icones\n";
	$result = $link->query( "SELECT * FROM " . $prefix . "notations WHERE classe = '$classe' AND discipline = '$discipline'" );
	$r      = mysqli_fetch_array( $result );
	echo $r[ 'libelles' ] . ";" . $r[ 'descriptions' ] . ";" . $r[ 'notations' ] . ";" . $r[ 'icones' ] . "\n";
	$result = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE classe = '$classe' AND discipline = '$discipline' ORDER BY id ASC" );
	echo "#Id;Nom;Competences;Baremes;Ceintures;NombreEvaluations;AutoEvaluation;Mode;Trimestre;Date\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo substr( $r[ 'id' ], strlen( $classe ) + strlen( $discipline ) + 2 ) . ";" . stripslashes( $r[ 'nom' ] ) . ";" . $r[ 'competences' ] . ";" . $r[ 'baremes' ] . ";" . $r[ 'ceintures' ] . ";" . $r[ 'nbevaluations' ] . ";" . $r[ 'autoevaluation' ] . ";" . $r[ 'mode' ] . ";" . $r[ 'trimestre' ] . ";" . affdate( $r[ 'date' ] ) . "\n";
	}
} elseif ( $cont == "classes" ) {
	header( "Content-type:text/csv; charset: UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Classes.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "classes ORDER BY niveau DESC, id ASC" );
	echo "#Niveau;Id;Nom\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo $r[ 'niveau' ] . ";" . $r[ 'id' ] . ";" . stripslashes( $r[ 'nom' ] ) . "\n";
	}
} elseif ( $cont == "disciplines" ) {
	header( "Content-type:text/csv; charset: UTF-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Disciplines.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "disciplines ORDER BY id ASC" );
	echo "#Id;Nom;Active\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo $r[ 'id' ] . ";" . stripslashes( $r[ 'nom' ] ) . ";" . stripslashes( $r[ 'active' ] ) . "\n";
	}
} elseif ( $cont == "niveaux" ) {
	header( "Content-type:text/csv; charset=utf-8" );
	header( "Content-disposition: attachment; filename=\"Zephire-Niveaux.csv\"" );
	$result = $link->query( "SELECT * FROM " . $prefix . "niveaux ORDER BY id ASC" );
	echo "#Id;Nom\n";
	while ( $r = mysqli_fetch_array( $result ) ) {
		echo $r[ 'id' ] . ";" . stripslashes( $r[ 'nom' ] ) . "\n";
	}
}
mysqli_close( $link );
?>