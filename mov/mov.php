<?php
//mode = apprentissage :
//xml : vers entier ou pas
//error_reporting(0);
//récupérer le dico des diérèses
$diereses = file_get_contents("dierese.csv");
$diereses = explode("\n", $diereses);
foreach ($diereses as $dierese) {
    $dierese = explode("\t", $dierese);
    print_r($dierese);

    //$search = $dierese[0];
    
    //$replace = $dierese[1];

    
}

//print_r($replace);
return;
//marquer les diérèses
$text = file_get_contents("test.txt");
$text = str_replace($search, $replace, $text);

//ça ne marche pas

//si un mot possède à la fois h, ent et dié

//majuscules
//mot inclus ds un autre

$verses = explode("\n", $text);
$l = 0;
$csv = array();
foreach ($verses as $verse) {
    
    if (strpos($verse, "encore") or strpos($verse, "nuptiale") or strpos($verse, "haine")) {
        continue;
    }
    preg_match_all("#eau|oeu|oeil|oui|aie|eui|oue|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ié|iè|iê|io|iu|ui|uei|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([ ,.:;!\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[ ,.:;!\?\n]*$)|(nt[ ,.:;!\?\n]*$)))#ui", $verse, $matches);
    $count = count($matches[0]);
    //couper le vers ou le prendre en entier si 6
    //insérer les balises
    //diérèses 
    if ($count < 12 and $count > 9) {
        $l++;
        echo "\n";
        echo $count . " ";
        echo $verse;
        print_r($matches[0]);
        preg_match_all("#([^ '’]*(?<![qg])[iu])([aeioæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $diereses, PREG_SET_ORDER);
        preg_match_all("#([^ '’]*e)(nt(?=[ ,.:;!\?\n]*$))#ui", $verse, $ents, PREG_SET_ORDER);
        preg_match_all("#(e[ ,.:;!\?\n]*)(h[^ '’,.]*)#ui", $verse, $hs, PREG_SET_ORDER);
        print_r($hs);
        
        if (count($diereses) + count($ents) + count($hs) == 12 - $count) {
            foreach ($diereses as $dierese) {
                $csv[$dierese[1] . "#" . $dierese[2]] = $dierese[0];
            }
            foreach ($ents as $ent) {
                $csv[$ent[1] . "#" . $ent[2]] = $ent[0];
            }
            foreach ($hs as $h) {
                $csv["#" . $h[2]] = $h[2];
            }
        }
    }
}
//diérèses
$csv = array_unique($csv);
print_r($csv);
$string = "";
foreach ($csv as $key => $line) {
    $string.= "$key\t$line\n";
}
file_put_contents("dierese.csv", trim($string));

//h : par défaut muet à l'initial, le remplacer par un joker cons

//voy : par défaut lié, insére un joker cons en cas de diérèse


?>
