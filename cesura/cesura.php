<?php

//mode = apprentissage :

//xml : vers entier ou pas


//error_reporting(0);


//récupérer le dico des diérèses

$texts = glob($argv[1]);
$learn = $argv[2];
$debug = (isset($argv[3]))? $argv[3] : false;

if (!$learn) {
    $diereses = file_get_contents("dierese.csv");
    $diereses = explode("\n", $diereses);
    foreach ($diereses as $dierese) {
	$dierese = explode("\t", $dierese);
	$search[] = $dierese[1];
	$replace[] = $dierese[0];
    }

    //marquer les diérèses
    
}

//si un mot possède à la fois h, ent et dié

//majuscules


//mot inclus ds un autre

$csv = array();
$total = 0;
$success = 0;
$fail = 0;
foreach ($texts as $text) {
    $text = file_get_contents($text);
    
    if (!$learn) {
	$text = str_replace($search, $replace, $text);
    }
    $verses = explode("\n", $text);
    foreach ($verses as $verse) {
	$total++;
	//oui, oie,ée, uie, iai, aou, oè
	preg_match_all("#eau|oeu|œu|oui|oeil|aie|aou|eui|iai|oue|oie|oè|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ée|ié|iè|iê|io|iu|ui|uei|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([  ,.«»\)\(\-\":;!|)\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[  ,.:;!«»\)\(\"\?\n]*$)|(nt[  ,.:;«»!\"\)\(\?\n]*$)))#ui", $verse, $matches, PREG_OFFSET_CAPTURE);
	$count = count($matches[0]);

	//print_r($matches[0]);
	
	if (($count == 12 or $count == 6) and !$debug) {
	    $success++;
	    $cesura = strpos(substr($verse, $matches[0][5][1], $matches[0][6][1] - $matches[0][5][1]) , " ");
	    
	    if ($cesura) {
		$verse = str_replace("#", "", $verse);
		$hemistiche1 = substr($verse, 0, $matches[0][5][1] + $cesura);
		echo "\n".$hemistiche1 . "\t\t\t";		
		if ($count == 12) {
		    $hemistiche2 = substr($verse, $matches[0][5][1] + $cesura);
    		echo $hemistiche2 . "";
		}
	    }
	}


	
	if (($debug == "inf" and $count < 12) or ($debug == "sup" and $count > 12)) {
	    echo $count . " ";
	    echo $fail . " \n" . $verse . "\n";
	    $fail++;
	    echo "\n";
	    print_r($matches[0]);
	    
	    if (!$learn) {
		continue;
	    }
	    preg_match_all("#([^ '’]*(é|i|y|(?<![qg])[u]))([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $diereses, PREG_SET_ORDER);
	    preg_match_all("#([^ '’]*[^aeiouæàâéèêîïœôû '’]y)([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $ys, PREG_SET_ORDER);
	    preg_match_all("#([^ '’]*e)(nt(?=[ ,.:;!\?\n]*$))#ui", $verse, $ents, PREG_SET_ORDER);
	    preg_match_all("#(e[ ,.:;!\?\n]*)(h[^ '’,.]*)#ui", $verse, $hs, PREG_SET_ORDER);

	    //print_r($hs);
	    
	    //mode apprentissage

	    
	    if (count($diereses) + count($ents) + count($ys) + count($hs) == 12 - $count) {


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
		echo $l . " \n" . $verse . "\n";
		$l++;
		echo "\n";
		echo $count . " ";
		print_r($matches[0]);
		print_r($diereses);
	    } elseif (count($diereses) + count($ents) + count($ys) + count($hs) < 12 - $count) {
	    }
	}
    }

    //print_r($csv);
    
}
echo $success/$total;

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
