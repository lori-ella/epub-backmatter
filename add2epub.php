<?php

	// Adding item and itemref to package file
    // $dom carried over from epub file structure check
	
	if ($replace === TRUE){
		foreach ($dom->getElementsByTagName("item") as $itemtag){
            if ($itemtag->getAttribute("href") == $chpNumSrc){
				$whichitemid = $itemtag->getAttribute("id");
				$itemtag->parentNode->removeChild($itemtag);
			}
        }
		foreach ($dom->getElementsByTagName("itemref") as $whichitemref){
            if ($whichitemref->getAttribute("idref") === $whichitemid){
				$whichitemref->parentNode->removeChild($whichitemref);
			}
        }
		foreach ($dom->getElementsByTagName("reference") as $whichref){
            if ($whichref->getAttribute("href") == $chpNumSrc){
				$whichref->parentNode->removeChild($whichref);
			}
        }
	}

    $newitem = $dom->createElement("item");
    $newitem->setAttribute("id", $addedPageID);
    $newitem->setAttribute("href", $newpagepath);
    $newitem->setAttribute("media-type", "application/xhtml+xml");
      
    $tags = $dom->getElementsByTagName("manifest");
        foreach ($tags as $domElement) {    
            $domElement->appendChild($newitem);
        }

    $newitemref = $dom->createElement("itemref");
    $newitemref->setAttribute("idref", $addedPageID);

    $tags = $dom->getElementsByTagName("spine");
        foreach ($tags as $domElement) {    
            $domElement->appendChild($newitemref);
        }

    // Save new XML to file
	$newXML = str_replace('xmlns=""', '', $newXML);
    $newxml = $dom->saveXML();
    $result = file_put_contents($spine_path, $newxml);
	
    if ($result === FALSE){
						$errormessage = "Unable to write new manifest XML file: " .$epub;
						customError ($errorfile, $errormessage);
						}
	cleanXML ($spine_path);
?>