<?php
$current_menu_item = "reports";
include("config.php");
include("header.php");

//echo "SUBMIT: $submit TYPE: $type WHEN: $when<BR>";
if($submit or $first_page or $next_page or $previous_page or $last_page or $rows_entered){
switch($order_by){
	case "campaign":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "adgroup":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "keyword":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "match_type":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "clicks":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "leads":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "s/u":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "payout":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "epc":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "cpc":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "revenue":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "cost":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "net":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "gm":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
	case "roi":
		if($asc_desc=="asc"){ $ad = "desc";}
		if($asc_desc=="desc"){ $ad = "asc";}
	break;
}
if($order_by == "" AND $type != "log"){
	$order_by = "clicks";
}elseif($order_by == "" AND $type == "log"){
	$order_by = "timestamp";
	$asc_desc = "desc";
}
if($asc_desc == ""){
	$ad = "desc";
}
// figure out different date ranges
$now = time();

if($start_time < 1){

switch($when){
	case "alltime":
		$start_time = "1";
		$end_time = strtotime("now");
	break;
	case "today":
		$start_time  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$end_time = mktime(23, 59, 59, date("m")  , date("d"), date("Y"));
		//$start_time = strtotime("-1 day");
		//$end_time = strtotime("now");
	break;
	case "yesterday":
		$start_time  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
		$end_time = mktime(23, 59, 59, date("m")  , date("d")-1, date("Y"));
		//$start_time = strtotime("-2 day");
		//$end_time = strtotime("-1 day");
	break;
	case "last7":
		$start_time = strtotime("-7 day");
		$end_time = strtotime("now");
	break;
	case "mtd":
		$start_time = strtotime("first day",strtotime(date('F 0 Y')));
		$end_time = strtotime("now");
	break;
	case "last month":
		$start_time = strtotime("first day",mktime(0,0,0,date("n")-1,1,date("Y")));
		$end_time = strtotime("last day",mktime(0,0,0,date("n"),0,date("Y")));
	break;
	case "thismonthlastyear":
		$start_time = strtotime("first day",mktime(0,0,0,date("n")-13,0,date("Y")));
		$end_time = strtotime("last day",mktime(0,0,0,date("n")-12,0,date("Y")));
	break;
	case "custom":
		$start_time = mktime(0,0,0,$a_month, $a_day, $a_year);
		$end_time = mktime(23, 59, 59, $b_month, $b_day, $b_year);
	break;
}

}// end if start_time < 1
// grab the total number of records.
/*
if($type==""){
	$type="campaign";
}
*/

	//echo "TOTAL RECORDS: ".$total_records."<BR>";
	//echo "SQL: ".$sqll."<BR>";
if($first_page){
	// means they want to see the first page of results
	$offset = 0;
	$start = 0;
	$show_per_page = $old_show_per_page;
}
if($next_page){
	// means they want to see the first page of results
	$offset += 1;
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
}
if($previous_page){
	// means they want to see the first page of results
	$offset = $offset - 1;
	$start = $offset*$old_show_per_page;
	$show_per_page = $old_show_per_page;
}
if($last_page){

	$ot = $total_records/$old_show_per_page;
	$offset = floor($ot);
	$start = $offset*$old_show_per_page;
	if($start == $total_records){
		$start -= $offset;
	}
	$show_per_page = $old_show_per_page;

	// means they want to see the first page of results
	//$offset = ($total_records/$old_show_per_page)-1;
	//$start = $offset*$old_show_per_page;
	//$show_per_page = $old_show_per_page;
}
/*
if($rows_entered){
	// means they want to see the first page of results
	$offset = ($total_records/$old_show_per_page);
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
// Display the keywords and all their info
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$sqll = "SELECT count(DISTINCT(`$type`)) as total_records  FROM `kw_log` WHERE `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND `revenue_updated` > 0 AND `cost_updated` > 0";
	$resl = @mysql_query($sqll);
	$value = @mysql_fetch_array($resl);
	$total_records = @$value[total_records];
if($dig != "on"){
?>
<CENTER>
<h1 style="align:center;"><?php echo ucfirst($type)?> Report</h1>
<?php
//include("reports_nav_menu.php");
	reports_nav_menu($type, $when);
}else{
?>
<CENTER>
<h1 style="align:center;"><?php echo ucfirst($campaign_name)?> Report</h1>
<?php
//include("reports_nav_menu.php");
	reports_nav_menu($type, $when);
}
	$adgroup_name = urlencode($adgroup_name);
	$campaign_name = urlencode($campaign_name);
	$variables = "?type=$type&offset=$offset&start=$start&show_per_page=$show_per_page&when=$when&asc_desc=$ad&submit=Get+Report&dig=$dig&campaign_name=$campaign_name&adgroup_name=$adgroup_name";
if($type == "export_csv_cost" OR $type == "export_csv_kws"){
	

	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	
	switch($type){
		case "export_csv_kws":$date_field = "timestamp";$table = "kw_log";break;
		case "export_csv_cost":$date_field = "int_date";$table = "cost";break;
	}

	$result=mysql_query("select * from $table WHERE `$date_field` >= '$start_time' AND `$date_field` <= '$end_time'");

	$out = '';

	// Get all fields names in table "name_list" in database "tutorial".
	$fields = mysql_list_fields($database,$table);

	// Count the table fields and put the value into $columns.
	$columns = mysql_num_fields($fields);


	// Put the name of all fields to $out.
	for ($i = 0; $i < $columns; $i++) {
	$l=mysql_field_name($fields, $i);
	$out .= '"'.$l.'",';
	}
	$out .="\n";

	// Add all values in the table to $out.
	while ($l = mysql_fetch_array($result)) {
	for ($i = 0; $i < $columns; $i++) {
	$out .='"'.$l["$i"].'",';
	}
	$out .="\n";
	}
	
	echo "<table><tr><td>";
	echo "Hit CTRL - A to copy all and paste it into notepad or excel</td></tr><tr><td>";
	echo "<textarea style=\"width:700px;height:240px;\">$out</textarea>";
	echo "</td></tr></table>";
	/*
	// Open file export.csv.
	$f = fopen ('export.csv','w');

	// Put all values from $out to export.csv.
	fputs($f, $out);
	fclose($f);

	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename="export.csv"');
	readfile('export.csv');
	*/
}
elseif($type == "log"){
	// show the raw click log
	// subid :: timestamp :: referer :: browser :: ip_address :: query_string :: campaign :: adgroup :: keyword :: source :: network :: network_name :: network_offer
	
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	
	?>
	<table width="1480">
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=id">Subid</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=timestamp">Date/Time</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=referer">Referer</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=browser">Browser</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=ip_address">IP Address</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=query_string">Query String</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Campaign</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroup</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=keyword">Keyword</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=source">Source</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network">Network</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_name">Network Name</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_offer">Network Offer</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);

	$sql = "SELECT * FROM `kw_log` WHERE `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
	//echo "SQL: $sql <BR><BR>";
	
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

	$display_referer = $val['referer'];
	if($display_referer == ""){
		$display_referer_td = "No Referer";
	}
	else{
		$display_referer_td = substr($display_referer, 0, 30)."...";
	}

	$display_qs = $val['query_string'];
	if($display_qs == ""){
		$display_qs_td = "No Query String";
	}
	else{
		$display_qs_td = substr($display_qs, 0, 30)."...";
	}
	?>
	<tr class="<?php echo $row?>">
	<!-- 	<td><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_campaign;?>"></td> -->	
		<td align="left"><?php echo $val['id'];?></td>
		<td align="left"><?php $ts = $val['timestamp']; echo date("m/d/Y h:m:s", $ts);?></td>
		<td align="left" onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_referer?>&nbsp;&nbsp;')" onmouseout="UnTip()"><?php echo $display_referer_td;?></td>
		<td class="right"><?php echo $val['browser'];?></td>
		<td align="left"><?php echo $val['ip_address'];?></td>
		<td align="left" onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_qs?>&nbsp;&nbsp;')" onmouseout="UnTip()"><?php echo $display_qs_td;?></td>
		<td align="left"><?php echo $val['campaign'];?></td>
		<td align="left"><?php echo $val['adgroup'];?></td>
		<td align="left"><?php echo $val['keyword'];?></td>
		<td align="left"><?php echo $val['source'];?></td>
		<td align="left"><?php echo $val['network'];?></td>
		<td align="left"><?php echo $val['network_name'];?></td>
		<td align="left"><?php echo $val['network_offer'];?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="13" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php

}
elseif($type=="campaign"){
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	?>
	<table width="1280">
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Campaign</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroups</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=keyword">Keywords</a></th>						
	<!-- 	<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=match_type">Match Types</a></th> -->						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=clicks">Clicks</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=leads">Leads</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=s/u">S/U</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=payout">Payout</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=epc">EPC</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cpc">Max CPC</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=revenue">Revenue</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cost">Cost</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=net">Net</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=gm">GM</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=roi">ROI</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);


	//$sql = "SELECT campaign, COUNT(DISTINCT adgroup) AS adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND cost_updated > 0 AND revenue_updated > 0 GROUP BY campaign ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
	// New Sql Below:

	// NEED: clicks, epc, cpc, cost, net, gm, roi
	
	$sql = "SELECT campaign, COUNT(DISTINCT adgroup) AS adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND cost_updated > 0 AND revenue_updated > 0 GROUP BY campaign ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	//echo $sql."<BR>";

		$sql = "SELECT campaign, COUNT(DISTINCT adgroup) AS adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' GROUP BY campaign ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
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
	
	// campaign
	$display_campaign = $val['campaign'];

	// adgroup
	$display_adgroup = $val['adgroup'];

	// keyword
	$display_keyword = $val['keyword'];

	// match type
	$display_match_type = $val['match_type'];

	// grab all cost data needed
	// clicks
	// cpc

	$sql2 = "SELECT SUM(clicks) AS clicks, SUM(total_cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";
	//echo $sql2."<BR>";
	$result2 = mysql_query($sql2);

	$val2 = mysql_fetch_array($result2);

	// clicks
	$display_clicks = $val2['clicks'];

	// leads
	$display_leads = $val['leads'];

	// su
	//$su = $val['su'];
	if($display_leads > 0){
		$su = @($display_leads/$display_clicks)*100;
	}else{
		$su = "n/a";
	}
	$display_su = number_format($su, 2, '.', ',')." %";

	// payout
	$po = $val['payout'];
	$display_payout = number_format($po, 2, '.', ',');

	// revenue
	//$revenue = @($display_leads * $display_payout);
	$revenue = $val['revenue'];
	$display_revenue = number_format($revenue, 2, '.', ',');

	// epc 
	$epc = @($revenue/$display_clicks);
	////$epc = $val['epc'];
	$display_epc = number_format($epc, 2, '.', ',');

	if($val2[clicks] > 0){
		// avg cpc
		$cpc = $val2[cost]/$val2[clicks];
	}else{
		$cpc = 0;
	}
	
	$display_cpc = number_format($cpc, 2, '.', ',');

	// cost
	//$cost = @($display_clicks * $display_avg_cpc);
	$cost = $val2['cost'];
	$display_cost = number_format($cost, 2, '.', ',');

	// net
	$net = $revenue - $cost;
	//$net = $val['net'];
	$display_net = number_format($net, 2, '.', ',');

	// gross marging
	$gm = @($net / $revenue);
	$gm *= 100;
	//$gm = $val['gm'];
	$display_gm = number_format($gm, 2, '.', ',')." %";

	// roi
	$roi = @($net / $cost);
	$roi *= 100;
	//$roi = $val['roi'];
	$display_roi = number_format($roi, 2, '.', ',')." %";

	$ucampaign = urlencode($display_campaign);
	?>
	<tr class="<?php echo $row?>">
	<!-- 	<td><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_campaign;?>"></td> -->	
		<td align="left" onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_campaign?>&nbsp;&nbsp;')" onmouseout="UnTip()"><a href="<?php echo $_SERVER[PHP_SELF];echo "?type=adgroup&offset=$offset&start=$start&show_per_page=$show_per_page&when=$when&asc_desc=$ad&submit=Get+Report";?>&dig=on&campaign_name=<?php echo $ucampaign;?>" style="color:black"><?php echo $display_campaign;?></a></td>
		<td align="right"onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_adgroup?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_adgroup;?>"></td>
		<td align="right"><?php echo $display_keyword?></td>
		<!-- <td class="first"><?php echo $display_match_type?></td> -->
		<td align="right"><?php echo $display_clicks?></td>
		<td align="right"><?php echo $display_leads?></td>
		<td align="right"><?php echo $display_su?></td>
		<td align="right"><?php echo $display_payout?></td>
		<td align="right"><?php echo $display_epc?></td>
		<td align="right"><?php echo $display_cpc?></td>
		<td align="right"><?php echo $display_revenue?></td>
		<td align="right"><?php echo $display_cost?></td>
		<td align="right"><?php echo $display_net?></td>
		<td align="right"><?php echo $display_gm?></td>
		<td align="right"><?php echo $display_roi?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="15" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
}
elseif($type=="adgroup"){
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	?>
	<table width="1280">
	<tr>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroup</a></th>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Campaign</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=keyword">Keywords</a></th>						
		<!-- <th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=match_type">Match Type</a></th> -->						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=clicks">Clicks</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=leads">Leads</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=s/u">S/U</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=payout">Payout</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=epc">EPC</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cpc">Max CPC</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=revenue">Revenue</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cost">Cost</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=net">Net</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=gm">GM</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=roi">ROI</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	if($dig=="on"){
		
		$campaign = urldecode($campaign_name);

		$sql = "SELECT campaign, adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND campaign = '$campaign' GROUP BY campaign, adgroup ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
	}else{
		
		$sql = "SELECT campaign, adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' GROUP BY adgroup ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
	}

	//$sql = "SELECT campaign, adgroup, COUNT(DISTINCT keyword) AS keyword , match_type , SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' GROUP BY adgroup ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
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
	// campaign
	$display_campaign = $val['campaign'];

	// adgroup
	$display_adgroup = $val['adgroup'];

	// keyword
	$display_keyword = $val['keyword'];

	// match type
	$display_match_type = $val['match_type'];

	// grab all cost data needed
	// clicks
	// cpc

	$sql2 = "SELECT SUM(clicks) AS clicks, SUM(total_cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND adgroup = '$display_adgroup' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";
	
	$result2 = mysql_query($sql2);

	$val2 = mysql_fetch_array($result2);

	// clicks
	$display_clicks = $val2['clicks'];

	// leads
	$display_leads = $val['leads'];

	// su
	//$su = $val['su'];
	if($display_leads > 0){
		$su = @($display_leads/$display_clicks)*100;
	}else{
		$su = "n/a";
	}
	$display_su = number_format($su, 2, '.', ',')." %";

	// payout
	$display_payout = $val['payout'];

	// revenue
	//$revenue = @($display_leads * $display_payout);
	$revenue = $val['revenue'];
	$display_revenue = number_format($revenue, 2, '.', ',');

	// epc 
	$epc = @($revenue/$display_clicks);
	//$epc = $val['epc'];
	$display_epc = number_format($epc, 2, '.', ',');

	
	if($val2[clicks] > 0){
		// avg cpc
		$cpc = $val2[cost]/$val2[clicks];
	}else{
		$cpc = 0;
	}

	$display_cpc = number_format($cpc, 2, '.', ',');

	// cost
	//$cost = @($display_clicks * $display_avg_cpc);
	$cost = $val2['cost'];
	$display_cost = number_format($cost, 2, '.', ',');

	// net
	$net = $revenue - $cost;
	//$net = $val['net'];
	$display_net = number_format($net, 2, '.', ',');
	// net
	//$net = $revenue - $cost;
	//$net = $val['net'];
	//$display_net = number_format($net, 2, '.', ',');

	// gross marging
	$gm = @($net / $revenue);
	$gm *= 100;
	//$gm = $val['gm'];
	$display_gm = number_format($gm, 2, '.', ',')." %";

	// roi
	$roi = @($net / $cost);
	$roi *= 100;
	//$roi = $val['roi'];
	$display_roi = number_format($roi, 2, '.', ',')." %";
	$ucampaign = urlencode($display_campaign);
	$uadgroup = urlencode($display_adgroup);
	?>
	<tr class="<?php echo $row?>">
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_adgroup?>&nbsp;&nbsp;')" onmouseout="UnTip()"><a href="<?php echo $_SERVER[PHP_SELF];echo "?type=keyword&offset=$offset&start=$start&show_per_page=$show_per_page&when=$when&asc_desc=$ad&submit=Get+Report";?>&dig=on&campaign_name=<?php echo $ucampaign;?>&adgroup_name=<?php echo $uadgroup;?>" style="color:black"><?php echo $display_adgroup;?></a></td>
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_campaign?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_campaign;?>"></td>
		<td class="first"><?php echo $display_keyword?></td>
		<!-- <td class="first"><?php echo $display_match_type?></td> -->
		<td align="right"><?php echo $display_clicks?></td>
		<td align="right"><?php echo $display_leads?></td>
		<td align="right"><?php echo $display_su?></td>
		<td align="right"><?php echo $display_payout?></td>
		<td align="right"><?php echo $display_epc?></td>
		<td align="right"><?php echo $display_cpc?></td>
		<td align="right"><?php echo $display_revenue?></td>
		<td align="right"><?php echo $display_cost?></td>
		<td align="right"><?php echo $display_net?></td>
		<td align="right"><?php echo $display_gm?></td>
		<td align="right"><?php echo $display_roi?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="15" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
}
elseif($type=="keyword"){
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	?>
	<table width="1280">
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Campaign</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroup</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=keyword">Keyword</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=match_type">Match Type</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=clicks">Clicks</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=leads">Leads</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=s/u">S/U</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=payout">Payout</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=epc">EPC</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cpc">Max CPC</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=revenue">Revenue</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cost">Cost</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=net">Net</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=gm">GM</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=roi">ROI</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);

	if($dig == "on"){
		
		$campaign = urldecode($campaign_name);
		$adgroup = urldecode($adgroup_name);

		$sql = "SELECT campaign, adgroup, keyword , match_type , network, SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND campaign = '$campaign' AND adgroup = '$adgroup' GROUP BY campaign, adgroup, keyword , match_type ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	
	}else{

		$sql = "SELECT campaign, adgroup, keyword , match_type, network, SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' GROUP BY campaign, adgroup, keyword , match_type ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";
	}

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
	// campaign
	$display_campaign = $val['campaign'];

	// adgroup
	$display_adgroup = $val['adgroup'];

	// keyword
	$display_keyword = $val['keyword'];

	// match type
	$display_match_type = $val['match_type'];

	// grab all cost data needed
	// clicks
	// cpc
	
	// network
	$display_network = $val['network'];

	if($display_network == "content"){

		$sql2 = "SELECT SUM(clicks) AS clicks, SUM(total_cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND adgroup = '$display_adgroup' AND keyword ='$display_keyword' AND match_type = '$display_match_type' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";	
		
	}else{

		$sql2 = "SELECT SUM(clicks) AS clicks, SUM(total_cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND adgroup = '$display_adgroup' AND keyword ='$display_keyword' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";

	}

	//echo $sql2."<BR>";

	$result2 = mysql_query($sql2);

	$val2 = mysql_fetch_array($result2);

	// clicks
	$display_clicks = $val2['clicks'];

	// leads
	$display_leads = $val['leads'];

	// su
	//$su = $val['su'];
	if($display_leads > 0){
		$su = @($display_leads/$display_clicks)*100;
	}else{
		$su = "n/a";
	}
	$display_su = number_format($su, 2, '.', ',')." %";

	// payout
	$display_payout = $val['payout'];

	// revenue
	//$revenue = @($display_leads * $display_payout);
	$revenue = $val['revenue'];
	$display_revenue = number_format($revenue, 2, '.', ',');

	// epc 
	$epc = @($revenue/$display_clicks);
	//$epc = $val['epc'];
	$display_epc = number_format($epc, 2, '.', ',');

	if($val2[clicks] > 0){
		// avg cpc
		$cpc = $val2[cost]/$val2[clicks];
	}else{
		$cpc = 0;
	}
	
	$display_cpc = number_format($cpc, 2, '.', ',');

	// cost
	//$cost = @($display_clicks * $display_avg_cpc);
	$cost = $val2['cost'];
	$display_cost = number_format($cost, 2, '.', ',');

	// net
	$net = $revenue - $cost;
	//$net = $val['net'];
	$display_net = number_format($net, 2, '.', ',');

	// gross marging
	$gm = @($net / $revenue);
	$gm *= 100;
	//$gm = $val['gm'];
	$display_gm = number_format($gm, 2, '.', ',')." %";

	// roi
	$roi = @($net / $cost);
	$roi *= 100;
	//$roi = $val['roi'];
	$display_roi = number_format($roi, 2, '.', ',')." %";
	?>
	<tr class="<?php echo $row?>">
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_campaign?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_campaign;?>"></td>
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_adgroup?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_adgroup;?>"></td>
		<td class="first"><?php echo $display_keyword?></td>
		<td class="first"><?php echo $display_match_type?></td>
		<td align="right"><?php echo $display_clicks?></td>
		<td align="right"><?php echo $display_leads?></td>
		<td align="right"><?php echo $display_su?></td>
		<td align="right"><?php echo $display_payout?></td>
		<td align="right"><?php echo $display_epc?></td>
		<td align="right"><?php echo $display_cpc?></td>
		<td align="right"><?php echo $display_revenue?></td>
		<td align="right"><?php echo $display_cost?></td>
		<td align="right"><?php echo $display_net?></td>
		<td align="right"><?php echo $display_gm?></td>
		<td align="right"><?php echo $display_roi?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="15" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
} // end if type is keywords
elseif($type=="rotation"){
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	?>
	<table width="1400">
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Rotation Name</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroup</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_name">Network</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_offer">Offer</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=clicks">Clicks</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=leads">Leads</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=s/u">S/U</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=payout">Payout</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=epc">EPC</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cpc">Max CPC</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=revenue">Revenue</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cost">Cost</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=net">Net</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=gm">GM</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=roi">ROI</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);


	$sql = "SELECT rotation_name, adgroup, network_name , network_offer, SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND cost_updated > 0 AND revenue_updated > 0 GROUP BY rotation_name, network_name, network_offer ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";

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
	// rotation name
	$display_rotation_name = $val['rotation_name'];


	// campaign
	$display_campaign = $val['campaign'];

	// adgroup
	$display_adgroup = $val['adgroup'];

	// keyword
	$display_network_name = $val['network_name'];

	// match type
	$display_network_offer = $val['network_offer'];

	// clicks
	$display_clicks = $val['clicks'];

	// leads
	$display_leads = $val['leads'];

	// su
	//$su = $val['su'];
	if($display_leads > 0){
		$su = @($display_leads/$display_clicks)*100;
	}else{
		$su = "n/a";
	}
	$display_su = number_format($su, 2, '.', ',')." %";

	// payout
	$display_payout = $val['payout'];

	// revenue
	//$revenue = @($display_leads * $display_payout);
	$revenue = $val['revenue'];
	$display_revenue = number_format($revenue, 2, '.', ',');
	
	// grab all cost data needed
	// clicks
	// cpc

	$sql2 = "SELECT SUM(clicks) AS clicks, SUM(cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND adgroup = '$display_adgroup' AND keyword ='$display_keyword' AND match_type = '$display_match_type' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";
	
	$result2 = mysql_query($sql2);

	$val2 = mysql_fetch_array($result2);

	// clicks
	$display_clicks = $val2['clicks'];

	// epc 
	$epc = @($revenue/$display_clicks);
	//$epc = $val['epc'];
	$display_epc = number_format($epc, 2, '.', ',');

	// avg cpc
	if($val2[clicks] > 0){
		// avg cpc
		$cpc = $val2[cost]/$val2[clicks];
	}else{
		$cpc = 0;
	}
	$display_cpc = number_format($cpc, 2, '.', ',');

	// cost
	//$cost = @($display_clicks * $display_avg_cpc);
	$cost = $val2['cost'];
	$display_cost = number_format($cost, 2, '.', ',');

	// net
	$net = $revenue - $cost;
	//$net = $val['net'];
	$display_net = number_format($net, 2, '.', ',');

	// gross marging
	$gm = @($net / $revenue);
	$gm *= 100;
	//$gm = $val['gm'];
	$display_gm = number_format($gm, 2, '.', ',')." %";

	// roi
	$roi = @($net / $cost);
	$roi *= 100;
	//$roi = $val['roi'];
	$display_roi = number_format($roi, 2, '.', ',')." %";
	?>
	<tr class="<?php echo $row?>">
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_rotation_name?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_rotation_name;?>"></td>
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_adgroup?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_adgroup;?>"></td>
		<td class="first"><?php echo $display_network_name?></td>
		<td class="first"><?php echo $display_network_offer?></td>
		<td align="right"><?php echo $display_clicks?></td>
		<td align="right"><?php echo $display_leads?></td>
		<td align="right"><?php echo $display_su?></td>
		<td align="right"><?php echo $display_payout?></td>
		<td align="right"><?php echo $display_epc?></td>
		<td align="right"><?php echo $display_cpc?></td>
		<td align="right"><?php echo $display_revenue?></td>
		<td align="right"><?php echo $display_cost?></td>
		<td align="right"><?php echo $display_net?></td>
		<td align="right"><?php echo $display_gm?></td>
		<td align="right"><?php echo $display_roi?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="15" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
} // end if type is rotation
elseif($type=="offer"){
	page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name, $adgroup_name);
	?>
	<table width="1400">
	<tr>
	<!-- 	<th class="first" style="background-color: #9BCE00"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=campaign">Campaign</a></th> -->
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=adgroup">Adgroup</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_name">Network</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=network_offer">Offer</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=clicks">Clicks</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=leads">Leads</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=s/u">S/U</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=payout">Payout</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=epc">EPC</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cpc">Max CPC</a></th>						
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=revenue">Revenue</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=cost">Cost</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=net">Net</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=gm">GM</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="reports.php<?php echo $variables;?>&order_by=roi">ROI</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);


	$sql = "SELECT campaign, adgroup, network_name , network_offer, SUM(clicks) AS clicks, SUM(leads)AS leads, AVG(`s/u`) AS su, MAX(`payout`) AS payout, AVG(epc) AS epc, MAX(cpc) AS cpc, SUM(revenue) AS revenue, SUM(cost) AS cost, SUM(net) AS net, AVG(gm) AS gm, AVG(roi) AS roi FROM `kw_log` WHERE `keyword` !='' AND `timestamp` >= '$start_time' AND `timestamp` <= '$end_time' AND cost_updated > 0 AND revenue_updated > 0 GROUP BY network_offer , network_name ORDER BY `$order_by` $asc_desc LIMIT $start, $show_per_page";

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
	// campaign
	$display_campaign = $val['campaign'];

	// adgroup
	$display_adgroup = $val['adgroup'];

	// keyword
	$display_network_name = $val['network_name'];

	// match type
	$display_network_offer = $val['network_offer'];

	// grab all cost data needed
	// clicks
	// cpc

	$sql2 = "SELECT SUM(clicks) AS clicks, SUM(cost) AS cost FROM `cost` WHERE campaign = '$display_campaign' AND adgroup = '$display_adgroup' AND keyword ='$display_keyword' AND match_type = '$display_match_type' AND int_date >= '$start_time' AND int_date <= '$end_time' GROUP BY campaign";
	
	$result2 = mysql_query($sql2);

	$val2 = mysql_fetch_array($result2);

	// clicks
	$display_clicks = $val2['clicks'];

	// leads
	$display_leads = $val['leads'];

	// su
	//$su = $val['su'];
	if($display_leads > 0){
		$su = @($display_leads/$display_clicks)*100;
	}else{
		$su = "n/a";
	}
	$display_su = number_format($su, 2, '.', ',')." %";

	// payout
	$display_payout = $val['payout'];

	// revenue
	//$revenue = @($display_leads * $display_payout);
	$revenue = $val['revenue'];
	$display_revenue = number_format($revenue, 2, '.', ',');

	// epc 
	$epc = @($revenue/$display_clicks);
	//$epc = $val['epc'];
	$display_epc = number_format($epc, 2, '.', ',');

	// avg cpc
	if($val2[clicks] > 0){
	// avg cpc
		$cpc = $val2[cost]/$val2[clicks];
	}else{
		$cpc = 0;
	}
	$display_cpc = number_format($cpc, 2, '.', ',');

	// cost
	//$cost = @($display_clicks * $display_avg_cpc);
	$cost = $val2['cost'];
	$display_cost = number_format($cost, 2, '.', ',');

	// net
	$net = $revenue - $cost;
	//$net = $val['net'];
	$display_net = number_format($net, 2, '.', ',');

	// gross marging
	$gm = @($net / $revenue);
	$gm *= 100;
	//$gm = $val['gm'];
	$display_gm = number_format($gm, 2, '.', ',')." %";

	// roi
	$roi = @($net / $cost);
	$roi *= 100;
	//$roi = $val['roi'];
	$display_roi = number_format($roi, 2, '.', ',')." %";
	?>
	<tr class="<?php echo $row?>">
	<!-- 	<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_campaign?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_campaign;?>"></td> -->
		<td onmouseover="Tip('&nbsp;&nbsp;<?php echo $display_adgroup?>&nbsp;&nbsp;')" onmouseout="UnTip()"><input type="text" class="<?php echo $input_color?>" value="<?php echo $display_adgroup;?>"></td>
		<td class="first"><?php echo $display_network_name?></td>
		<td class="first"><?php echo $display_network_offer?></td>
		<td align="right"><?php echo $display_clicks?></td>
		<td align="right"><?php echo $display_leads?></td>
		<td align="right"><?php echo $display_su?></td>
		<td align="right"><?php echo $display_payout?></td>
		<td align="right"><?php echo $display_epc?></td>
		<td align="right"><?php echo $display_cpc?></td>
		<td align="right"><?php echo $display_revenue?></td>
		<td align="right"><?php echo $display_cost?></td>
		<td align="right"><?php echo $display_net?></td>
		<td align="right"><?php echo $display_gm?></td>
		<td align="right"><?php echo $display_roi?></td>
	</tr>
		
	<?php
	}
	?>
	<tr>
		<td colspan="14" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
	<?php
} // end if type is offer

}// end if submit is get report
else{
	// show default main page here
	?>
	<div id="content-wrap">
		<div id="content">
			<div style="width:760;align:center;">		
				<h1>Reports</h1>
				
				<p><span style="background-color: #EEF7DB">What does this page do?</span></p>			
				
				<p class="gbox">The reports section is very valuable.  You can view all your campaigns, adgroups and keywords by different timeframes using the links above.  You can also click on any column heading to sort by that column in ascending or descending order.  This is very powerful because you can see at a glance what your top and bottom campaigns, adgroups and keywords are.  Then you can use this information for your day to day account management.</p>
			</div>
	<?php
	//include("reports_nav_menu.php");
	reports_nav_menu($type, $when);
}
include("footer.php");
?>