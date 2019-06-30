<?php
// Complies with EPUB spec. Tested with Sigil and Vellum.
    $container_path = $temp_path . "/META-INF/container.xml";
    $container_xml = file_get_contents($container_path);
    if ($container_xml === FALSE){
						$errormessage = "Couldn't open container XML file: " .$epub;
						customError ($errorfile, $errormessage);
						}

    // Look in the container to find the spine: content.opf
    $container = new SimpleXMLElement($container_xml);
    $spine_path = $temp_path . "/" . $container->rootfiles[0]->rootfile["full-path"];
    
    // Pull up the spine
    $spine_xml = file_get_contents($spine_path);
    if ($spine_xml === FALSE){
						$errormessage = "Couldn't open opf: " .$epub;
						customError ($errorfile, $errormessage);
						}

    // Open epub package details--content.opf(spine, manifest)
    $dom = new DOMDocument;
    $dom->loadXML($spine_xml);
	
    foreach ($dom->getElementsByTagName('item') as $element){
        if ($element->getAttribute('media-type') == "application/xhtml+xml"){
			
			if (!$srcarray){
				$textpath = $element->getAttribute('href');
				if (strpos($textpath, '/') !== false){
					$a = strtok($textpath, '/');
					$textpath = $a . "/";
				} else {
					$textpath = NULL;
				}
			}
			$srcarray[] = $element->getAttribute('href');
        }
    }
	//Used to average number of pages in epub to determine middle page
	//This is used if a page isn't replaced and the stylesheets from old file is reused

	$getPagesCount = count($srcarray);
	if ($getPagesCount > 2){
				$getPagesCount = round($getPagesCount / 2);
			}
	$getPageSrc = $srcarray[$getPagesCount];
?>