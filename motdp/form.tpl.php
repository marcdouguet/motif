<html>
    <head>
        <meta charset='UTF-8'/>
    </head>
    <body>
        <div class="center">
            <h3>Visualisation de la dynamique des échanges</h3>
            <p>Extension de <a href="https://github.com/oeuvres/Teinte">Teinte</a>, pack de transformation pour XML-TEI</p>
            <p>Veuillez sélectionner la pièce à visualiser :</p>
            <form method="post">
                    <select name="file">
                        <?php foreach ($files as $file){
                            $key = $file;
                            $file = basename($file, ".xml");?>
                        <option value="<?php echo "../".$key; ?>"><?php echo $file; ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="post"/>
                    <input type="submit" value="Valider" />
            </form>
        </div>
    </body>
</html>