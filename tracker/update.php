<?php
extract($_REQUEST);
include("config.php");
include("header.php");
$dbh = mysql_connect ($db_location, $username, $password); 
mysql_select_db ($database,$dbh);
?>
<div id="content-wrap">
	<div id="content">
		<div style="width:860;align:center;">
<?php
/*
This file will grab all the cost data for a specific day and campaign and update the db accordingly
- adwords content network campaign
- adwords search network campaign
- yahoo content network campaign
- yahoo search network campaign
- msn content network campaign
- msn search network campaign
*/
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
	<h1 style="align:left;">Update Your Cost & Revenue Stats</h1>
	</div>
	<div style="width:860;align:left;">
	<p style="align:left;"><span style="background-color: #EEF7DB">What does this page do?</span></p>			
	<p style="align:left;" class="gbox">One of the most powerful features of this tracking system is the ability to reconcile your cost data with your revenue date to give you the numbers on each campaign, adgroup and keyword.  Adwords is done automatically, but Yahoo and MSN are still manual since they don't freely give out their api keys at this point.  (For MSN you need a $10k monthly spend on average and Yahoo just takes forever to decide, but you need a high monthly spend with them as well).  A scraper solution is being worked on to bypass the need for any api keys on your part.</p>
	<h1>Rules: </h1>
	<ol>
	<li>One day at a time, you tell the script what day you're uploading.</li>
	<li>Don't upload cost data twice.</li>
	</ol>
	<h1>Adwords Report:</h1>
	<p>This one will be done automatically for all your campaigns regardless of content or search network using the api system, which you will have already set up.</p>
	<h1>MSN Report:</h1>
	<p>Use this one:
		<ol>
			<li><B>Basic Settings</B><br>
				Report: Select "Keyword Performance"<br>
				View (unit of time): Select "Day"<br>
				Date Range: "Yesterday" OR "Custom Date Range... with ONE date as the start and the same date as the end<br>
			</li>
			<li><B>Advanced Settings</B><br>
				Report Scope: Select "All accounts, campaigns, and ad groups<br>
				Add or remove columns<br>
				check "Delivered match type"<br>
				check "Destination URL"<br>
			</li>
			<li><B>Templates and scheduling (optional)</B><br>
				Name this report: Name it Yesterdays MSN Cost Data<br>
				Template: check "Save as report template"<br>
				Schedule: check "Schedule this report to run automatically"<br>
			</li>
		</ol>
	</p>
	<p>To run: Select "Daily" every "1" days<br>
		Send report to: your-email-address@domain.com<br>
		Download format: Select "csv"<br><br>
		<B>Your column headings should be:</B><br>
		Date, Account Name, Campaign, Ad group, Ad Distribution, Keyword, Current maximum cpc, Delivered match type, Destination URL, Impressions, Clicks, CTR, Avg. CPC, Spend, Avg. position<br><br>
		That's it for MSN Adcenter</p>
	<h1>Yahoo Search Marketing:</h1>
	<p>Click on "Reports" Tab, Click on "Keyword Performance"<br><br>
		Select "All" under the "Account" dropdown box<br><br>
		For the date range, (located under the "Download this Report" link), Select "Yesterday" OR "Custom Date Range" with the Start Date & End Date set to the same date<br><br>
		Click on "Download this Report" Select "csv format"<br><br>
		<B>Your column headings should be:</B><br><br>
		Keyword, Ad Group, Campaign, Impressions, CTR (%), Clicks, Avg CPC ($), Cost ($), Avg Position</p>
	<center><h1>Update Your Data</h1></center><br>
	<?php
