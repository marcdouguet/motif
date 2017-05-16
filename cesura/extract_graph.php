<?php

//fichier de corpus

//évolution

$files = glob($argv[1]);
$i = 0;
$patterns = array();
foreach ($files as $file) {
    $filename = basename($file, ".xml");
    $author = substr($filename, 0, strpos($filename, "_"));
    $text = new DOMDocument();
    $text->preserveWhiteSpace = false;
    $text->load($file);
    $hemistichs = $text->getElementsByTagName("seg");
    foreach ($hemistichs as $hemistich) {
	$patterns_author[$filename][] = clean($hemistich->textContent);
	$patterns[] = clean($hemistich->textContent);
    }
}



$patterns = array_count_values($patterns);
arsort($patterns);

//foreach ($patterns as $pattern => $count) {
//    echo $pattern . "\t" . $count."\n";
//}
//return;
//afc
$array = array();

foreach($patterns_author as $key => $author){


    $array[$key] = array_count_values($author);


}

$patterns  = array_slice($patterns, 0, 60);
foreach ($patterns as $pattern => $null) {
    echo "\t\"" . $pattern."\"";
}
echo "\n";
foreach ($patterns_author as $play => $null) {
    echo $play;
    foreach ($patterns as $pattern => $null) {
	if(array_key_exists($pattern, $array[$play])){
	    echo "\t1";
	    //.($array[$play][$pattern]+10);
	}else{
	    echo "\t0";
	}
    }
    echo "\n";
}

function clean($string) {

    $string = mb_strtolower($string, "UTF-8");
    $string = str_replace(array(
	".",
	",",
	";",
	":",
	"?",
	"!",
	"…"
    ) , "", $string);
    $string = trim($string);
    return $string;
}
?>