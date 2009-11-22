<?php
// functions file

function graphs_nav_menu($type, $when){
?>
<script>
		function addOptions(_obj) {
		if (_obj.value == 'custom') {
		document.getElementById('show_date_fields').style.display = '';
		document.getElementById('show_date_fields2').style.display = '';
		}
		else {
		document.getElementById('show_date_fields').style.display = 'none';
		document.getElementById('show_date_fields2').style.display = 'none';
		}
		}
		</script>
<form method="post" action="<?php echo $_SERVER[PHP_SELF];?>">
<table cellpadding="0" cellspacing="0" width="400">
<tr>
	<td>Graph On:</td>
	<td>Date Range:</td>
<!-- 	<td>Time Zone:</td>
	<td>Currency:</td> -->
</tr>
<tr>
	<td><select name="type" class="input_dropdown">
        <option value="clicks" <?php if($type=="clicks"){echo "selected";}?>>Raw Clicks </option>
        <option value="rep" <?php if($type=="clicks"){echo "selected";}?>>Revenue Expense Profit </option>

        </select>
	</td>
	<td><select name="when" class="input_dropdown" onChange="javascript:addOptions(this);">
        <option value="alltime" <?php if($when=="alltime"){echo "selected";}?>>All Time</option>
        <option value="today" <?php if($when=="today"){echo "selected";}?>>Last 24 Hours</option>
        <option value="yesterday" <?php if($when=="yesterday"){echo "selected";}?>>Yesterday</option>
        <option value="last7" <?php if($when=="last7"){echo "selected";}?>>Last 7 Days</option>
        <option value="lastmonth" <?php if($when=="lastmonth"){echo "selected";}?>>Last Month</option>
        <option value="mtd" <?php if($when=="mtd"){echo "selected";}?>>Month To Date</option>
        <option value="thismonthlastyear" <?php if($when=="thismonthlastyear"){echo "selected";}?>>This Month Last Year</option>
        <option value="custom" <?php if($when=="custom"){echo "selected";}?>>Custom Date Range</option>
		</select>
	</td>
<!-- 	<td><select name="timezone" class="input_dropdown">
        <option value="no_adjustment">No Adjustment</option>
		</select>	
	</td>
	<td><select name="currency" class="input_dropdown">
        <option value="no_adjustment">No Adjustment</option>

		</select>	
	</td> -->
</tr>
<tr id="show_date_fields" style="display:none">
	<td>Start Date:</td>
	<td>End Date:</td>
</tr>
<tr id="show_date_fields2" style="display:none">
	<td><select name="a_day" class="input_dropdown" id="a_day" style="width:40px;">
        <option value="01" >01</option>
        <option value="02" >02</option>
        <option value="03" >03</option>
        <option value="04" >04</option>
        <option value="05" >05</option>
        <option value="06" >06</option>
        <option value="07" >07</option>
        <option value="08" >08</option>
        <option value="09" >09</option>
        <option value="10" >10</option>
        <option value="11" >11</option>
        <option value="12" >12</option>
        <option value="13" >13</option>
        <option value="14" >14</option>
        <option value="15" >15</option>
        <option value="16" >16</option>
        <option value="17" >17</option>
        <option value="18" >18</option>
        <option value="19" >19</option>
        <option value="20" >20</option>
        <option value="21" >21</option>
        <option value="22" >22</option>
        <option value="23" >23</option>
        <option value="24" >24</option>
        <option value="25" >25</option>
        <option value="26" >26</option>
        <option value="27" >27</option>
        <option value="28" >28</option>
        <option value="29" >29</option>
        <option value="30" >30</option>
        <option value="31" >31</option>
        </select>
        <select name="a_month" tabindex="2" class="input_dropdown" id="a_month" style="width:60px;">
        <option value="01" >Jan</option>
        <option value="02" >Feb</option>
        <option value="03" >Mar</option>
        <option value="04" >Apr</option>
        <option value="05" >May</option>
        <option value="06" >Jun</option>
        <option value="07" >Jul</option>
        <option value="08" >Aug</option>
        <option value="09" >Sep</option>
        <option value="10" >Oct</option>
        <option value="11" >Nov</option>
        <option value="12" >Dec</option>
        </select>
		<select class="input_dropdown" tabindex="3" name="a_year" id="a_year" style="width:70px;">
		<option value=2020>2020</option>
		<option value=2019>2019</option>
		<option value=2018>2018</option>
		<option value=2017>2017</option>
		<option value=2016>2016</option>
		<option value=2015>2015</option>
		<option value=2014>2014</option>
		<option value=2013>2013</option>
		<option value=2012>2012</option>
		<option value=2011>2011</option>
		<option value=2010>2010</option>
		<option value=2009>2009</option>
		<option value=2008 selected>2008</option>
		<option value=2007>2007</option>
		<option value=2006>2006</option>
		<option value=2005>2005</option>
		<option value=2004>2004</option>
		<option value=2003>2003</option>
		<option value=2002>2002</option>
		<option value=2001>2001</option>
		<option value=2000>2000</option>
		<option value=1999>1999</option>
		<option value=1998>1998</option>
		<option value=1997>1997</option>
		<option value=1996>1996</option>
		<option value=1995>1995</option>
		<option value=1994>1994</option>
		<option value=1993>1993</option>
		<option value=1992>1992</option>
		<option value=1991>1991</option>
		<option value=1990>1990</option>				
		</select>
	</td>
	<td>		<select name="b_day" tabindex="1" class="input_dropdown" id="a_day" style="width:40px;">
        <option value="01" >01</option>
        <option value="02" >02</option>
        <option value="03" >03</option>
        <option value="04" >04</option>
        <option value="05" >05</option>
        <option value="06" >06</option>
        <option value="07" >07</option>
        <option value="08" >08</option>
        <option value="09" >09</option>
        <option value="10" >10</option>
        <option value="11" >11</option>
        <option value="12" >12</option>
        <option value="13" >13</option>
        <option value="14" >14</option>
        <option value="15" >15</option>
        <option value="16" >16</option>
        <option value="17" >17</option>
        <option value="18" >18</option>
        <option value="19" >19</option>
        <option value="20" >20</option>
        <option value="21" >21</option>
        <option value="22" >22</option>
        <option value="23" >23</option>
        <option value="24" >24</option>
        <option value="25" >25</option>
        <option value="26" >26</option>
        <option value="27" >27</option>
        <option value="28" >28</option>
        <option value="29" >29</option>
        <option value="30" >30</option>
        <option value="31" >31</option>
        </select>
        <select name="b_month" tabindex="2" class="input_dropdown" id="a_month" style="width:60px;">
        <option value="01" >Jan</option>
        <option value="02" >Feb</option>
        <option value="03" >Mar</option>
        <option value="04" >Apr</option>
        <option value="05" >May</option>
        <option value="06" >Jun</option>
        <option value="07" >Jul</option>
        <option value="08" >Aug</option>
        <option value="09" >Sep</option>
        <option value="10" >Oct</option>
        <option value="11" >Nov</option>
        <option value="12" >Dec</option>
        </select>
		<select class="input_dropdown" tabindex="3" name="b_year" id="a_year" style="width:70px;">
		<option value=2020>2020</option>
		<option value=2019>2019</option>
		<option value=2018>2018</option>
		<option value=2017>2017</option>
		<option value=2016>2016</option>
		<option value=2015>2015</option>
		<option value=2014>2014</option>
		<option value=2013>2013</option>
		<option value=2012>2012</option>
		<option value=2011>2011</option>
		<option value=2010>2010</option>
		<option value=2009>2009</option>
		<option value=2008 selected>2008</option>
		<option value=2007>2007</option>
		<option value=2006>2006</option>
		<option value=2005>2005</option>
		<option value=2004>2004</option>
		<option value=2003>2003</option>
		<option value=2002>2002</option>
		<option value=2001>2001</option>
		<option value=2000>2000</option>
		<option value=1999>1999</option>
		<option value=1998>1998</option>
		<option value=1997>1997</option>
		<option value=1996>1996</option>
		<option value=1995>1995</option>
		<option value=1994>1994</option>
		<option value=1993>1993</option>
		<option value=1992>1992</option>
		<option value=1991>1991</option>
		<option value=1990>1990</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4"><input type="submit" name="submit" value="Get Report"></td>
</tr>
</table>

</form>
<?php
/*
print <<<EOF
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Campaigns</td>
	<td>[ <a href="reports.php?type=campaign&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
<tr>
	<td>Adgroups</td>
	<td>[ <a href="reports.php?type=adgroup&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
<tr>
	<td>Keywords</td>
	<td>[ <a href="reports.php?type=keyword&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
</table>
EOF;
*/
}

