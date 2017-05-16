<?php

//fichier de corpus
$files = glob($argv[1]);
$i = 0;
$patterns = array();
$strings = array();
foreach ($files as $file) {
    $text = new DOMDocument();
    $text->preserveWhiteSpace = false;
    $text->load($file);
    $hemistichs = $text->getElementsByTagName("seg");
    foreach ($hemistichs as $hemistich) {
	$patterns[$i] = "";
	$words = $hemistich->getElementsByTagName("w");
	foreach ($words as $word) {
	    $patterns[$i].= $word->getAttribute("tag") . " ";
	}
	$strings[$patterns[$i]][] = $hemistich->textContent;
	$i++;
    }
}
$patterns = array_count_values($patterns);
arsort($patterns);

//print_r($strings);
foreach ($patterns as $pattern => $count) {
    echo
	$pattern
	. "\t"
	. $count
	. "\t"
	. round((($count / $i) * 100) , 2)
	. "\t"
	. $strings[$pattern][0]
	;
    if (isset($strings[$pattern][1])) {
	echo
	"\t"
	.$strings[$pattern][1]
	;
    }
    echo "\n";

    //."\t".round((($count/$i)*100),2)
    
    //print_r($strings[$pattern]);

    
}
?>