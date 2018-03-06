<?php
ini_set('memory_limit', '512M');
//error_reporting(0);
include ("db.php");
include ("functions.php");
$db = connect();
$sql = "DELETE FROM 'lines'";
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
    $lines = $xp->evaluate("//tei:l[count(tei:seg)=2]");
    echo $genre;
    foreach ($lines as $line) {
	$data = array(
	    $play,
	    $author,
	    $title,
	    $genre,
	    $date,
	);
	$data[] = $line_n = $line->getAttribute("n");
	$act_n = $xp->evaluate("./ancestor::*[@type='act'][@n]", $line);
	$data[] = $act_n = ($act_n->length > 0) ? $act_n->item(0)->getAttribute("n") : "";
	$scene_n = $xp->evaluate("./ancestor::*[@type='scene'][@n]", $line);
	$data[] = $scene_n = ($scene_n->length > 0) ? $scene_n->item(0)->getAttribute("n") : "";
	$data[] = $line_id = $line->getAttribute("xml:id");
	//$line_id = $line_id->item(0)->getAttribute("xml:id");

	$patterns[$i] = "";
	$words = $line->getElementsByTagName("w");
	foreach ($words as $word) {
	    $patterns[$i].= $word->getAttribute("tag") . " ";
	}
	$data[] = clean($line->textContent);
	$data[] = $patterns[$i];	
	$data[] = $line->textContent;
	$data[] = $line->textContent." (".$author.", ".$title.", ".roman($act_n).", ".$scene_n.", v. ".$line_n.")";
	$i++;
	
	//print_r($data);
	//continue;
	//$data[] = strlen($hemistich->textContent);
	$sql = "INSERT INTO lines (
	    play,
	    author,
	    title,
	    genre,
	    created,
	    line_n,
	    act_n,
	    scene_n,
	    line_id,
	    graph,
	    cat,
	    cat_full,
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
	    ?
	)
	";
	insert($sql, $db, $data);
    }
}
//à partir des motis de la base, compter le nombre d'occurrence dans la prose et les txt
?>