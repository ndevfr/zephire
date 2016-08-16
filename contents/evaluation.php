<?php
echo "<h1>Evaluer</h1>";
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
		$infocl    = infocl( $classe );
		$niveau    = $infocl[ 'niveau' ];
		$nomclasse = $infocl[ 'nom' ];
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
		} elseif ( $modech == 0 ) {
			$codes = $codespdef;
		}
		$maxpoints = 0;
		foreach ( $barmch as $barpts ) {
			$maxpoints = $maxpoints + $barpts;
		}
		$notech = ( $maxpoints > 0 ) && ( $modech != 1 );
		$compch = $infoch[ 'competences' ];
	}
	if ( !empty( $eleve ) ) {
		$infoel      = infoel( $eleve );
		$nomeleve    = $infoel[ 'nom' ];
		$prenomeleve = $infoel[ 'prenom' ];
	}
	if ( isset( $_GET[ 'corr' ] ) ) {
		$corr = $link->real_escape_string( $_GET[ 'corr' ] );
	} else {
		$corr = 0;
	}
	echo "<div class='selecteurs'><p>";
	selectclasse( "\"evaluation.php?idcl=\" + this.value" );
	selectdiscipline( "\"evaluation.php?idcl=$classe&amp;idds=\" + this.value" );
	selectchapitre( "\"evaluation.php?idcl=$classe&amp;idds=$discipline&amp;idch=\" + this.value + \"&amp;idel=$eleve\"" );
	selecteleve( "\"evaluation.php?idcl=$classe&amp;idds=$discipline&amp;idch=$idchapitre&amp;idel=\" + this.value" );
	echo "</p></div>";
	function majcps()
	{
		global $prefix, $link, $comp, $absent, $nonnote, $classe, $discipline, $chapitre, $eleve, $compch, $barmch, $mxevch, $niveau;
		$comp = array();
		if ( ( !empty( $classe ) ) && ( !empty( $discipline ) ) && ( !empty( $chapitre ) ) && ( !empty( $eleve ) ) ) {
			$infoel    = infoel( $eleve );
			$recupeval = recupevalch( $chapitre, $eleve );
			$lesevals  = $recupeval[ 0 ];
			$absent    = $recupeval[ 1 ];
			$nonnote   = $recupeval[ 2 ];
			$result    = $link->query( "SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC" );
			$i         = 0;
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
			return true;
		} else {
			return false;
		}
	}
	majcps();
	if ( !empty( $_POST[ 'submit' ] ) ) {
		$lstevals    = "";
		$lstcomps    = "";
		$selectbilan = "";
		if ( !empty( $_POST[ "selectbilan" ] ) ) {
			$selectbilan = $_POST[ "selectbilan" ];
		}
		for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
			$idcomp = $comp[ $i ][ 'id' ];
			if ( $i > 0 ) {
				$lstcomps .= ",";
				$lstevals .= ",";
			}
			$lstcomps .= $comp[ $i ][ 'id' ];
			for ( $j = 0; $j < $mxevch; $j++ ) {
				if ( $j > 0 ) {
					$lstevals .= "-";
				}
				$lstevals .= $_POST[ $idcomp . "," . $j ];
			}
		}
		if ( !empty( $_POST[ 'abs' ] ) ) {
			$absent = 1;
		} else {
			$absent = 0;
		}
		if ( !empty( $_POST[ 'nn' ] ) ) {
			$nonnote = 1;
		} else {
			$nonnote = 0;
		}
		$result = $link->query( "SELECT * FROM " . $prefix . "evaluations WHERE classe = '$classe' AND discipline = '$discipline' AND chapitre = '$idchapitre' AND eleve = $eleve" );
		if ( $result->num_rows > 0 ) {
			$sql = "UPDATE " . $prefix . "evaluations SET competences = '$lstcomps', evaluations = '$lstevals', bilan = '$selectbilan', absent = '$absent', nonnote = '$nonnote' WHERE classe = '$classe' AND discipline = '$discipline' AND chapitre = '$idchapitre' AND eleve = $eleve";
		} else {
			$sql = "INSERT INTO " . $prefix . "evaluations (id, competences, evaluations, bilan, absent, nonnote, classe, eleve, discipline, chapitre) VALUES ('$chapitre.$eleve', '$lstcomps', '$lstevals', '$selectbilan', $absent, $nonnote, '$classe', $eleve, '$discipline', '$idchapitre')";
		}
		$link->query( $sql );
		majcps();
	}
	$cptvalide = 0;
	$cptpoints = 0;
	if ( empty( $_GET[ 'av' ] ) ) {
		$av = 0;
	} elseif ( $_GET[ 'av' ] == 1 ) {
		$av = 1;
	} else {
		$av = 0;
	}
	function selectmult( $j, $classe )
	{
		global $discipline, $comp, $autoch, $codes, $modech;
		$txt = "";
		if ( $modech == 0 ) {
			if ( ( ( $autoch ) && ( $j > 0 ) ) || ( !$autoch ) || ( modeav() ) ) {
				$txt = "<select onchange='";
				for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
					if ( ( $comp[ $i ][ "evals" ][ $j ] == "" ) OR ( modeav() ) ) {
						$txt .= "document.getElementById(\"selectev$j-$i\").selectedIndex=this.selectedIndex;";
					}
				}
				$txt .= "'>";
				foreach ( $codes as $cd ) {
					$txt .= "<option value='$cd'>" . donnelib( $cd, $classe, $discipline, $modech ) . "</option>";
				}
				$txt .= "</select>";
			}
		}
		return $txt;
	}
	if ( ( !empty( $classe ) ) && ( !empty( $discipline ) ) && ( !empty( $chapitre ) ) && ( !empty( $eleve ) ) ) {
		$tabindex = 6;
		$nbcomps  = sizeof( $comp );
		echo "<form action='evaluation.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $idchapitre . "&amp;idel=" . $eleve . "' method='POST'>";
		echo "<div style='width:100%;overflow:scroll;'><table class='data large' style='border:0px;'><thead><tr><th>Intitul&eacute;</th>";
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
			echo "<th style='width:50px;'>Valid&eacute;e</th>";
		}
		if ( $notech ) {
			echo "<th style='width:50px;'>Points</th><th style='width:50px;'>Bar&egrave;me</th>";
		}
		echo "</tr></thead><tbody>";
		for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
			$idcomp = $comp[ $i ][ 'id' ];
			echo "<tr><td style='text-align:left;padding-left:4px;'><strong>" . $comp[ $i ][ 'libelle' ] . "</strong> " . $comp[ $i ][ 'nom' ];
			if ( !empty( $comp[ $i ][ 'socle' ] ) ) {
				echo " [" . $comp[ $i ][ 'socle' ] . "]";
			}
			$j = 0;
			if ( $autoch ) {
				if ( modeav() ) {
					if ( $modech == 2 ) {
						echo "<td><input type='number' pattern='[0-9]' min='0' max='" . $comp[ $i ][ 'points' ] . "' step='0.5' class='inputnote' tabindex='" . ( $tabindex + $i ) . "' name='" . $comp[ $i ][ 'id' ] . ",$j' value='" . $comp[ $i ][ 'evals' ][ $j ] . "' /></td>";
					} else {
						echo "<td><select id='selectev$j-$i' tabindex='" . ( $tabindex + $i ) . "' name='" . $comp[ $i ][ 'id' ] . ",$j'>";
						if ( $modech == 1 ) {
							$codesform = recupceintures( $chapitre, $comp[ $i ][ 'id' ] );
						} else {
							$codesform = $codes;
						}
						foreach ( $codesform as $cd ) {
							echo "<option value='$cd'";
							if ( $comp[ $i ][ 'evals' ][ $j ] == $cd ) {
								echo " selected";
							}
							echo ">" . donnelib( $cd, $classe, $discipline, $modech ) . "</option>";
						}
						echo "</select></td>";
					}
				} else {
					if ( $modech == 2 ) {
						echo "<td>" . $comp[ $i ][ 'evals' ][ $j ];
					} else {
						echo "<td>" . enimage( $comp[ $i ][ 'evals' ][ $j ], $classe, $discipline, $modech );
					}
					echo "<input type='hidden' name='" . $comp[ $i ][ 'id' ] . ",$j' value='" . $comp[ $i ][ 'evals' ][ $j ] . "' /></td>";
				}
				$tabindex = $tabindex + $nbcomps;
				$j        = 1;
			}
			while ( $j < $mxevch ) {
				if ( $modech == 2 ) {
					echo "<td><input type='number' pattern='[0-9]' min='0' max='" . $comp[ $i ][ 'points' ] . "' step='0.5' class='inputnote' tabindex='" . ( $tabindex + $i + $j * $nbcomps ) . "' name='" . $comp[ $i ][ 'id' ] . ",$j' value='" . $comp[ $i ][ 'evals' ][ $j ] . "' /></td>";
				} else {
					if ( ( ( $comp[ $i ][ 'evals' ][ $j ] == "" ) || ( modeav() ) ) ) {
						echo "<td><select id='selectev$j-$i' tabindex='" . ( $tabindex + $i + $j * $nbcomps ) . "' name='" . $comp[ $i ][ 'id' ] . ",$j'>";
						if ( $modech == 1 ) {
							$codesform = recupceintures( $chapitre, $comp[ $i ][ 'id' ] );
						} else {
							$codesform = $codes;
						}
						foreach ( $codesform as $cd ) {
							echo "<option value='$cd'";
							if ( $comp[ $i ][ 'evals' ][ $j ] == $cd ) {
								echo " selected";
							}
							echo ">" . donnelib( $cd, $classe, $discipline, $modech ) . "</option>";
						}
						echo "</select></td>";
					} else {
						echo "<td>" . enimage( $comp[ $i ][ 'evals' ][ $j ], $classe, $discipline, $modech );
						echo "<input type='hidden' name='" . $comp[ $i ][ 'id' ] . ",$j' value='" . $comp[ $i ][ 'evals' ][ $j ] . "' /></td>";
					}
				}
				$j++;
			}
			if ( $modech == 0 ) {
				echo "<td>" . enimage( estvalide( $comp[ $i ][ 'evals' ], $autoch, $modech ), $classe, $discipline, $modech ) . "</td>";
			}
			if ( $notech ) {
				if ( $modech == 2 ) {
					echo "<td>" . donnedernote( $comp[ $i ][ 'evals' ], $autoch ) . "</td>";
					$cptpoints += donnedernote( $comp[ $i ][ 'evals' ], $autoch );
				} else {
					echo "<td>" . round( donnenote( $comp[ $i ][ 'evals' ], $classe, $discipline, $autoch ) * $comp[ $i ][ 'points' ] / 100, 2 ) . "</td>";
					$cptpoints += round( donnenote( $comp[ $i ][ 'evals' ], $classe, $discipline, $autoch ) * $comp[ $i ][ 'points' ] / 100, 2 );
				}
				echo "<td>" . $comp[ $i ][ 'points' ] . "</td>";
			}
			if ( $modech != 2 ) {
				if ( estvalide( $comp[ $i ][ 'evals' ], $autoch ) == "OUI" ) {
					$cptvalide += 1;
				}
			}
			echo "</tr>";
		}
		if ( $absent ) {
			$estabs = true;
		} else {
			$estabs = false;
		}
		if ( $nonnote ) {
			$estnn = true;
		} else {
			$estnn = false;
		}
		echo "<tr><td class='noborder' style='text-align:left;padding-left:4px;'></td>";
		for ( $j = 0; $j < $mxevch; $j++ ) {
			echo "<td class='noborder' style='width:60px;'>" . selectmult( $j, $classe ) . "</td>";
		}
		if ( $estabs ) {
			if ( $modech == 0 ) {
				echo "<td style='width:50px;'><strong>ABS</strong></td>";
			}
			if ( $notech ) {
				echo "<td style='width:50px;'><strong>ABS</strong></td><td style='width:50px;'><strong>" . $maxpoints . "</strong></td>";
			}
		} elseif ( $estnn ) {
			if ( $modech == 0 ) {
				echo "<td style='width:50px;'><strong>NN</strong></td>";
			}
			if ( $notech ) {
				echo "<td style='width:50px;'><strong>NN</strong></td><td style='width:50px;'><strong>" . $maxpoints . "</strong></td>";
			}
		} else {
			if ( $modech == 0 ) {
				echo "<td style='width:50px;'><strong>" . round( $cptvalide / sizeof( $comp ) * 100, 1 ) . " %</strong></td>";
			}
			if ( $notech ) {
				echo "<td style='width:50px;'><strong>" . $cptpoints . "</strong></td><td style='width:50px;'><strong>" . $maxpoints . "</strong></td>";
			}
		}
		echo "</tr></tbody></table></div>";
		echo "<p style='text-align:left;'><label><input type='checkbox' name='abs'";
		if ( $estabs ) {
			echo " checked";
		}
		echo "/> Not&eacute;(e) absent(e).</label>";
		echo "<span id='sep'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><label><input type='checkbox' name='nn'";
		if ( $estnn ) {
			echo " checked";
		}
		echo "/> Non Not&eacute;(e).</label></p>";
		if ( $modech == 0 ) {
			echo "<p style='text-align:left;'><strong>BILAN AUTO : </strong>" . enimage( bilanauto( $eleve, $chapitre ), $classe, $discipline, 0 ) . "<br /><strong>BILAN PROF : </strong><select id='selectbilan' name='selectbilan'>";
			foreach ( $codes as $cd ) {
				echo "<option value='$cd'";
				if ( bilanprof( $eleve, $chapitre ) == $cd ) {
					echo " selected";
				}
				echo ">" . donnelib( $cd, $classe, $discipline, $modech ) . "</option>";
			}
			echo "</select></p>";
		}
		if ( modeav() ) {
			echo "<p class='noprint'><input type='button' value='Simple' onClick='location.href=\"evaluation.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $idchapitre . "&amp;idel=" . $eleve . "\"' /> ";
		} else {
			echo "<p class='noprint'><input type='button' value='Avanc&eacute;' onClick='location.href=\"evaluation.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $idchapitre . "&amp;idel=" . $eleve . "&amp;av=1\"' /> ";
		}
		echo "<input name='submit' type='submit' value='Envoyer' /></p></form>";
		include( "explication.php" );
	} else {
		echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline, soit le chapitre, soit l'&eacute;l&egrave;ve.</p>";
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant que professeur.</p>";
}
?>