function reports_nav_menu($type, $when){
?>
<script>
		function addOptions(_obj) {
		if (_obj.value == 'custom') {
		document.getElementById('show_date_fields').style.display = '';
		document.getElementById('show_date_fields2').style.display = '';
		}
		else {
		document.getElementById('show_date_fields').style.display = 'none';
		document.getElementById('show_date_fields2').style.display = 'none';
		}
		}
		</script>
<form method="post" action="<?php echo $_SERVER[PHP_SELF];?>">
<table cellpadding="0" cellspacing="0" width="400">
<tr>
	<td>Report On:</td>
	<td>Date Range:</td>
<!-- 	<td>Time Zone:</td>
	<td>Currency:</td> -->
</tr>
<tr>
	<td><select name="type" class="input_dropdown">
        <option value="campaign" <?php if($type=="campaign"){echo "selected";}?>>All Campaigns</option>
        <option value="adgroup" <?php if($type=="adgroup"){echo "selected";}?>>All Adgroups</option>
        <option value="keyword" <?php if($type=="keyword"){echo "selected";}?>>All Keywords</option>
        <option value="rotation" <?php if($type=="rotation"){echo "selected";}?>>All Rotations</option>
        <option value="offer" <?php if($type=="offer"){echo "selected";}?>>All Offers</option>
        <option value="log" <?php if($type=="log"){echo "selected";}?>>Raw Click Log</option>
        <option value="export_csv_cost" <?php if($type=="export_csv_cost"){echo "selected";}?>>Export Cost Data To CSV File</option>
        <option value="export_csv_kws" <?php if($type=="export_csv_kws"){echo "selected";}?>>Export Keyword Data To CSV File</option>

        </select>
	</td>
	<td><select name="when" class="input_dropdown" onChange="javascript:addOptions(this);">
        <option value="alltime" <?php if($when=="alltime"){echo "selected";}?>>All Time</option>
        <option value="today" <?php if($when=="today"){echo "selected";}?>>Last 24 Hours</option>
        <option value="yesterday" <?php if($when=="yesterday"){echo "selected";}?>>Yesterday</option>
        <option value="last7" <?php if($when=="last7"){echo "selected";}?>>Last 7 Days</option>
        <option value="lastmonth" <?php if($when=="lastmonth"){echo "selected";}?>>Last Month</option>
        <option value="mtd" <?php if($when=="mtd"){echo "selected";}?>>Month To Date</option>
        <option value="thismonthlastyear" <?php if($when=="thismonthlastyear"){echo "selected";}?>>This Month Last Year</option>
        <option value="custom" <?php if($when=="custom"){echo "selected";}?>>Custom Date Range</option>
		</select>
	</td>
<!-- 	<td><select name="timezone" class="input_dropdown">
        <option value="no_adjustment">No Adjustment</option>
		</select>	
	</td>
	<td><select name="currency" class="input_dropdown">
        <option value="no_adjustment">No Adjustment</option>

		</select>	
	</td> -->
</tr>
<tr id="show_date_fields" style="display:none">
	<td>Start Date:</td>
	<td>End Date:</td>
</tr>
<tr id="show_date_fields2" style="display:none">
	<td><select name="a_day" class="input_dropdown" id="a_day" style="width:40px;">
        <option value="01" >01</option>
        <option value="02" >02</option>
        <option value="03" >03</option>
        <option value="04" >04</option>
        <option value="05" >05</option>
        <option value="06" >06</option>
        <option value="07" >07</option>
        <option value="08" >08</option>
        <option value="09" >09</option>
        <option value="10" >10</option>
        <option value="11" >11</option>
        <option value="12" >12</option>
        <option value="13" >13</option>
        <option value="14" >14</option>
        <option value="15" >15</option>
        <option value="16" >16</option>
        <option value="17" >17</option>
        <option value="18" >18</option>
        <option value="19" >19</option>
        <option value="20" >20</option>
        <option value="21" >21</option>
        <option value="22" >22</option>
        <option value="23" >23</option>
        <option value="24" >24</option>
        <option value="25" >25</option>
        <option value="26" >26</option>
        <option value="27" >27</option>
        <option value="28" >28</option>
        <option value="29" >29</option>
        <option value="30" >30</option>
        <option value="31" >31</option>
        </select>
        <select name="a_month" tabindex="2" class="input_dropdown" id="a_month" style="width:60px;">
        <option value="01" >Jan</option>
        <option value="02" >Feb</option>
        <option value="03" >Mar</option>
        <option value="04" >Apr</option>
        <option value="05" >May</option>
        <option value="06" >Jun</option>
        <option value="07" >Jul</option>
        <option value="08" >Aug</option>
        <option value="09" >Sep</option>
        <option value="10" >Oct</option>
        <option value="11" >Nov</option>
        <option value="12" >Dec</option>
        </select>
		<select class="input_dropdown" tabindex="3" name="a_year" id="a_year" style="width:70px;">
		<option value=2020>2020</option>
		<option value=2019>2019</option>
		<option value=2018>2018</option>
		<option value=2017>2017</option>
		<option value=2016>2016</option>
		<option value=2015>2015</option>
		<option value=2014>2014</option>
		<option value=2013>2013</option>
		<option value=2012>2012</option>
		<option value=2011>2011</option>
		<option value=2010>2010</option>
		<option value=2009>2009</option>
		<option value=2008 selected>2008</option>
		<option value=2007>2007</option>
		<option value=2006>2006</option>
		<option value=2005>2005</option>
		<option value=2004>2004</option>
		<option value=2003>2003</option>
		<option value=2002>2002</option>
		<option value=2001>2001</option>
		<option value=2000>2000</option>
		<option value=1999>1999</option>
		<option value=1998>1998</option>
		<option value=1997>1997</option>
		<option value=1996>1996</option>
		<option value=1995>1995</option>
		<option value=1994>1994</option>
		<option value=1993>1993</option>
		<option value=1992>1992</option>
		<option value=1991>1991</option>
		<option value=1990>1990</option>				
		</select>
	</td>
	<td>		<select name="b_day" tabindex="1" class="input_dropdown" id="a_day" style="width:40px;">
        <option value="01" >01</option>
        <option value="02" >02</option>
        <option value="03" >03</option>
        <option value="04" >04</option>
        <option value="05" >05</option>
        <option value="06" >06</option>
        <option value="07" >07</option>
        <option value="08" >08</option>
        <option value="09" >09</option>
        <option value="10" >10</option>
        <option value="11" >11</option>
        <option value="12" >12</option>
        <option value="13" >13</option>
        <option value="14" >14</option>
        <option value="15" >15</option>
        <option value="16" >16</option>
        <option value="17" >17</option>
        <option value="18" >18</option>
        <option value="19" >19</option>
        <option value="20" >20</option>
        <option value="21" >21</option>
        <option value="22" >22</option>
        <option value="23" >23</option>
        <option value="24" >24</option>
        <option value="25" >25</option>
        <option value="26" >26</option>
        <option value="27" >27</option>
        <option value="28" >28</option>
        <option value="29" >29</option>
        <option value="30" >30</option>
        <option value="31" >31</option>
        </select>
        <select name="b_month" tabindex="2" class="input_dropdown" id="a_month" style="width:60px;">
        <option value="01" >Jan</option>
        <option value="02" >Feb</option>
        <option value="03" >Mar</option>
        <option value="04" >Apr</option>
        <option value="05" >May</option>
        <option value="06" >Jun</option>
        <option value="07" >Jul</option>
        <option value="08" >Aug</option>
        <option value="09" >Sep</option>
        <option value="10" >Oct</option>
        <option value="11" >Nov</option>
        <option value="12" >Dec</option>
        </select>
		<select class="input_dropdown" tabindex="3" name="b_year" id="a_year" style="width:70px;">
		<option value=2020>2020</option>
		<option value=2019>2019</option>
		<option value=2018>2018</option>
		<option value=2017>2017</option>
		<option value=2016>2016</option>
		<option value=2015>2015</option>
		<option value=2014>2014</option>
		<option value=2013>2013</option>
		<option value=2012>2012</option>
		<option value=2011>2011</option>
		<option value=2010>2010</option>
		<option value=2009>2009</option>
		<option value=2008 selected>2008</option>
		<option value=2007>2007</option>
		<option value=2006>2006</option>
		<option value=2005>2005</option>
		<option value=2004>2004</option>
		<option value=2003>2003</option>
		<option value=2002>2002</option>
		<option value=2001>2001</option>
		<option value=2000>2000</option>
		<option value=1999>1999</option>
		<option value=1998>1998</option>
		<option value=1997>1997</option>
		<option value=1996>1996</option>
		<option value=1995>1995</option>
		<option value=1994>1994</option>
		<option value=1993>1993</option>
		<option value=1992>1992</option>
		<option value=1991>1991</option>
		<option value=1990>1990</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="4"><input type="submit" name="submit" value="Get Report"></td>
</tr>
</table>

</form>
<?php
/*
print <<<EOF
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Campaigns</td>
	<td>[ <a href="reports.php?type=campaign&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=campaign&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
<tr>
	<td>Adgroups</td>
	<td>[ <a href="reports.php?type=adgroup&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=adgroup&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
<tr>
	<td>Keywords</td>
	<td>[ <a href="reports.php?type=keyword&when=alltime">Alltime</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=today">Today</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=yesterday">Yesterday</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=last7">Last 7 Days</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=lastmonth">Last Month</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=mtd">Month To Date</a> ]</td>
	<td>[ <a href="reports.php?type=keyword&when=thismonthlastyear">This Month Last Year</a> ]</td>
</tr>
</table>
EOF;
*/
}

