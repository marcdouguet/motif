<?php
include ("functions.php");
$al_type = $_POST["tagger1"];
$tt_type = $_POST["tagger2"];
define_tagger($_POST["tagger1"], "AL");
define_tagger($_POST["tagger2"], "TT");
$al = get_results($al);
$tt = get_results($tt);
$count_al = count($al);
$count_tt = count($tt);
$t = 0;
$a = 0;
$i = 1;
$tag_div = 0;
$tok_div = 0;
while ($a < $count_al and $t < $count_tt) {
    
    if (mb_strtolower($al[$a][0], "UTF-8") == mb_strtolower($tt[$t][0], "UTF-8")) {
        echo line($al, $tt, $a, $t, $i, $tag_div);
        $i++;
        $a++;
        $t++;
    } elseif (strlen($al[$a][0]) > strlen($tt[$t][0])) {
        echo tok_line($al, $tt, $a, $t, $i, $tok_div);
        $a++;
        $t++;
        $i++;
	$local = str_replace(array(" ","’","'","-"),"",$al[$a][0]);
	$loctt = str_replace(array(" ","’","'","-"),"",$tt[$t][0]);
        //al contient plusieurs token de tt : on saute les tokens de tt jusqu'au prochain token commun
        while ($t < $count_tt and $local != $loctt) {

                echo "<tr class='error'><td>$i </td><td></td><td>".$al[$a][0]."</td><td></td><td>" . $tt[$t][TT] . "</td><td>" . $tt[$t][0] . "</td><td>Tokenisation</td></tr>";
                $t++;
                $i++;
		$loctt .= str_replace(array(" ","’","'","-"),"",$tt[$t][0]);
 
        }
    } elseif (strlen($al[$a][0]) < strlen($tt[$t][0])) {
        echo tok_line($al, $tt, $a, $t, $i, $tok_div);
        $a++;
        $t++;
        $i++;
        while ($a < $count_al) {
            
            if (mb_strtolower($al[$a][0], "UTF-8") == mb_strtolower($tt[$t][0], "UTF-8")) {
                echo line($al, $tt, $a, $t, $i, $tag_div);
                $i++;
                $a++;
                $t++;
                break;
            } else {
                echo "<tr class='error'><td>$i</td><td></td><td>" . $al[$a][0] . "</td><td>" . $al[$a][AL] . "</td><td></td><td></td><td>Tokenisation</td></tr>";
                $a++;
                $i++;
                continue;
            }
        }
    } else {
        echo line($al, $tt, $a, $t, $i, $tag_div);
        $i++;
        $a++;
        $t++;
    }
}

//col de gauche : textfield sur lemme, textfield avec autosuggest sur pos, sauver les résultats
//surligner les homographes
//étiquetter de la tei ? sinon, extraire un txt avec / et \ /h1\h2/ ou /\h2/ ou /h1\/ (mais je perds les personnages)

?>