<?php
include("config.php");
/* NEW URL: use with content generator v.2
http://www.ppc-coach.com/tracker/a.php?campaign={campaign}&adgroup={adgroup}&keyword={keyword}&match_type={match_type}&ad={creative}&website={placement}&network=content&ppcengine=adwords&z=4
*/
extract($_REQUEST);
//
// take all incoming variables, add some internal ones and insert the record into a mysql database
//
$campaign = urldecode($campaign);
$network = urldecode($network);
$ppcengine = urldecode($ppcengine);
$adgroup = urldecode($adgroup);
$keyword = urldecode($keyword);
$match_type = urldecode($match_type);
$referer = $_SERVER['HTTP_REFERER'];
$timestamp = time();
$browser = $_SERVER['HTTP_USER_AGENT'];

$ip_address = $_SERVER['REMOTE_ADDR'];
$query_string = $_SERVER['QUERY_STRING'];

// remove all whitespaces

$campaign = trim($campaign);
$network = trim($network);
$ppcengine = trim($ppcengine);
$adgroup = trim($adgroup);
$keyword = trim($keyword);
$match_type = trim($match_type);

//
// connect to the db
//
$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ($database);
//
//SELECT table_a.*, IF(table_a.id = table_b.sid,'yes','no') AS in_b FROM table_a LEFT JOIN table_b ON table_a.id = table_b.sid;
//
// insert the data into the logging table
//
if($browser != 'AdsBot-Google (+http://www.google.com/adsbot.html)'){
	$sql = "INSERT INTO `kw_log` (`id`,`adgroup` ,`keyword` ,`match_type` ,`ad` ,`website` ,`direct_link` ,`referer` ,`timestamp` ,`browser` ,`ip_address` ,`query_string` ,`aff_link`, `network`, `clicks`, `campaign`, `source`)VALUES (NULL , '$adgroup', '$keyword', '$match_type', '$ad', '$website', '$direct_link', '$referer', '$timestamp', '$browser', '$ip_address', '$query_string', '$aff_link', '$network', '1', '$campaign', '$source')";
	$result = mysql_query($sql);
	$hidden_id = mysql_insert_id();
}
//
// grab the id of the record and if a direct link, send it to the offer, if not use the variable $hidden_id on your landing page for the affiliate link subid
//

// New Landing Page Rotation Code Start...

// split the z variable to see if it's a lp rotation or not.
if(preg_match("/r/i", $z)){
	// it's a landing page rotation
	
	// strip out the r
	$z = str_replace("r", "", $z);
	
	// start landing page rotation sorting

		$sql = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$z' AND last = 'show'";
		$result = mysql_query($sql);
		$the_value = mysql_fetch_array($result);
		// if no result, just take the first one in the rotation as default
		if($the_value['offer_id'] == ""){
			$sql6 = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$z' AND order = 1";
			$result6 = mysql_query($sql6);
			$the_value = mysql_fetch_array($result6);
			$offer_id = $the_value['offer_id'];
			$order = $the_value['order'];
			$number_of_offers = $the_value['number_of_offers'];
			$rotation_name = $the_value['rotation_name'];
			$id = $the_value['id'];
		}else{
			// here a 'show' was found so show it
			$offer_id = $the_value['offer_id'];
			$order = $the_value['order'];
			$number_of_offers = $the_value['number_of_offers'];
			$rotation_name = $the_value['rotation_name'];
			$id = $the_value['id'];
		}
			
			// update that one with last = "this one"
			$sql2 = "UPDATE `lp_rotations` SET `last` = 'this one' WHERE `id` = '$id'";
			$result2 = mysql_query($sql2);
			// figure out which offer will be next based on the order and number of offers
			if($order == $number_of_offers){
				$order = 1;
			}else{
				$order++;
			}
			// update the rotations table, set last = show where the order and rotation id match
			$sql3 = "UPDATE `lp_rotations` SET `last` = 'show' WHERE `order` = '$order' AND `rotation_id` = '$z'";
			$result3 = mysql_query($sql3);
		

	// end landing page rotation sorting

	$sql3 = "SELECT `link` FROM `landing_pages` WHERE `id` = '$offer_id'";
	$result3 = mysql_query($sql3);
	$az = mysql_fetch_array($result3);

	$mystring = $az['link'];
	$findme   = '?';
	$pos = strpos($mystring, $findme);
	// Note our use of ===.  Simply == would not work as expected
	// because the position of 'a' was the 0th (first) character.
	if ($pos === false) {
		//echo "The string '$findme' was not found in the string '$mystring'";
		$url = $az['link']."?a=".$hidden_id."&b=".$b."&c1=".$c1;
	} else {
		//echo "The string '$findme' was found in the string '$mystring'";
		//echo " and exists at position $pos";
		$url = $az['link']."&a=".$hidden_id."&b=".$b."&c1=".$c1;
	}

	// update the click record with this landing page id and timestamp for the landing page click

	header( "Location: $url" ) ;
	
}else{
	// it's not a landing page rotation

	if($z > 0 AND $b == 0){ // Here it's a direct link going right to the single offer
		//echo "B is: ".$b."<BR>";
		// for ppv use this link:
		// http://ppc-coach.com/tracker/a-new.php?campaign={campaign}&keyword={keyword}&source=mediatraffic&z=23&b=0
		// there is no rotation it's just one offer straight to the merchant
		$sql2 = "SELECT * FROM `offers` WHERE `id` = '$z'";
		//	echo "SELECT * FROM `offers` WHERE `id` = '$z'<BR>";
		$result2 = mysql_query($sql2);
		$az = mysql_fetch_array($result2);
		$url = $az['aff_link'].$hidden_id;
		// update the kw_log file
		// update the kw_log table
		$timestamp_left = time();
		$sql5 = "UPDATE `kw_log` SET `rotation_name`='no_rotation', `network_name` = '$az[network_name]', `network_offer` = '$az[network_offer]', `network_payout` = '$az[network_payout]', `timestamp_left`='$timestamp_left' WHERE `id` = '$hidden_id'";
		$result5 = mysql_query($sql5);
		header( "Location: $url" ) ;
	}
	elseif($z == 0 AND $b > 0){ // Direct link rotation to merchant offers
		$a = $hidden_id;
		include("b.php");
	}
	else{ // Here it's a landing page using a rotation
		// send it to the landing page
		$sql3 = "SELECT `link` FROM `landing_pages` WHERE `id` = '$z'";
		$result3 = mysql_query($sql3);
		$az = mysql_fetch_array($result3);
		$mystring = $az['link'];
		$findme   = '?';
		$pos = strpos($mystring, $findme);
		// Note our use of ===.  Simply == would not work as expected
		// because the position of 'a' was the 0th (first) character.
		if ($pos === false) {
			//echo "The string '$findme' was not found in the string '$mystring'";
			$url = $az['link']."?a=".$hidden_id."&b=".$b."&c1=".$c1;
		} else {
			//echo "The string '$findme' was found in the string '$mystring'";
			//echo " and exists at position $pos";
			$url = $az['link']."&a=".$hidden_id."&b=".$b."&c1=".$c1;
		}
		header( "Location: $url" ) ;
	}

} // end if it's not a lp rotation
?>