function manager_nav_menu(){
	print <<<EOF
<table cellpadding="0" cellspacing="0">
<tr>
	<!-- <td>Menu</td> -->
	<td>[ <a href="manager.php?action=landing_pages">Landing Pages</a> ]</td>
	<td>[ <a href="manager.php?action=offers">Offers</a> ]</td>
	<td>[ <a href="manager.php?action=get_link">Get a.php Link</a> ]</td>
	<td>[ <a href="manager.php?action=sync">Sync</a> ]</td>
	<td>[ <a href="manager.php?action=lp_rotation">Landing Page Rotations</a> ]</td>
	<td>[ <a href="manager.php?action=rotation">Offer Rotations</a> ]</td>
	<td>[ <a href="manager.php?action=subids">Edit Subid(s)</a> ]</td>

</tr>
</table>
EOF;
}

function page_nav_menu($start, $show_per_page, $offset, $type, $start_time, $end_time, $when, $order_by, $asc_desc, $total_records, $dig, $campaign_name){
if($start==0){
	$temp_start=1;
}else{
	$temp_start=$start;
}
$total_records = number_format($total_records);
$page = $offset + 1;
$total_pages = $total_records/$show_per_page;
$total_pages = ceil($total_pages);
if($page > $total_pages){
	$page -= 1;
}
$total_records_d = number_format($total_records);
print <<<abc
<form method="post" action="$_SERVER[PHP_SELF]">
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Page $page/$total_pages</td>
	<td><input type="submit" name="first_page" value="First Page"></td>
	<td><input type="submit" name="next_page" value="Next Page"></td>
	<td><input type="submit" name="previous_page" value="Previous Page"></td>
	<td><input type="submit" name="last_page" value="Last Page"></td>
	<td>Show <input type="text" name="show_per_page" style="width:20px;" value="$show_per_page"> rows <!-- starting at row <input type="text" name="start" style="width:20px;" value="$start"> --> <input type="submit" name="rows_entered" value="Go"></td>
	<td>Total Records: $total_records_d</td>	
</tr>
</table>
<input type="hidden" name="old_show_per_page" value="$show_per_page">
<input type="hidden" name="start" value="$start">
<input type="hidden" name="offset" value="$offset">
<input type="hidden" name="type" value="$type">
<input type="hidden" name="start_time" value="$start_time">
<input type="hidden" name="end_time" value="$end_time">
<input type="hidden" name="when" value="$when">
<input type="hidden" name="order_by" value="$order_by">
<input type="hidden" name="asc_desc" value="$asc_desc">
<input type="hidden" name="dig" value="$dig">
<input type="hidden" name="total_records" value="$total_records_d">
<input type="hidden" name="campaign_name" value="$campaign_name">
</form>
abc;

}

