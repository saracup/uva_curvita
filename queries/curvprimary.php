<?php
//Search for faculty affiliation with training programs
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//copy and backup the existing training.csv file
uvasomcurvfiles_copy('primary');
//query data
$curvResult = mysqli_query($curvConn,"SELECT users.id, offices.name,
	(SELECT CASE 
		WHEN affiliations.primary LIKE '1' THEN  offices.institution_id
	END) as 'institution_id'
FROM fd_users AS users
INNER JOIN affiliations ON users.id = affiliations.user_id
LEFT JOIN offices ON affiliations.office_id = offices.id
WHERE users.type LIKE 'Faculty' AND research_interest_title IS NOT NULL
AND email IS NOT NULL
AND affiliations.primary LIKE 1 AND offices.name NOT LIKE 'Unaffiliated'
AND affiliations.disabled_at IS NULL OR affiliations.disabled_at >= NOW()
AND affiliations.ended_on IS NULL OR affiliations.ended_on >= NOW()
AND affiliations.updated_at > DATE_SUB(CURDATE(), INTERVAL 5 DAY)
ORDER BY users.id
") or die(mysql_error());
$uvasomfaculty ='"userid","primary"'."\r\n";
while($row = mysqli_fetch_array($curvResult))
  {
$userid = $row['id'];
	$primary = ltrim(preg_replace("/,/", "&#44;",$row['name']));
	$institution = $row['institution_id'];
	if (($institution === '1') && (!empty($institution))){
	$uvasomfaculty .= '"'.$userid.'",';
	$uvasomfaculty .= '"'.preg_replace("/- /", ": ",$primary).'"'."\r\n";
	}
  }  
//update file
uvasomcurvfiles_update('primary');
mysqli_close($curvConn);
uvasomcurvfiles_delete('primary');
?>
