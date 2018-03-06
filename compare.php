<?php

//autocitation
//citation des précédents
//proximité au corpus
//clef	valeur [exemple]

ini_set('memory_limit', '512M');

$global = array();

$patterns1 = parse($argv[1], $global);
$file1 = basename($argv[1], ".csv");
$total1 = array_sum($patterns1);

$patterns2 = parse($argv[2], $global);
$file2 = basename($argv[2], ".csv");
$total2 = array_sum($patterns2);
echo "Motif\tOccurrences $file1\tFréquence $file1\tOccurrences $file2\tFréquence $file2\tFréquence $file1-Fréquence $file2\t(Fréquence $file1-Fréquence $file2)/(Fréquence $file1+Fréquence $file2)\n";

foreach ($global as $pattern => $value) {
    $patterns1[$pattern] = isset($patterns1[$pattern]) ? $patterns1[$pattern] : 0;
    $patterns2[$pattern] = isset($patterns2[$pattern]) ? $patterns2[$pattern] : 0;
    $freq1 = round(($patterns1[$pattern] / $total1)*100, 2);
    $freq2 = round(($patterns2[$pattern] / $total2)*100, 2);
    $bias = ($freq1+$freq2) > 0 ? round((($freq1-$freq2)/($freq1+$freq2))*100, 2) : 0;
    //bias =  ( myfreq - franfreq ) / (myfreq + franfreq);
    echo
    $pattern
    ."\t"
    .$patterns1[$pattern]
    ."\t"
    .$freq1
    ."\t"
    .$patterns2[$pattern]
    ."\t"
    .$freq2
    ."\t"    
    .($freq1-$freq2)
    ."\t"
    .$bias
    ."\n"
    ;
    
}

function parse($file, &$global) {

    $file = trim(file_get_contents($file));
    $file = explode("\n", $file);

    array_shift($file);
     $total = 0;
    $array = array();
    foreach ($file as $line) {
	$fields = explode("\t", $line);
	$array[$fields[0]] = $fields[1];
	//if(isset($fields[2])){$array[$fields[0]]["ex"] = $fields[2];}
	//$total+= $fields[1];
	$global[$fields[0]] = "";
    }
    return $array;
}

//concaténer motif et exemple ?

//pour chaque motif : motif	valeur1	proportion1	valeur2	propotion2	différence (ou division si différent de 0 ?) proportion1/proportion2


?>