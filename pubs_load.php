<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
$curvid = $_GET['facultyid'];
$curvUrl = 'http://SOMWEB:jauntyclipboardguy2013@somcurveweb.eservices.virginia.edu/display_service/users/'.$curvid.'/publications.xml';
//echo $curvid;
//echo '<h4 class="publications">Selected Publications</h4>'."\n";
//echo '	<div class="publications" style="background-image:url(/sharedassets/images/ajax-loader_large.gif);background-repeat:no-repeat;background-position:center 30px;overflow:hidden;min-height:150px;">'."\n";
//$curvid = '2';
//step1
$ch = curl_init(); 
//step2
//curl_setopt($cSession,CURLOPT_URL,'http://bims:bims4wdc@somcurveweb.eservices.virginia.edu/display_service/users/'.$curvid.'/publications.xml');
curl_setopt($ch, CURLOPT_URL, $curvUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$xml = curl_exec ($ch);
curl_close ($ch);
if (@simplexml_load_string($xml)) {
echo '		<div class="publications_loaded" style="background-image:none;background-color:#fff;opacity:1.0;min-height:150px;">'."\n";
//step5
//if(empty($pubs)) {
	//echo '<div class="publications_load">'."\n";
$xml2 = simplexml_load_file($curvUrl);
$baseObject = $xml2->getName();

	//make an array from the data in the file
	$pubs = array();
	if ($baseObject === 'objects') {
		foreach($xml2->object as $p)
		{
			$pubs[] = $p;
		}
	}
	if ($baseObject === 'articles') {
		foreach($xml2->article as $p)
		{
			$pubs[] = $p;
		}
	}
	if ($baseObject === 'abstracts') {
		foreach($xml2->abstract as $p)
		{
			$pubs[] = $p;
		}
	}
	//if there is no data in the array, return a message to the browser
	//otherwise, apply the sort function to the array
	usort($pubs, "cmp");
	// $pubs is now sorted by the dates in descending order
	// output publication list
		  foreach($pubs as $p) {
		echo "		<p>";
			foreach($p->children() as $authors) {
			  foreach($authors->children() as $author) {
				echo $author->{'last-name'}." ".$author->{'first-name'};
				if(!empty($author->{'middle-initial'}))
				{ 
				echo $author->{'middle-initial'};
				}
				echo ", ";
				;
			  }	
		
			}
			echo $p->title.", ";
			echo substr($p->{'date'},0,4)."; ";
			echo $p->publisher.". ";
			echo $p->volume;
			echo "(".$p->issue.") ";
			echo $p->pages.". ";
			if ($baseObject != 'abstracts'){
			echo '<a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$p->externalID.'" target="_blank">PMID: '.$p->externalID.'</a>';
				if(!empty($p->pmcid))
			{ 
			echo ' | <a href="http://www.ncbi.nlm.nih.gov/pmc/articles/'.$p->pmcid.'" target="_blank">PMCID: '.$p->pmcid.'</a>';
			}
			//Iterate through the authors
			echo "		</p>";
		  }
	
			echo '	<div class="clearfix faculty"></div>'."\n";
			}
		}
		if (empty($xml2) || ($baseObject == 'nil-classes') ||($baseObject == ''))
{
			echo '		<div class="publications_loaded" style="background-image:none;background-color:#fff;opacity:1.0;min-height:150px;">'."\n";
			echo '			<p>No publications on file for this individual.</p>'."\n";
		}
		//echo '		</div>'."\n";
		echo '	</div>'."\n";
//sort function to order by date in descending order
function cmp($a, $b){
    if ((int)$a->date[0] == (int)$b->date[0]) {
        return 0;
    }
    return ((int)$a->date[0] > (int)$b->date[0]) ? -1 : 1;
}
//
?>
