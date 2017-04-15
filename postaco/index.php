<!DOCTYPE html>
<html>
    <head>
      <title>Part of speech tagger comparator</title>
      <meta charset="UTF-8"/>
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="js/TableFilter/dist/tablefilter/style/tablefilter.css">
        <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="js/TableFilter/dist/tablefilter/tablefilter.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </head>
    <body>


<?php
if(isset($_POST["tt"]) && isset($_POST["al"])){
    $tt = $_POST["tt"];
    $al = $_POST["al"];
?>
        <div><table id="results">
            <thead>
                <tr>
                    <td>N°</td>
                    <td>Token <?php echo $_POST["tagger1"];?></td>
                    <td>Catégorie <?php echo $_POST["tagger1"];?></td>
                    <td>Catégorie <?php echo $_POST["tagger2"];?></td>
                    <td>Token <?php echo $_POST["tagger2"];?></td>
                    <td>Contexte</td>
                    <td>Divergence</td>
                </tr>
            </thead>
            <tbody>
<?php
    include("postaco.php");
?>        
                
            </tbody>
        </table>
            </div>
        <div>
        <p><?php echo $tok_div; ?> divergences de tokenisation (<?php echo round(($tok_div/$i)*100, 2);?>%)</p>
        <p><?php echo $tag_div; ?> divergences d'étiquetage (<?php echo round(($tag_div/$i)*100, 2);?>%)</p>
        </div>
        <?php  }else{?>
        <form method="post">
            <div class="form">
                <div class="input">
                    <select name="tagger1">
                        <option value="Alix" selected>Alix</option>
                        <option value="Treetagger">Treetagger</option>
                    </select>
                    <textarea name="al"><?php echo file_get_contents("al.txt");?></textarea>
                </div>
                <div class="input">
                    <select name="tagger2">
                        <option value="Alix">Alix</option>
                        <option value="Treetagger" selected>Treetagger</option>
                    </select>
                    <textarea name="tt"><?php echo file_get_contents("tt.txt");?></textarea>
                </div>   
            </div>
            <input type="submit">
        </form>
        <?php } ?>
    </body>
</html>
