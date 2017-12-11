<?php
//database info
//$curvConn = mysqli_connect('somcurvedb.med.virginia.edu','cad3r','Babs!1956','curvita_production') or die(mysql_error());
$curvConn = mysqli_connect('somcurvedb.med.virginia.edu','ran2n','snakeroot2015','curvita_production') or die(mysql_error());
print_r($curvConn);
$uvasomroot = '/var/www/med';
//make backups before updating csv files
echo $uvasomroot;

//uvasomcurvfiles_update("ash");

function uvasomcurvfiles_copy($curvquery) {
	global $uvasomroot;
	date_default_timezone_set('America/New_York');
	//$existcurvfile = $_SERVER['DOCUMENT_ROOT']."/sharedassets/curvita/csv/".$curvquery.".csv";
	//$newcurvfile = $_SERVER['DOCUMENT_ROOT']."/sharedassets/curvita/csv/backups/".$curvquery."_".date('YmdHis').".csv";
	$existcurvfile = $uvasomroot."/sharedassets/curvita/csv/".$curvquery.".csv";
	$newcurvfile = $uvasomroot."/sharedassets/curvita/csv/backups/".$curvquery."_".date('YmdHis').".csv";
		if (!copy($existcurvfile, $newcurvfile)) {
			$message = "Copy process for ".$curvquery.".csv has failed at ".date('d/m/Y H:i:s').".";
			mail('ran2n@virginia.edu', 'Error Copying '.$curvquery.'.csv', $message);
		}
		else {
			$message = "Copy process for ".$curvquery.".csv succeeded at ".date('d/m/Y H:i:s').". Total size: ".(filesize($newcurvfile)/1048576)."MB";
			mail('ran2n@virginia.edu', 'Success Copying '.$curvquery.'.csv', $message);
		}
}
//update csv files
function uvasomcurvfiles_update($curvquery) {
	global $uvasomfaculty;
	global $uvasomroot;
	echo $uvasomfaculty;
	echo $uvasomroot;
	//$fn	= $uvasomroot."/sharedassets/curvita/csv/*.csv";
	$fn	= "/var/www/med/sharedassets/curvita/csv/".$curvquery.".csv";
	$fp = fopen($fn,"w+") or die ("Error opening file in write mode!");
	fputs($fp,$uvasomfaculty);
	fclose($fp) or die ("Error closing file!");
	
}
function uvasomcurvfiles_delete($curvquery)
   {
	global $uvasomroot;
	$files = glob($uvasomroot."/sharedassets/curvita/csv/backups/*.csv");
  	$time  = time();
  	foreach ($files as $file){

	 if (is_file($file))
      		if ($time - filemtime($file) >= 60*60) // 2 days
        		unlink($file);
   	 }
   }
echo "Testing";
?>
