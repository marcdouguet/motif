<!DOCTYPE html>
<html>
    <head>
      <title>Part of speech tagger comparator</title>
      <meta charset="UTF-8"/>
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <form method="post">
            Alix : <input type="file" name="al"/>
            Treetagger : <input type="file" name="tt"/>
            <input type="submit">
        </form>

<?php
//part of speech tagger comparator
if(isset($_POST["tt"]) && isset($_POST["al"])){
    $tt = $_POST["tt"];
    $al = $_POST["al"];
?>
        <table>
            <thead>
                <tr>
                    <td>Token Alix</td>
                    <td>Catégorie Alix</td>
                    <td>Catégorie Treetagger</td>
                    <td>Token Treetagger</td>
                </tr>
            </thead>
            <tbody>
<?php
    include("postaco.php");
   
?>        
                
            </tbody>
        </table>
        <div>
        <p><?php echo $tok_div; ?> divergences de tokenisation (<?php echo round(($tok_div/$count_al)*100, 2);?>%)</p>
        <p><?php echo $tag_div; ?> divergences d'étiquettage (<?php echo round(($tag_div/$count_al)*100, 2);?>%)</p>
        </div>
        <?php  }?>
    </body>
</html>