if($submit){
	set_time_limit(0);

	$click_column -= 1;
	$cost_column -= 1;

	$total = 0;
	$success = 0;

	$file = $_FILES["file"]["tmp_name"];
	$handle = fopen($file, "r");
	$number=0;

	while (($data = fgetcsv($handle, 1000, ",", '"')) != FALSE) {

		$offset = $offset_hours *60*60;
		$now = time();


			if($currency_symbol == 'R$'){
				$cost1 = (float) str_replace(",", ".", $data[$cost_column]);
			}


		if($update == "adwords"){
			
			// figure out the date from the m-d-Y of the drop down box

			$start_time  = mktime(0, 0, 0, $a_month, $a_day, $a_year);
			$start_time = strtotime();
			$mysql_datetime = gmdate("Y-m-d H:i:s", $start_time);
			$engine = "adwords";

			$sql = "INSERT INTO `cost` (`int_date`, `pretty_date`, `engine`, `network`, `campaign`,`adgroup` ,`keyword` ,`keyword_status` ,`keyword_mincpc` ,`keyword_desturl` ,`match_type` ,`impressions` ,`clicks`, `cpc`, `total_cost` ,`position`) VALUES ('$start_time' , '$mysql_datetime', '$engine', '$network', '$data[2]', '$data[1]', '$data[0]', '$kw_status', '$kw_mincpc', '$kw_desturl', '$kw_match_type', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]')";
			//echo $sql."<BR>";
			$result = mysql_query($sql,$dbh);
			$success += @mysql_affected_rows();
			@mysql_free_result($result);

		} // end if adwords
		elseif($update == "yahoo"){
			
			// figure out the date from the m-d-Y of the drop down box

			$start_time  = mktime(0, 0, 0, $a_month, $a_day, $a_year);
			$start_time = strtotime();
			$mysql_datetime = gmdate("Y-m-d H:i:s", $start_time);
			$engine = "yahoo";

			$sql = "INSERT INTO `cost` (`int_date`, `pretty_date`, `engine`, `network`, `campaign`,`adgroup` ,`keyword` ,`keyword_status` ,`keyword_mincpc` ,`keyword_desturl` ,`match_type` ,`impressions` ,`clicks`, `cpc`, `total_cost` ,`position`) VALUES ('$start_time' , '$mysql_datetime', '$engine', '$network', '$data[2]', '$data[1]', '$data[0]', '$kw_status', '$kw_mincpc', '$kw_desturl', '$kw_match_type', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]')";
			//echo $sql."<BR>";
			$result = mysql_query($sql,$dbh);
			$success += @mysql_affected_rows();
			@mysql_free_result($result);
		}// end if yahoo
		elseif($update == "msn"){
			
			// figure out the date from the m-d-Y of the drop down box

			$start_time  = mktime(0, 0, 0, $a_month, $a_day, $a_year);
			$start_time = strtotime();
			$mysql_datetime = gmdate("Y-m-d H:i:s", $start_time);
			$engine = "msn";

			$sql = "INSERT INTO `cost` (`int_date`, `pretty_date`, `engine`, `network`, `campaign`,`adgroup` ,`keyword` ,`keyword_status` ,`keyword_mincpc` ,`keyword_desturl` ,`match_type` ,`impressions` ,`clicks`, `cpc`, `total_cost` ,`position`) VALUES ('$start_time' , '$mysql_datetime', '$engine', '$data[4]', '$data[2]', '$data[3]', '$data[5]', '$kw_status', '$kw_mincpc', '$data[8]', '$data[7]', '$data[9]', '$data[10]', '$data[12]', '$data[13]', '$data[14]')";
			//echo $sql."<BR>";
			$result = mysql_query($sql,$dbh);
			$success += @mysql_affected_rows();
			@mysql_free_result($result);
				
		}// end if msn
		elseif($update == "revenue"){
			$column = $subid_column - 1;
			if($conversion_column == "na"){
				$sql = "UPDATE kw_log SET leads = '1', revenue = network_payout, payout = network_payout, revenue_updated = '$now' WHERE id = $data[$column]";
			}
			$conv_column = $conversion_column - 1;
			if($data[$conv_column]==1){
				$sql = "UPDATE kw_log SET leads = '1', revenue = network_payout, payout = network_payout, revenue_updated = '$now' WHERE id = $data[$column]";
			}
			//echo "Revenue Sql: ".$sql."<BR>";
			$t = mysql_query($sql,$dbh);
			if(mysql_affected_rows() > 0){
				$success += @mysql_affected_rows();
			}
			@mysql_free_result($t);
			$msg = "$success conversions updated";
		}// end if revenue
	

	}// end while loop


}// end of submit
?>

	<center><?php echo $msg?>
	<form name="update_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
	<table align="center" border="0" width="860" id="table1" cellpadding="2" bordercolor="#C0C0C0">
	<tr>
		<td align="right" style="width:200px;">Update :</td>
		<script>
		function addOptions(_obj) {
		if (_obj.value == 'revenue') {
		document.getElementById('cost').style.display = 'none';
		document.getElementById('subid').style.display = '';
		document.getElementById('conv').style.display = '';
		}
		else {
		document.getElementById('cost').style.display = '';
		document.getElementById('subid').style.display = 'none';
		document.getElementById('conv').style.display = 'none';
		}
		}
		</script>
		<td><select name="update" class="input_dropdown" onChange="javascript:addOptions(this);">
			<option>Select Your Option Below</option>
			<option value="revenue">Revenue :: Subid Report Upload</option>
			<!-- <option value="revenue_no_leads">Revenue :: Update (No Leads or Sales for Timeframe)</option> -->
			<option value="adwords">Adwords Cost Report</option>
			<!-- <option value="adwords_search">Adwords Search Network Report</option> -->
			<option value="yahoo">Yahoo Cost Report</option>
			<!-- <option value="yahoo_content">Cost :: Yahoo Content Network Report</option> -->
			<option value="msn">MSN Cost Report</option>
		</td>
	</tr>
	<tr id="cost" style="display:none">
		<td align="right" style="width:200px;">Date of Report :</td>
		<td align="left">
			<table align="left">
			<tr>
			<td align="left">
				<select name="a_day" tabindex="1" class="dropdown-loans" id="a_day" style="width:40px;">
				<?php 
				$yesterday = date("d")-1;
				if($yesterday < 10){
					$yesterday = "0".$yesterday;
				}
				echo "<option value=".$yesterday.">".$yesterday."</option>\n";				

				for($i=1;$i<32;$i++){
					/*
					if($i == date("j")){
						$selected = "selected=yes";
					}else{
						$selected = "";
					}
					*/
					if($i < 10){
						$fi="0".$i;
					}else{
						$fi = $i;
					}
					echo "<option value=".$fi.">".$fi."</option>\n";				
				}
				?>
				</select>
				<select name="a_month" tabindex="2" class="dropdown-loans" id="a_month" style="width:60px;">
				<?php
				$arr = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");

				if(date("j")-1 < 1){
					$ma = date("m")-1;
					$mb = date("M")-1;
					echo "<option value=".$ma.">".$mb."</option>\n";				
				}else{
					$ma = date("m");
					$mb = date("M");
					echo "<option value=".$ma.">".$mb."</option>\n";
				}
				
				foreach($arr as $a => $b){			
					/*
					if($a == date("m")){
						$selected = "selected=yes";
					}else{
						$selected = "";
					}	
					*/
					echo "<option value=".$a.' '.$selected.">".$b."</option>\n";				
				}
				?>
				
				</select>
				<select class="dropdown-loans" tabindex="3" name="a_year" id="a_year" style="width:70px;">
				<?php
				for($i=2000;$i<=2050;$i++){
																					
					$selected = "";
					if($i==date("Y")){
						$selected = "selected=yes";
					}
				
				//$formatted_i = number_format($i);
				?>
				<option value=<?php echo $i." ".$selected?>><?php echo $i?></option>

				<?php
				}
				?>
				</select>
			</td> 
			</tr>
			</table>
		</td>
	</tr>
 
	<tr id="subid" style="display:none">
		<td  onmouseover="Tip('&nbsp;&nbsp;This is the column number that the subid value is in.  It is different for each network.&nbsp;&nbsp;')" onmouseout="UnTip()" align="right">Column Number Of Subid :</td>
		<td><select name="subid_column" class="input_dropdown">
		<?php
		for($q=1;$q<21;$q++){
		?>
			<option value="<?php echo $q?>"><?php echo $q?></option>
		<?php
		}
		?></select>
		</td>
	</tr>
	<tr id="conv" style="display:none">
		<td  onmouseover="Tip('&nbsp;&nbsp;This is the column number that the conversion value is in.  It is different for each network.  For Direct Track networks and the download optional info report, leave it as n/a, for Neverblueads set it to 4.&nbsp;&nbsp;')" onmouseout="UnTip()" align="right">Column Number Of Conversions :</td>
		<td><select name="conversion_column" class="input_dropdown">
			<option value="na">n/a</option>
		<?php
		for($q=1;$q<21;$q++){
		?>
			<option value="<?php echo $q?>"><?php echo $q?></option>
		<?php
		}
		?></select>
		</td>
	</tr>
	<tr>
		<td align="right">File Name :</td>
		<td><input class="input_field" type="file" name="file"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="Update Data"></td>
	</tr>
	</table>
	</form>
