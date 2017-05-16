<?php
$texte = file_get_contents("resultats.csv");
$lignes = explode("\n", $texte);
$i = 0;
$length = 4;
$total = count($lignes) - ($length - 1);
while ($i < $total) {
    $j = 0;
    while ($j < $length) {
	$mot[] = explode("\t", $lignes[$i + $j]);
	$j++;
    }

    $i = $i + 1;
    
    if (count($mot_0) < 3 or count($mot_1) < 3 or count($mot_2) < 3 or count($mot_3) < 3) {
	continue;
    }
    $motif = $mot_0[1] . " " . $mot_1[1] . " " . $mot_2[1] . " " . $mot_3[1];
    $graphies = $mot_0[0] . " " . $mot_1[0] . " " . $mot_2[0] . " " . $mot_3[0];
    $motifs[] = $motif;
    $occurrences[$motif][] = $graphies;
}
$motifs = array_count_values($motifs); //on transforme $motifs : désormais, la clef de chaque élément corespond au motif, et la valeur, à son nombre d'occurrences.

asort($motifs); //et on trie par ordre croissant

foreach ($motifs as $motif => $frequence) {
    print_r($occurrences[$motif]); //on croise l'array $motifs et l'array $occurrences : pour chaque élément de $motifs, on affiche l'élément de $occurrences qui à la même clef

    
}
?>