function page_nav_menu_mgr($start, $show_per_page, $offset, $action, $order_by, $asc_desc, $total_records){
/*
if($start==0){
	$temp_start=1;
}else{
	$temp_start=$start;
}
*/
$total_records = number_format($total_records);
$page = $offset + 1;
$page = ceil($page);
$total_pages = $total_records/$show_per_page;
$total_pages = ceil($total_pages);
if($page > $total_pages){
	$page -= 1;
}
$total_records_d = number_format($total_records);
print <<<abc
<form method="post" action="$_SERVER[PHP_SELF]">
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Page $page/$total_pages</td>
	<td><input type="submit" name="first_page" value="First Page">&nbsp;&nbsp;<input type="submit" name="next_page" value="Next Page">&nbsp;&nbsp;<input type="submit" name="previous_page" value="Previous Page">&nbsp;&nbsp;<input type="submit" name="last_page" value="Last Page"></td>
</tr>
<tr>
	<td>Total Records: $total_records_d</td>
	<td>Show <input type="text" name="show_per_page" style="width:20px;" value="$show_per_page"> rows <!-- starting at row <input type="text" name="start" style="width:20px;" value="$start"> --> <input type="submit" name="rows_entered" value="Go"></td>
		
</tr>
</table>
<input type="hidden" name="old_show_per_page" value="$show_per_page">
<input type="hidden" name="start" value="$start">
<input type="hidden" name="offset" value="$offset">
<input type="hidden" name="action" value="$action">
<input type="hidden" name="order_by" value="$order_by">
<input type="hidden" name="asc_desc" value="$asc_desc">
<input type="hidden" name="total_records" value="$total_records_d">
</form>
abc;

}
?>