<?php
// show the dates of updates for each campaign revenue and cost data
?>
	<center>
	<table>
	<tr>
		<th class="first" style="background-color: #9BCE00"><a class="reports" href="update.php?order_by=campaign_name&asc_desc=<?php echo $ad?>">Campaign Name</a></th>
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="update.php?order_by=campaign_name&asc_desc=<?php echo $ad?>">Last Day of Revenue Data</a></th>	
		<th style="background-color: #9BCE00" height="20"><a class="reports" href="update.php?order_by=campaign_name&asc_desc=<?php echo $ad?>">Last Day of Cost Data</a></th>
	</tr>
	<?php
	$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ($database);
	$start = 0;
	$end = 30;
	$sql = "SELECT DISTINCT(campaign) AS campaign, MAX(timestamp) AS time_stamp, MAX(cost_updated) AS cost_updated, MAX(revenue_updated) AS revenue_updated FROM kw_log WHERE cost_updated > 0 AND revenue_updated > 0 GROUP BY campaign ORDER BY $order_by $asc_desc LIMIT 0 , 25";
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
	if($val['revenue_updated'] == 0){
		$display_ru = "never";
	}else{
		$display_ru = date("n/d/Y",$val['time_stamp']);
	}
	if($val['cost_updated'] == 0){
		$display_cu = "never";
	}else{
		$display_cu = date("n/d/Y",$val['time_stamp']);
	}
	?>
	<tr class="<?php echo $row?>">
		<td align="left"><?php echo $val['campaign']?></td>
		<td align="center"><?php echo $display_ru;?></td>
		<td align="center"><?php echo $display_cu;?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="3" style="background-color: #000000">&nbsp;</td>
	</tr>
	</table>
	</CENTER>
</div>
<?php
include("footer.php");
?>