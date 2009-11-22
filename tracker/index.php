<?php
$current_menu_item = "home";
include_once("config.php");
include_once("header.php");
// Display the keywords and all their info
?>
<div class="banner">

<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" id="AutoNumber6" height="128">
<tr>
	<td width="100%" height="18"></td>
</tr>
<tr>
	<td width="100%" height="38"><p align="left"><font color="#FFFFFF"><span style="font-size: 16pt"><b>P</b></span></font><b><font color="#FFFFFF" style="font-size: 16pt">PC Coach Tracking System</font></b></td>
</tr>
<tr>
	<td width="100%" height="1"><p align="left"><img border="0" src="../images/green-bullet.gif" align="baseline" width="7" height="7"><b><font color="#DADADA" style="font-size: 12pt">Track Every Click<br clear="all"></font></b><img border="0" src="../images/green-bullet.gif" align="baseline" width="7" height="7"><b><font color="#DADADA" style="font-size: 12pt">Hosted By You<br clear="all"></font></b><img border="0" src="../images/green-bullet.gif" align="baseline" width="7" height="7"><b><font color="#DADADA" style="font-size: 12pt">Now Every Keywords EPC, ROI & GM<br clear="all"></font></b><img border="0" src="../images/green-bullet.gif" align="baseline" width="7" height="7"><b><font color="#DADADA" style="font-size: 12pt">Easy To Use Technology</font></b></td>
</tr>
<tr>
	<td width="100%" height="18"></td>
</tr>
<tr>
	<td width="100%" height="18"></td>
</tr>
<tr>
	<td width="100%" height="18"></td>
</tr>
<tr>
	<td width="100%" height="18"></td>
</tr>
</table>
</div>
<!-- content-wrap starts here -->
<div id="content-wrap">
	<div id="content">		
<!-- 		<div id="sidebar" >
			<div class="login">
			<?php
			/*
			if($_COOKIE['logged_in'] == "yes"){
				echo "You are logged in as ".$_COOKIE['site_username'];
			}elseif($login == "necessary"){
			*/
			?>
				<h1>&nbsp;<img border="0" src="../images/login.gif" align="absmiddle" style="border-width: 0" width="16" height="16"> Account Access</h1>
				<form method="POST" action="check-user.php">
				<p>
					<input type="text" name="site_username" size="30" value="Username" style="color: #000000">
					<br clear="all">
					<img border="0" src="../images/clear.gif" width="15" height="1">
					<input type="password" name="site_password" size="30" value="Password" style="color: #000000">
				</p>
				<p>
					Remember me <input type="checkbox" name="remember" value="ON">
				</p>
				<input type="hidden" name="maa" value="do_login">				
				<p>
					<input class="button" type="submit" name="submit" value="Login" />
					</form>
				</p>
				<?php
				/*
				}
				*/
				?>
			</div>
		</div> -->
		<div id="main" style="width: 100%; height: 598">		
			<div class="">
				<a name="TemplateInfo"></a>	
				<h1>Weclome to The PPC Coach Tracker</h1>
				<p>The purpose of this site is to provide members of the <span style="background-color: #EEF7DB">PPC-Coach.com</span> with the ability to track their campaigns, keywords and adgroups accurately and easily.  This means you can get accurate data on every campaign, adgroup and keywords EPC, GM and ROI.  With the ever increasing competition out there, one of the most important factors for success is the ability to track everything right down to the keyword level.  Now it is possible, without having to risk your data on someone elses server or use clunky excel spreadsheets.  PPC Coach Tracker is <span style="background-color: #EEF7DB">easy to set up and very user friendly</span>.</p>

			</div>
			
				<a name="SampleTags"></a>
				<p>
												
			
				
			
			
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" id="AutoNumber5" height="1">

<!--                   <tr>
                    <td width="38%" height="128">
				<a href="http://targetedclix.com/">
				<img border="0" src="../images/hostingplan1.jpg" width="254" height="126" style="border-width: 0"></a></td>
                    <td width="62%" height="256" rowspan="2" valign="top">
				<a href="http://targetedclix.com/">
				<img border="0" src="../images/features.jpg" width="194" height="256" style="border-width: 0"></a></td>
                  </tr>
                  <tr>
                    <td width="38%" height="128">
				<a href="http://targetedclix.com/">
				<img border="0" src="../images/hostingplan2.jpg" width="254" height="126" style="border-width: 0"></a></td>
                  </tr>
                  <tr>
                    <td width="100%" height="1" colspan="2">
                <p></td>
                  </tr> -->
                  <tr>
                    <td width="100%" height="44" colspan="2">
				So if you've already set up your database tables and updated your configuration file, then go ahead and log in now.  Of course if you have any questions or need support, visit the PPC-Coach.com forums..
               </td>
                  </tr>
                </table>

		
</body>

<?php
include("footer.php");
?>