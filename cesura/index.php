<?php

/*
tableau
select avec tous les fichiers du corpus
*/
set_time_limit('120');
ini_set('memory_limit', '512M');
include("functions.php");
include("db.php");
$db = connect();

if(isset($_GET["h"])){
	echo '<!DOCTYPE html>
	<html>
	    <head>
	        <meta charset="UTF-8"/>
	        <title>Hémistiches répétés</title>
	        <link rel="stylesheet" href="style.css"/>
	    </head>

	    <body>
	      <div class="occ-list"><p>Cliquer sur une occurrence pour voir le contexte</p>';

	$occurrences = get_occurrences($_GET["h"], $db);
	echo '<h1>'.$_GET["h"].'</h1>';
	echo '<h2>'.count($occurrences).' occurrences</h2>';
	echo '<ul>';
	foreach($occurrences as $occurrence){
		echo '<li><a target="_blank" href="http://www.theatre-classique.fr/pages/programmes/edition.php?t=../documents/'.strtoupper($occurrence["play"]).'.xml#A'.$occurrence["act_n"].'.S'.$occurrence["act_n"].$occurrence["scene_n"].'">'.$occurrence["author"].', <i>'.$occurrence["title"].'</i> ('.$occurrence["genre"].', '.$occurrence["created"].'), '.roman($occurrence["act_n"]).', '.$occurrence["scene_n"].', v. '.$occurrence["line_n"].' : '.$occurrence["line"].'</a></li>';
	}
	echo '</ul>';
	echo '</div></body></html>';
	return;
}
if(!isset($_POST["text"])){
	include("form.html");
	return;
}
echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>Hémistiches répétés</title>
        <link rel="stylesheet" href="style.css"/>
    </head>

    <body>

      <div class="result"><p>Cliquer sur un hémistiche pour afficher toutes les occurrences</p><br/><br/>';


$diereses = file_get_contents("dierese.csv");
$diereses = explode("\n", $diereses);
foreach ($diereses as $dierese) {
	$dierese = explode("\t", $dierese);
	$search[] = $dierese[1];
	$replace[] = $dierese[0];
}
// $file = "../../tcp5t/corneillep_cinna.txt";
// $text = file_get_contents($file);
$verses = explode("\n", $_POST["text"]);
foreach ($verses as $verse) {
	echo "<p>";
	$verse = preg_replace($search, $replace, $verse);
	$verse = preg_replace("#qu(?=[aeiouyæàâäéèêëîïœôöùûü])#ui", "$", $verse);
	$verse = preg_replace("#gu(?=[aeiouyæàâäéèêëîïœôöùûü])#ui", "@", $verse);
	preg_match_all("#eau|oeu|œu|oui|oei|œi|aie|aou|uie|eui|iai|oue|oie|oè|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ée|ié|iè|iê|io|iu|ui|uei|ue|ïe|ïu|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([  ,.…«»\)\(\-\":;!|)\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[  ,.…:;!«»\)\(\"\?\n]*$)|(nt[  ,.…:;«»!\"\)\(\?\n]*$)))#ui", $verse, $matches, PREG_OFFSET_CAPTURE);
	$count = count($matches[0]);
	if ($count == 12) {
		$cesura = strrpos(substr($verse, $matches[0][5][1], $matches[0][6][1] - $matches[0][5][1]) , " ");
		if ($cesura) {
			$hemistich = substr($verse, 0, $matches[0][5][1] + $cesura);
			echo display($hemistich, $db, 1);
			echo " ";
			$hemistich = substr($verse, $matches[0][5][1] + $cesura + 1);
			echo display($hemistich, $db, 2);
		}
	} elseif ($count == 6) {
		echo display($verse, $db, 1);
	} else{
		echo remove_dieresis($verse, 1);
	}
	echo "</p>";
}

echo '</div></body></html>';

function display($hemistich, $db, $position){
	$hemistich = remove_dieresis($hemistich, $position);

	$count = occurrences($hemistich, $db);
	// $hemistich = str_replace("'", , $subject [, &$replace_count])
	//fffff = 1
	//ff0000 > 20
	//ff888 < 20
	// $count = 255-((255*$count)/119);
	if($count <= 1){
		return "<a>".$hemistich."</a>";

	}
	else {
		$color = 210-((255*$count)/119);
		return '<a target="_blank" href="index.php?h='.$hemistich.'" title="'.$count.' occurrences" style="background-color:rgb(255,'.$color.','.$color.');">'.$hemistich.'</a>';

	}
	// else{
	// 	//2 = 9999
	// 	//3 = 8888
	// 	//0000 > 20
	// 	$count = 10000-min(($count*1000), 10000);
	// 	$count = "ff".sprintf('%04d', $count);;
	// }

}
function occurrences($string, $db){
	$string = clean($string);
	$string = str_replace("'", "''", $string);
	$sql = "SELECT c as count FROM hc WHERE graph = '".$string."'";
	$count = select($sql, $db);
	return $count["count"];
}

function remove_dieresis($string, $position) {


	//nettoie l'hémistiche pour affichage
	$string = str_replace(array(
			"#",
			"@",
			"$"
		) , array(
			"",
			"gu",
			"qu"
		) , $string);
		if($position == 1){
			$string = ucfirst($string);
		}
	return $string;
}
function get_occurrences($h, $db){
	$h = clean($h);
	$h = str_replace("'", "''", $h);
	$sql = "SELECT play, author, title, genre, created, act_n, scene_n, line_n, line FROM patterns where graph = '".$h."'";
	$results = mselect($sql, $db);
	return $results;
}
?>
