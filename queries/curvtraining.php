<?php
//Search for faculty affiliation with training programs
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//copy and backup the existing training.csv file
uvasomcurvfiles_copy('training');
//query data
$curvResult = mysqli_query($curvConn,
"SELECT users.id as userid,grant_applications.name as trainingprogram
FROM participants
LEFT JOIN fd_users AS users ON users.id  = participants.user_id
LEFT JOIN grant_applications ON grant_applications.id = participants.grant_application_id
WHERE participants.type = 'FacultyParticipant' 
AND (grant_applications.id = 22
OR grant_applications.id = 24
OR grant_applications.id = 27
OR grant_applications.id = 10
OR grant_applications.id = 23
OR grant_applications.id = 16
OR grant_applications.id = 18
OR grant_applications.id = 12
OR grant_applications.id = 6
OR grant_applications.id = 9
OR grant_applications.id = 7
OR grant_applications.id = 13
OR grant_applications.id = 19 ) 
AND participants.disabled_at IS NULL OR participants.disabled_at >= NOW()
ORDER BY users.id") or die(mysql_error());
$uvasomfaculty ='"userid","trainingprogram"'."\r\n";

while($row = mysqli_fetch_array($curvResult))
  {
	$userid = $row['userid'];
	$trainingprogram = $row['trainingprogram'];
	$uvasomfaculty .= '"'.$userid.'",';
	$uvasomfaculty .= '"'.$trainingprogram.'"'."\r\n";
  }  
//update file
uvasomcurvfiles_update('training');
mysqli_close($curvConn);
uvasomcurvfiles_delete('training');
?>
