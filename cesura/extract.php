<?php

$files = glob($argv[1]);
$i = 0;
$patterns = array();
$strings = array();
foreach($files as $file){
    $text = new DOMDocument();
    $text->preserveWhiteSpace = false;
    $text->load($file);
    $hemistichs = $text->getElementsByTagName("seg");
    foreach($hemistichs as $hemistich){
	$patterns[$i] = "";
	$words = $hemistich->getElementsByTagName("w");
	foreach($words as $word){
	    $patterns[$i] .= $word->getAttribute("tag")." ";
	}
	$strings[$patterns[$i]][] = $hemistich->textContent;
	$i++;
    }
}
$patterns = array_count_values($patterns);
asort($patterns);
//print_r($strings);
foreach($patterns as $pattern => $count){
    echo $pattern."\n";
    print_r($strings[$pattern]);
}
?>