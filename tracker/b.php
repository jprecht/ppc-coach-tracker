<?php
extract($_REQUEST);
include("config.php");
$dbh = mysql_connect ($db_location, $username, $password); 
mysql_select_db ($database);
// select all the rotations in descending order based on the "b" variable
$sql = "SELECT * FROM `rotations` WHERE `rotation_id` = '$b' AND last = 'show'";
$result = mysql_query($sql);
$the_value = mysql_fetch_array($result);
// if no result, just take the first one in the rotation as default
if($the_value['offer_id'] == ""){
	$sql6 = "SELECT * FROM `rotations` WHERE `rotation_id` = '$b' AND order = 1";
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
if($r_1!=1){ // this means it's not one offer in the rotation
	// update that one with last = "this one"
	$sql2 = "UPDATE `rotations` SET `last` = 'this one' WHERE `id` = '$id'";
	$result2 = mysql_query($sql2);
	// figure out which offer will be next based on the order and number of offers
	if($order == $number_of_offers){
		$order = 1;
	}else{
		$order++;
	}
	// update the rotations table, set last = show where the order and rotation id match
	$sql3 = "UPDATE `rotations` SET `last` = 'show' WHERE `order` = '$order' AND `rotation_id` = '$b'";
	$result3 = mysql_query($sql3);
}
// grab the offer information
$sql4 = "SELECT * FROM `offers` WHERE `id` = '$offer_id'";
$result4 = mysql_query($sql4);
$val = mysql_fetch_array($result4);
$network_id = $val['network_id'];
$link = $val['aff_link'].$a;
$network_name = $val['network_name'];
$network_offer = $val['network_offer'];
$network_payout = $val['network_payout'];
// update the kw_log table
$timestamp_left = time();
$sql5 = "UPDATE `kw_log` SET `rotation_name`='$rotation_name', `network_name` = '$network_name', `network_offer` = '$network_offer', `network_payout` = '$network_payout', `timestamp_left`='$timestamp_left' WHERE `id` = '$a'";
$result5 = mysql_query($sql5);
//echo "SQL5: ".$sql5."<BR>";
header( "Location: $link") ;
?>
