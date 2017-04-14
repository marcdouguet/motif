<?php

function get_results($file) {

    $text = file_get_contents($file);
    $text = explode("\n", $text);
    $array = array();
    foreach ($text as $line) {
        $array[] = explode("\t", $line);
    }
    return $array;
}

function get_context($array, $offset) {

    $i = -5;
    $context = "";
    while ($i < 0) {
        if(!isset($array[$offset+$i])){
            $i++;
            continue;
        }
        $context.= " " . $array[$offset+$i][0];
        //$context .= "a "; 
        $i++;
    }
    
    $context.= " <b>" . $array[$offset][0] . "</b>";
    $i++;
    while ($i < 5) {
        if(!isset($array[$offset+$i])){
            break;
            }
        $context.= " " . $array[$offset+$i][0];
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
?>