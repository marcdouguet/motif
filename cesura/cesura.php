<?php



ini_set('memory_limit', '512M');

//echo ini_get("memory_limit");

//return;


//récupérer le dico des diérèses
// $files = file_get_contents($argv[1]);
// $files = explode("\n",$files);
$files = glob($argv[1]);
$learn = $argv[2];
$mode = $argv[3];
$debug = isset($argv[4]) ? $argv[4] : false;

if (!$learn) {
	$diereses = file_get_contents("dierese.csv");
	$diereses = explode("\n", $diereses);
	foreach ($diereses as $dierese) {
		$dierese = explode("\t", $dierese);
		$search[] = $dierese[1];
		$replace[] = $dierese[0];
	}
}

//majuscules
$csv = array();

//compter les vers
$total = 0;
$success = 0;
$fail = 0;
$results = array();
foreach ($files as $file) {
	$file_name = basename($file, ".".$mode);

	if ($file_name == "corneillep_agesilas") {
		continue;
	}


	if ($mode == "xml") {
		$text = new DOMDocument();
		$text->load($file);
		$xp = new DOMXPath($text);
		$xp->registerNamespace("tei", "http://www.tei-c.org/ns/1.0");
		$verses = $xp->evaluate("//tei:body//tei:sp[not(ancestor::tei:spGrp)]/tei:l");
	} else {
		$text = file_get_contents($file);
		$verses = explode("\n", $text);
	}
	foreach ($verses as $node) {

		if ($mode == "xml") {
			$verse = $node->textContent;
		} else {
			$verse = $node;
		}
		$total++;
		continue;
		$verse = preg_replace($search, $replace, $verse);
		$verse = preg_replace("#qu(?=[aeiouyæàâäéèêëîïœôöùûü])#ui", "$", $verse);
		$verse = preg_replace("#gu(?=[aeiouyæàâäéèêëîïœôöùûü])#ui", "@", $verse);
		preg_match_all("#eau|oeu|œu|oui|oei|œi|aie|aou|uie|eui|iai|oue|oie|oè|eh|ai|aî|au|oi|oî|ou|où|oû|ei|eu|ieau|ioeu|ia|iaî|iau|ioi|iou|ioù|iei|ieu|ïeu|ia|ie|ée|ié|iè|iê|io|iu|ui|uei|ue|ïe|ïu|(?<![qg])u|(?<=[g])u(?![aeioyæàâäéèêëîïœôöùûü])|[aioæàâäéèêëîïœôöùûü]|(?<![aeioæàâäéèêëîïœôöùûü])y(?![aeioæàâäéèêëîïœôöùûü])|e(?!([  ,.…«»\)\(\-\":;!|)\?\n]*([\nhaeiouyæàâäéèêëîïœôöùûü]|$)|(s[  ,.…:;!«»\)\(\"\?\n]*$)|(nt[  ,.…:;«»!\"\)\(\?\n]*$)))#ui", $verse, $matches, PREG_OFFSET_CAPTURE);
		$count = count($matches[0]);
		echo $count."\t".$verse."\n";
		if ($count == 12) {
			$cesura = strrpos(substr($verse, $matches[0][5][1], $matches[0][6][1] - $matches[0][5][1]) , " ");

			if ($cesura) {
				$success++;
				$hemistich1 = substr($verse, 0, $matches[0][5][1] + $cesura);
				$hemistich1 = clean($hemistich1, $mode);
				$results[] = $hemistich1;
				$hemistich2 = substr($verse, $matches[0][5][1] + $cesura + 1);
				$hemistich2 = clean($hemistich2, $mode);
				$results[] = $hemistich2;

				if ($mode == "xml") {
					$node->nodeValue = "";
					//$space = $text->createTextNode(" ");
					$space = $text->createElement("c", " ");
					$hemistich1 = $text->createElement("seg", $hemistich1);
					$hemistich2 = $text->createElement("seg", $hemistich2);
					$node->appendChild($hemistich1);
					$node->appendChild($space);
					$node->appendChild($hemistich2);
				}
			} elseif (!$cesura and $debug == "ces") {
				echo $verse;
				print_r($matches[0]);
			}
		} elseif ($count == 6) {
			$success++;
			$verse = clean($verse, $mode);
			$results[] = $verse;

			if ($mode == "xml") {
				$node->nodeValue = "";
				$verse = $text->createElement("seg", $verse);
				$node->appendChild($verse);
			}
		}

		if (($debug == "inf" and $count < 12) or ($debug == "sup" and $count > 12)) {
			$fail++;
			echo $count . " ";
			echo $fail . " \n" . $verse . "\n";
			echo "\n";
			print_r($matches[0]);

			//mode apprentissage

			if (!$learn) {
				continue;
			}
			preg_match_all("#([^ '’]*(é|i|y|(?<![qg])[u]))([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $diereses, PREG_SET_ORDER);
			preg_match_all("#([^ '’]*[^aeiouæàâéèêîïœôû '’]y)([aeiouæàâéèêîïœôû][^ '’,.]*)#ui", $verse, $ys, PREG_SET_ORDER);
			preg_match_all("#([^ '’]*e)(nt(?=[ ,.:;!\?\n]*$))#ui", $verse, $ents, PREG_SET_ORDER);
			preg_match_all("#(e[ ,.:;!\?\n]*)(h[^ '’,.]*)#ui", $verse, $hs, PREG_SET_ORDER);

			if (count($diereses) + count($ents) + count($ys) + count($hs) == 12 - $count) {
				foreach ($diereses as $dierese) {
					$csv[$dierese[1] . "#" . $dierese[3]] = $dierese[0];
				}
				foreach ($ys as $y) {
					$csv[$y[1] . "#" . $y[2]] = $y[0];
				}
				foreach ($ents as $ent) {
					$csv[$ent[1] . "#" . $ent[2]] = $ent[0];
				}
				foreach ($hs as $h) {
					$csv["#" . $h[2]] = $h[2];
				}
			} elseif (count($diereses) + count($ents) + count($ys) + count($hs) < 12 - $count) {
			}
		}
	}

	if ($mode == "xml") {
		file_put_contents("../../tcp5c/" . $file_name . ".xml", $text->saveXML());
	}
}
echo "Vers et parties de vers traités : $total\n";
return;
echo "Vers et parties de vers reconnus comme alexandrin ou hémistiche : $success\n";
echo "Pourcentage : " . round(($success / $total) * 100, 2) . "\n";

//mode apprentissage

if (!$learn) {

	return;
}
asort($csv);
$csv = array_unique($csv);
$string = "";
foreach ($csv as $key => $line) {
	$string.= "$key\t/\b$line\b/ui\n";
}
$string = str_replace(array(
		"@",
		"$"
	) , array(
		"gu",
		"qu"
	) , $string);
file_put_contents("dierese.csv", trim($string));

//h : par défaut muet à l'initial, le remplacer par un joker cons

//voy : par défaut lié, insére un joker cons en cas de diérèse


function clean($string) {


	//nettoie l'hémistiche pour affichage
	$string = str_replace(array(
			"#",
			"@",
			"$"
		) , array(
			"",
			"gu",
			"qu"
		) , $string);
	return $string;
}

?>
