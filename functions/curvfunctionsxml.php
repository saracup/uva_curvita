<?php
//database info
//$curvConn = mysqli_connect('somcurvedb.med.virginia.edu','cad3r','Babs!1956','curvita_production') or die(mysql_error());
$curvConn = mysqli_connect('somcurvedb.med.virginia.edu','ran2n','snakeroot2015','curvita_production') or die(mysql_error());
$uvasomroot = $_SERVER['DOCUMENT_ROOT'];
//make backups before updating xml files
function uvasomcurvfiles_copy($curvquery) {
	global $uvasomroot;
	date_default_timezone_set('America/New_York');
	//$existcurvfile = $_SERVER['DOCUMENT_ROOT']."/sharedassets/curvita/xml/".$curvquery.".xml";
	//$newcurvfile = $_SERVER['DOCUMENT_ROOT']."/sharedassets/curvita/xml/backups/".$curvquery."_".date('YmdHis').".xml";
	$existcurvfile = $uvasomroot."/sharedassets/curvita/xml/".$curvquery.".xml";
	$newcurvfile = $uvasomroot."/sharedassets/curvita/xml/backups/".$curvquery."_".date('YmdHis').".xml";
		if (!copy($existcurvfile, $newcurvfile)) {
			$message = "Copy process for ".$curvquery.".xml has failed at ".date('d/m/Y H:i:s').".";
			mail('somweb@virginia.edu', 'Error Copying '.$curvquery.'.xml', $message);
		}
		else {
			$message = "Copy process for ".$curvquery.".xml succeeded at ".date('d/m/Y H:i:s').". Total size: ".(filesize($newcurvfile)/1048576)."MB";
			mail('somweb@virginia.edu', 'Success Copying '.$curvquery.'.xml', $message);
		}
}
//update xml files
function uvasomcurvfiles_update($curvquery) {
	global $uvasomfaculty;
	global $uvasomroot;
	//$fn	= $uvasomroot."/sharedassets/curvita/xml/".$curvquery.".xml";
	//$fn	= "var/www/vhosts/med/sharedassets/curvita/xml/".$curvquery.".xml";
	//$fp = fopen($fn,"w+") or die ("Error opening file in write mode!");
	//fputs($fp,$uvasomfaculty);
	//fclose($fp) or die ("Error closing file!");
	file_put_contents($uvasomroot."/sharedassets/curvita/xml/".$curvquery.".xml", $uvasomfaculty->flush(true), FILE_APPEND);

	
}

?>
