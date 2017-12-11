<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
 //include all functions
require_once('/var/www/med/sharedassets/curvita/functions/curvfunctions.php');
//Search for the faculty member's basic metadata
uvasomcurvfiles_copy('fac');
$curvResult = mysqli_query($curvConn,"SELECT DISTINCT users.id,
	users.updated_at,
	username,
	last_name,
	first_name, 
	middle_name,
	email,
	campus_phone,
	fax_number,
	rank,
	research_interest_title,
	research_interest_description,
	personal_statement,
	personal_website_url,
mentor_volunteers,
mentor_payees,
mentor_credit,
	(SELECT CASE 
		WHEN affiliations.primary LIKE '1' THEN  offices.institution_id
	END) as 'institution_id',
	(SELECT CASE 
		WHEN addresses.type LIKE 'CampusAddress' THEN  addresses.address1
	END) as 'address1',
	(SELECT CASE 
		WHEN addresses.type LIKE 'CampusAddress' THEN  addresses.address2
	END) as 'address2',
	(SELECT CASE 
		WHEN addresses.type LIKE 'CampusAddress' THEN  addresses.city
	END) as 'city',
	(SELECT CASE 
		WHEN addresses.type LIKE 'CampusAddress' THEN  addresses.state
	END) as 'state',
	(SELECT CASE 
		WHEN addresses.type LIKE 'CampusAddress' THEN  addresses.postal_code
	END) as 'postal_code'

FROM fd_users AS users
INNER JOIN affiliations ON users.id = affiliations.user_id
INNER JOIN offices ON affiliations.office_id = offices.id
LEFT JOIN addresses ON addresses.user_id = users.id
WHERE users.type LIKE 'Faculty' 
AND users.research_interest_title IS NOT NULL AND users.research_interest_title != ''
AND email IS NOT NULL
AND (affiliations.disabled_at IS NULL OR affiliations.disabled_at >= NOW())
AND (affiliations.ended_on IS NULL OR affiliations.ended_on >= NOW())
AND offices.name NOT LIKE '%Unaffiliated%'
AND institution_id LIKE 1
AND (users.updated_at > DATE_SUB(CURDATE(), INTERVAL 5 DAY)  
	OR addresses.updated_at  > DATE_SUB(CURDATE(), INTERVAL 5 DAY)
	)
GROUP BY users.id
ORDER BY last_name,first_name,middle_name") or die(mysql_error());
$uvasomfaculty ='"id","username","institution","first_name","middle_name","last_name","email","campus_phone","fax_number","rank","research_interest_title","research_interest_description","personal_statement","address1","address2","city","state","postal_code","personal_website_url","takes_undergrads"'."\r\n";
/*AND (users.updated_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY)  
	OR addresses.updated_at  > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
	)
AND (users.updated_at > DATE_SUB(CURDATE(), INTERVAL 1 DAY)  
	OR addresses.updated_at  > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
	)


*/
while($row = mysqli_fetch_array($curvResult))
  {
	  
  	$id = $row['id'];
	$username = $row['username'];
	$first_name = htmlentities($row['first_name']);
	$middle_name = htmlentities($row['middle_name']);
	$last_name = htmlentities($row['last_name']);
	$email = strtolower($row['email']);
	$tel = $row['campus_phone'];
	$fax = $row['fax_number'];
	$rank = $row['rank'];
	$research_interest_title = htmlentities($row['research_interest_title']);
	$research_interest_description = ltrim($row['research_interest_description']);
	$personal_statement = str_replace( array( "\r" , "\n" ) ,'' , htmlentities($row['personal_statement'], ENT_QUOTES));
	$address1 = $row['address1'];
	$address2 = $row['address2'];
	$city = $row['city'];
	$state = $row['state'];
	$postal_code = $row['postal_code'];
	$personalweburl = $row['personal_website_url'];
	$institution = $row['institution_id'];
	$takesundergrads = max($mentor_volunteers = $row['mentor_volunteers'],$mentor_payees = $row['mentor_payees'],$mentor_credit = $row['mentor_credit']);
	$uvasomfaculty .= '"'.$id.'",';
	$uvasomfaculty .= '"'.$username.'",';
	$uvasomfaculty .= '"'.$institution.'",';
	$uvasomfaculty .= '"'.$first_name.'",';
	$uvasomfaculty .= '"'.$middle_name.'",';
	$uvasomfaculty .= '"'.$last_name.'",';
	$uvasomfaculty .= '"'.$email.'",';
	$uvasomfaculty .= '"'.$tel.'",';
	$uvasomfaculty .= '"'.$fax.'",';
	$uvasomfaculty .= '"'.$rank.'",';
	$uvasomfaculty .= '"'.utf8_decode($research_interest_title).'",';
	$uvasomfaculty .= '"'.utf8_decode($research_interest_description).'",';
	$uvasomfaculty .= '"'.$personal_statement.'",';
	$uvasomfaculty .= '"'.$address1.'",';
	$uvasomfaculty .= '"'.$address2.'",';
	$uvasomfaculty .= '"'.$city.'",';
	$uvasomfaculty .= '"'.$state.'",';
	$uvasomfaculty .= '"'.$postal_code.'",';
	$uvasomfaculty .= '"'.$personalweburl.'",';
	$uvasomfaculty .= '"'.$takesundergrads.'",'."\r\n";
	}
//update data file
uvasomcurvfiles_update('fac');
mysqli_close($curvConn);
uvasomcurvfiles_delete('fac');
?>
