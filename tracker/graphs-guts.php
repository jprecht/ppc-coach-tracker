<?php
	// show default main page here
	?>
	<div id="content-wrap">
		<div id="content">
			<div style="width:760;align:center;">		
				<h1>Graphs</h1>
				
				<p><span style="background-color: #EEF7DB">What does this page do?</span></p>			
				
				<p class="gbox">The graphs section is very valuable.  You can view all your campaigns, adgroups and keywords by different timeframes using the links above.  You can also click on any column heading to sort by that column in ascending or descending order.  This is very powerful because you can see at a glance what your top and bottom campaigns, adgroups and keywords are.  Then you can use this information for your day to day account management.</p>
			</div>
	<?php
	//include("graphs_nav_menu.php");
	graphs_nav_menu($type, $when);
if($submit){

	// figure out different date ranges
	$now = time();
	switch($when){
		case "alltime":
			$start_time = "1";
			$end_time = strtotime("now");
			$display_title = "All Time Clicks";
		break;
		case "today":
			$start_time  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
			$end_time = mktime(23, 59, 59, date("m")  , date("d"), date("Y"));
			$display_title = "Today's Clicks";
			//$start_time = strtotime("-1 day");
			//$end_time = strtotime("now");
		break;
		case "yesterday":
			$start_time  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
			$end_time = mktime(23, 59, 59, date("m")  , date("d")-1, date("Y"));
			$display_title = "Yesterday's Clicks";
			//$start_time = strtotime("-2 day");
			//$end_time = strtotime("-1 day");
		break;
		case "last7":
			$start_time = strtotime("-7 day");
			$end_time = strtotime("now");
			$display_title = "Clicks For The Last Seven Days";
		break;
		case "mtd":
			$start_time = strtotime("first day",strtotime(date('F 0 Y')));
			$end_time = strtotime("now");
			$display_title = "Month To Date Clicks";
		break;
		case "last month":
			$start_time = strtotime("first day",mktime(0,0,0,date("n")-1,0,date("Y")));
			$end_time = strtotime("last day",mktime(0,0,0,date("n"),0,date("Y")));
			$display_title = "Clicks For Last Month";
		break;
		case "thismonthlastyear":
			$start_time = strtotime("first day",mktime(0,0,0,date("n")-13,0,date("Y")));
			$end_time = strtotime("last day",mktime(0,0,0,date("n")-12,0,date("Y")));
			$display_title = "Clicks This Time Last Year";
		break;
		case "custom":
			$start_time = mktime(0,0,0,$a_month, $a_day, $a_year);
			$end_time = mktime(23, 59, 59, $b_month, $b_day, $b_year);
			$display_title = "Clicks from $a_month/$a_day/$a_year to $b_month/$b_day/$b_year";
		break;
	}
	if($type == "clicks"){
		
		$FC = new FusionCharts("Line","800","400");
		$FC->setSwfPath("graphs/FusionCharts/");
		$strParam="caption=$display_title;xAxisName=Date/Time;yAxisName=Clicks;decimalPrecision=0; formatNumberScale=0; rotateNames=1";
		$FC->setChartParams($strParam);

		$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($database);

		for($i=$start_time;$i<=$end_time;$i+=86400){ 
			$end = $i + 86399;
			$strQuery = "select sum(clicks) as TotOutput from kw_log where timestamp BETWEEN $i AND $end";
			$result2 = mysql_query($strQuery) or die(mysql_error());
			$ors2 = mysql_fetch_array($result2);
			$FC->addChartData($ors2['TotOutput'],"name=".date("d/m/y", $i));
			mysql_free_result($result2);
		}
		mysql_close($dbh);
		$FC->renderChart();

	}// end if type is clicks
	
	if($type == "rep"){
		
		$categories = array("Revenue", "Expense");
		
		$revenue = array();
		$expense = array();

		$FC = new FusionCharts("MSColumn3D","800","400");
		$FC->setSwfPath("graphs/FusionCharts/");
		$strParam="caption=Revenue/Expense/Profit;xAxisName=Date/Time;yAxisName=$;decimalPrecision=2; formatNumberScale=$0.00; rotateNames=1";
		$FC->setChartParams($strParam);

		for($i=$start_time;$i<=$end_time;$i+=86400){ 
			$FC->addCategory(date("d/m/Y",$i));
		}
		
		foreach($categories as $cat){
			
			$FC->addDataset($cat); 

			if($cat == "Revenue"){
				$field_name = "revenue";
			}elseif($cat == "Expense"){
				$field_name = "cost";
			}
			$dbh = mysql_connect ($db_location, $username, $password); // or die ('I cannot connect to the database because: ' . mysql_error());
			mysql_select_db ($database);
			
			$num = 0;

			for($i=$start_time;$i<=$end_time;$i+=86400){ 
				$end = $i + 86399;
				$strQuery = "select sum($field_name) as TotOutput from kw_log where timestamp BETWEEN $i AND $end";
				$result2 = mysql_query($strQuery) or die(mysql_error());
				$ors2 = mysql_fetch_array($result2);
				$FC->addChartData($ors2['TotOutput']);
				mysql_free_result($result2);
				
				if($cat == "Revenue"){
					$revenue[] = $ors2['TotOutput'];
				}elseif($cat == "Expense"){
					$expense[] = $ors2['TotOutput'];
				}
				$num++;
			}
			mysql_close($dbh);
		}
		
		$FC->addDataset("Profit"); 
		
		$profit = array();

		for($i=0;$i<=sizeof($revenue);$i++){
			$profit = $revenue[$i] - $expense[$i];
			$FC->addChartData($profit);
		}

		$FC->renderChart();

	}// end if type is clicks

}
?>