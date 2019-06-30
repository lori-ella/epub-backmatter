<?php
class ExtendedZip extends ZipArchive {
    // Special thanks to Giorgio Barchiesi for this class
    // https://stackoverflow.com/a/21044047
    // Member function to add a whole file system subtree to the archive
    public function addTree($dirname, $localname = '') {
        if ($localname)
            $this->addEmptyDir($localname);
        $this->_addTree($dirname, $localname);
    }

    // Internal function, to recurse
    protected function _addTree($dirname, $localname) {
        $dir = opendir($dirname);
        while ($filename = readdir($dir)) {
            // Discard . and ..
            if ($filename == '.' || $filename == '..')
                continue;

            // Proceed according to type
            $path = $dirname . '/' . $filename;
            $localpath = $localname ? ($localname . '/' . $filename) : $filename;
            if (is_dir($path)) {
                // Directory: add & recurse
                //$this->addEmptyDir($localpath);
                $this->_addTree($path, $localpath);
            }
            else if (is_file($path)) {
                // File: just add
                $this->addFile($path, $localpath);
            }
        }
        closedir($dir);
    }

    // Helper function
    public static function zipTree($dirname, $zipFilename, $flags = 0, $localname = '') {
        $zip = new self();
        $zip->open($zipFilename, $flags);
        $zip->addTree($dirname, $localname);
        $zip->close();
    }
}

//
function cleanXML ($file2clean){
	$search = ' xmlns=""';
	$file_contents = file_get_contents($file2clean);
	$file_contents = str_replace($search,"",$file_contents);
	file_put_contents($file2clean,$file_contents);
}

/**
 * Remove the directory and its content (all files and subdirectories).
 * @param string $dir the directory name
 * https://gist.github.com/irazasyed/4340722
 */
function rmrf($dir) {
    foreach (glob($dir) as $file) {
        if (is_dir($file)) { 
            rmrf("$file/*");
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}

//function to get style link from xhtml file in epub
function getStyles($xhtmlfile){
	global $cssarray;
	$file_xhtml = file_get_contents($xhtmlfile);
    if ($file_xhtml === FALSE){
		$errormessage = "The file [" .$file_xhtml. "] does not exist in: " .$epub;
		customError ($errorfile, $errormessage);
		}
	
	$file_xhtml = str_replace("\r\n", "\n", $file_xhtml);
	$file_xhtml = str_replace("\n\n", "\n", $file_xhtml);

    $xhtmldoc = new DOMDocument;

    $xhtmldoc->loadHTML($file_xhtml);

	foreach ($xhtmldoc->getElementsByTagName('link') as $csstag){
						$csshref = $csstag->getAttribute('href');
						$cssarray[] = $csshref;
						}
}
function putStyles($xhtmlfile){
	global $cssarray;
	$csscount = count($cssarray);
	$file_xhtml = file_get_contents($xhtmlfile);
    if ($file_xhtml === FALSE){
		$errormessage = "The file [" .$file_xhtml. "] does not exist in: " .$epub;
		customError ($errorfile, $errormessage);
		}

    $xhtmldoc = new DOMDocument;
    $xhtmldoc->loadXML($file_xhtml);

	for ($a = 0; $a < $csscount; $a++){
		$newcss = $xhtmldoc->createElement("link");
		$newcss->setAttribute("rel", "stylesheet");
		$newcss->setAttribute("type", "text/css");
		$newcss->setAttribute("href", $cssarray[$a]);
		$headtag = $xhtmldoc->getElementsByTagName("head");
        foreach ($headtag as $headElement) {    
            $headElement->appendChild($newcss);
			}
		}

	//$newXML = str_replace('xmlns=""', '', $newXML);
	$domTranObj = $xslProcessor->transformToDoc($xhtmldoc);
	$updated = $domTranObj->saveXML();

	//$updated = $xhtmldoc->saveXML();
    $result = file_put_contents($xhtmlfile, $updated);
    if ($result === FALSE){
						$errormessage = "Unable to write css links to new file: " .$epub;
						customError ($errorfile, $errormessage);
						}
	cleanXML ($xhtmlfile);
}

?>
