<?php

//transforme les xml en txt en ne gardant que les morceaux en alexandrins
$files = glob($argv[1]);
print_r($files);
foreach ($files as $file) {
    $file_name = basename($file, ".xml");
    

    //toison, andromede
    $dom = new DOMDocument();
    $dom->load($file);
    $xp = new DOMXPath($dom);
    $xp->registerNamespace("tei", "http://www.tei-c.org/ns/1.0");
    $lines = $xp->evaluate("//tei:body//tei:sp[not(ancestor::tei:spGrp)]/tei:l");

    //stances
    
    //parties chantées

    
    //concat l@part

    $string = "";
    foreach ($lines as $line) {
        
        if ($line->getAttribute("part") == "I" or $line->getAttribute("part") == "M") {
            $string.= $line->textContent." ";
        } else {
            $string.= $line->textContent . "\n";
        }
    }
    $string = trim($string);
    file_put_contents("../tcpt/" . $file_name . ".txt", $string);
}
?>
