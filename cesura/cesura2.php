<?php

//error_reporting(0);
$text = file_get_contents("test.txt");

//eh
//
//$results = array_values(array_unique($results[0]));
//
//print_r($results);
//return;
//$text = mb_strtolower($text);
$verses = explode("\n", $text);
$l = 0;
foreach($verses as $verse){
    if(strpos($verse, "encore") or strpos($verse, "nuptiale") or strpos($verse, "haine")){continue;}
    $results = array();
    //preg_match_all("#((?<![q])[u]['’aeiouæàâäéèêëîïœôöùûü]*)|((?<![g])[u]['’aeioæàâäéèêëîïœôöùûü]*)|((?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü]))|([aioæàâäéèêëîïœôöùûü][aiouæàâäéèêëîïœôöùûü]*)|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|(e(?!([ ,.:;!\?\n]*([\nhaeiouæàâäéèêëîïœôöùûü]|$)|(s[ ,.:;!\?\n]*$)|(nt[ ,.:;!\?\n]*$))))#", $verse, $results);
    preg_match_all("#eau|oeu|oeil|oui|aie|eui|oue|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ié|iè|iê|io|iu|ui|uei|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([ ,.:;!\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[ ,.:;!\?\n]*$)|(nt[ ,.:;!\?\n]*$)))#ui",$verse,$results);
    $count =  count($results[0]);
        //echo $count." ";
    if($count > 12 and $count >10){
        print_r($results[0]);
        $l++;
        echo $count." ";
        echo $verse."\n";
        }
}
    echo $l;
return;
$i = 0;
$l = 0;
$errors = "";

//vers
while ($i < count($verses)) {

    //$verse = strrev($verses[$i]);
    $characters = preg_split('//u', $verses[$i], -1, PREG_SPLIT_NO_EMPTY);
    $j = count($characters) - 1;
    $k = 0;
    $syllabes = 0;
    $phoneme[] = 0;

    //lettres
    while ($j > - 1) {
        $character = $characters[$j];

        //si cons
        if (type($character) == 2 and $phoneme[$k] != 2) {
            
            if (($k == 0 and $character == "s") or ($k == 0 and $character == "t" and $characters[$j-1] == "n" and $characters[$j-2] != "e") or ($k == 0 and $character == "n" and $characters[$j+1] == "t")) {
            } else {
                $k++;
                $phoneme[$k] = 2;
            }
        }

        //space
        elseif ($k != 0 and type($character) == 0 and $phoneme[$k] != 0) {
            $k++;
            $phoneme[$k] = 0;
        }

        //voy
        elseif (type($character) == 1 and ($phoneme[$k] != 1)) {
            
            if ($character == "u" and $characters[$j - 1] == "q") {
            } else {
                $k++;
                $phoneme[$k] = 1;
                $characters[$j] = strtoupper($character);
                $syllabes++;
            }
        }

        //e
        elseif (type($character) == 3) {
            
            if ($k != 0 and (($phoneme[$k] == 2) or ($phoneme[$k] == 0 and $phoneme[$k - 1] == 2))) {
                $k++;
                $phoneme[$k] = 1;
                $characters[$j] = strtoupper($character);
                $syllabes++;
            }
        } elseif (type($character) == 6) {
            
            if ($phoneme[$k] == 1) {
                $k++;
                $phoneme[$k] = 2;
            }
        }
        //hiatus
        elseif (type($character) == 7 and $phoneme) {
            $k++;
            $phoneme[$k] = 7;
            $characters[$j] = strtoupper($character);
            $syllabes++;
        }
        //y
        elseif (type($character) == 4){
            //avant voyelle : compte comme une consonne
            if($phoneme[$k]==1){
                $k++;
                $phoneme[$k] = 2;
            }else{
                $k++;
                $phoneme[$k] = 1;
                $characters[$j] = strtoupper($character);
                $syllabes++;
            }
        }
        echo "j=" . $j . " k=" . $k . " " . $character . " " . $phoneme[$k] . " " . $syllabes . "\n";
        $j--;
    }

    //echo "\n";
    
    if ($syllabes != 12 and $syllabes != 0 and $syllabes > 9) {
        echo $syllabes . "\n";
        echo implode($characters) . "\n";
        $errors.= $verses[$i] . "\n";
        $l++;
    }

    //echo $verses[$i];
    $i++;

    //echo "\n\n";
    
}
echo $l;
file_put_contents("test.txt", $errors);

function type($char) {

    $voy = array(
        "à",
        "a",
        "è",
        "i",
        "î",
        "o",
        "u",
        "ù",
        "A",
        "À",
        "È",
        "I",
        "Î",
        "O",
        "U",
        "Ù",
    );
    $hiatus = array(
        "é",
        "ê",
        "ô",
        "â",
        "û",
        "ï",
        "ö",
        "ë",
        "ä",
        "É",
        "Ê",
        "Ô",
        "Â",
        "Û",
    );
    $cons = array(
        "b",
        "c",
        "ç",
        "d",
        "f",
        "g",
        "j",
        "k",
        "l",
        "m",
        "n",
        "p",
        "q",
        "r",
        "s",
        "t",
        "v",
        "w",
        "x",
        "z",
        "B",
        "C",
        "Ç",
        "D",
        "F",
        "G",
        "J",
        "K",
        "L",
        "M",
        "N",
        "P",
        "Q",
        "R",
        "S",
        "T",
        "V",
        "W",
        "X",
        "Z",
    );
    $space = array(
        " ",
        ".",
        ",",
        ".",
        ";",
        "?",
        "!",
    );
    $separator = array(
        "-",
        "'",
        "’",
    );
    
    if (in_array($char, $voy)) {
        return 1;
    } elseif (in_array($char, $cons)) {
        return 2;
    } elseif (in_array($char, $space)) {
        return 0;
    } elseif (in_array($char, $separator)) {
        return 5;
    } elseif (strtolower($char) == "e") {
        return 3;
    } elseif (strtolower($char) == "y") {
        return 4;
    } elseif (strtolower($char) == "h") {
        return 6;
    } elseif (in_array($char, $hiatus)) {
        return 7;
    }
}

//h : par défaut cons, le remplacer par un joker voy

//voy : par défaut lié, insére un joker cons en cas de diérèse


//problème : s final


?>