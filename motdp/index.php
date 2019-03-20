<?php
error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE);
set_time_limit(360);
$files = file_get_contents("../../manadram/corpus.txt");
$files = explode("\n", $files);
//$files = glob("../tcp5/*.xml");
include("form.tpl.php");

if(isset($_POST["post"])){
	$file=$_POST["file"];
        $xsl = new DOMDocument();
        $xsl->load("Teinte/tei2html.xsl");
        $inputdom = new DomDocument();
        $inputdom->load($file);
        $proc = new XSLTProcessor();
        $xsl = $proc->importStylesheet($xsl);
        $newdom = $proc->transformToDoc($inputdom);
        $html =$newdom->SaveXML();
        echo $html;
}
?>