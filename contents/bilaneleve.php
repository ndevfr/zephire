<?php
$classe     = !empty($_GET['idcl']) ? $_GET['idcl'] : NULL;
$classe     = verifclasse($link->real_escape_string($classe));
$discipline = !empty($_GET['idds']) ? $_GET['idds'] : NULL;
$discipline = verifdiscipline($link->real_escape_string($discipline));
$chapitre   = !empty($_GET['idch']) ? $_GET['idch'] : NULL;
$chapitre   = verifchapitre($link->real_escape_string($chapitre), $classe, $discipline);
$eleve      = !empty($_GET['idel']) ? $_GET['idel'] : NULL;
$eleve      = verifeleve($link->real_escape_string($eleve), $classe);
if (estprof()) {
    if (!empty($classe)) {
        $infocl    = infocl($classe);
        $niveau    = $infocl['niveau'];
        $nomclasse = $infocl['nom'];
    }
}
if ((!empty($eleve)) && (estprof())) {
    $infoel = infoel($eleve);
} else {
    $eleve = ideleve();
}
if ($eleve == -1) {
    echo "<h1>Bilan &eacute;l&egrave;ve</h1>";
} else {
    $infoel      = infoel($eleve);
    $classe      = $infoel['idclasse'];
    $nomeleve    = $infoel['nom'];
    $prenomeleve = $infoel['prenom'];
    if (estprof()) {
        echo "<h1>Bilan &eacute;l&egrave;ve de $prenomeleve $nomeleve</h1>";
    } else {
        echo "<h1>Mes &eacute;valuations</h1>";
    }
    $infocl = infocl($classe);
    $niveau = $infocl['niveau'];
}
if (!empty($chapitre)) {
    $infoch = infoch($chapitre);
    $nomch  = $infoch['nom'];
    $barmch = $infoch['baremes'];
    $nbevch = $infoch['nbevaluations'];
    $autoch = $infoch['autoevaluation'];
    $modech = $infoch['mode'];
    if ($autoch) {
        $mxevch = $nbevch + 1;
    } else {
        $mxevch = $nbevch;
    }
    if ($modech == 1) {
        $codes = $ceintures;
    } else if ($modech == 0) {
        $codes = $codespdef;
    }
    $maxpoints = 0;
    foreach ($barmch as $barpts) {
        $maxpoints = $maxpoints + $barpts;
    }
    $notech = ($maxpoints > 0) && ($modech != 1);
    $compch = $infoch['competences'];
}


echo "<div class='selecteurs'><p>";
if (estprof()) {
    selectclasse("\"bilaneleve.php?idcl=\" + this.value");
    selecteleve("\"bilaneleve.php?idcl=$classe&amp;idds=$discipline&amp;idch=$chapitre&amp;idel=\" + this.value");
}
if ((estadmin()) OR (!estprof())) {
    selectdiscipline("\"bilaneleve.php?idcl=$classe&amp;idds=\" + this.value + \"&amp;idel=$eleve\"");
}
selectchapitre("\"bilaneleve.php?idcl=$classe&amp;idds=$discipline&amp;idch=\" + this.value + \"&amp;idel=$eleve\"");
echo "</p></div>";

