<?php
//line start to lower : prend un fichier txt, met en minuscule les car en début de ligne sauf après une ponctuation forte, pour les noms de la castList et les noms propres figurant ailleurs
$file = basename($argv[1], ".txt");
$text = file_get_contents($argv[1]);
$text = preg_replace("/(\s*\n)|\n+/u","\n",$text);//espaces + lb, multiples lb > lb
$text = preg_replace("/\n[\s ]*/u","\n",$text);//lb + espaces  > lb
$text = preg_replace_callback('/([^.]\n)([A-Z])/u',
                           function ($word) {return $word[1].strtolower($word[2]);},
                           $text);
preg_match_all("/([^.?!…] )([A-Z][^ \n.,;:?!…]*)/", $text, $names);
$names_lower = array_map('strtolower', $names[2]);
$text = str_replace($names_lower,$names[2],$text);
$text = str_replace("\n", " ", $text);//tt réagit aux \n ?
echo $text;
//file_put_contents($file."_lower.txt", $text);
?>