<?php
//longueur des pièces
//récupérer les motifs graph
//chercher les motifs graph dans verse et remplir la table
//chercher les motifs graph dans prose et remplir la table
//récupérer les motifs cat
//chercher les motifs cat dans verse et remplir la table
//chercher les motifs cat dans prose et remplir la table
include("db.php");
include("functions.php");
$db = connect();
$graphs = trim(file_get_contents(">10.csv"));
$graphs = explode("\n", $graphs);
// foreach($plays as $play){
//   $count += str_word_count(file_get_contents($play));
// }

$csv = "";
foreach ($graphs as $graph){
  $csv .= $graph."\t";
  $graph = str_replace("'","''",$graph);
  // $sql = "SELECT graph, count(graph) as graph_count from patterns where graph = 'en l''état où je suis' and position = 1 group by graph";
  $sql = "SELECT graph, count(graph) as graph_count from patterns where graph = '".$graph."' and position = 1 group by graph";
  $count1 = select($sql, $db);
  $sql = "SELECT graph, count(graph) as graph_count from patterns where graph = '".$graph."' and position = 2 group by graph";
  $count2 = select($sql, $db);
  $csv .= $count1["graph_count"]."\t".$count2["graph_count"]."\n";
  }
echo $csv;
?>
