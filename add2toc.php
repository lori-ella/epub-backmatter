<?php
// Open ToC -- Table of Contents
// toc.ncx
    $container_path = $temp_path . "/OEBPS/toc.ncx";

    $toc_xml = file_get_contents($container_path);
    if ($toc_xml === FALSE){
						$errormessage = "Couldn't open the table of contents: " .$epub;
						customError ($errorfile, $errormessage);
						}

    $dom = new DOMDocument;
    $dom->loadXML($toc_xml);
    $navs = $dom->getElementsByTagName("navPoint");
    
    // Need to count the navpoints to add page at end of ToC at value "playorder"
	$navcount = $navs->length;

    // Create new ToC entry
	
    $newNavPoint = $dom->createElement("navPoint");
    $newNavPoint->setAttribute("id", $addedPageID);
	if (isset($chpPlayOrder)) {
	$newNavPoint->setAttribute("playOrder", $chpPlayOrder);
	} else {
    $newNavPoint->setAttribute("playOrder", ($navcount +1));
	}

    $tags = $dom->getElementsByTagName("navMap");
        foreach ($tags as $domElement) {    
            $domElement->appendChild($newNavPoint);
        }
    function addElementToTag($dom, $tagName, $elementName, $elementText) {
        $newElement = $dom->createElement($elementName);
        $newElement->appendChild($dom->createTextNode($elementText));
        $tags = $dom->getElementsByTagName($tagName);
        foreach ($tags as $domElement) {    
            $domElement->appendChild($newElement);
        }
    }
    
    addElementToTag($dom, "navPoint", "navLabel", "");
    addElementToTag($dom, "navLabel", "text", $addedPageTitle);

    $elem = $dom->createElement("content");
    $elem->setAttribute("src", $newpagepath);
    $tags = $dom->getElementsByTagName("navPoint");
    foreach ($tags as $domElement) {    
        $domElement->appendChild($elem);
    }	
		
	// Save new XML to ToC file
    $newxml = $dom->saveXML();
    $result = file_put_contents($container_path, $newxml);
    if ($result === FALSE){
						$errormessage = "Unable to write new ToC XML file: " .$epub;
						customError ($errorfile, $errormessage);
						}
	cleanXML ($container_path);
?>