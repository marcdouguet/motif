<?php

//mode = apprentissage :

//xml : vers entier ou pas


//error_reporting(0);


//récupérer le dico des diérèses
$texts = glob($argv[1]);
$learn = $argv[2];

if (!$learn) {
    $diereses = file_get_contents("dierese.csv");
    $diereses = explode("\n", $diereses);
    foreach ($diereses as $dierese) {
        $dierese = explode("\t", $dierese);
        $search[] = $dierese[0];
        $replace[] = $dierese[1];
    }

    //print_r($replace);
    
    //marquer les diérèses

}

//si un mot possède à la fois h, ent et dié

//majuscules


//mot inclus ds un autre

$csv = array();
   $l = 0;
   foreach ($texts as $text) {
    echo $text."\n";

    $text = file_get_contents($text);
    if(!$learn){
        $text = str_replace($search, $replace, $text);
    }
    $verses = explode("\n", $text);
 
    foreach ($verses as $verse) {
        
        if (strpos($verse, "encore") or strpos($verse, "nuptiale") or strpos($verse, "haine")) {
            continue;
        }
        //oui, oie,ée, uie, iai, aou, oè
        preg_match_all("#eau|oeu|œu|oui|oeil|aie|aou|eui|oue|oie|oè|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iai|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ée|ié|iè|iê|io|iu|ui|uei|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([  ,.«»\)\(\-\":;!|)\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[  ,.:;!«»\)\(\"\?\n]*$)|(nt[  ,.:;«»!\"\)\(\?\n]*$)))#ui", $verse, $matches);
        $count = count($matches[0]);
        //manquions, acquiet
        //-louez-en
        //refaire avec les autres pièces, refaire en concat les vers
        
        //couper le vers ou le prendre en entier si 6
        
        //insérer les balises

        
        //diérèses

        
        if ($count < 12) {
            
              

            preg_match_all("#([^ '’]*(é|i|y|(?<![qg])[u]))([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $diereses, PREG_SET_ORDER);
            preg_match_all("#([^ '’]*[^aeiouæàâéèêîïœôû '’]y)([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $ys, PREG_SET_ORDER);
            preg_match_all("#([^ '’]*e)(nt(?=[ ,.:;!\?\n]*$))#ui", $verse, $ents, PREG_SET_ORDER);
            preg_match_all("#(e[ ,.:;!\?\n]*)(h[^ '’,.]*)#ui", $verse, $hs, PREG_SET_ORDER);

            //print_r($hs);
            
            //mode apprentissage

            
            if (!$learn) {
                continue;
            }
            
            if (count($diereses) + count($ents) + count($ys) + count($hs) == 12 - $count) {
             
                //echo "ent\n";
                //print_r($ents);
                //echo "hs\n";
                //print_r($hs);
                
                foreach ($diereses as $dierese) {
                    $csv[$dierese[1] . "#" . $dierese[3]] = $dierese[0];
                }
                foreach ($ys as $y) {
                    $csv[$y[1] . "#" . $y[2]] = $y[0];
                }
                foreach ($ents as $ent) {
                    $csv[$ent[1] . "#" . $ent[2]] = $ent[0];
                }
                foreach ($hs as $h) {
                    $csv["#" . $h[2]] = $h[2];
                }
                
                
                echo $l." \n".$verse."\n";
                            echo "\n";
            echo $count . " ";
            print_r($matches[0]);
            print_r($diereses);
                                    $l++;
            }elseif(count($diereses) + count($ents) + count($ys) + count($hs) < 12 - $count){
   
  
               
            }
        }
    }
    //print_r($csv);
}
echo $l;
//mode apprentissage

if (!$learn) {
    return;
}
asort($csv);
$csv = array_unique($csv);
$string = "";
foreach ($csv as $key => $line) {
    $string.= "$key\t$line\n";
}
file_put_contents("dierese.csv", trim($string));

//h : par défaut muet à l'initial, le remplacer par un joker cons

//voy : par défaut lié, insére un joker cons en cas de diérèse


?>
