<?php
$errorfile = fopen("log.txt", "a") or die("Unable to open log file!");
fwrite($errorfile, "********* " . date("Y.m.d h:i:sa") . " *********\r\n");
$folderName = $addedDirectory . "-" . date('m-d-Y-His');
mkdir($folderName);

//error handler function
function customError($errfile, $errstr) {
  fwrite($errfile, $errstr . "\r\n");
  fclose($errfile);
  echo (new WindowsFormsApp1\PhpErrors);
  echo WindowsFormsApp1\PhpErrors::DoAlert($errstr);
  
}

?>