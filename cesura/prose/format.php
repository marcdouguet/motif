<?php
	$files = glob("*.xml");
	foreach($files as $file){
		$filename = basename($file, ".xml");
		$dom = new DOMDocument();
		$dom->load($file);
		$xp = new DOMXPath($dom);
		$sentences = $xp->evaluate("//body//s");
		foreach($sentences as $sentence){
			$text .= $sentence->textContent." ";
		}
		echo $text;
		file_put_contents($filename.".txt", $text);
	}
	?>