<?php

function get_results($file) {


    //$text = file_get_contents($file);
    $text = explode("\n", $file);
    $array = array();
    foreach ($text as $line) {
        $array[] = explode("\t", $line);
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

function pos_convert($pos) {

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
    return $pos;
}

function line($al, $tt, &$a, &$t, &$i, &$tag_div) {

    $pos_al = pos_convert($al[$a][AL]);
    $pos_tt = pos_convert($tt[$t][TT]);
    $context = get_context($al, $a);
    
    if ($pos_al != $pos_tt) {
        $error = "class = 'error'";
        $error_type = "Étiquetage";
        $tag_div++;
    } else {
        $error = $error_type = "";
    }
    return "<tr $error><td>$i</td><td>" . $al[$a][0] . "</td><td>" . $al[$a][AL] . "</td><td>" . $tt[$t][TT] . "</td><td>" . $tt[$t][0] . "</td><td>$context</td><td>$error_type</td></tr>";
}

function tok_line($al, $tt, &$a, &$t, &$i, &$tok_div) {

    $tok_div++;
    return "<tr class='error'><td>$i</td><td>" . $al[$a][0] . "</td><td>" . $al[$a][AL] . "</td><td>" . $tt[$t][TT] . "</td><td>" . $tt[$t][0] . "</td><td></td><td>Tokenisation</td></tr>";
}

function define_tagger($post, $constant) {
    switch($post){
        case "Alix":
            $value = 2;
            break;
        default:
            $value = 1;
    }
    define($constant, $value);
}    
?>