if ($eleve !== -1) {
    function majcps()
    {
        global $prefix, $link, $comp, $absent, $nonnote, $classe, $discipline, $chapitre, $eleve, $compch, $barmch, $mxevch, $niveau;
        $comp = array();
        if ((!empty($classe)) && (!empty($discipline)) && (!empty($chapitre)) && (!empty($eleve))) {
            $infoel    = infoel($eleve);
            $recupeval = recupevalch($chapitre, $eleve);
            $lesevals  = $recupeval[0];
            $absent    = $recupeval[1];
            $nonnote   = $recupeval[2];
            $result    = $link->query("SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC");
            $i         = 0;
            while ($r = mysqli_fetch_array($result)) {
                $comp[$i]['id']      = $r['id'];
                $comp[$i]['nom']     = stripslashes($r['nom']);
                $comp[$i]['socle']   = $r['socle'];
                $comp[$i]['libelle'] = substr($comp[$i]['id'], strlen($niveau) + strlen($discipline));
                $idcomp              = $comp[$i]['id'];
                $comp[$i]['points']  = $barmch[$comp[$i]['id']];
                if (isset($lesevals[$idcomp])) {
                    $evals = explode("-", $lesevals[$idcomp]);
                    if (sizeof($evals) < $mxevch) {
                        for ($k = sizeof($evals); $k < $mxevch; $k++) {
                            $evals[$k] = "";
                        }
                    }
                } else {
                    for ($k = 0; $k < $mxevch; $k++) {
                        $evals[$k] = "";
                    }
                }
                $comp[$i]['evals'] = $evals;
                $i++;
            }
            return true;
        } else {
            return false;
        }
    }
    majcps();
    
    if (!empty($_POST['submit'])) {
        $lstevals    = "";
        $lstcomps    = "";
        $selectbilan = "";
        if (!empty($_POST["selectbilan"])) {
            $selectbilan = $_POST["selectbilan"];
        }
        for ($i = 0; $i < sizeof($comp); $i++) {
            $idcomp = $comp[$i]['id'];
            if ($i > 0) {
                $lstcomps .= ",";
                $lstevals .= ",";
            }
            $lstcomps .= $comp[$i]['id'];
            for ($j = 0; $j < $mxevch; $j++) {
                if ($j > 0) {
                    $lstevals .= "-";
                }
                $lstevals .= $_POST[$idcomp . "," . $j];
            }
        }
        $evaluations = $lstcomps . "|" . $lstevals . "|" . $selectbilan;
        if (!empty($_POST['abs'])) {
            $absent = 1;
        } else {
            $absent = 0;
        }
        if (!empty($_POST['nn'])) {
            $nonnote = 1;
        } else {
            $nonnote = 0;
        }
        $result = $link->query("SELECT * FROM " . $prefix . "evaluations WHERE chapitre = '$chapitre' AND eleve = $eleve");
        if ($result->num_rows > 0) {
            $sql = "UPDATE " . $prefix . "evaluations SET evaluations = '$evaluations', absent = '$absent', nonnote = '$nonnote' WHERE chapitre = '$chapitre' AND eleve = $eleve";
        } else {
            $sql = "INSERT INTO " . $prefix . "evaluations (chapitre, eleve, evaluations, absent, nonnote) VALUES ('$chapitre', $eleve, '$evaluations', $absent, $nonnote)";
        }
        $link->query($sql);
        majcps();
    }
    $cptvalide = 0;
    $cptpoints = 0;
    if (empty($_GET['av'])) {
        $av = 0;
    } elseif ($_GET['av'] == 1) {
        $av = 1;
    } else {
        $av = 0;
    }
    function selectmult($j, $classe)
    {
        global $comp, $autoch, $codes, $modech;
        $txt = "";
        if ($modech == 0) {
            if (($autoch) && ($j == 0) && (!estprof())) {
                $txt = "<select onchange='";
                for ($i = 0; $i < sizeof($comp); $i++) {
                    if ($comp[$i]["evals"][$j] == "") {
                        $txt .= "document.getElementById(\"selectev$j-$i\").selectedIndex=this.selectedIndex;";
                    }
                }
                $txt .= "'>";
                foreach ($codes as $cd) {
                    $txt .= "<option value='$cd'>" . donnelib($cd, $classe, $modech) . "</option>";
                }
                $txt .= "</select>";
            }
        }
        return $txt;
    }
    if ((!empty($classe)) && (!empty($discipline)) && (!empty($chapitre)) && (!empty($eleve))) {
        echo "<form action='bilaneleve.php?idcl=" . $classe . "&idds=" . $discipline . "&amp;idch=" . $chapitre . "&amp;idel=" . $eleve . "' method='POST'>";
        echo "<div style='width:100%;overflow:scroll;'><table class='data large' style='border:0px;'><thead><tr><th>Intitul&eacute;</th>";
        if ($autoch) {
            echo "<th style='width:60px;'>Autoeval</th>";
        }
        if ($nbevch == 1) {
            echo "<th style='width:60px;'>Eval</th>";
        } else {
            for ($k = 0; $k < $nbevch; $k++) {
                echo "<th style='width:60px;'>Eval " . ($k + 1) . "</th>";
            }
        }
        if ($modech == 0) {
            echo "<th style='width:50px;'>Valid&eacute;e</th>";
        }
        if ($notech) {
            echo "<th style='width:50px;'>Points</th><th style='width:50px;'>Bar&egrave;me</th>";
        }
        echo "</tr></thead><tbody>";
        for ($i = 0; $i < sizeof($comp); $i++) {
            $idcomp = $comp[$i]['id'];
            echo "<tr><td style='text-align:left;padding-left:4px;'><strong>" . $comp[$i]['libelle'] . "</strong> " . $comp[$i]['nom'];
            if (!empty($comp[$i]['socle'])) {
                echo " [" . $comp[$i]['socle'] . "]";
            }
            $j = 0;
            if (($autoch) && (!estprof())) {
                if ((($comp[$i]['evals'][$j] == "") || (modeav()))) {
                    if ($modech == 2) {
                        echo "<td><input type='number' min='0' max='" . $comp[$i]['points'] . "' step='0.5' class='inputnote' tabindex='" . ($tabindex + $i) . "' name='" . $comp[$i]['id'] . ",$j' value='" . $comp[$i]['evals'][$j] . "' /></td>";
                    } else {
                        echo "<td><select id='selectev$j-$i' name='" . $comp[$i]['id'] . ",$j'>";
                        if ($modech == 1) {
                            $codesform = recupceintures($chapitre, $comp[$i]['id']);
                        } else {
                            $codesform = $codes;
                        }
                        foreach ($codesform as $cd) {
                            echo "<option value='$cd'";
                            if ($comp[$i]['evals'][$j] == $cd) {
                                echo " selected";
                            }
                            echo ">" . donnelib($cd, $classe, $modech) . "</option>";
                        }
                        echo "</select></td>";
                    }
                } else {
                    if ($modech == 2) {
                        echo "<td>" . $comp[$i]['evals'][$j];
                    } else {
                        echo "<td>" . enimage($comp[$i]['evals'][$j], $classe, $modech);
                    }
                    echo "<input type='hidden' name='" . $comp[$i]['id'] . ",$j' value='" . $comp[$i]['evals'][$j] . "' /></td>";
                }
                $j = 1;
            }
            while ($j < $mxevch) {
                if ($modech == 2) {
                    echo "<td>" . $comp[$i]['evals'][$j];
                } else {
                    echo "<td>" . enimage($comp[$i]['evals'][$j], $classe, $modech);
                }
                echo "<input type='hidden' name='" . $comp[$i]['id'] . ",$j' value='" . $comp[$i]['evals'][$j] . "' /></td>";
                $j++;
            }
            if ($modech == 0) {
                echo "<td>" . enimage(estvalide($comp[$i]['evals'], $autoch, $modech), $classe, $modech) . "</td>";
            }
            if ($notech) {
                if ($modech == 2) {
                    echo "<td>" . donnedernote($comp[$i]['evals'], $autoch) . "</td>";
                    $cptpoints += donnedernote($comp[$i]['evals'], $autoch);
                } else {
                    echo "<td>" . round(donnenote($comp[$i]['evals'], $classe, $autoch) * $comp[$i]['points'] / 100, 2) . "</td>";
                    $cptpoints += round(donnenote($comp[$i]['evals'], $classe, $autoch) * $comp[$i]['points'] / 100, 2);
                }
                echo "<td>" . $comp[$i]['points'] . "</td>";
            }
            if ($modech != 2) {
                if (estvalide($comp[$i]['evals'], $autoch) == "OUI") {
                    $cptvalide += 1;
                }
            }
            echo "</tr>";
        }
        if ($absent) {
            $estabs = true;
        } else {
            $estabs = false;
        }
        
        if ($nonnote) {
            $estnn = true;
        } else {
            $estnn = false;
        }
        if ((!estprof()) || ($modech != 1)) {
            echo "<tr><td class='noborder'></td>";
            for ($j = 0; $j < $mxevch; $j++) {
                echo "<td class='noborder'>" . selectmult($j, $classe) . "</td>";
            }
            if ($estabs) {
                if ($modech == 0) {
                    echo "<td><strong>ABS</strong></td>";
                }
                if ($notech) {
                    echo "<td><strong>ABS</strong></td><td><strong>" . $maxpoints . "</strong></td>";
                }
            } elseif ($estnn) {
                if ($modech == 0) {
                    echo "<td><strong>NN</strong></td>";
                }
                if ($notech) {
                    echo "<td><strong>NN</strong></td><td><strong>" . $maxpoints . "</strong></td>";
                }
            } else {
                if ($modech == 0) {
                    echo "<td><strong>" . round($cptvalide / sizeof($comp) * 100, 1) . " %</strong></td>";
                }
                if ($notech) {
                    echo "<td><strong>" . $cptpoints . "</strong></td><td><strong>" . $maxpoints . "</strong></td>";
                }
            }
            echo "</tr>";
        }
        echo "</tbody></table></div>";
        if ($modech == 0) {
            echo "<p style='text-align:left;'><strong>BILAN DU CHAPITRE : </strong>" . enimage(bilanch($eleve, $chapitre), $classe, 0) . "</p>";
        }
        if ((!estprof()) && ($autoch == 1)) {
            if (modeav()) {
                echo "<p class='noprint'><input type='button' value='Simple' onClick='location.href=\"bilaneleve.php?idcl=" . $classe . "&amp;idch=" . $chapitre . "&idds=" . $discipline . "&amp;idel=" . $eleve . "\"' />";
            } else {
                echo "<p class='noprint'><input type='button' value='Avanc&eacute;' onClick='location.href=\"bilaneleve.php?idcl=" . $classe . "&amp;idch=" . $chapitre . "&idds=" . $discipline . "&amp;idel=" . $eleve . "&amp;av=1\"' />";
            }
            echo "<input name='submit' type='submit' value='Envoyer' /></p>";
        }
        echo "</form>";
        include("explication.php");
    } else {
        echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline, soit le chapitre, soit l'&eacute;l&egrave;ve.</p>";
    }
} else {
    if (estprof()) {
        echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline, soit le chapitre, soit l'&eacute;l&egrave;ve.</p>";
    } else {
        echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'&eacute;l&egrave;ve.</p>";
    }
}
?>