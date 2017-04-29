<?php

//line start to lower : prend un fichier txt, met en minuscule les car en début de ligne sauf après une ponctuation forte, pour les noms de la castList et les noms propres figurant ailleurs
$files = glob($argv[1]);
foreach ($files as $file) {
    $file_name = basename($file);
    $names = array();
    $text = file_get_contents($file);
    $text = preg_replace("/(\s*\n)|\n+/u", "\n", $text); //espaces + lb, multiples lb > lb

    $text = preg_replace("/\n[\s ]*/u", "\n", $text); //lb + espaces  > lb

    $text = preg_replace_callback('/([^.]\n)([A-Z])/u', 
    function ($word) {

	return $word[1] . mb_strtolower($word[2], "UTF-8");
    }
    , $text);
    preg_match_all("/([^.?!\(\)…] )([A-Z][^ \n.,;:?!…]*)/u", $text, $names);
    //$names_lower = array_map('strtolower', $names[2]);
    $array = array();
    
    foreach($names[2] as $name){
	$name = str_replace(array("(",")","[","]","/"), "", $name);
	$array[] = "/(?=\n)".mb_strtolower($name,"UTF-8")."\b/u";
    }

    $text = preg_replace($array, $names[2], $text);
    $text = str_replace("\n", " ", $text); //tt réagit aux \n ?

    file_put_contents("../tcpi/" . $file_name, $text);
}

//revoir str_replace : exact match ?

?>