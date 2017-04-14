<?php
function get_results($file){
    
    $text = file_get_contents($file);
    $text = explode("\n", $text);
    $array = array();
    foreach($text as $line){
        $array[] = explode("\t", $line);
        
    }
    return $array;
    
}
function pos_convert($pos){
    $pos = preg_replace("/:[a-zA-Z]+$/", "",$pos);
    $pos = preg_replace("/[a-z]+/", "", $pos);
    $pos = str_replace(array("KON","INT","NOM","PRP","SENT","ABR", "VERB", "NAME"),array("CONJ", "EXCL", "SUB", "PREP", "PUN", "SUB", "VER", "NAM"),$pos);
    return $pos;
}

?>