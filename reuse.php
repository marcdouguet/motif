<?php

//où calculer le taux de réemprunt ? la diversité des motifs ? calculer la diversité par rapport au sous-corpus (Corneille se cite lui-même) ou au corpus moins le sous-corpus (Corneille cite les autres) ou au deux (Corneille a recours à des éléments formulaire) ?
//comment définier le corpus ? même auteur ? fenêtre d'année ? pièces antérieures ? 

    $hemistichs_n = count($results);
    $results = array_count_values($results);
    asort($results);
    $hemistichs_n_unique = count($results);
    
    if (!$debug) {
	print_r($results);
	echo "Hémistiches : $hemistichs_n\n";
	echo "Hémistiches dédoublonnés : $hemistichs_n_unique\n";
	echo "Pourcentage de réemploi : " . round((($hemistichs_n - $hemistichs_n_unique) / $hemistichs_n) * 100, 2) . "\n";
    }
    //écrire les trois chiffres dans un csv -r ?
    
?>