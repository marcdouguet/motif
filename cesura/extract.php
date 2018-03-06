<?php
ini_set('memory_limit', '512M');
//error_reporting(0);
include ("db.php");
include ("functions.php");
$db = connect();
// $sql = "SELECT play FROM plays";
// $patterns = mselect($sql, $db);
//
// foreach($patterns as $pattern){
//
//   $text = file_get_contents("../../tcp5t/".$pattern["play"].".txt");
//   $words_count = str_word_count($text);
//   $verses_count = substr_count($text, "\n");
//   echo $pattern["play"]."\t".$words_count."\t".$verses_count."\n";
//   $data = array($words_count, $verses_count);
//   $sql = "UPDATE plays SET words_count = '".$words_count."', verses_count = '".$verses_count."' WHERE play = '".$pattern["play"]."';";
//   insert($sql, $db);
// }
// return;
$sql = "DELETE FROM 'patterns'";
insert($sql, $db);
$files = strpos($argv[1], ".txt") ? explode("\n", file_get_contents($argv[1])) : glob($argv[1]);

//print_r($files);
$i = 0;
$patterns = array();
$strings = array();
foreach ($files as $file) {
    $play = basename($file, ".xml");
    $text = new DOMDocument();
    $text->preserveWhiteSpace = false;
    $text->load($file);
    $xp = new DOMXPath($text);
    $xp->registerNamespace("tei", "http://www.tei-c.org/ns/1.0");
    $author = $xp->evaluate("//tei:titleStmt/tei:author/tei:surname");
    $author = ($author->length > 0) ? $author->item(0)->textContent : "";
    $title = $xp->evaluate("//tei:titleStmt/tei:title[not(@type)]");
    $title = ($title->length > 0) ? $title->item(0)->textContent : "";
//    $lines = $xp->evaluate("//tei:seg");
//    //continue;
//
//    if ($lines->length > 1000) {
//    } else {
//	echo $play;
//
//	print_r($lines);
//    }
//
//    continue;
    $date = $xp->evaluate("//tei:creation/tei:date[@type='created']");
    $date = ($date->length > 0) ? substr($date->item(0)->getAttribute("when") , 0, 4) : "";
    $date = (int)$date;
    $genre = $xp->evaluate("//tei:keywords/tei:term[@type='genre']");
    $genre = ($genre->length > 0) ? $genre->item(0)->textContent : "";
    $genre = str_replace(array("-ballet", " galante"), "", $genre);
    $genre = str_replace(array("Comédie héroïque", "Pastorale"), array("Tragédie", "Tragi-comédie"), $genre);
    $hemistichs = $text->getElementsByTagName("seg");
    echo $genre;
    foreach ($hemistichs as $hemistich) {
	$data = array(
	    $play,
	    $author,
	    $title,
	    $genre,
	    $date,
	);
	$line = $xp->evaluate("./parent::tei:l[@xml:id]", $hemistich);
	$preceding = $xp->evaluate("./preceding-sibling::tei:seg",$hemistich);
	$part = $xp->evaluate("./parent::tei:l", $hemistich)->item(0)->getAttribute("part");
	$data[] = $line_n = ($line->length > 0) ? $line->item(0)->getAttribute("n") : "";
	$act_n = $xp->evaluate("./ancestor::*[@type='act'][@n]", $hemistich);
	$data[] = $act_n = ($act_n->length > 0) ? $act_n->item(0)->getAttribute("n") : "";
	$scene_n = $xp->evaluate("./ancestor::*[@type='scene'][@n]", $hemistich);
	$data[] = $scene_n = ($scene_n->length > 0) ? $scene_n->item(0)->getAttribute("n") : "";
	$data[] = $line_id = ($line->length > 0) ? $line->item(0)->getAttribute("xml:id") : "";
	$data[] = $line = ($line->length > 0) ? $line->item(0)->textContent : "";//$hemistich->textContent
	//$line_id = $line_id->item(0)->getAttribute("xml:id");

	$patterns[$i] = "";
	$words = $hemistich->getElementsByTagName("w");
	foreach ($words as $word) {
	    $patterns[$i].= $word->getAttribute("tag") . " ";
	}

	$data[] = clean($hemistich->textContent);
	$data[] = $patterns[$i];
	$data[] = $hemistich->textContent;
	$data[] = $position = ($preceding->length == 0 or $part=="I") ? 1 : 2;
	$strings[$patterns[$i]][] = $hemistich->textContent;
	$data[] = $line." (".$author.", ".$title.", ".roman($act_n).", ".$scene_n.", v. ".$line_n.")";

	//$data[] = strlen($hemistich->textContent);
	$sql = "INSERT INTO patterns (
	    play,
	    author,
	    title,
	    genre,
	    created,
	    line_n,
	    act_n,
	    scene_n,
	    line_id,
	    line,
	    graph,
	    cat,
	    graph_full,
	    position,
	    graph_ref
	) VALUES (
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?,
	    ?
	)
	";
	insert($sql, $db, $data);
	$i++;
    }
}
//à partir des motis de la base, compter le nombre d'occurrence dans la prose et les txt
?>
