<?php

//clef	valeur [exemple]


$global = array();

$patterns1 = parse($argv[1], $global);
$total1 = $patterns1[1];
$patterns1 = $patterns1[0];

$patterns2 = parse($argv[2], $global);
$total2 = $patterns2[1];
$patterns2 = $patterns2[0];

foreach ($global as $pattern => $value) {
    $freq1 = $patterns1[$pattern]["n"] / $total1;
    $freq2 = $patterns2[$pattern]["n"] / $total2;
    echo
    $pattern
    ."\t"
    .$patterns1[$pattern]["n"]
    ."\t"
    .$freq1
    ."\t"
    .$freq1-$freq2
    ."\t"
    ."\t"
    ."\t"
    ."\t"
    ."\t"
    ;
    
}

function parse($file, &$global) {

    $file = file_get_contents($file);
    $file = explode("\n", $file);
    $total = 0;
    $array = array();
    foreach ($file as $line) {
	$fields = explode("\t", $line);
	$array[$fields[0]]["n"] = $fields[1];
	$array[$fields[0]]["ex"] = $fields[2];
	$total+= $fields[1];
	$global[$fields[0]] = "";
    }
    return array(
	$total,
	$array
    );
}

//concaténer motif et exemple ?

//pour chaque motif : motif	valeur1	proportion1	valeur2	propotion2	différence (ou division si différent de 0 ?) proportion1/proportion2


?>