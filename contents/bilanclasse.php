<?php
echo "<h1>Bilan classe</h1>";
if ( estprof() ) {
	$classe     = !empty( $_GET[ 'idcl' ] ) ? $_GET[ 'idcl' ] : NULL;
	$classe     = verifclasse( $link->real_escape_string( $classe ) );
	$discipline = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
	$discipline = verifdiscipline( $link->real_escape_string( $discipline ) );
	$chapitre   = !empty( $_GET[ 'idch' ] ) ? $_GET[ 'idch' ] : NULL;
	$chapitre   = verifchapitre( $link->real_escape_string( $chapitre ), $classe, $discipline );
	$idchapitre = substr( $chapitre, strlen( $classe ) + strlen( $discipline ) + 2 );
	$eleve      = !empty( $_GET[ 'idel' ] ) ? $_GET[ 'idel' ] : NULL;
	$eleve      = verifeleve( $link->real_escape_string( $eleve ), $classe );
	if ( !empty( $classe ) ) {
		$infocl     = infocl( $classe );
		$niveau     = $infocl[ 'niveau' ];
		$competence = !empty( $_GET[ 'idcp' ] ) ? $_GET[ 'idcp' ] : NULL;
		$competence = verifcompetence( $link->real_escape_string( $competence ), $chapitre, $classe );
	}
	if ( !empty( $chapitre ) ) {
		$infoch = infoch( $chapitre );
		$nomch  = $infoch[ 'nom' ];
		$barmch = $infoch[ 'baremes' ];
		$nbevch = $infoch[ 'nbevaluations' ];
		$autoch = $infoch[ 'autoevaluation' ];
		$modech = $infoch[ 'mode' ];
		if ( $autoch ) {
			$mxevch = $nbevch + 1;
		} else {
			$mxevch = $nbevch;
		}
		if ( $modech == 1 ) {
			$codes = $ceintures;
		} else {
			$codes = $codespdef;
		}
		$maxpoints = 0;
		foreach ( $barmch as $barpts ) {
			$maxpoints = $maxpoints + $barpts;
		}
		$notech = ( $maxpoints > 0 ) && ( $modech != 1 );
		$compch = $infoch[ 'competences' ];
		$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC" );
		$i      = 0;
		while ( $r = mysqli_fetch_array( $result ) ) {
			$comp[ $i ][ 'id' ]      = $r[ 'id' ];
			$comp[ $i ][ 'points' ]  = $barmch[ $comp[ $i ][ 'id' ] ];
			$comp[ $i ][ 'id' ]      = substr( $comp[ $i ][ 'id' ], strlen( $niveau ) + strlen( $discipline ) + 2 );
			$comp[ $i ][ 'nom' ]     = stripslashes( $r[ 'nom' ] );
			$comp[ $i ][ 'socle' ]   = $r[ 'socle' ];
			$comp[ $i ][ 'libelle' ] = $comp[ $i ][ 'id' ];
			$idcomp                  = $comp[ $i ][ 'id' ];
			if ( isset( $lesevals[ $idcomp ] ) ) {
				$evals = explode( "-", $lesevals[ $idcomp ] );
				if ( sizeof( $evals ) < $mxevch ) {
					for ( $k = sizeof( $evals ); $k < $mxevch; $k++ ) {
						$evals[ $k ] = "";
					}
				}
			} else {
				for ( $k = 0; $k < $mxevch; $k++ ) {
					$evals[ $k ] = "";
				}
			}
			$comp[ $i ][ 'evals' ] = $evals;
			$i++;
		}
	}
	echo "<div class='selecteurs'><p>";
	selectclasse( "\"bilanclasse.php?idcl=\" + this.value" );
	selectdiscipline( "\"bilanclasse.php?idcl=$classe&amp;idds=\" + this.value" );
	selectchapitre( "\"bilanclasse.php?idcl=$classe&amp;idds=$discipline&amp;idch=\" + this.value" );
	echo "</p></div>";
	if ( ( !empty( $classe ) ) && ( !empty( $discipline ) ) && ( !empty( $chapitre ) ) ) {
		for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
			$idcompetence[ $i ]   = $comp[ $i ][ 'id' ];
			$desccompetence[ $i ] = "<strong>" . substr( $idcompetence[ $i ], strlen( $niveau ) + strlen( $discipline ) + 2 ) . "</strong> " . stripslashes( $comp[ $i ][ 'nom' ] );
			if ( $comp[ $i ][ 'socle' ] != '' ) {
				$desccompetence[ $i ] .= " [" . $comp[ $i ][ 'socle' ] . "]";
			}
		}
		$desccomps = array_combine( $idcompetence, $desccompetence );
		for ( $j = 0; $j < sizeof( $idcompetence ); $j++ ) {
			foreach ( $codes as $acq ) {
				$recap[ $idcompetence[ $j ] ][ $acq ] = 0;
			}
		}
		$result = $link->query( "SELECT * FROM " . $prefix . "eleves WHERE classe = '$classe' ORDER BY nom ASC, prenom ASC" );
		$totel  = $result->num_rows;
		while ( $el = mysqli_fetch_array( $result ) ) {
			$eleve       = $el[ 'id' ];
			$nomeleve    = $el[ 'nom' ];
			$prenomeleve = $el[ 'prenom' ];
			for ( $j = 0; $j < sizeof( $idcompetence ); $j++ ) {
				$recupeval = recupevalch( $chapitre, $eleve );
				$lesevals  = $recupeval[ 0 ];
				$absent    = $recupeval[ 1 ];
				$nonnote   = $recupeval[ 2 ];
				$lst3evals = $lesevals[ $idcompetence[ $j ] ];
				$val3evals = explode( "-", $lst3evals );
				$dereval   = recupdereval( $val3evals );
				if ( $modech == 2 ) {
					$baremecomp = $barmch[ $idcompetence[ $j ] ];
					$dereval    = donneacqui( $classe, $dereval, $baremecomp );
				}
				$recap[ $idcompetence[ $j ] ][ $dereval ]++;
			}
		}
		echo "<div style='width:100%;overflow:scroll;'><table class='data large'><thead><tr><th>Intitul&eacute;</th>";
		foreach ( $codes as $cd ) {
			echo "<th style='width:60px;'>" . enimage( $cd, $classe, $discipline, $modech ) . "</th>";
		}
		for ( $j = 0; $j < sizeof( $idcompetence ); $j++ ) {
			echo "<tr><td style='text-align:left'>" . $desccomps[ $idcompetence[ $j ] ] . "</td>";
			foreach ( $codes as $cd ) {
				echo "<td>" . round( $recap[ $idcompetence[ $j ] ][ $cd ] / $totel * 100, 0 ) . " %</td>";
			}
			echo "</tr>";
		}
		echo "</tbody></table>";
		echo "<h2>Par comp&eacute;tence</h2>";
		echo "<div class='selecteurs'><p>";
		selectcompetence( "\"bilanclasse.php?idcl=$classe&amp;idds=$discipline&amp;idch=$idchapitre&amp;idcp=\" + this.value" );
		echo "</p></div>";
	} else {
		echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline, soit le chapitre.</p>";
	}
	if ( ( !empty( $classe ) ) && ( !empty( $chapitre ) ) && ( !empty( $competence ) ) ) {
		$result = $link->query( "SELECT * FROM " . $prefix . "eleves WHERE classe = '$classe' ORDER BY nom ASC, prenom ASC" );
		echo "<table class='data'>";
		echo "<thead><tr><th>El&egrave;ve</th>";
		if ( $autoch ) {
			echo "<th style='width:60px;'>Autoeval</th>";
		}
		if ( $nbevch == 1 ) {
			echo "<th style='width:60px;'>Eval</th>";
		} else {
			for ( $k = 0; $k < $nbevch; $k++ ) {
				echo "<th style='width:60px;'>Eval " . ( $k + 1 ) . "</th>";
			}
		}
		if ( $modech == 0 ) {
			echo "<th>Valid&eacute;e ?</th>";
		}
		echo "</tr></thead><tbody>";
		while ( $el = mysqli_fetch_array( $result ) ) {
			$eleve       = $el[ 'id' ];
			$nomeleve    = $el[ 'nom' ];
			$prenomeleve = $el[ 'prenom' ];
			$recupeval   = recupevalch( $chapitre, $eleve );
			$baremecomp  = $barmch[ $competence ];
			$lesevals    = $recupeval[ 0 ];
			$absent      = $recupeval[ 1 ];
			$nonnote     = $recupeval[ 2 ];
			if ( $lesevals !== -1 ) {
				$evals = explode( "-", $lesevals[ $competence ] );
				if ( sizeof( $evals ) < $mxevch ) {
					for ( $k = sizeof( $evals ); $k < $mxevch; $k++ ) {
						$evals[ $k ] = "";
					}
				}
				if ( $modech == 2 ) {
					foreach ( $evals as $k => $v ) {
						$evals[ $k ] = donneacqui( $classe, $v, $baremecomp );
					}
				}
			} else {
				for ( $k = 0; $k < $mxevch; $k++ ) {
					$evals[ $k ] = "";
				}
			}
			echo "<tr><td style='text-align:left;'>$nomeleve $prenomeleve</td>";
			foreach ( $evals as $eval ) {
				echo "<td>" . enimage( $eval, $classe, $discipline, $modech ) . "</td>";
			}
			if ( $modech == 0 ) {
				echo "<td>" . enimage( estvalide( $evals, $autoch ), $classe, $discipline, $modech ) . "</td>";
			}
			echo "</tr>";
		}
		echo "</tbody></table></div>";
	}
	if ( ( !empty( $classe ) ) && ( !empty( $chapitre ) ) ) {
		include( "explication.php" );
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant que professeur.</p>";
}
?>