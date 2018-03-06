<?php
ini_set('memory_limit', '512M');
$mode = $argv[1];
include("db.php");

//$db = connect();
$db = new PDO('sqlite:patterns.sqlite');
$sql = "select count(distinct play) as play_count, count(".$mode.") as hem_count, count(distinct ".$mode.") as hem_unique_count from patterns order by play_count asc";
$req = $db->query($sql);
$all = $req->fetch(PDO::FETCH_ASSOC);

$sql = "select ".$mode." as graph, count(".$mode.") as graph_count from patterns group by ".$mode." order by graph_count desc";
$req = $db->query($sql);
while($graph = $req->fetch(PDO::FETCH_ASSOC)){
    $all["graphs"][$graph["graph"]] = $graph["graph_count"];
}

$sql = "select author, count(distinct play) as play_count, count(".$mode.") as hem_count, count(distinct ".$mode.") as hem_unique_count from patterns group by author order by play_count desc";
$req = $db->query($sql);
$authors = $req->fetchAll(PDO::FETCH_ASSOC);
$i = 0;
foreach ($authors as $author){
    if($i>18){break;}
    $array[$author["author"]] = $author;
    $sql  = "select ".$mode." as graph, count(".$mode.") as graph_count from patterns where author = '".$author["author"]."' group by ".$mode." order by graph_count desc";
    $req = $db->query($sql);
    while($graph = $req->fetch(PDO::FETCH_ASSOC)){
	$array[$author["author"]]["graphs"][$graph["graph"]] = $graph["graph_count"];
    }
    $i++;
}
$authors = $array;

$sql = "select genre, count(distinct play) as play_count, count(".$mode.") as hem_count, count(distinct ".$mode.") as hem_unique_count from patterns group by genre order by play_count desc";
$req = $db->query($sql);
$genres = $req->fetchAll(PDO::FETCH_ASSOC);
foreach ($genres as $genre){
    $array[$genre["genre"]] = $genre;
    $sql  = "select ".$mode." as graph, count(".$mode.") as graph_count from patterns where genre = '".$author["author"]."' group by ".$mode." order by graph_count desc";
    $req = $db->query($sql);
    while($graph = $req->fetch(PDO::FETCH_ASSOC)){
	$array[$genre["genre"]]["graphs"][$graph["graph"]] = $graph["graph_count"];
    }
}
$genres = $array;

echo "\t";
echo "Corpus\t\t\t";
foreach($authors as $key=>$author){
    echo $key."\t\t\t";
}
echo "\n";

//nombre de pièces
echo "Nombre de pièces";
echo "\t";
echo $all["play_count"];
echo "\t\t\t";
foreach ($authors as $author){
    echo $author["play_count"]."\t\t\t";
}
echo "\n";

//nombre d'hémistiches
echo "Nombres d'hémistiches";
echo "\t";
echo $all["hem_count"];
echo "\t\t\t";
foreach ($authors as $author){
    echo $author["hem_count"]."\t\t\t";
}
echo "\n";

//nombre d'hémistiches dédoublonnés
echo "Nombre d'hémistiches dédoublonnés";
echo "\t";
echo $all["hem_unique_count"];
echo "\t\t\t";
foreach ($authors as $author){
    echo $author["hem_unique_count"]."\t\t\t";
}
echo "\n";

//taux (problème)
echo "Taux d'auto-citation";
echo "\t";
echo round((($all["hem_count"]-$all["hem_unique_count"])/$all["hem_count"])*1000, 2);
echo "\t\t\t";
foreach ($authors as $key=>$author){
    echo round((($authors[$key]["hem_count"]-$authors[$key]["hem_unique_count"])/$authors[$key]["hem_count"])*1000, 2)."\t\t\t";
}

echo "\n";

//hémistiches originaux par rapport au corpus
echo "Nombre d'hémistiches originaux";
echo "\t\t\t\t";
    //print_r($all["graphs"]);

foreach($authors as $author=>$graphs){
        //print_r( $graphs["graphs"]);
    $sql = "select ".$mode." as graph from patterns where author != '".$author."'";
    $req = $db->query($sql);
    $corpus = $req->fetchAll(PDO::FETCH_COLUMN,0);
    $corpus = array_count_values($corpus);
    $reused = array_intersect_key($corpus, $graphs["graphs"]);
  
    $originals = count($graphs["graphs"])-count($reused);
    
    $authors[$author]["originals"] = $originals;
    echo $originals."\t\t\t";
}
echo "\n";
//taux d'hémistiches originaux
echo "Taux d'hémistiches originaux";
echo "\t\t\t\t";
foreach($authors as $author=>$graphs){
    echo round(($graphs["originals"]/$graphs["hem_count"])*100,2)."\t\t\t";
}
echo "\n";
//longueur du texte


echo "\t";
echo "Occurrences\tFréquence\tSpécificité";
foreach($authors as $key=>$author){
    echo "\tOccurrences\tFréquence\tSpécificité";
}
echo "\n";

foreach($all["graphs"] as $graph=>$count){
    $frequence = round(($count/$all["hem_count"])*1000,2);
    echo $graph;
    echo "\t";
    echo $count;
    echo "\t";
    echo $frequence;
    echo "\t";
    echo 0;
    echo "\t";
    foreach($authors as $author=>$graphs){
	if(isset($graphs["graphs"][$graph])){
	    $frequence_author = round(($graphs["graphs"][$graph]/$graphs["hem_count"])*1000,2);
	    echo $graphs["graphs"][$graph];
	    echo "\t";
	    echo $frequence_author;
	    echo "\t";
	    echo $frequence_author-$frequence;
	    echo "\t";
	}else{
	    echo 0;
	    echo "\t";
	    echo 0;
	    echo "\t";
	    echo 0-$frequence;
	    echo "\t";
	}
	
    }
    //count prose
    //frequence prose
    //count sur l
    //frequence sur l
    //first occ
    $sql = 'select author, title, act_n, scene_n, line_n, line, min(created) as created from patterns where line != "" and '.$mode.' = "'.$graph.'"';
    //echo $sql;
    //continue;

    $req = $db->query($sql);
    //var_dump($req);
    $occ = $req->fetch(PDO::FETCH_ASSOC);
    //continue;
    echo $occ["author"];
    echo "\t";
    echo $occ["title"];
    echo "\t";
    echo $occ["created"];
    echo "\t";
    echo $occ["act_n"];
    echo "\t";
    echo $occ["scene_n"];    
    echo "\t";
    echo $occ["line_n"];    
    echo "\t";
    echo $occ["line"];
    //last occ
    $sql = 'select author, title, act_n, scene_n, line_n, line, max(created) as created from patterns where  line != "" and '.$mode.'  = "'.$graph.'"';
    $req = $db->query($sql);
    $occ = $req->fetch(PDO::FETCH_ASSOC);
    echo "\t";
    echo $occ["author"];
    echo "\t";
    echo $occ["title"];
    echo "\t";
    echo $occ["created"];
    echo "\t";
    echo $occ["act_n"];
    echo "\t";
    echo $occ["scene_n"];    
    echo "\t";
    echo $occ["line_n"];    
    echo "\t";
    echo $occ["line"];
    
//    if($mode == "graph"){
//	echo "\t";
//	//echo substr($graph,0,1);
//	$first_car = substr($graph,0,1);
//	if(in_array($first_car, array("a","e","i","o","u","y")) or $first_car == "é" or $first_car == "à" or $first_car == "ê"){
//	    echo 1;
//	}else{
//	    echo 0;
//	}
//    }
    echo "\n";
}
return;


?>