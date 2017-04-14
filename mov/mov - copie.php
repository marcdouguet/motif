<?php
$text = file_get_contents("test.txt");

//$text = mb_strtolower($text);
$verses = explode("\n", $text);
$i = 0;

//vers
while ($i < count($verses)) {
    $characters = preg_split('//u', $verses[$i], -1, PREG_SPLIT_NO_EMPTY);
    $j = 0;
    $k = 0;
    $syllabes = 0;
    $phoneme[] = 2;

    //lettres
    while ($j < count($characters)) {
        //si cons
        if (type($characters[$j]) == 2 and $phoneme[$k] != 2) {
            $k++;
            $phoneme[$k] = 2;
            
            if (($phoneme[$k - 1] == 0 and $phoneme[$k - 2] == 3) or ($phoneme[$k - 1] == 3 and $phoneme[$k-2] == 2) or ($phoneme[$k - 1] == 3 and $j==2)) {
                $syllabes++;
            }
        }
        //space
        elseif (type($characters[$j]) == 0 and $phoneme[$k] != 0) {
            $k++;
            $phoneme[$k] = 0;
        }
        //voy
        elseif (type($characters[$j]) == 1 and ($phoneme[$k] != 1 and $phoneme[$k] != 3)) {
            $k++;
            $phoneme[$k] = 1;
            $syllabes++;
        }
        //e
        elseif (type($characters[$j]) == 3) {
            $k++;
            $phoneme[$k] = 3;
        }
        echo $characters[$j]." ".$phoneme[$k]." ".$syllabes."\n";
        $j++;
    }

    //echo "\n";
    
    echo $syllabes . "\n";
    if($syllabes != 12){echo $verses[$i]."\n";}

    
    //echo $verses[$i];

    $i++;
}

function type($char) {

    $voy = array(
        "a",
        "i",
        "o",
        "u",
        "y",
        "é",
        "è",
        "ê",
        "ô",
        "à",
        "â",
        "ù",
        "û",
    );
    $cons = array(
        "b",
        "c",
        "ç",
        "d",
        "f",
        "g",
        "h",
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
    } elseif ($char == "y") {
        return 4;
    }
}
?>