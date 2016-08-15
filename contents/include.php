<?php
require_once("../contents/ext/MobileDetect/Mobile_Detect.php");

if (function_exists("date_default_timezone_set"))
    date_default_timezone_set("Europe/Paris");

// Se connecter a la BDD
$link = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($link->connect_errno) {
    echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

// Fonction array_combine si elle n'existe pas
if (!function_exists('array_combine')) {
    function array_combine($arr1, $arr2)
    {
        $arr = array();
        for ($i = 0; $i < sizeof($arr1); $i++) {
            $arr[$arr1[$i]] = $arr2[$i];
        }
        return $arr;
    }
}

// Nom de l'application
$nomapp = "Plateforme d'&eacute;valuation";

// Description de l'application
$descapp = $SCH;

// Fonction de cryptage des mots de passe
function encrypt($data)
{
    global $RNE;
    $key  = $RNE;
    $data = serialize($data);
    $td   = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
    $iv   = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = base64_encode(mcrypt_generic($td, '!' . $data));
    mcrypt_generic_deinit($td);
    return $data;
}

// Fonction de decryptage des mots de passe
function decrypt($data)
{
    global $RNE;
    $key = $RNE;
    $td  = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
    $iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = mdecrypt_generic($td, base64_decode($data));
    mcrypt_generic_deinit($td);
    if ((substr($data, 0, 1) != '!')) {
        return false;
    }
    $data = substr($data, 1, strlen($data) - 1);
    return unserialize($data);
}

// Fonction donnant l'adresse web de l'application
function linkapp()
{
    $linksite = explode("/", $_SERVER['REQUEST_URI']);
    unset($linksite[sizeof($linksite) - 1]);
    $linksite = implode("/", $linksite);
    return $linksite . "/";
}

// Fonction testant si l'utilisateur est connecte
function estconnecte()
{
    global $prefix, $link;
    if ((!empty($_SESSION['acusername'])) && (!empty($_SESSION['acpassword'])) && (!empty($_SESSION['actype']))) {
        $username = $link->real_escape_string($_SESSION['acusername']);
        $password = $link->real_escape_string($_SESSION['acpassword']);
        $tabsql   = $link->real_escape_string($_SESSION['actype']);
        $result   = $link->query("SELECT * FROM " . $prefix . "$tabsql WHERE username='$username' AND password='$password'");
        if ($result->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

// Fonction testant si l'utilisateur est administrateur
function estadmin()
{
    global $prefix, $link;
    if (estconnecte()) {
        if ($_SESSION['actype'] == "profs") {
            $username = $link->real_escape_string($_SESSION['acusername']);
            $password = $link->real_escape_string($_SESSION['acpassword']);
            $result   = $link->query("SELECT * FROM " . $prefix . "profs WHERE username='$username' AND password='$password'");
            $r        = mysqli_fetch_array($result);
            if ($r['admin'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Fonction recuperant l'ID d'un eleve connecte (-1 si non connecte en tant qu'eleve)
function ideleve()
{
    global $prefix, $link;
    if (estconnecte()) {
        if ($_SESSION['actype'] == "eleves") {
            $username = $link->real_escape_string($_SESSION['acusername']);
            $password = $link->real_escape_string($_SESSION['acpassword']);
            $result   = $link->query("SELECT * FROM " . $prefix . "eleves WHERE username='$username' AND password='$password'");
            $r        = mysqli_fetch_array($result);
            return $r['id'];
        } else {
            return -1;
        }
    } else {
        return -1;
    }
}

// Fonction testant si l'utilisateur est professeur
function estprof()
{
    global $prefix;
    if (estconnecte()) {
        return $_SESSION['actype'] == "profs";
    } else {
        return false;
    }
}

// Fonction recuperant les classes d'un professeur
function classesprof()
{
    global $prefix, $link;
    if (estconnecte()) {
        if ($_SESSION['actype'] == "profs") {
            $username = $link->real_escape_string($_SESSION['acusername']);
            $password = $link->real_escape_string($_SESSION['acpassword']);
            $result   = $link->query("SELECT * FROM " . $prefix . "profs WHERE username='$username' AND password='$password'");
            $r        = mysqli_fetch_array($result);
            return explode(",", $r['classes']);
        } else {
            return array();
        }
    } else {
        return array();
    }
}

// Fonction recuperant la discipline d'un professeur
function disciplineprof()
{
    global $prefix, $link;
    if (estconnecte()) {
        if ($_SESSION['actype'] == "profs") {
            $username = $link->real_escape_string($_SESSION['acusername']);
            $password = $link->real_escape_string($_SESSION['acpassword']);
            $result   = $link->query("SELECT * FROM " . $prefix . "profs WHERE username='$username' AND password='$password'");
            $r        = mysqli_fetch_array($result);
            return $r['discipline'];
        } else {
            return array();
        }
    } else {
        return array();
    }
}

$codespdef = array(
    '',
    'NA',
    'ECA',
    'PA',
    'A'
);

$ceintures = array(
    '',
    'BLA',
    'JAU',
    'ORA',
    'VER',
    'BLE',
    'MAR',
    'NOI'
);

// Fonction retournant le libelle correspondant au niveau d'acquisition sur une competence
function donnelib($id, $classe, $mode)
{
    global $prefix, $link;
    if ($mode == 1) {
        $libs = array(
            "" => "...",
            "BLA" => "Blanche",
            "JAU" => "Jaune",
            "ORA" => "Orange",
            "VER" => "Verte",
            "BLE" => "Bleue",
            "MAR" => "Marron",
            "NOI" => "Noire"
        );
        return $libs[$id];
    } elseif ($mode == 0) {
        $result  = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
        $r       = mysqli_fetch_array($result);
        $lstlibs = $r['libelles'];
        $leslibs = explode("|", $lstlibs);
        $libs    = array(
            "" => "...",
            "NA" => $leslibs[0],
            "ECA" => $leslibs[1],
            "PA" => $leslibs[2],
            "A" => $leslibs[3],
            "ATT" => "?",
            "OUI" => "OUI",
            "NON" => "NON"
        );
        return $libs[$id];
    }
}

// Fonction retournant la description correspondant au niveau d'acquisition sur une competence
function donnedesc($id, $classe)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
    $r        = mysqli_fetch_array($result);
    $lstdescs = $r['descriptions'];
    $lesdescs = explode("|", $lstdescs);
    $descs    = array(
        "" => "",
        "NA" => $lesdescs[0],
        "ECA" => $lesdescs[1],
        "PA" => $lesdescs[2],
        "A" => $lesdescs[3]
    );
    return $descs[$id];
}

// Fonction retournant le pourcentage de points acquis sur une competence
function donnenota($id, $classe)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
    $r        = mysqli_fetch_array($result);
    $lstnotes = $r['notations'];
    $lesnotes = explode("|", $lstnotes);
    $notes    = array(
        "" => 0,
        "NA" => $lesnotes[0],
        "ECA" => $lesnotes[1],
        "PA" => $lesnotes[2],
        "A" => $lesnotes[3]
    );
    return $notes[$id];
}

// Fonction retournant un état d'acquisition en fonction des points donnés sur une competence
function donneacqui($classe, $note, $max)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
    $r        = mysqli_fetch_array($result);
    $lstnotes = $r['notations'];
    $lesnotes = explode("|", $lstnotes);
    if (($note == "") OR ($note == "ABS") OR ($note == "NN")) {
        return "";
    }
    if ($note < $lesnotes[1] * $max / 100) {
        return "NA";
    } else if ($note < $lesnotes[2] * $max / 100) {
        return "ECA";
    } else if ($note < $lesnotes[3] * $max / 100) {
        return "PA";
    } else {
        return "A";
    }
}

// Fonction retournant le pourcentage de points acquis sur une competence
function donnenote($tab, $classe, $autoeval)
{
    if ($autoeval) {
        $nmin = 1;
    } else {
        $nmin = 0;
    }
    for ($n = sizeof($tab) - 1; $n >= $nmin; $n--) {
        if ($tab[$n] <> '') {
            return donnenota($tab[$n], $classe);
        }
    }
    return 0;
}

// Fonction retournant la dernière note
function donnedernote($tab, $autoeval)
{
    if ($autoeval) {
        $nmin = 1;
    } else {
        $nmin = 0;
    }
    for ($n = sizeof($tab) - 1; $n >= $nmin; $n--) {
        if ($tab[$n] <> '') {
            return $tab[$n];
        }
    }
    return 0;
}

// Fonction transformant les codes en images
function enimage($code, $classe, $mode)
{
    global $prefix, $link;
    if ($mode == 1) {
        $image = "../contents/images/Ceintures/$code.png";
        if (file_exists($image)) {
            return "<img alt='$code' src='$image' />";
        } else {
            return donnelib($code, $classe, $code);
        }
    } else {
        $result  = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
        $r       = mysqli_fetch_array($result);
        $packact = $r['icones'];
        if ($code !== "") {
            $image = "../contents/images/Icones/$packact/$code.png";
        } else {
            $image = "../contents/images/Icones/$packact/NE.png";
        }
        if (file_exists($image)) {
            return "<img alt='$code' src='$image' />";
        } else {
            return donnelib($code, $classe, $code);
        }
    }
}

// Fonction testant la validation d'une competence
function estvalide($tab, $autoeval)
{
    $ptscomp = array(
        "" => 0,
        "NA" => 0,
        "ECA" => 1,
        "PA" => 2,
        "A" => 3
    );
    if ($autoeval) {
        unset($tab[0]);
        $tab = array_values($tab);
    }
    $tabevals = array();
    for ($n = 0; $n < sizeof($tab); $n++) {
        if ($tab[$n] !== '') {
            $tabevals[] = $tab[$n];
        }
    }
    if (sizeof($tabevals) >= 1) {
        if ($tabevals[sizeof($tabevals) - 1] == "A") {
            return "OUI";
        } else {
            return "NON";
        }
    } else {
        return "ATT";
    }
    /* $score = 0;
    $total = 0;
    for ( $n = sizeof($tabevals) -1; $n >= 0; $n-- ){
    $score = $score + ($n+1)*$ptscomp[$tabevals[$n]];
    $total = $total + ($n+1);
    }
    $score = $score / $total;
    $scoreval = (sizeof($tabevals)*3) / $total;
    if ( sizeof($tabevals) >= 1 ) {
    if ( $score >= $scoreval ){
    return "OUI";
    } else {
    return "NON";
    }	
    } else {
    return "ATT";
    }*/
}

// Fonction recuperant les informations relatives à une classe donnee
function infocl($cl)
{
    global $prefix, $link;
    $result = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$cl'");
    return mysqli_fetch_array($result);
}

function effectifcl($cl)
{
    global $prefix, $link;
    $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$cl'");
    return $result->num_rows;
}

// Fonction recuperant les informations relatives à un chapitre donne
function infoch($ch)
{
    global $prefix, $niveau, $discipline, $link;
    $result  = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id = '$ch'");
    $r       = mysqli_fetch_array($result);
    $arrcomp = explode(',', $r['competences']);
    for ($k = 0; $k < sizeof($arrcomp); $k++) {
        $arrcomp[$k] = $niveau . $discipline . $arrcomp[$k];
    }
    $barcomp = explode(',', $r['baremes']);
    if (sizeof($arrcomp) > sizeof($barcomp)) {
        for ($i = sizeof($barcomp); $i < sizeof($arrcomp); $i++) {
            $barcomp[] = 0;
        }
    } else if (sizeof($arrcomp) < sizeof($barcomp)) {
        for ($i = sizeof($barcomp) - 1; $i >= sizeof($arrcomp); $i--) {
            unset($barcomp[$i]);
        }
    }
    $bareme      = array_combine($arrcomp, $barcomp);
    $lstcomp     = implode(",", $arrcomp);
    $competences = "'" . str_replace(",", "','", $lstcomp) . "'";
    return array(
        'id' => $r['id'],
        'nom' => $r['nom'],
        'competences' => $competences,
        'baremes' => $bareme,
        'nbevaluations' => $r['nbevaluations'],
        'autoevaluation' => $r['autoevaluation'],
        'mode' => $r['mode'],
        'trimestre' => $r['trimestre'],
        'date' => $r['date']
    );
}

// Fonction envoyant une liste de baremes correcte
function corrigebaremes($competences, $baremes)
{
    global $discipline, $niveau;
    $arrcomp = explode(',', $competences);
    for ($k = 0; $k < sizeof($arrcomp); $k++) {
        $arrcomp[$k] = $niveau . $discipline . $arrcomp[$k];
    }
    if ($baremes != "") {
        $barcomp = explode(',', $baremes);
    } else {
        $barcomp = array();
    }
    if (sizeof($arrcomp) > sizeof($barcomp)) {
        for ($i = sizeof($barcomp); $i < sizeof($arrcomp); $i++) {
            $barcomp[] = 0;
        }
    } else if (sizeof($arrcomp) < sizeof($barcomp)) {
        for ($i = sizeof($barcomp) - 1; $i >= sizeof($arrcomp); $i--) {
            unset($barcomp[$i]);
        }
    }
    return implode(",", $barcomp);
}

// Fonction recuperant les informations relatives à un eleve donne
function infoel($el)
{
    global $prefix, $link;
    $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE id = '$el'");
    return mysqli_fetch_array($result);
}

// Fonction recuperant la derniere evaluation dans le tableau
function recupdereval($tab)
{
    for ($i = sizeof($tab) - 1; $i >= 0; $i--) {
        if ($tab[$i] !== "") {
            return $tab[$i];
        }
    }
    return "";
}

// Fonction cherchant les evaluations d'un chapitre dans le tableau
function recupevalch($chapitre, $eleve)
{
    global $prefix, $link;
    $result = $link->query("SELECT * FROM " . $prefix . "evaluations WHERE chapitre = '$chapitre' AND eleve = $eleve");
    if ($result->num_rows > 0) {
        $r                       = mysqli_fetch_array($result);
        $tabevaluations          = $r["evaluations"];
        $tablevalutation         = explode("|", $tabevaluations);
        $levaluation['idcps']    = $tablevalutation[0];
        $levaluation['lesevals'] = $tablevalutation[1];
        $lesevals                = array_combine(explode(',', $levaluation['idcps']), explode(',', $levaluation['lesevals']));
        $absent                  = $r["absent"];
        $nonnote                 = $r["nonnote"];
    } else {
        $lesevals = -1;
        $absent   = 0;
        $nonnote  = 0;
    }
    return array(
        $lesevals,
        $absent,
        $nonnote
    );
}

// Fonction déterminant les couleurs de ceinture valides pour une compétence
function recupceintures($chapitre, $competence)
{
    global $prefix, $link, $ceintures;
    $result   = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id = '$chapitre'");
    $infoch   = mysqli_fetch_array($result);
    $arrceint = explode('&', $infoch['ceintures']);
    $lstceint = $ceintures;
    foreach ($arrceint as $ceint) {
        $tabceint = explode('|', $ceint);
        $idcomp   = $tabceint[0];
        if ((!empty($idcomp)) && ($idcomp == $competence)) {
            for ($i = 1; $i < sizeof($ceintures); $i++) {
                if (empty($tabceint[$i])) {
                    unset($lstceint[$i]);
                }
            }
            if (sizeof($lstceint) <= 1) {
                return $ceintures;
            } else {
                return array_values($lstceint);
            }
        }
    }
    return $ceintures;
}

// Fonction faisant le bilan des évaluations d'un chapitre
function bilanauto($eleve, $chapitre)
{
    $recupeval = recupevalch($chapitre, $eleve);
    $tab       = $recupeval[0];
    $notes     = array(
        "" => 0,
        "NA" => 0,
        "ECA" => 0.3,
        "PA" => 0.7,
        "A" => 1
    );
    $count     = 0;
    $notebilan = 0;
    if ($tab !== -1) {
        foreach ($tab as $t) {
            $t         = explode("-", $t);
            $count     = $count + 1;
            $derev     = recupdereval($t);
            $notebilan = $notebilan + $notes[$derev];
        }
        $notebilan = $notebilan / $count;
        if ($notebilan < 0.2) {
            return "NA";
        } elseif ($notebilan <= 0.5) {
            return "ECA";
        } elseif ($notebilan < 0.8) {
            return "PA";
        } else {
            return "A";
        }
    }
}

// Fonction retournant la valeur par défaut du bilan : celle déjà entrée si elle existe, celle automatique sinon.
function bilanprof($eleve, $chapitre)
{
    $recupeval      = recupevalch($chapitre, $eleve);
    $tabevaluations = $recupeval[0];
    $n              = 0;
    $levaluation    = array();
    $bilanprof      = "";
    $trouvech       = false;
    while (($n < sizeof($tabevaluations) - 1) && ($trouvech !== true)) {
        $lstlevalutation = $tabevaluations[$n];
        $tablevalutation = explode("|", $tabevaluations[$n]);
        if ($tablevalutation[0] == $chapitre) {
            $bilanprof = $tablevalutation[3];
            $trouvech  = true;
        }
        $n++;
    }
    return $bilanprof;
}

function bilanch($eleve, $chapitre)
{
    $bilanprof = bilanprof($eleve, $chapitre);
    if ($bilanprof == "") {
        return bilanauto($eleve, $chapitre);
    } else {
        return $bilanprof;
    }
}

// Fonction retournant la note obtenue par un eleve sur un chapitre donne
function noteeleve($classe, $discipline, $chapitre, $eleve)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
    $infocl   = mysqli_fetch_array($result);
    $niveau   = $infocl['niveau'];
    $result   = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id = '$chapitre'");
    $infoch   = mysqli_fetch_array($result);
    $autoeval = $infoch['autoevaluation'];
    $modech   = $infoch['mode'];
    $arrcomp  = explode(',', $infoch['competences']);
    for ($k = 0; $k < sizeof($arrcomp); $k++) {
        $arrcomp[$k] = $niveau . $discipline . $arrcomp[$k];
    }
    $tabbar = explode(',', $infoch['baremes']);
    for ($i = 0; $i < min(sizeof($arrcomp), sizeof($tabbar)); $i++) {
        $bareme[$arrcomp[$i]] = $tabbar[$i];
    }
    //$bareme = array_combine($arrcomp, explode(',',$infoch['baremes']));
    $result    = $link->query("SELECT * FROM " . $prefix . "eleves WHERE id = $eleve AND idclasse='$classe'");
    $recupeval = recupevalch($chapitre, $eleve);
    $notes     = $recupeval[0];
    $absent    = $recupeval[1];
    $nonnote   = $recupeval[2];
    if ($notes !== -1) {
        if ($absent == 1) {
            return "ABS";
        }
        if ($nonnote == 1) {
            return "NN";
        }
        $idcps   = array_keys($notes);
        $notetot = 0;
        for ($k = 0; $k < sizeof($idcps); $k++) {
            $lanote = explode("-", $notes[$idcps[$k]]);
            if ($modech == 2) {
                $notetot = $notetot + donnedernote($lanote, $autoeval);
            } else {
                $notetot = $notetot + round(donnenote($lanote, $classe, $autoeval) * $bareme[$idcps[$k]] / 100, 2);
            }
        }
        return $notetot;
    } else {
        return "NE";
    }
}

// Fonction retournant les notes obtenues par une classe sur un chapitre donne
function notesclasse($classe, $discipline, $chapitre)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
    $lesnotes = array();
    $k        = 0;
    while ($el = mysqli_fetch_array($result)) {
        $lesnotes[$el['id']] = noteeleve($classe, $discipline, $chapitre, $el['id']);
        $k++;
    }
    return $lesnotes;
}
// Fonction retournant toutes les notes obtenues par un élève
function noteseleve($classe, $discipline, $eleve)
{
    global $prefix, $link;
    $result   = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id LIKE '$classe%' ORDER BY id");
    $lesnotes = array();
    $k        = 0;
    while ($ch = mysqli_fetch_array($result)) {
        $lesnotes[$el['id']] = noteeleve($classe, $discipline, $ch, $eleve);
        $k++;
    }
    return $lesnotes;
}

// Fonction calculant la moyenne des valeurs comprises dans un tableau
function moyenne($tab)
{
    global $prefix;
    $sum = 0;
    $nb  = 0;
    foreach ($tab as $k => $v) {
        if (($v !== "ABS") && ($v !== "NN") && ($v !== "NE") && ($v !== "")) {
            $sum += $v;
            $nb += 1;
        }
    }
    if ($nb !== 0) {
        return round($sum / $nb, 1);
    } else {
        return "";
    }
}


// Fonction calculant la médiane des valeurs comprises dans un tableau
function mediane($tab)
{
    global $prefix;
    foreach ($tab as $k => $v) {
        if (($v == "ABS") OR ($v == "NN") OR ($v == "NE") OR ($v == "")) {
            unset($tab[$k]);
        }
    }
    $nb = sizeof($tab);
    sort($tab);
    if ($nb > 0) {
        if ($nb % 2 == 0) {
            return (($tab[$nb / 2 - 1] + $tab[$nb / 2]) / 2);
        } else {
            return $tab[($nb - 1) / 2];
        }
    } else {
        return "";
    }
}

// Fonction calculant Q1 des valeurs comprises dans un tableau
/* function q1( $tab ){
global $prefix;
foreach ( $tab as $k => $v ) {
if ( ($v=="ABS") OR ($v=="NN") OR ($v=="NE") OR ($v=="") ) {
unset($tab[$k]);
}
}
$nb=sizeof($tab);
sort($tab);
return($tab[ceil($nb*0.25)-1]);
}*/

// Fonction calculant Q3 des valeurs comprises dans un tableau
/*function q3( $tab ){
global $prefix;
foreach ( $tab as $k => $v ) {
if ( ($v=="ABS") OR ($v=="NN") OR ($v=="NE") OR ($v=="") ) {
unset($tab[$k]);
}
}
$nb=sizeof($tab);
sort($tab);
return($tab[ceil($nb*0.75)-1]);
}*/

function effectif($tab)
{
    global $prefix;
    foreach ($tab as $k => $v) {
        if (($v == "ABS") OR ($v == "NE") OR ($v == "")) {
            unset($tab[$k]);
        }
    }
    return sizeof($tab);
}

// Fonction affichant une date SQL
function affdate($date)
{
    return $date;
    //$tabdate = explode("-",$date);
    //return $tabdate[2]."/".$tabdate[1]."/".$tabdate[0];
}

// Fonction affichant une date SQL (sans l'année)
function affdatemini($date)
{
    $tabdate = explode("-", $date);
    return $tabdate[2] . "/" . $tabdate[1];
}

// Fonction transformant une date au format SQL
function envdate($date)
{
    return $date;
    //tabdate = explode("/",$date);
    //return $tabdate[2]."-".$tabdate[1]."-".$tabdate[0];
}

// Fonction pour afficher le selecteur de classe
function selectniveau($lien)
{
    global $prefix, $link, $niveau;
    echo "<label style='display: inline-block'>Niveau : <select name='selclasse' tabindex='1' onchange='location.href=$lien'>";
    echo "<option value=''>...</option>";
    $result = $link->query("SELECT * FROM " . $prefix . "niveaux ORDER BY id DESC");
    if ($result->num_rows == 1) {
        $r         = mysqli_fetch_array($result);
        $idniveau  = $r['id'];
        $nomniveau = $r['nom'];
        echo "<option value='$idniveau' selected>$nomniveau</option>";
    } else {
        while ($r = mysqli_fetch_array($result)) {
            $idniveau  = $r['id'];
            $nomniveau = $r['nom'];
            if ($idniveau == $niveau) {
                echo "<option value='$idniveau' selected>$nomniveau</option>";
            } else {
                echo "<option value='$idniveau'>$nomniveau</option>";
            }
        }
    }
    echo "</select></label> ";
}


// Fonction pour afficher le selecteur de classe
function selectclasse($lien, $bool = false)
{
    global $prefix, $link, $classe, $discipline, $chapitre, $eleve;
    $clprof      = classesprof();
    $lstclprof   = implode(",", $clprof);
    $classesprof = "'" . str_replace(",", "','", $lstclprof) . "'";
    echo "<label style='display: inline-block'>Classe : <select name='selclasse' tabindex='1' onchange='location.href=$lien'>";
    echo "<option value=''>...</option>";
    if ((estadmin()) OR ($bool)) {
        $result = $link->query("SELECT * FROM " . $prefix . "classes ORDER BY niveau DESC, id ASC");
    } else {
        $result = $link->query("SELECT * FROM " . $prefix . "classes WHERE id in ($classesprof) ORDER BY niveau DESC, id ASC");
    }
    if ($result->num_rows == 1) {
        $r         = mysqli_fetch_array($result);
        $idclasse  = $r['id'];
        $nomclasse = $r['nom'];
        echo "<option value='$idclasse' selected>$nomclasse</option>";
    } else {
        while ($r = mysqli_fetch_array($result)) {
            $idclasse  = $r['id'];
            $nomclasse = $r['nom'];
            if ($idclasse == $classe) {
                echo "<option value='$idclasse' selected>$nomclasse</option>";
            } else {
                echo "<option value='$idclasse'>$nomclasse</option>";
            }
        }
    }
    echo "</select></label> ";
}

// Fonction pour afficher le selecteur de discipline
function selectdiscipline($lien, $bool = false)
{
    global $prefix, $link, $classe, $discipline, $chapitre, $eleve;
    if ((estadmin()) OR (!estprof()) OR ($bool)) {
        echo "<label style='display: inline-block'>Discipline : <select name='seldiscipline' tabindex='2' onchange='location.href=$lien'>";
        echo "<option value=''>...</option>";
        $result = $link->query("SELECT * FROM " . $prefix . "disciplines WHERE active = 1 ORDER BY nom ASC");
        if ($result->num_rows == 1) {
            $r             = mysqli_fetch_array($result);
            $iddiscipline  = $r['id'];
            $discipline    = $iddiscipline;
            $nomdiscipline = $r['nom'];
            echo "<option value='$iddiscipline' selected>$nomdiscipline</option>";
        } else {
            while ($r = mysqli_fetch_array($result)) {
                $iddiscipline  = $r['id'];
                $nomdiscipline = $r['nom'];
                if ($iddiscipline == $discipline) {
                    echo "<option value='$iddiscipline' selected>$nomdiscipline</option>";
                } else {
                    echo "<option value='$iddiscipline'>$nomdiscipline</option>";
                }
            }
            
        }
        echo "</select></label> ";
    } else {
        $result        = $link->query("SELECT * FROM " . $prefix . "disciplines WHERE id = '$discipline'");
        $r             = mysqli_fetch_array($result);
        $nomdiscipline = $r['nom'];
        echo " Discipline : <select name='seldiscipline'><option value='$discipline'>$nomdiscipline</option></select> ";
    }
}

// Fonction pour afficher le selecteur de chapitre
function selectchapitre($lien)
{
    global $prefix, $link, $classe, $discipline, $chapitre, $eleve;
    echo "<label style='display: inline-block'>Chapitre : <select name='selchapitre' tabindex='3' onchange='location.href=$lien'>";
    echo "<option value=''>...</option>";
    if (!empty($discipline) && !empty($classe)) {
        $result = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id LIKE '$classe$discipline%' ORDER BY id ASC");
        if ($result->num_rows == 1) {
            $r           = mysqli_fetch_array($result);
            $idchapitre  = $r['id'];
            $nomchapitre = $r['nom'];
            echo "<option value='$idchapitre' selected>$numchapitre : $nomchapitre</option>";
        } else {
            while ($r = mysqli_fetch_array($result)) {
                $idchapitre  = $r['id'];
                $nomchapitre = $r['nom'];
                $numchapitre = substr($idchapitre, strlen($classe) + strlen($discipline));
                if ($idchapitre == $chapitre) {
                    echo "<option value='$idchapitre' selected>$numchapitre : $nomchapitre</option>";
                } else {
                    echo "<option value='$idchapitre'>$numchapitre : $nomchapitre</option>";
                }
            }
        }
    }
    echo "</select></label> ";
}

// Fonction pour afficher le selecteur d'eleve
function selecteleve($lien)
{
    global $prefix, $link, $classe, $discipline, $chapitre, $eleve, $competence;
    echo "<label style='display: inline-block'>Eleve : <select name='seleleve' tabindex='4' onchange='location.href=$lien'>";
    echo "<option value=''>...</option>";
    if (!empty($classe)) {
        $tabideleves = array();
        $iel         = 0;
        $result      = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
        while ($r = mysqli_fetch_array($result)) {
            $ideleve           = $r['id'];
            $nomeleve          = $r['nom'];
            $prenomeleve       = $r['prenom'];
            $tabideleves[$iel] = $r['id'];
            if ($ideleve == $eleve) {
                echo "<option value='$ideleve' selected>$nomeleve $prenomeleve</option>";
            } else {
                echo "<option value='$ideleve'>$nomeleve $prenomeleve</option>";
            }
            $iel++;
        }
    }
    echo "</select></label> ";
    if (!empty($classe)) {
        if (!empty($eleve)) {
            $indexidel = array_search($eleve, $tabideleves);
            if ($indexidel - 1 >= 0) {
                $indexpvel = $tabideleves[$indexidel - 1];
            }
            $indexnxel = $tabideleves[$indexidel + 1];
            if (!empty($indexpvel)) {
                $indexpvlk = str_replace("\" + this.value", "$indexpvel\"", $lien);
                echo " <input type='button' value='<' onclick='location.href=$indexpvlk' /> ";
            }
            if (!empty($indexnxel)) {
                $indexnxlk = str_replace("\" + this.value", "$indexnxel\"", $lien);
                echo "<input type='button' value='>' onclick='location.href=$indexnxlk' /> ";
            }
        }
    }
}

// Fonction pour afficher le selecteur de competence
function selectcompetence($lien)
{
    global $prefix, $link, $classe, $discipline, $chapitre, $eleve, $competence;
    echo "Compétence : <select name='selcompetence' onchange='location.href=$lien'>";
    echo "<option value=''>...</option>";
    $infocl = infocl($classe);
    $niveau = $infocl['niveau'];
    $infoch = infoch($chapitre);
    $compch = $infoch['competences'];
    $result = $link->query("SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC");
    while ($r = mysqli_fetch_array($result)) {
        $idcompetence    = $r['id'];
        $nomcompetence   = stripslashes($r['nom']);
        $soclecompetence = $r['socle'];
        if ($idcompetence == $competence) {
            echo "<option value='$idcompetence' selected>" . substr($idcompetence, strlen($niveau) + strlen($discipline)) . " : $nomcompetence";
            if (!empty($soclecompetence)) {
                echo " [" . $soclecompetence . "]";
            }
            echo "</option>";
        } else {
            echo "<option value='$idcompetence'>" . substr($idcompetence, strlen($niveau) + strlen($discipline)) . " : $nomcompetence";
            if (!empty($soclecompetence)) {
                echo " [" . $soclecompetence . "]";
            }
            echo "</option>";
        }
    }
    echo "</select> ";
}

// Fonction pour verifier la classe selectionnee
function verifniveau($niveau)
{
    global $prefix, $link;
    if (empty($niveau)) {
        return "";
    } elseif (estprof()) {
        return $niveau;
    } else {
        return "";
    }
}

// Fonction pour verifier la classe selectionnee
function verifclasse($classe)
{
    global $prefix, $link;
    if (empty($classe)) {
        return "";
    } elseif (estadmin()) {
        return $classe;
    } elseif (estprof()) {
        if (in_array($classe, classesprof())) {
            return $classe;
        } else {
            return "";
        }
    } else {
        return $classe;
    }
}

// Fonction pour verifier la discipline selectionnee
function verifdiscipline($discipline)
{
    global $prefix, $link;
    if (estprof()) {
        if (empty($discipline)) {
            return disciplineprof();
        } elseif (estadmin()) {
            return $discipline;
        } else {
            return disciplineprof();
        }
    } else {
        if (empty($discipline)) {
            return "";
        } else {
            return $discipline;
        }
    }
}

// Fonction pour verifier le chapitre selectionne
function verifchapitre($chapitre, $classe, $discipline)
{
    global $prefix, $link;
    if (empty($chapitre)) {
        return "";
    } elseif (preg_match('/' . $classe . $discipline . '\w+/i', $chapitre)) {
        return $chapitre;
    } else {
        return "";
    }
}

// Fonction pour verifier la competence selectionnee
function verifcompetence($competence, $chapitre, $classe)
{
    global $prefix, $link;
    $infocl = infocl($classe);
    $niveau = $infocl['niveau'];
    $infoch = infoch($chapitre);
    $compch = $infoch['competences'];
    if (empty($competence)) {
        return "";
    } else {
        if (in_array("'" . $competence . "'", explode(",", $compch))) {
            return $competence;
        } else {
            return "";
        }
    }
}

// Fonction pour vérifier l'eleve selectionne
function verifeleve($eleve, $classe)
{
    global $prefix, $link;
    if (empty($eleve)) {
        return "";
    } else {
        $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE id = '$eleve'");
        $r      = mysqli_fetch_array($result);
        if ($r['idclasse'] == $classe) {
            return $eleve;
        } else {
            return "";
        }
    }
}


// Fonction sauvegardant la base de donnees dans un fichier .sql
function gensql()
{
    global $prefix,$dbhost, $dbuser, $dbpass, $dbname;
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($db->connect_errno) {
	    echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$prefix = str_replace("_","\_",$prefix);
    $sql = "SHOW TABLES FROM " . $dbname;
    $sql = 'SELECT table_name FROM information_schema.tables WHERE table_type = "base table" AND table_schema="'.$dbname.'" AND table_name LIKE "'.$prefix.'%"';
    $tables = $db->query($sql) or die(mysqli_error($db) . $sql);
    print_r($tables);
    $insertions = "--\n";
    $insertions .= "-- Sauvegarde automatique de la BDD\n";
    $insertions .= "-- Generee le " . date("d/m/Y") . " ‡ " . date("H:m") . "\n";
    $insertions .= "-- Base de donnees: `" . $dbname . "`\n";
    $insertions .= "--\n";
    while ($donnees = mysqli_fetch_array($tables)) {
        $insertions .= "\n-- --------------------------------------------------------\n";
        $table = $donnees[0];
        $insertions .= "\n--\n";
        $insertions .= "-- Structure de la table `" . $table . "`\n";
        $insertions .= "--\n\n";
        $sql = 'SHOW CREATE TABLE ' . $table;
        $res = $db->query($sql) or die(mysqli_error($db) . $sql);
        if ($res) {
            $backup_file = '../contents/backup/' . $table . '.sql.gz';
            $fp          = gzopen($backup_file, 'w');
            $tableau     = mysqli_fetch_array($res);
            $tableau[1] .= ";\n";
            $insertions .= str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $tableau[1]);
            $insertions .= "\n--\n";
            $insertions .= "-- Contenu de la table `" . $table . "`\n";
            $insertions .= "--\n\n";
            $req_table = $db->query('SELECT * FROM ' . $table) or die(mysqli_error($db) . $sql);
            $nbr_champs = mysqli_num_fields($req_table);
            while ($ligne = mysqli_fetch_array($req_table)) {
                $insertions .= 'INSERT INTO ' . $table . ' VALUES (';
                for ($i = 0; $i < $nbr_champs; $i++) {
                    $insertions .= '\'' . $db->real_escape_string($ligne[$i]) . '\', ';
                }
                $insertions = substr($insertions, 0, -2);
                $insertions .= ");\n";
            }
        }
        mysqli_free_result($res);
    }
    return $insertions;
}
// Fonction listant les sous-dossiers
function listedossiers($dir)
{
    $dir      = "../contents/" . $dir;
    $dir_list = array();
    if ($objs = glob($dir . "/*")) {
        foreach ($objs as $obj) {
            if (is_dir($obj)) {
                $dir_list[] = $obj;
            }
        }
    }
    return $dir_list;
}
// Fonction permettant d'acceder à un fichier distant
function getcontents($src, $step = 0)
{
    if ($step > 3)
        return false;
    // Try curl to read remote file
    if (function_exists('curl_init')) {
        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_URL, $src);
        @curl_setopt($ch, CURLOPT_HEADER, 0);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        @curl_setopt($ch, CURLOPT_USERAGENT, 'osCSS-2 Net Install');
        $content = @curl_exec($ch);
        @curl_close($ch);
        if ($content !== false) {
            return $content;
        }
    }
    // Try file_get_contents to read remote file
    if ((boolean) ini_get('allow_url_fopen')) {
        $content = @file_get_contents($src);
        if ($content !== false) {
            return $content;
        }
    }
    // Try fsockopen to read remote file
    $src  = parse_url($src);
    $host = $src['host'];
    $path = $src['path'];
    if (($s = @fsockopen($host, 80, $errno, $errstr, 5)) === false) {
        return false;
    }
    fwrite($s, 'GET ' . $path . " HTTP/1.0\r\n" . 'Host: ' . $host . "\r\n" . "User-Agent: osCSS-2 Svn upadte \r\n" . "Accept: text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*\r\n" . "\r\n");
    $i          = 0;
    $in_content = false;
    while (!feof($s)) {
        $line = fgets($s, 4096);
        if (rtrim($line, "\r\n") == '' && !$in_content) {
            $in_content = true;
            $i++;
            continue;
        }
        if ($i == 0) {
            if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', rtrim($line, "\r\n"), $m)) {
                fclose($s);
                return false;
            }
            $status = (integer) $m[2];
            if ($status < 200 || $status >= 400) {
                fclose($s);
                return false;
            }
        }
        if (!$in_content) {
            if (preg_match('/Location:\s+?(.+)$/', rtrim($line, "\r\n"), $m)) {
                fclose($s);
                return $this->fetchRemote(trim($m[1]), $dest, $step + 1);
            }
            $i++;
            continue;
        }
        $content .= $line;
        $i++;
    }
    fclose($s);
    return $content;
}
function getSslPage($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function modeav()
{
    global $av;
    return $av == 1;
}
?>