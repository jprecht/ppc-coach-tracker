<?php
include("config.php");
$current_menu_item = "manager";
include("header.php");
	//echo "TOTAL RECORDS: ".$total_records."<BR>";
	//echo "SQL: ".$sqll."<BR>";
if($first_page){
	$offset = 0;
	$start = 0;
	$show_per_page = $old_show_per_page;
}
if($next_page){	
	$offset += 1;
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
}
if($previous_page){
	$offset = $offset - 1;;
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
}
if($last_page){

	$ot = $total_records/$old_show_per_page;
	$offset = floor($ot);
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
	
}
/*
if($rows_entered){
	$offset = ($total_records/$old_show_per_page);
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
}
*/
// default values if none are set
if($offset==""){
	$offset=0;
}
if($start==""){
	$start=0;
}
if($show_per_page==""){
	$show_per_page=50;
}
?>
<div id="content-wrap">
	<div id="content">
		<div style="width:860;align:center;">
<!-- content goes here -->
<CENTER>
<h1 style="align:center;">Manager</h1>
<?php
manager_nav_menu();
if($action == "offers"){
	switch($order_by){
	case "id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "network_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "aff_link":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "network_name":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "network_offer":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "network_payout":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	}
	if($order_by == ""){
		$order_by = "id";
	}
	if($asc_desc == ""){
		$ad = "DESC";
	}
	?>

	<center><h1>Add A New Offer</h1></center><br>
	<?php
	if($submit == "Add Offer"){
		// insert it into the db
		$network_offer = str_replace("'", "", $network_offer);
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "INSERT INTO `offers` (`id` ,`network_id` ,`aff_link` ,`network_name` ,`network_offer` ,`network_payout`)VALUES (NULL , '$network_id', '$aff_link', '$network_name','$network_offer', '$network_payout')";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Submit was succesful";
		}
	}
		?>
	<center><?php echo $msg?>
	<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
	<tr>
		<td width="150" align="right">Network Id :</td>
		<td><input type="text" name="network_id" class="input_field" value=""></td>
	</tr>
	<tr>
		<td align="right">Affiliate Link :</td>
		<td><input type="text" name="aff_link" class="input_field" value=""></td>
	</tr>
	<tr>
		<td align="right">Network Name :</td>
		<td><input type="text" name="network_name" class="input_field" value=""></td>
	</tr>
	<tr>
		<td align="right">Network Offer :</td>
		<td><input type="text" name="network_offer" class="input_field" value=""></td>
	</tr>
	<tr>
		<td align="right">Network Payout :</td>
		<td><input type="text" name="network_payout" class="input_field" value=""></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="action" value="offers"><input type="submit" name="submit" value="Add Offer"></td>
	</tr>
		<tr>
		<td colspan="2">CAUTION: Do not use single quotes anywhere in the form above, if you do the tracker will not function correctly.</td>
	</tr>
	</table>
	</form>
	</center>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sqll = "SELECT count(DISTINCT(`network_offer`)) as total_records  FROM `offers`";
	$resl = @mysql_query($sqll);
	$value = @mysql_fetch_array($resl);
	$total_records = @$value[total_records];
	page_nav_menu_mgr($start, $show_per_page, $offset, $action, $order_by, $asc_desc, $total_records);
	?>
	<table>
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="manager.php?action=offers&order_by=id&asc_desc=<?php echo $ad?>">Id</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=offers&order_by=network_id&asc_desc=<?php echo $ad?>">Network Id</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=offers&order_by=aff_link&asc_desc=<?php echo $ad?>">Affiliate Link</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=offers&order_by=network_name&asc_desc=<?php echo $ad?>">Network Name</a></th>					
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=offers&order_by=network_offer&asc_desc=<?php echo $ad?>">Network Offer</a></th>		
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=offers&order_by=network_payout&asc_desc=<?php echo $ad?>">Network Payout</a></th>
		<th style="background-color: #9BCE00" height="20" colspan="3">Action</th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sql = "SELECT * FROM `offers` ORDER BY `$order_by` $asc_desc LIMIT $start , $show_per_page";
	$row = "row-b";
	$result = mysql_query($sql);
	while ($val = mysql_fetch_array($result)){
		if($row == "row-a"){
			$row = "row-b";
			$input_color = "input_color_b";
		}
		elseif($row == "row-b"){
			$row = "row-a";
			$input_color = "input_color_a";
		}
	?>
	<tr class="<?php echo $row?>">
		<td align="right"><?php echo $val['id']?></td>
		<td align="right"><?php echo $val['network_id']?></td>
		<td align="right"><?php echo $val['aff_link']?></td>
		<td align="right"><?php echo $val['network_name']?></td>
		<td align="right"><?php echo $val['network_offer']?></td>
		<td align="right"><?php echo $val['network_payout']?></td>
		<td align="right"><a href="manager.php?action=edit_offer&id=<?php echo $val['id']?>">Edit</a></td>
		<td align="right"><a href="manager.php?action=del_offer&id=<?php echo $val['id']?>">Delete</a></td>
		<td align="right"><a href="<?php echo $val['aff_link']?>" target="_blank">Test Link</a></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="9" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
}
elseif($action == "edit_offer"){
?>
	<center><h1>Edit Offer</h1></center><br>
	<?php
	if($submit == "Edit Offer"){
		// insert it into the db
		$network_offer = str_replace("'", "", $network_offer);
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "UPDATE `offers` SET `network_id` = '$network_id', `aff_link` = '$aff_link', `network_name` = '$network_name', `network_offer` = '$network_offer', `network_payout` = '$network_payout' WHERE `id` = '$id'";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Edit was succesful";
			echo $msg;
		}
	}else{
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "SELECT * FROM `offers` WHERE `id` = '$id'";
		$result = mysql_query($sql);
		$val = mysql_fetch_array($result);
	?>
	<center><?php echo $msg?>
	<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
	<tr>
		<td width="150" align="right">Network Id :</td>
		<td><input type="text" name="network_id" class="input_field" value="<?php echo $val['network_id'];?>"></td>
	</tr>
	<tr>
		<td align="right">Affiliate Link :</td>
		<td><input type="text" name="aff_link" class="input_field" value="<?php echo $val['aff_link'];?>"></td>
	</tr>
	<tr>
		<td align="right">Network Name :</td>
		<td><input type="text" name="network_name" class="input_field" value="<?php echo $val['network_name'];?>"></td>
	</tr>
	<tr>
		<td align="right">Network Offer :</td>
		<td><input type="text" name="network_offer" class="input_field" value="<?php echo $val['network_offer'];?>"></td>
	</tr>
	<tr>
		<td align="right">Network Payout :</td>
		<td><input type="text" name="network_payout" class="input_field" value="<?php echo $val['network_payout'];?>"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="action" value="edit_offer"><input type="submit" name="submit" value="Edit Offer"></td>
	</tr>
	<tr>
		<td colspan="2">CAUTION: Do not use single quotes anywhere in the form above, if you do the tracker will not function correctly.</td>
	</tr>
	</table>
	<input type="hidden" name="id" value="<?php echo $id?>">
	</form>
	</center>
	<?php
	}
}
elseif($action == "del_offer"){
	?>
	<center><h1>Delete Offer</h1></center><br>
	<?php
	// delete it from the db
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sql = "DELETE FROM `offers` WHERE `id` = '$id'";
	$result = mysql_query($sql);
	if($result==1){
		$msg = "Delete was succesful";
		echo $msg;
	}	
} // end of offers section ######################################################################################################################################################################
elseif($action == "landing_pages"){
	switch($order_by){
	case "id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "link":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "nickname":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "timestamp":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	}
	if($order_by == ""){
		$order_by = "id";
	}
	if($asc_desc == ""){
		$ad = "DESC";
	}
	?>

	<center><h1>Add A New Landing Page</h1></center><br>
	<?php
	if($submit == "Add Landing Page"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "INSERT INTO `landing_pages` (`id` ,`link` ,`nickname` ,`timestamp`)VALUES (NULL , '$link', '$nickname', NOW())";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Submit was succesful";
		}
	}
		?>
	<center><?php echo $msg?>
	<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
	<tr>
		<td width="150" align="right">Nickname :</td>
		<td><input type="text" name="nickname" class="input_field" value=""></td>
	</tr>
	<tr>
		<td align="right">Link :</td>
		<td><input type="text" name="link" class="input_field" value=""></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="action" value="landing_pages"><input type="submit" name="submit" value="Add Landing Page"></td>
	</tr>
	</table>
	</form>
	</center>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sqll = "SELECT count(DISTINCT(`link`)) as total_records  FROM `landing_pages`";
	$resl = @mysql_query($sqll);
	$value = @mysql_fetch_array($resl);
	$total_records = @$value[total_records];
	page_nav_menu_mgr($start, $show_per_page, $offset, $action, $order_by, $asc_desc, $total_records);
	?>
	<table>
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="manager.php?action=landing_pages&order_by=id&asc_desc=<?php echo $ad?>">Id</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=landing_pages&order_by=nickname&asc_desc=<?php echo $ad?>">Nickname</a></th>	
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=landing_pages&order_by=link&asc_desc=<?php echo $ad?>">Link</a></th>	
		<th style="background-color: #9BCE00" height="20" colspan="3">Action</th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	//$start = 0;
	$end = 30;
	$sql = "SELECT * FROM `landing_pages` ORDER BY `$order_by` $asc_desc LIMIT $start , $show_per_page";
	$row = "row-b";
	$result = mysql_query($sql);
	while ($val = mysql_fetch_array($result)){
		if($row == "row-a"){
			$row = "row-b";
			$input_color = "input_color_b";
		}
		elseif($row == "row-b"){
			$row = "row-a";
			$input_color = "input_color_a";
		}
	?>
	<tr class="<?php echo $row?>">
		<td align="right"><?php echo $val['id']?></td>
		<td align="right"><?php echo $val['nickname']?></td>
		<td align="right"><?php echo $val['link']?></td>
		<td align="right"><a href="manager.php?action=edit_lp&id=<?php echo $val['id']?>">Edit</a></td>
		<td align="right"><a href="manager.php?action=del_lp&id=<?php echo $val['id']?>">Delete</a></td>
		<td align="right"><a href="<?php echo $val['link']?>" target="_blank">Test Link</a></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="9" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
}
elseif($action == "edit_lp"){
?>
	<center><h1>Edit Landing Page</h1></center><br>
	<?php
	if($submit == "Edit Offer"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "UPDATE `landing_pages` SET `nickname` = '$nickname', `link` = '$link' WHERE `id` = '$id'";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Edit was succesful";
			echo $msg;
		}
	}else{
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "SELECT * FROM `landing_pages` WHERE `id` = '$id'";
		$result = mysql_query($sql);
		$val = mysql_fetch_array($result);
	?>
	<center><?php echo $msg?>
	<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
	<tr>
		<td width="150" align="right">Nickname :</td>
		<td><input type="text" name="nickname" class="input_field" value="<?php echo $val['nickname'];?>"></td>
	</tr>
	<tr>
		<td align="right">Link :</td>
		<td><input type="text" name="link" class="input_field" value="<?php echo $val['link'];?>"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="action" value="edit_lp"><input type="submit" name="submit" value="Edit Offer"></td>
	</tr>
	</table>
	<input type="hidden" name="id" value="<?php echo $id?>">
	</form>
	</center>
	<?php
	}
}
elseif($action == "del_lp"){
	?>
	<center><h1>Delete Landing Page</h1></center><br>
	<?php
	// delete it from the db
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sql = "DELETE FROM `landing_pages` WHERE `id` = '$id'";
	$result = mysql_query($sql);
	if($result==1){
		$msg = "Delete was succesful";
		echo $msg;
	}	
}
elseif($action == "sync"){
	?>
	<h1 style="align:left;">Sync Campaign Names or Adgroup Names</h1>
	</div>
	<div style="width:760;align:left;">
	<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
	<p style="align:left;" class="gbox">If you ever decide to change the name of an adgroup or campaign, then you have to update all the camapigns and adgroup names here.  Otherwise the system will tread the edited campaign name or edited adgroup as a new one.</p>
	<?php
}
elseif($action == "rotation"){
	switch($order_by){
	case "id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "rotation_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "rotation_name":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "offer_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "next_offer_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "last":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	}
	if($order_by == ""){
		$order_by = "id";
	}
	if($asc_desc == ""){
		$ad = "DESC";
	}
	?>
	<h1 style="align:left;">Set Up Your Rotation Packages</h1>
	</div>
	<div style="width:760;align:left;">
	<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
	<p style="align:left;" class="gbox">You have your offers, now you want to rotate offers evenly so you can find the best one.  Set up new rotation packages here or edit an existing rotation package based on your numbers.</p>
	<center><h1>Add A New Rotation</h1></center><br>
	<?php
	if($submit == "Add Offers To Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		if($status == "waiting for offers"){
			for($x=1;$x<=$number_of_offers;$x++){
				if($x==1){
					$sql = "INSERT INTO `rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `last`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', 'show', '$number_of_offers', 'set')";		
				}else{
					$sql = "INSERT INTO `rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', '$number_of_offers', 'set')";
				}
				$result = mysql_query($sql);
			}
		}
		if($result==1){
			$msg = "Edit was succesful";
			echo $msg;
			// delete the old record
			$sql = "DELETE FROM `rotations` WHERE `rotation_id` = '$rotation_id' AND `status` = 'waiting for offers' LIMIT 1";		
			$result = mysql_query($sql);
		}
	}
	if($submit == "Add Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "INSERT INTO `rotations` (`id` ,`rotation_id` ,`rotation_name`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$number_of_offers', 'waiting for offers')";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "New rotation created successfully, go and edit it now.";
		}
		?>
		<center><h1>Edit Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$b = mysql_fetch_array($result2);
			$num = $b['number_of_offers'];
			$start = 1;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Offer</td>
		</tr>
		<?php
		for($z=1;$z<=$num;$z++){
		?>
		<tr>			
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					?>
					<option value="<?php echo $val['id'];?>"><?php echo $val['network_name']." :: ".$val['network_offer']." :: $".$val['network_payout'];?></option>
				<?php
				}
				?>
				</option>
				</select>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="rotation">
				<input type="hidden" name="rotation_id" value="<?php echo $b['rotation_id']?>">
				<input type="hidden" name="rotation_name" value="<?php echo $b['rotation_name']?>">
				<input type="hidden" name="number_of_offers" value="<?php echo $b['number_of_offers']?>">	
				<input type="hidden" name="status" value="<?php echo $b['status']?>">		
			<input type="submit" name="submit" value="Add Offers To Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}// end if "add rotation" is pushed
	else{
		?>
		<center><?php echo $msg?>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td width="150" align="right">Rotation ID :</td>
			<td><input type="text" name="rotation_id" class="input_field" value=""></td>
		</tr>
		<tr>
			<td align="right">Rotation Name :</td>
			<td><input type="text" name="rotation_name" class="input_field" value=""></td>
		</tr>
			<tr>
			<td align="right">Number of Offers :</td>
			<td><select name="number_of_offers" class="input_dropdown">
				<?php
				for($i=1;$i<101;$i++){
				?>
				<option value="<?php echo $i?>"><?php echo $i?></option>
				<?php
				}
				?>
				</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="action" value="rotation"><input type="submit" name="submit" value="Add Rotation"></td>
		</tr>
		</table>
		</form>
			<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sqll = "SELECT count(DISTINCT(`rotation_id`)) as total_records  FROM `rotations`";
	$resl = @mysql_query($sqll);
	$value = @mysql_fetch_array($resl);
	$total_records = @$value[total_records];
	page_nav_menu_mgr($start, $show_per_page, $offset, $action, $order_by, $asc_desc, $total_records);
	?>
		<table>
		<tr>
			<th class="first" style="background-color: #9BCE00"><a class="reports" href="manager.php?action=rotation&order_by=rotation_id&asc_desc=<?php echo $ad?>">Rotation Id</a></th>
			<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=rotation&order_by=rotation_name&asc_desc=<?php echo $ad?>">Rotation Name</a></th>	
			<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=rotation&order_by=&asc_desc=<?php echo $ad?>">Offers</a></th>	
			<th style="background-color: #9BCE00" height="20" colspan="2">Action</th>
		</tr>
		<?php
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		//$start = 0;
		//$end = 30;
		$sql = "SELECT rotation_id, rotation_name, number_of_offers FROM `rotations` GROUP BY `rotation_id` ORDER BY `$order_by` $asc_desc LIMIT $start , $show_per_page";

		//echo $sql;

		$row = "row-b";
		$result = mysql_query($sql);
		while ($val = mysql_fetch_array($result)){
			if($row == "row-a"){
				$row = "row-b";
				$input_color = "input_color_b";
			}
			elseif($row == "row-b"){
				$row = "row-a";
				$input_color = "input_color_a";
			}
		?>
		<tr class="<?php echo $row?>">
			<td align="right"><?php echo $val['rotation_id']?></td>
			<td align="right"><?php echo $val['rotation_name']?></td>
			<td align="right"><?php echo $val['number_of_offers']?></td>
			<td align="right"><a href="manager.php?action=edit_rotation&rotation_id=<?php echo $val['rotation_id']?>">Edit</a></td>
			<td align="right"><a href="manager.php?action=delete_rotation&rotation_id=<?php echo $val['rotation_id']?>">Delete</a></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="5" style="background-color: #000000">&nbsp;</td>
		</tr>
		</table>
		</CENTER>
		<?php
	}
}
elseif($action == "adding_more"){
	if($submit == "Add New Offers To Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		for($x=$start;$x<=$new_total_offers;$x++){
			$sql = "INSERT INTO `rotations` (`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES ('$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', '$new_total_offers', 'set')";
			//$sql = "INSERT INTO `rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$x', '$number_of_offers', 'set')";
			echo $sql."<BR>";
			$result = mysql_query($sql);
		}
		// now update all number_of_offers in this rotation id with the new number
		$sql = "UPDATE `rotations` SET `number_of_offers`= '$new_total_offers' WHERE `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		$sql = "UPDATE `rotations` SET `last`= '' WHERE `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		$sql = "UPDATE `rotations` SET `last`= 'show' WHERE `order` = '1' AND `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Additional Offers Successfully Added.";
			echo $msg;
		}
	}else{
	?>
		<center><h1>Add Offers To Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$b = mysql_fetch_array($result2);
			$number_of_offers = $b['number_of_offers'];
			$start = $number_of_offers+1;
			$new_total_offers = $number_of_offers + $additional_offers;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Offer</td>
		</tr>
		<?php
		for($z=$start;$z<=$new_total_offers;$z++){
		?>
		<tr>			
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					?>
					<option value="<?php echo $val['id'];?>"><?php echo $val['network_name']." :: ".$val['network_offer']." :: $".$val['network_payout'];?></option>
				<?php
				}
				?>
				</option>
				</select>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="adding_more">
				<input type="hidden" name="rotation_id" value="<?php echo $b['rotation_id']?>">
				<input type="hidden" name="rotation_name" value="<?php echo $b['rotation_name']?>">
				<input type="hidden" name="new_total_offers" value="<?php echo $new_total_offers?>">	
				<input type="hidden" name="start" value="<?php echo $start?>">		
			<input type="submit" name="submit" value="Add New Offers To Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}
}
elseif($action == "edit_rotation"){
	if($submit=="Edit Rotation"){
		$success=0;

		for($x=0;$x<=$number_of_offers;$x++){
			if($delete[$x]=="on"){
				//echo "DELETE[x] $delete[$x] $x<BR>";
				// We're deleting offer(s) from the package
				$deleted_one = "yes";
				$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
				mysql_select_db ($database);
				$sql = "DELETE FROM `rotations` WHERE `id` = '$id[$x]'";
				$result = mysql_query($sql);
				if($result==1){
					$delete++;
				}

			}
		}

		if($deleted_one == "yes"){
				//echo "UPDATE ALL OFFERS: ".$update_all_offers."<BR>";
				// now update the number of offers field
				$sql = "SELECT COUNT(`rotation_id`) AS new FROM `rotations` WHERE `rotation_id` = '$rotation_id'";
				//echo $sql."<BR>";
				$result = mysql_query($sql);
				$a = mysql_fetch_array($result);
				$new = $a['new'];
				$sql = "UPDATE `rotations` SET `number_of_offers` = '$new' WHERE `rotation_id`= '$rotation_id'";
				$result = mysql_query($sql);
				// select everything from that rotation_id make sure one has show on it
				$sql2 = "SELECT * FROM `rotations` WHERE `rotation_id` = '$rotation_id' ORDER BY `order` ASC";
				$result2 = mysql_query($sql2);
				$first = 1;
				while($aaa = mysql_fetch_array($result2)){
					if($first == 1){
						$sql = "UPDATE `rotations` SET `order` = '$first', `last` = 'show' WHERE `id` = '$aaa[id]'";
					}else{
						$sql = "UPDATE `rotations` SET `order` = '$first', `last` = 'this' WHERE `id` = '$aaa[id]'";
					}
					$result = mysql_query($sql);
					$first++;
				}
		}else{
			// Nothing was deleted
			for($x=1;$x<=$number_of_offers;$x++){
				$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
				mysql_select_db ($database);
				if($x==1){
					$sql = "UPDATE `rotations` SET `offer_id` = '$offer_id[$x]', `order` = '$order[$x]', `last` = 'show' WHERE `id` = '$id[$x]'";
				}else{
					$sql = "UPDATE `rotations` SET `offer_id` = '$offer_id[$x]', `order` = '$order[$x]' WHERE `id` = '$id[$x]'";
				}
				$result = mysql_query($sql);
				if($result==1){
					$success++;
				}
			}
		}
		if($add_more_offers == "on"){
		// show the form asking for how many and send the submit button to the insert area.
		?>
		<center>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">How many would you like to add to this rotation?
			<select name="additional_offers" class="input_dropdown">
				<?php
				for($i=1;$i<101;$i++){
				?>
				<option value="<?php echo $i?>"><?php echo $i?></option>
				<?php
				}
				?>
			</select>			
			</td>
		</tr>		
		<tr>
			<td>
				<input type="hidden" name="action" value="adding_more">
				<input type="hidden" name="number_of_offers" value="<?php echo $number_of_offers?>">	
				<input type="hidden" name="rotation_id" value="<?php echo $rotation_id?>">
			<input type="submit" name="submit" value="Add Offers"></td>
		</tr>
		</table>
		</form>
		</center>
		</table>
		</center>
		<?php
		include("footer.php");
		exit;
		}
		echo "Offers were successfully updated.<BR>";
	}else{
		?>
		<center><h1>Edit Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$count=1;
			while($b = mysql_fetch_array($result2)){
				$offer_ids[$count]=$b['offer_id'];
				$id[$count]=$b['id'];
				$num = $b['number_of_offers'];
				$rotation_id = $b['rotation_id'];
				$count++;
			}
			$start = 1;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Offer</td>
			<td align="left">Delete</td>
		</tr>
		<?php
		for($z=1;$z<=$num;$z++){
		?>
		<tr>		
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"><input type="hidden" name="id[<?php echo $z?>]" value="<?php echo $id[$z];?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `offers` ORDER BY `network_name` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					if($val['id']==$offer_ids[$z]){
						$selected = "on";
					}
					?>
					<option <?php if($selected=="on"){echo "selected";}?> value="<?php echo $val['id'];?>"><?php echo $val['network_name']." :: ".$val['network_offer']." :: $".$val['network_payout'];?></option>
				<?php
				$selected = "off";
				}
				?>
				</select>
			</td>
			<td align="left"><input type="checkbox" name="delete[<?php echo $z?>]"></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td align="left" colspan="2"><input type="checkbox" name="add_more_offers"> Would you like to add more offers to this rotation? (click for yes)</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="edit_rotation">
				<input type="hidden" name="number_of_offers" value="<?php echo $num;?>">	
				<input type="hidden" name="rotation_id" value="<?php echo $rotation_id?>">	
			<input type="submit" name="submit" value="Edit Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}
}
elseif($action == "delete_rotation"){
	?>
	<center><h1>Delete Rotation</h1></center><br>
	<?php
	// delete it from the db
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sql = "DELETE FROM `rotations` WHERE `rotation_id` = '$rotation_id'";
	$result = mysql_query($sql);
	if($result==1){
		$msg = "Delete was succesful";
		echo $msg;
	}	
}
elseif($action == "get_link"){
	if($submit){
		$the_link = $aurl.'/a.php?campaign='.urlencode($campaign)."&adgroup=".urlencode($adgroup)."&keyword=".urlencode($keyword)."&match_type=".urlencode($match_type)."&ad=".urlencode($ad)."&network=".urlencode($network)."&source=".urlencode($source)."&z=".urlencode($z_input)."&b=".urlencode($b_input);
		$the_link = str_replace('%7B','{',$the_link);
		$the_link = str_replace('%7D','}',$the_link);

	}
	if($campaign == ""){
		$campaign = "{campaign}";
	}
	if($adgroup == ""){
		$adgroup = "{adgroup}";
	}
	if($keyword == ""){
		$keyword = "{keyword}";
	}
	if($match_type == ""){
		$match_type = "{match_type}";
	}
	if($ad == ""){
		$ad = "{creative}";
	}
	if($network == ""){
		$network = "{network}";
	}
?>
	<center>
	<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table align="center" border="0" width="600" id="table1" cellpadding="5" bordercolor="#C0C0C0">
	<tr>
		<td colspan="2"><b>TIP:</b>Remember that adwords does NOT replace {campaign} with the real campaign name or {adgroup} with the real adgroup name.  You MUST either run this created link through the generators OR manually update them yourself.  Otherwise your cost data will not update correctly.</td>
	</tr>
	<tr>
		<td width="250" align="right">Tracking Script Url (include the http://:</td>
		<td><input type="text" name="aurl" class="input_field" value="<?php echo $aurl?>"></td>
	</tr>
	<tr>
		<td align="right">Campaign Name :</td>
		<td><input type="text" name="campaign" class="input_field" value="<?php echo $campaign?>"></td>
	</tr>
	<tr>
		<td align="right">Adgroup :</td>
		<td><input type="text" name="adgroup" class="input_field" value="<?php echo $adgroup?>"></td>
	</tr>
	<tr>
		<td align="right">Keyword :</td>
		<td><input type="text" name="keyword" class="input_field" value="<?php echo $keyword?>"></td>
	</tr>
	<tr>
		<td align="right">Match Type (broad, phrase or exact):</td>
		<td><input type="text" name="match_type" class="input_field" value="<?php echo $match_type?>"></td>
	</tr>
		<tr>
		<td align="right">Ad {creative} for adwords:</td>
		<td><input type="text" name="ad" class="input_field" value="<?php echo $ad?>"></td>
	</tr>
	<tr>
		<td align="right">Network (content or search):</td>
		<td><input type="text" name="network" class="input_field" value="<?php echo $network?>"></td>
	</tr>
	<tr>
		<td align="right">Source (adwords, yahoo or msn):</td>
		<td><input type="text" name="source" class="input_field" value="<?php echo $source?>"></td>
	</tr>
	<tr>
		<td align="right">Z Value (Landing Page ID) :</td>
		<td><input type="text" name="z_input" class="input_field" value="<?php echo $z_input?>"></td>
	</tr>
	<tr>
		<td align="right">B Value (Rotation Package ID) :</td>
		<td><input type="text" name="b_input" class="input_field" value="<?php echo $b_input?>"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="action" value="get_link"><input type="submit" name="submit" value="Create Link"></td>
	</tr>
	<tr>
		<td align="right">Link (ctrl-a to select, ctrl-c to copy):</td>
		<td><textarea class="input_textarea" style="border:1px solid black;" cols="30" rows="5" name="the_result" wrap="hard"><?php echo $the_link?></textarea></td>
	</tr>
	</table>
	</form>
	</center>
<?php
}
elseif($action == "subids"){
	// search for subid, allow mass editing of them
	if($submit == "Get Subids"){
		// show the subid edit form
		?>
		<h1 style="align:left;">Subid Manager</h1>
		</div>
		<div style="width:1200;align:left;">
		<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
		<p style="align:left;" class="gbox">Go ahead and edit any part of the record you need to using the form below.</p>
		<center><h1>Edit Subid(s)</h1></center><br>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" style="width:1200px" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Date Time</td>
			<td align="left">Campaign</td>
			<td align="left">Adgroup</td>
			<td align="left">Keyword</td>
			<td align="left">Match Type</td>
			<td align="left">Ad</td>
			<td align="left">Website</td>
			<td align="left">Referer</td>
			<td align="left">Browser</td>
			<td align="left">IP Address</td>
			<td align="left">Query String</td>
			<td align="left">Rotation Name</td>
			<td align="left">Network Name</td>
			<td align="left">Network Offer</td>
			<td align="left">Network Payout</td>
			<td align="left">Clicks</td>
			<td align="left">Leads</td>
			<td align="left">S/U</td>
			<td align="left">Payout</td>
			<td align="left">EPC</td>
			<td align="left">CPO</td>
			<td align="left">Revenue</td>
			<td align="left">Cost</td>
			<td align="left">Net</td>
			<td align="left">ROI</td>
			<!-- <td align="left">Cost Updated</td>
			<td align="left">Revenue Updated</td>
			<td align="left">All Updated</td> -->
		</tr>
		<?php
		$lines = array();
		$lines = explode("\r",$the_subids);
		$num = 0;
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		foreach($lines as $sids){
			$sql = "SELECT * FROM `kw_log` WHERE id='$sids'";
			$result = mysql_query($sql);
			while($val = mysql_fetch_array($result)){
				?>
				<tr>
					<td align="left"><input type="hidden" name="timestamp[<?php echo $num;?>]" value="<?php echo $val['timestamp'];?>"><input type="text" name="date" value="<?php echo date("d/m/y h:m:s",$val['timestamp']);?>"></td>
					<td align="left"><input type="text" name="campaign[<?php echo $num;?>]" value="<?php echo $val['campaign'];?>"></td>
					<td align="left"><input type="text" name="adgroup[<?php echo $num;?>]" value="<?php echo $val['adgroup'];?>"></td>
					<td align="left"><input type="text" name="keyword[<?php echo $num;?>]" value="<?php echo $val['keyword'];?>"></td>
					<td align="left"><input type="text" name="match_type[<?php echo $num;?>]" value="<?php echo $val['match_type'];?>"></td>
					<td align="left"><input type="text" name="ad[<?php echo $num;?>]" value="<?php echo $val['ad'];?>"></td>
					<td align="left"><input type="text" name="website[<?php echo $num;?>]" value="<?php echo $val['website'];?>"></td>
					<td align="left"><input type="text" name="referer[<?php echo $num;?>]" value="<?php echo $val['referer'];?>"></td>
					<td align="left"><input type="text" name="browser[<?php echo $num;?>]" value="<?php echo $val['browser'];?>"></td>
					<td align="left"><input type="text" name="ip_address[<?php echo $num;?>]" value="<?php echo $val['ip_address'];?>"></td>
					<td align="left"><input type="text" name="query_string[<?php echo $num;?>]" value="<?php echo $val['query_string'];?>"></td>
					<td align="left"><input type="text" name="rotation_name[<?php echo $num;?>]" value="<?php echo $val['rotation_name'];?>"></td>
					<td align="left"><input type="text" name="network_name[<?php echo $num;?>]" value="<?php echo $val['network_name'];?>"></td>
					<td align="left"><input type="text" name="network_offer[<?php echo $num;?>]" value="<?php echo $val['network_offer'];?>"></td>
					<td align="left"><input type="text" name="network_payout[<?php echo $num;?>]" value="<?php echo $val['network_payout'];?>"></td>
					<td align="left"><input type="text" name="clicks[<?php echo $num;?>]" value="<?php echo $val['clicks'];?>"></td>
					<td align="left"><input type="text" name="leads[<?php echo $num;?>]" value="<?php echo $val['leads'];?>"></td>
					<td align="left"><input type="text" name="s/u[<?php echo $num;?>]" value="<?php echo $val['s/u'];?>"></td>
					<td align="left"><input type="text" name="payout[<?php echo $num;?>]" value="<?php echo $val['payout'];?>"></td>
					<td align="left"><input type="text" name="epc[<?php echo $num;?>]" value="<?php echo $val['epc'];?>"></td>
					<td align="left"><input type="text" name="cpc[<?php echo $num;?>]" value="<?php echo $val['cpc'];?>"></td>
					<td align="left"><input type="text" name="revenue[<?php echo $num;?>]" value="<?php echo $val['revenue'];?>"></td>
					<td align="left"><input type="text" name="cost[<?php echo $num;?>]" value="<?php echo $val['cost'];?>"></td>
					<td align="left"><input type="text" name="net[<?php echo $num;?>]" value="<?php echo $val['net'];?>"></td>
					<td align="left"><input type="text" name="roi[<?php echo $num;?>]" value="<?php echo $val['roi'];?>"></td>
					<!-- <td align="left"><input type="hidden" name="cost_updated[<?php echo $num;?>]" value="<?php echo $val['cost_updated'];?>"><input type="text" name="ccost_updated" value="<?php echo date("d/m/y h:m:s",$val['cost_updated']);?>"></td>
					<td align="left"><input type="hidden" name="revenue_updated[<?php echo $num;?>]" value="<?php echo $val['revenue_updated'];?>"><input type="text" name="rrevenue_updated" value="<?php echo date("d/m/y h:m:s",$val['revenue_updated']);?>"></td>
					<td align="left"><input type="hidden" name="all_updated[<?php echo $num;?>]" value="<?php echo $val['all_updated'];?>"><input type="text" name="aall_updated" value="<?php echo date("d/m/y h:m:s",$val['all_updated']);?>"></td> -->
				</tr><input type="hidden" name="sid[<?php echo $num;?>]" value=<?php echo $val['id'];?>>
				<?php
			}// end while loop
		}// end foreach loop
		?>
		<tr>
			<td colspan="25">
				<input type="hidden" name="action" value="subids">
				<input type="hidden" name="num" value="<?php echo $num;?>">	
				<input type="submit" name="submit" value="Edit Subids">
			</td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}elseif($submit == "Edit Subids"){
		// run the update of the subids
		$success = 0;
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		for($i=0;$i<=$num;$i++){
			$sql = "UPDATE kw_log SET timestamp = 'timestamp[$num]', campaign = '$campaign[$num]', adgroup = '$adgroup[$num]', keyword = 
			'$keyword[$num]', match_type = '$match_type[$num]', ad = '$ad[$num]', website = '$website[$num]', referer = '$referer[$num]', browser = '$browser[$num]', ip_address = '$ip_address[$num]', query_string = '$query_string[$num]', rotation_name = '$rotation_name[$num]', network_name = '$network_name[$num]', network_offer = '$network_offer[$num]', network_payout = '$network_payout[$num]', clicks = '$clicks[$num]', leads = '$leads[$num]', `s/u` = '$s/u[$num]', payout = '$payout[$num]', epc = '$epc[$num]', cpc = '$cpc[$num]', revenue = '$revenue[$num]', cost = '$cost[$num]', net = '$net[$num]', roi = '$roi[$num]', cost_updated = 'cost_updated[$num]', revenue_updated = 'revenue_updated[$num]', all_updated = 'all_updated[$num]' WHERE id='$sid[$num]'";
			$result = mysql_query($sql);
			if(mysql_affected_rows() > 0){
				$success++;
			}
		}
		echo $success." records updated successfully.<BR>";
	}else{
		?>
		<h1 style="align:left;">Subid Manager</h1>
		</div>
		<div style="width:760;align:left;">
		<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
		<p style="align:left;" class="gbox">Enter your subids, (one per line), in the textarea below.  You will then be able to edit any part of the record you want.</p>
		<center><h1>Enter Subid(s)</h1></center><br>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="600" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td width="250" align="right">Enter Subids:</td>
			<td><textarea class="input_textarea" style="border:1px solid black;" cols="30" rows="5" name="the_subids" wrap="hard"><?php echo $the_subids?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="hidden" name="action" value="subids"><input type="submit" name="submit" value="Get Subids"></td>
		</tr>
		</table>
		</center>
		<?php
	}
}

// New Landing Page Rotation Code Start...

elseif($action == "lp_rotation"){
	switch($order_by){
	case "id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "rotation_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "rotation_name":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "offer_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "next_offer_id":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "last":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	}
	if($order_by == ""){
		$order_by = "id";
	}
	if($asc_desc == ""){
		$ad = "DESC";
	}
	?>
	<h1 style="align:left;">Set Up Your Landing Page Rotation Packages</h1>
	</div>
	<div style="width:760;align:left;">
	<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
	<p style="align:left;" class="gbox">You have your landing pages, now you want to rotate them evenly so you can find the best one.  Set up new landing page packages here or edit an existing landing page rotation package based on your numbers.</p>
	<center><h1>Add A New Landing Page Rotation</h1></center><br>
	<?php
	if($submit == "Add Landing Pages To Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		if($status == "waiting for landing pages"){
			for($x=1;$x<=$number_of_offers;$x++){
				if($x==1){
					$sql = "INSERT INTO `lp_rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `last`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', 'show', '$number_of_offers', 'set')";		
				}else{
					$sql = "INSERT INTO `lp_rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', '$number_of_offers', 'set')";
				}
				$result = mysql_query($sql);
			}
		}
		if($result==1){
			$msg = "Edit was succesful";
			echo $msg;
			// delete the old record
			$sql = "DELETE FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id' AND `status` = 'waiting for landing pages' LIMIT 1";		
			$result = mysql_query($sql);
		}
	}
	if($submit == "Add Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		$sql = "INSERT INTO `lp_rotations` (`id` ,`rotation_id` ,`rotation_name`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$number_of_offers', 'waiting for landing pages')";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "New rotation created successfully, go and edit it now.";
		}
		?>
		<center><h1>Edit Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$b = mysql_fetch_array($result2);
			$num = $b['number_of_offers'];
			$start = 1;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Landing Page</td>
		</tr>
		<?php
		for($z=1;$z<=$num;$z++){
		?>
		<tr>			
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					?>
					<option value="<?php echo $val['id'];?>"><?php echo $val['nickname']." :: ".$val['link'];?></option>
				<?php
				}
				?>
				</option>
				</select>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="lp_rotation">
				<input type="hidden" name="rotation_id" value="<?php echo $b['rotation_id']?>">
				<input type="hidden" name="rotation_name" value="<?php echo $b['rotation_name']?>">
				<input type="hidden" name="number_of_offers" value="<?php echo $b['number_of_offers']?>">	
				<input type="hidden" name="status" value="<?php echo $b['status']?>">		
			<input type="submit" name="submit" value="Add Landing Pages To Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}// end if "add rotation" is pushed
	else{
		?>
		<center><?php echo $msg?>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td width="150" align="right">Rotation ID :</td>
			<td><input type="text" name="rotation_id" class="input_field" value=""></td>
		</tr>
		<tr>
			<td align="right">Rotation Name :</td>
			<td><input type="text" name="rotation_name" class="input_field" value=""></td>
		</tr>
			<tr>
			<td align="right">Number of Landing Pages :</td>
			<td><select name="number_of_offers" class="input_dropdown">
				<?php
				for($i=1;$i<101;$i++){
				?>
				<option value="<?php echo $i?>"><?php echo $i?></option>
				<?php
				}
				?>
				</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="action" value="lp_rotation"><input type="submit" name="submit" value="Add Rotation"></td>
		</tr>
		</table>
		</form>
			<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sqll = "SELECT count(DISTINCT(`rotation_id`)) as total_records  FROM `lp_rotations`";
	$resl = @mysql_query($sqll);
	$value = @mysql_fetch_array($resl);
	$total_records = @$value[total_records];
	page_nav_menu_mgr($start, $show_per_page, $offset, $action, $order_by, $asc_desc, $total_records);
	?>
		<table>
		<tr>
			<th class="first" style="background-color: #9BCE00"><a class="reports" href="manager.php?action=rotation&order_by=rotation_id&asc_desc=<?php echo $ad?>">Rotation Id</a></th>
			<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=rotation&order_by=rotation_name&asc_desc=<?php echo $ad?>">Rotation Name</a></th>	
			<th style="background-color: #9BCE00" height="20"><a class="reports" href="manager.php?action=rotation&order_by=&asc_desc=<?php echo $ad?>">Landing Pages</a></th>	
			<th style="background-color: #9BCE00" height="20" colspan="2">Action</th>
		</tr>
		<?php
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		//$start = 0;
		//$end = 30;
		$sql = "SELECT rotation_id, rotation_name, number_of_offers FROM `lp_rotations` GROUP BY `rotation_id` ORDER BY `$order_by` $asc_desc LIMIT $start , $show_per_page";

		//echo $sql;

		$row = "row-b";
		$result = mysql_query($sql);
		while ($val = mysql_fetch_array($result)){
			if($row == "row-a"){
				$row = "row-b";
				$input_color = "input_color_b";
			}
			elseif($row == "row-b"){
				$row = "row-a";
				$input_color = "input_color_a";
			}
		?>
		<tr class="<?php echo $row?>">
			<td align="right"><?php echo $val['rotation_id']?></td>
			<td align="right"><?php echo $val['rotation_name']?></td>
			<td align="right"><?php echo $val['number_of_offers']?></td>
			<td align="right"><a href="manager.php?action=lp_edit_rotation&rotation_id=<?php echo $val['rotation_id']?>">Edit</a></td>
			<td align="right"><a href="manager.php?action=lp_delete_rotation&rotation_id=<?php echo $val['rotation_id']?>">Delete</a></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="5" style="background-color: #000000">&nbsp;</td>
		</tr>
		</table>
		</CENTER>
		<?php
	}
}
elseif($action == "lp_adding_more"){
	if($submit == "Add New Landing Pages To Rotation"){
		// insert it into the db
		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);
		for($x=$start;$x<=$new_total_offers;$x++){
			$sql = "INSERT INTO `lp_rotations` (`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES ('$rotation_id', '$rotation_name', '$offer_id[$x]', '$order[$x]', '$new_total_offers', 'set')";
			//$sql = "INSERT INTO `lp_rotations` (`id` ,`rotation_id` ,`rotation_name`, `offer_id`, `order`, `number_of_offers`, `status`)VALUES (NULL , '$rotation_id', '$rotation_name', '$offer_id[$x]', '$x', '$number_of_offers', 'set')";
			echo $sql."<BR>";
			$result = mysql_query($sql);
		}
		// now update all number_of_offers in this rotation id with the new number
		$sql = "UPDATE `lp_rotations` SET `number_of_offers`= '$new_total_offers' WHERE `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		$sql = "UPDATE `lp_rotations` SET `last`= '' WHERE `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		$sql = "UPDATE `lp_rotations` SET `last`= 'show' WHERE `order` = '1' AND `rotation_id`='$rotation_id'";
		$result = mysql_query($sql);
		if($result==1){
			$msg = "Additional Landing Pages Successfully Added.";
			echo $msg;
		}
	}else{
	?>
		<center><h1>Add Landing Pages To Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$b = mysql_fetch_array($result2);
			$number_of_offers = $b['number_of_offers'];
			$start = $number_of_offers+1;
			$new_total_offers = $number_of_offers + $additional_offers;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Landing Page</td>
		</tr>
		<?php
		for($z=$start;$z<=$new_total_offers;$z++){
		?>
		<tr>			
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					?>
					<option value="<?php echo $val['id'];?>"><?php echo $val['nickname']." :: ".$val['link'];?></option>
				<?php
				}
				?>
				</option>
				</select>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="lp_adding_more">
				<input type="hidden" name="rotation_id" value="<?php echo $b['rotation_id']?>">
				<input type="hidden" name="rotation_name" value="<?php echo $b['rotation_name']?>">
				<input type="hidden" name="new_total_offers" value="<?php echo $new_total_offers?>">	
				<input type="hidden" name="start" value="<?php echo $start?>">		
			<input type="submit" name="submit" value="Add New Landing Pages To Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}
}
elseif($action == "lp_edit_rotation"){
	if($submit=="Edit Rotation"){
		$success=0;

		for($x=0;$x<=$number_of_offers;$x++){
			if($delete[$x]=="on"){
				//echo "DELETE[x] $delete[$x] $x<BR>";
				// We're deleting offer(s) from the package
				$deleted_one = "yes";
				$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
				mysql_select_db ($database);
				$sql = "DELETE FROM `lp_rotations` WHERE `id` = '$id[$x]'";
				$result = mysql_query($sql);
				if($result==1){
					$delete++;
				}

			}
		}

		if($deleted_one == "yes"){
				//echo "UPDATE ALL OFFERS: ".$update_all_offers."<BR>";
				// now update the number of landing pages field
				$sql = "SELECT COUNT(`rotation_id`) AS new FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id'";
				//echo $sql."<BR>";
				$result = mysql_query($sql);
				$a = mysql_fetch_array($result);
				$new = $a['new'];
				$sql = "UPDATE `lp_rotations` SET `number_of_offers` = '$new' WHERE `rotation_id`= '$rotation_id'";
				$result = mysql_query($sql);
				// select everything from that rotation_id make sure one has show on it
				$sql2 = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id' ORDER BY `order` ASC";
				$result2 = mysql_query($sql2);
				$first = 1;
				while($aaa = mysql_fetch_array($result2)){
					if($first == 1){
						$sql = "UPDATE `lp_rotations` SET `order` = '$first', `last` = 'show' WHERE `id` = '$aaa[id]'";
					}else{
						$sql = "UPDATE `lp_rotations` SET `order` = '$first', `last` = 'this' WHERE `id` = '$aaa[id]'";
					}
					$result = mysql_query($sql);
					$first++;
				}
		}else{
			// Nothing was deleted
			for($x=1;$x<=$number_of_offers;$x++){
				$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
				mysql_select_db ($database);
				if($x==1){
					$sql = "UPDATE `lp_rotations` SET `offer_id` = '$offer_id[$x]', `order` = '$order[$x]', `last` = 'show' WHERE `id` = '$id[$x]'";
				}else{
					$sql = "UPDATE `lp_rotations` SET `offer_id` = '$offer_id[$x]', `order` = '$order[$x]' WHERE `id` = '$id[$x]'";
				}
				$result = mysql_query($sql);
				if($result==1){
					$success++;
				}
			}
		}
		if($add_more_offers == "on"){
		// show the form asking for how many and send the submit button to the insert area.
		?>
		<center>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">How many would you like to add to this rotation?
			<select name="additional_offers" class="input_dropdown">
				<?php
				for($i=1;$i<101;$i++){
				?>
				<option value="<?php echo $i?>"><?php echo $i?></option>
				<?php
				}
				?>
			</select>			
			</td>
		</tr>		
		<tr>
			<td>
				<input type="hidden" name="action" value="lp_adding_more">
				<input type="hidden" name="number_of_offers" value="<?php echo $number_of_offers?>">	
				<input type="hidden" name="rotation_id" value="<?php echo $rotation_id?>">
			<input type="submit" name="submit" value="Add Landing Pages"></td>
		</tr>
		</table>
		</form>
		</center>
		</table>
		</center>
		<?php
		include("footer.php");
		exit;
		}
		echo "Landing Pages were successfully updated.<BR>";
	}else{
		?>
		<center><h1>Edit Rotation</h1></center><br>
		<?php

			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
			$result = mysql_query($sql);
			$sql2 = "SELECT * FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id'";
			$result2 = mysql_query($sql2);
			$count=1;
			while($b = mysql_fetch_array($result2)){
				$offer_ids[$count]=$b['offer_id'];
				$id[$count]=$b['id'];
				$num = $b['number_of_offers'];
				$rotation_id = $b['rotation_id'];
				$count++;
			}
			$start = 1;
		?>
		<center>
		<form name="add_offer_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table align="center" border="0" width="400" id="table1" cellpadding="2" bordercolor="#C0C0C0">
		<tr>
			<td align="left">Order</td>
			<td align="left">Landing Page</td>
			<td align="left">Delete</td>
		</tr>
		<?php
		for($z=1;$z<=$num;$z++){
		?>
		<tr>		
			<td align="left"><?php echo $z?><input type="hidden" name="order[<?php echo $z?>]" value="<?php echo $z?>"><input type="hidden" name="id[<?php echo $z?>]" value="<?php echo $id[$z];?>"></td>
			<td align="left">
				<select name="offer_id[<?php echo $z?>]" class="input_dropdown">
				<?php
				$sql = "SELECT * FROM `landing_pages` ORDER BY `link` DESC";
				$result = mysql_query($sql);
				while($val = mysql_fetch_array($result)){
					if($val['id']==$offer_ids[$z]){
						$selected = "on";
					}
					?>
					<option <?php if($selected=="on"){echo "selected";}?> value="<?php echo $val['id'];?>"><?php echo $val['nickname']." :: ".$val['link'];?></option>
				<?php
				$selected = "off";
				}
				?>
				</select>
			</td>
			<td align="left"><input type="checkbox" name="delete[<?php echo $z?>]"></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td align="left" colspan="2"><input type="checkbox" name="add_more_offers"> Would you like to add more landing pages to this rotation? (click for yes)</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="action" value="lp_edit_rotation">
				<input type="hidden" name="number_of_offers" value="<?php echo $num;?>">	
				<input type="hidden" name="rotation_id" value="<?php echo $rotation_id?>">	
			<input type="submit" name="submit" value="Edit Rotation"></td>
		</tr>
		</table>
		</form>
		</center>
		<?php
	}
}
elseif($action == "lp_delete_rotation"){
	?>
	<center><h1>Delete Rotation</h1></center><br>
	<?php
	// delete it from the db
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sql = "DELETE FROM `lp_rotations` WHERE `rotation_id` = '$rotation_id'";
	$result = mysql_query($sql);
	if($result==1){
		$msg = "Delete was succesful";
		echo $msg;
	}	
}

// New Landing Page Rotation Code End...

else{
	// show default main page here
	?>
		<h1 style="align:left;">Manage The System</h1>
	</div>
	<div style="width:760;align:left;">
		<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
		<p style="align:left;" class="gbox">This section is used to manage your offers, rotation packages, landing page information and syncing any campaign name or adgroup name changes you make along the way.  If you haven't already, you need to enter the offers using the links above.  Then enter the landing page information.  Offers can be used more then once in rotation packages.</p>
	<?php
}
?>
</div>
<?php
include("footer.php");
?>