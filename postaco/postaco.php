<?php
include ("functions.php");

//$ttpos = get_pos("pos_tt.txt");

//$alpos = get_pos("pos_al.txt");

$al = get_results($al);
$tt = get_results($tt);
$count_al = count($al);
$count_tt = count($tt);
$count = ($count_al < $count_tt) ? $count_al : $count_tt;
$i = 0;
$t = 0;
$a = 0;
$tag_div = 0;
$tok_div = 0;
while ($a < $count) {
    
    if ($al[$a][0] == $tt[$t][0]) {
        $pos_al = pos_convert($al[$a][2]);
        $pos_tt = pos_convert($tt[$t][1]);
        
        if ($pos_al != $pos_tt) {
            $error = "class = 'error'";
            $tag_div++;
        } else {
            $error = "";
        }
        echo "<tr $error><td>" . $al[$a][0] . "</td><td>" . $al[$a][2] . "</td><td>" . $tt[$t][1] . "</td><td>" . $tt[$t][0] . "</td></tr>";
        $a++;
        $t++;
        $i++;
    } elseif (strlen($al[$a][0]) > strlen($tt[$t][0])) {
        $tok_div++;
        echo "<tr class='error'><td>" . $al[$a][0] . "</td><td>" . $al[$a][2] . "</td><td>" . $tt[$t][1] . "</td><td>" . $tt[$t][0] . "</td></tr>";

        //al contient plusieurs token de tt : on saute les tokens de tt jusqu'au prochain token commun
        $a++;
        $t++;
        while ($t < $count_tt) {
            
            if ($al[$a][0] == $tt[$t][0]) {
                $pos_tt = pos_convert($tt[$t][1]);
                $pos_al = pos_convert($al[$a][2]);
                
                if ($pos_al != $pos_tt) {
                    $error = "class = 'error'";
                    $tag_div++;
                } else {
                    $error = "";
                }
                echo "<tr $error><td>" . $al[$a][0] . "</td><td>" . $al[$a][2] . "</td><td>" . $tt[$t][1] . "</td><td>" . $tt[$t][0] . "</td></tr>";
                $a++;
                $t++;
                $i++;
                break;
            } else {
                echo "<tr class='error'><td></td><td></td><td>" . $tt[$t][1] . "</td><td>" . $tt[$t][0] . "</td></tr>";
                $t++;
                continue;
            }
        }
    } else {
        $a++;
        $t++;
        $i++;
    }
}
?>