<?php
//Search for faculty affiliation with training programs
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//copy and backup the existing training.csv file
uvasomcurvfiles_copy('deg');
//query data
$curvResult = mysqli_query($curvConn,"SELECT distinct educations.user_id,degrees.name as degree, educations.discipline as discipline, institutions.name as institution 
FROM curvita_production.educations 
INNER JOIN fd_users AS users ON educations.user_id = users.id
INNER JOIN institutions ON institution_id = institutions.id
INNER JOIN degrees ON degree_id = degrees.id
INNER JOIN affiliations ON educations.user_id = affiliations.user_id
WHERE educations.disabled_at IS NULL
AND users.type LIKE 'Faculty'
AND affiliations.disabled_at IS NULL OR affiliations.disabled_at >= NOW()
AND affiliations.ended_on IS NULL OR affiliations.ended_on >= NOW()
ORDER BY users.id,educations.ended_date ASC;") or die(mysql_error());

$uvasomfaculty ='"userid","degrees"'."\r\n";
$curruserid = "";
$currdeg = "";
while($row = mysqli_fetch_array($curvResult))
  {
	$userid = $row['user_id'];
	if($userid != $curruserid) {
		if($curruserid == ""){
			$curruserid = $userid;
		}
		else
		{
		$uvasomfaculty .= $currdeg. '"'."\r\n";
		$currdeg = "";
		$curruserid = $userid;
		}
		$uvasomfaculty .= '"'.$userid. '", "';
	}
		$degree = ltrim(preg_replace("/,/", "&#44;",$row['degree']));
		$discipline = ltrim(preg_replace("/,/", "&#44;",$row['discipline']));
		$institution = ltrim(preg_replace("/,/", "&#44;",$row['institution']));
		$currdeg .= '<li>'.$degree;
				if(!empty($discipline)){
					$currdeg .= '&#44; '.$discipline;
					}
			$currdeg .= '&#44; '.$institution.'</li>';
	
  }  
//update file
uvasomcurvfiles_update('deg');
mysqli_close($curvConn);
uvasomcurvfiles_delete('deg');
?>
