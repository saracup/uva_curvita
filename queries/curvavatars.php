<?php
//Search for the faculty member's avatar
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
uvasomcurvfiles_copy('avatars');
$curvResult = mysqli_query($curvConn,"SELECT users.id as userid, documents.id,documents.doc_file_name,
users.username,
users.first_name,
users.middle_name,
users.last_name
FROM fd_users AS users
INNER JOIN documents ON documents.documentable_id = users.id 
WHERE users.type LIKE 'Faculty' AND doc_file_name IS NOT NULL
AND documents.disabled_at IS NULL
AND documents.updated_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
GROUP BY userid
HAVING MAX(documents.approved_at)") or die(mysql_error());
$uvasomfaculty ='"userid","avatar","username","first_name","middle_name","last_name"'."\r\n";
while($row = mysqli_fetch_array($curvResult))
  {
  	$id = $row['userid'];
	$avatar = 'http://somcurveweb.eservices.virginia.edu:80/avatars/'.$row['id'].'/'.$row['doc_file_name'];
	$uvasomfaculty .= '"'.$id.'",';
	$uvasomfaculty .= '"'.$avatar.'",';
	$uvasomfaculty .= '"'.$row['username'].'",';
	$uvasomfaculty .= '"'.$row['first_name'].'",';
	$uvasomfaculty .= '"'.$row['middle_name'].'",';
	$uvasomfaculty .= '"'.$row['last_name'].'"'."\r\n";
  }  
//update data file
uvasomcurvfiles_update('avatars');
mysqli_close($curvConn);
uvasomcurvfiles_delete('avatars');?>
