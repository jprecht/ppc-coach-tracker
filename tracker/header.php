<?php
include_once("check-user.php");
extract($_REQUEST);
include_once("config.php");
// license check removed now
// key.txt not needed anymore
include("functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Keywords" content="your, keywords" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Distribution" content="Global" />
<meta name="Robots" content="index,follow" />
<link rel="stylesheet" href="style.css" type="text/css" />
<title>PPC Coach Tracker :: Tracking Is The Key</title>	
</head>
<SCRIPT LANGUAGE="Javascript" SRC="graphs/FusionCharts/FusionCharts.js"></SCRIPT>
<body>
<script type="text/javascript" src="wz_tooltip.js"></script>
<script type="text/javascript" src="wz_tooltip.js"></script>
<!-- wrap starts here -->
<div id="wrap">
	<div id="header">
		<div id="header-content">	
		<h1 id="logo"><font face="Trebuchet MS" style="font-size: 32px"><a href="http://<?php echo $_SERVER['PHP_SELF'];?>">ppc<font color="#9BCE00">coach tracker</font></a></font><span style="font-weight: 400"><font color="#9BCE00" face="Jolly Raunchy"></font><font face="Trebuchet MS" size="3" color="#9BCE00"><br clear="all"></font><font face="Trebuchet MS" color="#BFBFBF" size="3">PPC Tracking System</font></span></h1>	
		<!-- Menu Tabs -->
		<ul>
			<li><a href="index.php" <?php if($current_menu_item == "home"){ echo "id=current";}?>>Home</a></li>
			<li><a href="quick-start.php" <?php if($current_menu_item == "quick_start"){ echo "id=current";}?>>Quick Start</a></li>
			<li><a href="manager.php" <?php if($current_menu_item == "manager"){ echo "id=current";}?>>Manager</a></li>
			<li><a href="reports.php" <?php if($current_menu_item == "reports"){ echo "id=current";}?>>Reports</a></li>
			<li><a href="graphs.php" <?php if($current_menu_item == "graphs"){ echo "id=current";}?>>Graphs</a></li>
			<li><a href="update.php" <?php if($current_menu_item == "update"){ echo "id=current";}?>>Update</a></li>
		</ul>		
		</div>
	</div>
