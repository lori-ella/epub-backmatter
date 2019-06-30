<?php
$cssarray = array();
global $cssarray;
//include_once 'init.php'; // included in shell program. If using from php, uncomment.
// Install directory:
    //backmatter.exe
    //mimetype.epub
    //newbackmatter.xhtml
        //Folders:
        //epub-temp
        //temp
include 'include.php';


// Settings to be passed in from form.
    //$epub = 'Dark-Immortality-Generic.epub'; // The epub we will modify
	//$added_page = 'newbackmatter.xhtml'; // The new page we're going to insert into it
	//$addedPageTitle = "Other Books by Author"; // New page title as appears in Table of Contents
	//$customFileName = "_endmatter";
	//$addedDirectory = "new"; //Default new directory for completed epubs
	$useoldstyle = TRUE;


// Static unless later versions allow for customizing
    $addedPageID = "newpageid0"; // New page id & idref attributes in package and ToC XML files.

// Server locations
    //$loc = 'epub-temp'; // folder for epubs to be changed
	
	if (stripos($epub, ".epub") === false){
		$errormessage = "Couldn't open file: " .$epub. " as epub.";
		customError ($errorfile, $errormessage);
		}
    $newepub = str_replace(".epub", "", $epub); // pull the epub file name
	
    $newepub = $newepub . $customFileName . ".epub"; // The new epub we will generate

    // Allocate a directory to work in. Deleted after new book is created
    $temp_file = uniqid("epub-");
	mkdir("temp");
    $temp_path = "temp" . "/" . $temp_file;
    $temp_file = $temp_file . ".epub";
    mkdir($temp_path) or die("Couldn't create temporary path.");

    // Open the epub archive
    $zip = new ZipArchive;
    $res = $zip->open($loc . "/" . $epub);
	
    if ($res !== TRUE){
		$errormessage = "Couldn't open epub: " .$epub;
		customError ($errorfile, $errormessage);
		}

    // Unzip the epub into a temporary location
    $zip->extractTo($temp_path);
    $zip->close();
	unlink($temp_path . "/" . 'mimetype');

    // check the file structure of epub
    // determines $textpath, directory for location of new file within EPUB
    // $textpath NULL if XHTML files stored in base directory
	if ($replace === TRUE){
		include 'deleteOld.php';
	}
    include 'checkepubfiles.php';

    // page location for ToC file
    $newpagepath = $textpath . basename($added_page);

    if (!copy($added_page, dirname($spine_path) . "/" . $newpagepath))
        die("Unable to copy new page into temporary location.");
    
    // Add style info from old file to the new page.
	
	if ($useoldstyle === TRUE){
		if ($replace === FALSE){
			getStyles($temp_path . "/OEBPS/" . $getPageSrc);
		}
		putStyles(dirname($spine_path) . "/" . $newpagepath);
	}
	
	
	// Add new file's information to the package file: content.opf
    include 'add2epub.php';

    // Add new file's information to the Table of Contents
    include 'add2toc.php';

    // Create the new EPUB file using a stub file which already contains the mimetype file
    copy ('mimetype.epub', $temp_file);
	ExtendedZip::zipTree($temp_path, $temp_file);
	
    $newfilename = $folderName . "/" . $newepub;
	//customError ($errorfile, $newfilename);
	rename ($temp_file, $newfilename);

    // Delete old EPUB files
    rmrf ($temp_path);
	rmrf ("temp");
	fwrite($errorfile, $newfilename . " completed successfully\r\n");
	
?>