<?php
 //find the table of contents
 global $cssarray;
 $chpPlayOrder = NULL;
 $container_path = $temp_path . "/OEBPS/toc.ncx";

    $toc_xml = file_get_contents($container_path);
    if ($toc_xml === FALSE){
		$errormessage = "The file [" .$toc_xml. "] does not exist in: " .$epub;
		customError ($errorfile, $errormessage);
		}

    $doc = new DOMDocument;
    $doc->loadXML($toc_xml);
	$navPointTag = $doc->getElementsByTagName('navPoint');

		for ($i = 0; $i < $navPointTag->length; $i++) {
			$temporary = $navPointTag->item($i)->nodeValue;

			if (stripos($temporary, $replaceChapter) !== false) {
				$chpNum = $i;
				$chpNumSrc = $doc->getElementsByTagName('content')[$chpNum]->getAttribute("src");
				//customError ($errorfile, $chpNumSrc);
				$chpPlayOrder = $navPointTag[$chpNum]->getAttribute("playOrder");
			}
		}
	
	$book = $doc->documentElement;
	$chapter = $book->getElementsByTagName('navPoint')->item($chpNum);
	if ($chapter === NULL){
		$errormessage = "The title [" .$replaceChapter. "] does not exist in: " .$epub;
		customError ($errorfile, $errormessage);
		}
	$chapter->parentNode->removeChild($chapter);
	
	$navContents = $doc->getElementsByTagName('navPoint');

		for ($i = 0; $i < $navContents->length; $i++) {
			$isContents = $navContents->item($i)->nodeValue;

			// Test if there is a contents file in the epub. If so, delete reference to page to be replaced.
			// If adding new page to the contents file, will need to be added outside of deleteOld.php which is inside a test for $replace.
			if (stripos($isContents, "contents") !== false){
					$contentsrc = $doc->getElementsByTagName('content')[$i]->getAttribute("src");
					$contents_path = $temp_path . "/OEBPS/" . $contentsrc;

					$html_file = file_get_contents($contents_path);
					if ($html_file === FALSE){
						$errormessage = "Couldn't open contents HTML: " .$epub;
						customError ($errorfile, $errormessage);
						}
					$html = new DOMDocument();
					$html->loadXML($html_file);

					foreach ($html->getElementsByTagName('a') as $htmltag){
						$ahref = $htmltag->getAttribute('href');
						//customError ($errorfile, $test1);
						if ($chpNumSrc == $ahref ){
							//$htmltag->parentNode->removeChild($htmltag);
							$htmltag->setAttribute('href', basename($added_page));
							foreach($htmltag->childNodes as $textnode) {
								if ($textnode->nodeType != XML_TEXT_NODE) {
									continue;
								}
								$textnode->nodeValue = $addedPageTitle;
							}
						}					
					$newxhtml = $html->saveXML();
					$xhtmlresult = file_put_contents($contents_path, $newxhtml);
					if ($xhtmlresult === FALSE){
						$errormessage = "Unable to write new HTML file: " .$epub;
						customError ($errorfile, $errormessage);
						}
					}
				}
		}

	// Save new XML to ToC file
    $newxml = $doc->saveXML();
    $result = file_put_contents($container_path, $newxml);
    if ($result === FALSE){
						$errormessage = "Unable to write new contents XML file: " .$epub;
						customError ($errorfile, $errormessage);
						}
	// Get stylesheet references from old file
	if ($useoldstyle === TRUE){
		getStyles($temp_path . "/OEBPS/" . $chpNumSrc);
	}
	
	// Delete old file that is being replaced.
	unlink($temp_path . "/OEBPS/" . $chpNumSrc) or die("Couldn't delete file");
	cleanXML ($container_path);
?>