<?php

function get_results($text) {


    //$text = preg_replace("/\n[0-9]+/","\n",$text);//intervalles ajoutés dans la sortie web de tt
    $text = str_replace("’", "'", $text);
    $text = explode("\n", $text);
    $array = array();
    foreach ($text as $line) {
	$line = explode("\t", $line);
	
	if (count($line) > 2) {
	    $array[] = $line;
	}
    }
    return $array;
}

function get_context($array, $offset) {

    $i = - 5;
    $context = "";
    while ($i < 0) {
	
	if (!isset($array[$offset + $i])) {
	    $i++;
	    continue;
	}
	$context.= " " . $array[$offset + $i][0];

	//$context .= "a ";
	$i++;
    }
    $context.= " <b>" . $array[$offset][0] . "</b>";
    $i++;
    while ($i < 6) {
	
	if (!isset($array[$offset + $i])) {
	    break;
	}
	$context.= " " . $array[$offset + $i][0];
	$i++;
    }
    return $context;
}

function pos_convert($pos, $tagger) {

    $pos = preg_replace("/:[a-zA-Z]+$/", "", $pos);
    $pos = preg_replace("/[a-z]+/", "", $pos);
    $pos = str_replace(array(
	"KON",
	"INT",
	"NOM",
	"PRP",
	"SENT",
	"ABR",
	"VERB",
	"NAME"
    ) , array(
	"CONJ",
	"EXCL",
	"SUB",
	"PREP",
	"PUN",
	"SUB",
	"VER",
	"NAM"
    ) , $pos);
    
    if ($tagger == "Presto") {
	$pos = preg_replace(array(
	    "/^N$/",
	    "/^V$/",
	    "/^A$/",
	    "/^P$/",
	    "/^D$/",
	    "/^R$/",
	    "/^C$/",
	    "/^M$/",
	    "/^S$/",
	    "/^I$/",
	    "/^F$/"
	) , array(
	    "SUB",
	    "VER",
	    "ADJ",
	    "PRO",
	    "DET",
	    "ADV",
	    "CONJ",
	    "NUM",
	    "PREP",
	    "EXCL",
	    "PUN"
	) , $pos);
    }
    return $pos;
}

function line($al, $tt, &$a, &$t, &$i, &$tag_div, $tagger1, $tagger2) {

    $pos_al = pos_convert($al[$a][AL], $tagger1);
    $pos_tt = pos_convert($tt[$t][TT], $tagger2);
    $context = get_context($al, $a);
    
    if ($pos_al != $pos_tt) {
	$error = "class = 'error'";
	$error_type = "Étiquetage";
	$tag_div++;
    } else {
	$error = $error_type = "";
    }
    return "<tr $error><td>$pos_tt</td><td>$context</td><td>" . $al[$a][0] . "</td><td>" . $al[$a][AL] . "</td><td>" . $tt[$t][TT] . "</td><td>" . $tt[$t][0] . "</td><td>$error_type</td></tr>";
}

function tok_line($al, $tt, &$a, &$t, &$i, &$tok_div) {

    $context = get_context($al, $a);
    $tok_div++;
    return "<tr class='error'><td>$i</td><td>$context</td><td>" . $al[$a][0] . "</td><td>" . $al[$a][AL] . "</td><td>" . $tt[$t][TT] . "</td><td>" . $tt[$t][0] . "</td><td>Tokenisation</td></tr>";
}

function define_tagger($post, $constant) {

    
    switch ($post) {
	case "Alix":
	    $value = 2;
	break;
	default:
	    $value = 1;
    }
    define($constant, $value);
}
?>