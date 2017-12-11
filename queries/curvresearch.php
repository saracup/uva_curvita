<?php
//Search for faculty affiliation with training programs
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//copy and backup the existing training.csv file
uvasomcurvfiles_copy('research');
//query data
$curvResult = mysqli_query($curvConn,
"SELECT  users.id as userid,offices.name as research, offices.id as office_id
FROM fd_users AS users
INNER JOIN affiliations ON users.id  = affiliations.user_id
INNER JOIN offices ON affiliations.office_id = offices.id
WHERE users.type = 'Faculty'
(offices.id LIKE 198
OR offices.id = 199
OR offices.id = 200
OR offices.id = 201
OR offices.id = 202
OR offices.id = 203
OR offices.id = 204
OR offices.id = 205
OR offices.id = 206
OR offices.id = 207
OR offices.id = 208
OR offices.id = 209
OR offices.id = 210
OR offices.id = 211
OR offices.id = 213
OR offices.id = 214
OR offices.id = 215
OR offices.id = 216
OR offices.id = 217
OR offices.id = 218
OR offices.id = 259
OR offices.id = 271
OR offices.id = 10)
AND offices.name LIKE '%Research Discipline%'
AND affiliations.disabled_at IS NULL OR affiliations.disabled_at >= NOW()
AND affiliations.ended_on IS NULL OR affiliations.ended_on >= NOW()
ORDER BY users.id") or die(mysql_error());
$uvasomfaculty ='"userid","researchdiscipline"'."\r\n";
//AND affiliations.updated_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
//$curruserid = "";
//$currtp = "";
while($row = mysqli_fetch_array($curvResult))
  {
	$userid = $row['userid'];
	$officesid = $row['office_id'];
	if ($officesid==='10'):$researchdiscipline="Miscellaneous";
	else: $researchdiscipline = ltrim($row['research']);
	endif;
	if (($officesid!='3')&&($officesid!='4')&&($officesid!='30')&&($officesid!='74')&&($officesid!='223')&&($officesid!='237')&&($officesid!='244')&&($officesid!='258')){
	$uvasomfaculty .= '"'.$userid.'",';
	$uvasomfaculty .= preg_replace("/,/", "&#44;",substr($researchdiscipline,21))."\r\n";
	}
	else {$uvasomfaculty .= '';}
  }  
//$uvasomfaculty .= "\r\n";
//update file
uvasomcurvfiles_update('research');
mysqli_close($curvConn);
uvasomcurvfiles_delete('research');
?>
