<?php
/**
 * this is a script for manipulating PDF files 
 * @author Merlin Becker
 * @created 07.02.2017
 * @version 1.0
 * 
 * 
 * @see http://hrt0kmt.hatenablog.com/entry/2015/05/27/170608
 * 
 * **/

/**
 * at first, look if all necessary libs are installed
 * 
 * **/
if(!is_dir("lib/fpdi")){
	download("https://www.setasign.com/downloads/103845/FPDI-1.6.1.zip#p-356","lib/fpdi/","proxy-et.iosb.fraunhofer.de:80");
}
if(!is_dir("lib/fpdf")){
	download("http://fpdf.de/downloads/fpdf181.zip","lib/fpdf","proxy-et.iosb.fraunhofer.de:80");
}

require_once('lib/fpdf/fpdf.php');
require_once('lib/fpdi/fpdi.php');

$format = ''; //horizontal 

//the page to insert
$thepage=2;

//$myfile = fopen('out/' . $filename, "w");

/**
 * @TODO load a pdf and add a blank page to page X
 * **/

foreach(glob('in/issues/GP*.pdf') as $file)
{
	$filename = basename($file);
	$fileout = 'out/' . $filename;

	// Ausgabe-PDF
	$out = new FPDI();

	// Vorhandenes PDF einlesen
	$pagecount = $out->setSourceFile($file);

	// Alle Seiten nacheinander importieren
	for($i = 1; $i <= $pagecount; $i++)
	{
			// Importiere Seite
			$tpl = $out->importPage($i); // , '/MediaBox'
			// Vorhandene Seite
			$out->addPage($format);
			$out->useTemplate($tpl);
			// Leere Seite anfügen (nur nicht am Ende)	
		if($i==$thepage){
			$out->addPage($format);
		}
	}

	$out->Output($fileout,"F");
}
echo "done";


phpinfo();


/**
 * @TODO generate thumbs of the first page and the flashpage
 * **/

/**
 * @TODO
 * **/

function download($src,$dest,$proxy=""){
	$output=array();
	$url =$src;
	
	$zipFile = "temp.zip";
	$zipResource = fopen($zipFile, "w");
	// Get The Zip File From Server
	$ch = curl_init();
	
	if($proxy!=""){
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FILE, $zipResource);
	$page = curl_exec($ch);
	if(!$page) {
		$output[]="- ".curl_error($ch);
	}
	curl_close($ch);

	/* Open the Zip file */
	$zip = new ZipArchive;
	$extractPath = $dest;
	if($zip->open($zipFile) != "true"){
		$output[]="- Unable to open the Zip File";
	}
	/* Extract Zip File */
	$zip->extractTo($extractPath);
	$zip->close();
	if(count($output)==0)return 0;
	return $output;
}
?>