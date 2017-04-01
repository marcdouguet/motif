<?php
$text = file_get_contents("test.txt");
$verses = explode("\n", $text);
$cons = array(
              "a",
              "e",
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
              "û",);
$voy = array(
              "b",
              "c",
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
$iv = 0;
//vers
while($iv<count($verses)){
    $syllabes[] = false;
    $characters = preg_split('//u', $verses[$iv], -1, PREG_SPLIT_NO_EMPTY);
    //$characters = mb_split($verses[$iv], "UTF-8");
    $ic = 1;
    $is = 0;
    //lettres
    while($ic<count($characters)){
        if(in_array($characters[$ic], $cons)){
            $syllabes[$ic] = false;
            
        }else{
            $syllabes[$ic] = true;
        }
        if($syllabes[$ic] and $syllabes[$ic]!= $syllabes[$ic-1]){
            $is++;
        }
        $ic++;
    }
    echo $is."\n";
    print_r($characters);
    $iv++;
}
?>