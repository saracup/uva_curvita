<?php
//Search for faculty affiliation with training programs
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//copy and backup the existing training.csv file
uvasomcurvfiles_copy('aff');
//query data
$curvResult = mysqli_query($curvConn,"SELECT affiliations.user_id, offices.name as otheraffiliations,affiliations.primary
FROM affiliations
INNER JOIN fd_users AS users ON users.id = affiliations.user_id
LEFT JOIN offices ON affiliations.office_id = offices.id
WHERE users.type LIKE 'Faculty' 
AND offices.name NOT LIKE 'RFD'
AND offices.name NOT LIKE 'BIMS'
AND offices.name NOT LIKE 'SOM'
AND offices.name NOT LIKE 'Research Discipline,%'
AND offices.name NOT LIKE 'Unaffiliated'
AND affiliations.disabled_at IS NULL OR affiliations.disabled_at >= NOW()
AND affiliations.ended_on IS NULL OR affiliations.ended_on >= NOW()
AND affiliations.updated_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
ORDER BY user_id") or die(mysql_error());

print_r($curvResult);

$uvasomfaculty ='"userid","otheraffiliations","primary"'."\r\n";
//WHERE offices.type NOT LIKE 'TrainingProgram' 

while($row = mysqli_fetch_array($curvResult))
  {
        print_r($row);
	$userid = $row['user_id'];
	$primary = $row['primary'];
	$affs = ltrim($row['otheraffiliations']);
	if ($primary!=1) {
	$uvasomfaculty .= '"'.$userid.'",';
	if (stristr($affs, 'Research Discipline') === FALSE){
		$otheraffiliations = preg_replace("/,/", "&#44;",$affs);
		}
		else {
 		$otheraffiliations =  preg_replace("/,/", "&#44;",substr($affs,21));
 		}
		$uvasomfaculty .= '"'.$otheraffiliations.'",';
		$uvasomfaculty .= '"'.$primary.'"'."\r\n";
	}
  }  
//update file
uvasomcurvfiles_update('aff');
mysqli_close($curvConn);
uvasomcurvfiles_delete('aff');
?>
