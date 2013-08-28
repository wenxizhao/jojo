<?php
/**
 * This file is part of the exporting module for Highcharts JS.
 * www.highcharts.com/license
 * 
 *  
 * Available POST variables:
 *
 * $filename  string   The desired filename without extension
 * $type      string   The MIME type for export. 
 * $width     int      The pixel width of the exported raster image. The height is calculated.
 * $svg       string   The SVG source code to convert.
 */
 ini_set('display_errors', 'on');

// Options
define ('BATIK_PATH', 'batik-rasterizer.jar');

///////////////////////////////////////////////////////////////////////////////
ini_set('magic_quotes_gpc', 'off');

$type = $_POST['type'];
$svg = (string) $_POST['svg'];
$filename = (string) $_POST['filename'];

// set temp dir
if (function_exists('sys_get_temp_dir')) {
	$tmp = sys_get_temp_dir();
} elseif (is_dir('temp')) {
	$tmp = 'temp';
} else {
	exit('No temp dir available');
};

// find batik or try to use imageMagick (convert)
$imageMagick = !file_exists(BATIK_PATH);

// prepare variables
if (!$filename) $filename = 'chart';
if (get_magic_quotes_gpc()) {
	$svg = stripslashes($svg);	
}

// check for malicious attack in SVG
if (strpos($svg, '<!ENTITY') !== false) {
	exit('Execution is stopped, the posted SVG could contain code for a mailcious attack');
}

$tempName = md5(rand());

// allow no other than predefined types
if ($type == 'image/png') {
	$typeString = '-m image/png';
	$ext = 'png';
	
} elseif ($type == 'image/jpeg') {
	$typeString = '-m image/jpeg';
	$ext = 'jpg';

} elseif ($type == 'application/pdf') {
	$typeString = '-m application/pdf';
	$ext = 'pdf';

} elseif ($type == 'image/svg+xml') {
	$ext = 'svg';	
}
$outfile = "$tmp/$tempName.$ext";

if (isset($typeString)) {
	
	// size
	if ($_POST['width']) {
		$width = (int)$_POST['width'];
		if ($width) {
			if($imageMagick) {
				$width = "-size $width"."x";
			} else {
				$width = "-w $width";
			}
		}
	}

	// generate the temporary file
	if (!file_put_contents("$tmp/$tempName.svg", $svg)) { 
		die("Couldn't create temporary file. Check that the directory permissions for
			the $tmp directory are set to 777.");
	}
	
	// do the conversion
	if ($imageMagick) {
		$cmd = "convert $width ".escapeshellarg("$tmp/$tempName.svg")." ".escapeshellarg("$outfile");
	} else {
		$cmd = "java -jar ". BATIK_PATH ." ".escapeshellarg("$typeString")." -d ".
			escapeshellarg("$outfile ")." $width ".escapeshellarg("$tmp/$tempName.svg");
	}
	$output = shell_exec($cmd." 2>&1");
	
	// catch error
	if (!is_file($outfile) || filesize($outfile) < 10) {		
		echo "<pre>$output</pre>";
		echo "Error while converting SVG. ";
		
		if (strpos($output, 'SVGConverter.error.while.rasterizing.file') !== false) {
			echo "
			<h4>Debug steps</h4>
			<ol>
			<li>Copy the SVG:<br/><textarea rows=5>" . htmlentities(str_replace('>', ">\n", $svg)) . "</textarea></li>
			<li>Go to <a href='http://validator.w3.org/#validate_by_input' target='_blank'>validator.w3.org/#validate_by_input</a></li>
			<li>Paste the SVG</li>
			<li>Click More Options and select SVG 1.1 for Use Doctype</li>
			<li>Click the Check button</li>
			</ol>";
		}
	}
	// stream it
	else {
		header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
		header("Content-Type: $type");		
		echo file_get_contents($outfile);
	}
	
	// delete it
	unlink("$tmp/$tempName.svg");
	unlink($outfile);

// SVG can be streamed directly back
} else if ($ext == 'svg') {
	header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
	header("Content-Type: $type");
	echo $svg;
	
} else {
	echo "Invalid type";
}
?>
