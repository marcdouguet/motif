<?php

//line start to lower : prend un fichier txt, met en minuscule les car en début de ligne sauf après une ponctuation forte, pour les noms de la castList et les noms propres figurant ailleurs
$files = glob($argv[1]);
$output = "";
foreach ($files as $file) {
    $names = array();
    $text = file_get_contents($file);
    $text = preg_replace("/(\s*\n)|\n+/u", "\n", $text); //espaces + lb, multiples lb > lb

    $text = preg_replace("/\n[\s ]*/u", "\n", $text); //lb + espaces  > lb

    $text = preg_replace_callback('/([^.]\n)([A-Z])/u', 
    function ($word) {

        return $word[1] . strtolower($word[2]);
    }
    , $text);
    preg_match_all("/([^.?!…] )([A-Z][^ \n.,;:?!…]*)/", $text, $names);
    $names_lower = array_map('strtolower', $names[2]);

    $text = str_replace($names_lower,$names[2],$text);
    $text = str_replace("\n", " ", $text); //tt réagit aux \n ?

    $output .= $text;
}
 echo $output;
//revoir str_replace : exact match ?

?>