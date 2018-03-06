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
$graphs = trim(file_get_contents(">10.csv"));
$graphs = explode("\n", $graphs);
$plays = glob("prose/*.txt");
$count = 0;
// foreach($plays as $play){
//   $count += str_word_count(file_get_contents($play));
// }

$csv = "";
foreach ($graphs as $graph){
  $graph = explode("\t", $graph);
  $graphs_count = 0;
  foreach($plays as $play){
    $graphs_count += substr_count(file_get_contents($play), $graph[0]);
  }
  $csv .= $graph[0]."\t".$graph[1]."\t".$graphs_count."\n";
}
echo $csv;
?>
