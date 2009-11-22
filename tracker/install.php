<?php

//Turn off the warings

error_reporting(1);



					
/* 
	Write Config File If Post Data is Present
*/

if ($_POST['config']==1)
{

$stringData ="<?php
// config file for ppc-coach-tracker.php
//
// You must input all the required information or the script will not work
//
// set your tracker username and password below
\$main_username = '".$_POST['main_username']."'; // set it to whatever you want your username to be
\$main_password = '".$_POST['main_password']."'; // set your main password
// mysql information
\$db_location = '".$_POST['db_location']."'; // usually localhost
\$username = '".$_POST['username']."'; // mysql db username
\$password = '".$_POST['password']."'; // mysql db password
\$database = '".$_POST['database']."'; // mysql database name
// ppc coach information
\$ppc_coach_username = '".$_POST['ppc_coach_username']."'; // input your ppc-coach.com username
\$ppc_coach_password = '".$_POST['ppc_coach_password']."'; // input your ppc-coach.com password
\$ppc_coach_email = '".$_POST['ppc_coach_email']."'; // input your ppc-coach.com email address
?>";

	
	$fh = fopen('config.php', 'w') or die("can't open file");
	fwrite($fh, $stringData);
	fclose($fh);												
}
	
					
/* 
	Write API Config File If Post Data is Present
*/

if ($_POST['api-config']==1)
{


$stringData="<?php
// mysql information
\$db_location = '".$_POST['db_location']."'; // usually localhost
\$db_username = '".$_POST['db_username']."'; // mysql db username
\$db_password = '".$_POST['db_password']."'; // mysql db password
\$db_database = '".$_POST['db_database']."'; // mysql database name

// Provide AdWords login information.
\$email = '".$_POST['email']."';
\$password = '".$_POST['password']."';

// Provide API information
// http://code.google.com/apis/adwords/
\$client_email = '".$_POST['client_email']."';// usually same as email
\$useragent = '".$_POST['useragent']."';
\$developer_token = '".$_POST['developer_token']."';
\$application_token = '".$_POST['application_token']."';
?>
";
	
	$fh = fopen('api/config.php', 'w') or die("can't open file");
	fwrite($fh, $stringData);
	fclose($fh);		

}


/*
	include file to string
*/					

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Install PPC Coach Tracker</title>

<style type="text/css">

	body {
	
		font-family:Arial, Helvetica, sans-serif;
		background-color: #BBBBBB;
	
	}

	.menuitem {
	
		display:block;
		background-color:#EAEAEA;
		color:#0066FF;
		padding:12px;
		text-decoration:none;
		font-size:16px;
		border:1px solid #999999;
		border-right:none;
	
	}

	.menuitemon {
	
		display:block;
		background-color:#ffffff;
		color:#0066FF;
		padding:12px;
		text-decoration:none;
		font-size:16px;
		border:1px solid #999999;
		border-right:none;
	
	}
	
	.menuitem:hover {
	
		text-decoration:underline;
		background-color:#CCEEFF;
	
	}
	
	.title {
	
		font-size:26px;
		font-family:Georgia, "Times New Roman", Times, serif;
		font-weight:normal;
		
	}

	.smallText {
	
		font-size:12px;
	
	}

</style>


</head>

<body>


<?php


/* Check other files */

include('config.php');


/* Establish Database Connection */



$link = mysql_connect($db_location, $username, $password);

mysql_select_db($database,$link);






/*
	Drop All Tables If Variable Is Passed
*/

if($_POST['reset-table']==1)
	{

		mysql_query('DROP TABLE `cost` ,`kw_log` ,`landing_pages` ,`lp_rotations` ,`offers` ,`rotations` ;');
	
	}
?>

<table cellspacing="0" cellpadding="0" align="center" width="960">

	<tr>
    	<td colspan="2">    

            <div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFDDDD; border:2px dotted #CC3333; color:#FF0000;">
            
            <strong>Once you finish installing please delete this file (install.php), leaving it on your server is a serious security risk!</strong>
            
            </div>
          
    	</td>
    </tr>

	<tr>
    
    	<td width="165" height="130" valign="top">
        
        	<?php if ($_GET['step']==1 || $_GET['step']=='') { $class='menuitemon'; } else { $class='menuitem'; } ?> 
        	<a href="?step=1"class="<?php echo $class; ?>"><strong>1. Verification</strong></a>
            
            <div style="border-right:1px solid #999999; font-size:4px;">&nbsp;</div>
        
        	<?php if ($_GET['step']==2) { $class='menuitemon'; } else { $class='menuitem'; } ?> 
        	<a href="?step=2"class="<?php echo $class; ?>"><strong>2. Configuration</strong></a>
            
            <div style="border-right:1px solid #999999; font-size:4px;">&nbsp;</div>
                
        	<?php if ($_GET['step']==3) { $class='menuitemon'; } else { $class='menuitem'; } ?> 
        	<a href="?step=3" class="<?php echo $class; ?>"><strong>3. Database Setup</strong></a>
            
            <div style="border-right:1px solid #999999; font-size:4px;">&nbsp;</div>
                            
        	<?php if ($_GET['step']==4) { $class='menuitemon'; } else { $class='menuitem'; } ?> 
        	<a href="?step=4" class="<?php echo $class; ?>"><strong>4. Google API</strong></a>
        </td>
    	<td style="background-color:#FFFFFF; border:1px solid #999999; border-left:none; padding-left:20px; padding-right:20px;" valign="top" rowspan="2">

<?php

/*------------------------------------------------------------------

Step 1 - Tell them to make sure they uploaded, give a good faq, and check for corruption
	
------------------------------------------------------------------*/   

?>     
			<?php if ($_GET['step']==1 || $_GET['step']=='') { ?>
            
                  <h1 class="title">Step 1 - Verify The Files</h1>
                  
                  
                  <?php if(is_writable('config.php')){  ?>
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>config.php exists and is writeable</strong></div>     
                        
                  <?php }else{ ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                        <strong>Config.php is NOT writeable</strong><br />
						<a href="http://www.youtube.com/watch?v=VfKTvYFGEvs">Click here</a> to see how to make files writeable using <a href="http://filezilla-project.org/">FileZilla (Freeware FTP Client)</a>
                     </div>               
                  
                               
                  <?php } ?>
                  
                
                  <?php if(is_writable('api/config.php')) { ?>
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>api/config.php exists and is writeable</strong></div>     
                        
                  <?php }else{ ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                        <strong>api/config.php is NOT writeable</strong><br />
						<a href="http://www.youtube.com/watch?v=VfKTvYFGEvs">Click here</a> to see how to make files writeable using <a href="http://filezilla-project.org/">FileZilla (Freeware FTP Client)</a>
                     </div>               
                  
                               
                  <?php } ?>
                  
                  <div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
                  
                  <strong>You should have already uploaded all tracker files in binary mode. They must be uploaded in binary mode due to the way the files are encrypted, if not, none of the tracker files will work. Check the bottom of this page to see if the files were uploaded properly.</strong>
<br />
<a href="?step=2"><b>If everything is uploaded properly click here to goto Step 2</b></a>
                  
                  </div>


					<h1 class="title">Frequently Asked Questions</h1>
                    
                    <div style="line-height:24px;">
                    
                    <b>Q:</b> <em>What Is FTP?</em><br />
                    <b>A:</b> FTP stands for file transfer protocol, and in short, it's how you get files from your computer to your online server.  To do ftp you should get an ftp client, luckily there is a fully featured, free FTP client you can download, it's called Filezilla (<a href="http://filezilla-project.org/">Click Here To Download It</a>).<br /><br />               
                    
                    <b>Q:</b> <em>How do I upload in binary?</em><br />
                    <b>A:</b> To do a binary transfer in Filezilla just goto the menu and click Transfer > Transfer Type > Binary<br /><br />
                    
                    <b>Q:</b> <em>How do I make sure my config is writable?</em><br />
                  	<b>A:</b> This installer automatically checks if files are writable, if they aren't, <a href="http://www.youtube.com/watch?v=VfKTvYFGEvs">here's a video tutorial</a> to show you how to make them writable in Filezilla<br /><br />
                    
                  	<b>Q:</b> <em>How do I delete install.php when I'm finished?</em><br />
                    <b>A:</b> Just go into your FTP client and delete install.php by selecting it and hitting delete. DO NOT leave this file on your server as it exposes your passwords and database information!<br />
<br />
                    
                    <b>Q:</b> <em>How do I get my google api account?</em><br />
                    <b>A:</b> The process of getting a google account is easy. We recommend reading the official google api getting started guide. Having an api account allows you to access your adwords data from anywhere, the first step is to get your developer token.  After you get your developer token you have to get an application token. When asking for both tokens just say you're building a professional keyword tracking tool, if it sounds legit you'll get approved pretty quickly!<br /><br />
                    
                    <b>Q:</b> <em>How do I setup a mysql database in cpanel?</em><br />
                    <b>A:</b> This is actually pretty easy to do. Just follow your hosts instructions on how to login to your cpanel (usually in your welcome email), and then <a href="http://www.youtube.com/watch?v=nfM0xNwkAMA">click here to watch a video tutorial</a>.<br />
<br />
</div>

				<h1 class="title">File Checker</h1>

                  <div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
                    <strong>If there is a fatal error below then your files were not uploaded properly<br />
					If there are no errors below then you are good to go! (There should be a login box if it worked)
                    </strong><br />
                  </div>

					<?php
                    	$files = array('a','b','check-user','footer','functions','graphs-guts','graphs','header','index','manager','quick-start','reports','speed-fix','update','api/adwords','api/runreport');
						
				
						
						foreach($files as $key => $value) {						
						    			           
						   get_include_contents($value.'.php');		
						
						}
						
					
					?>
                    
                   												
                    
                    
<br />




<?php

/*------------------------------------------------------------------

Step 2 - Just take inputs and write them to the config file
	
------------------------------------------------------------------*/   

?>                   
                  
            
			<?php } elseif ($_GET['step']==2) { ?>
            
                  <h1 class="title">Step 2 - Setup Your Configuration File</h1>
                  
 				  <?php if ($link && $main_username!='' && $main_password!='' && $db_location!='' && $username!='' && $password!='' && $database!='' && $ppc_coach_username!='' && $ppc_coach_password!='' && $ppc_coach_email!='') { ?>

                  	<div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>Step 2 complete! <a href="?step=3">Click here to setup your tracker database</a></strong>
                    
            		</div>
                              
                  <?php } ?>
                  
 				  <?php if ($_POST['config']==1) { ?>
                  
                  	<div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>Config.php has been successfully updated!</strong>
                    
            		</div>
                              
                  <?php } ?>
                  
                  <?php if(is_writable('config.php')){  ?>
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Config.php is writeable</strong></div>     
                        
                  <?php }else{ ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                        <strong>Config.php is NOT writeable</strong><br />
						<a href="http://www.youtube.com/watch?v=VfKTvYFGEvs">Click here</a> to see how to make files writeable using <a href="http://filezilla-project.org/">FileZilla (Freeware FTP Client)</a>
                     </div>
                  
                               
                  <?php } ?>
                  
                  
                  <?php
				  
				  
				   if(!$link) { 
				   
				   ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                     	   <strong>MySQL connection failed</strong><br />
							<?php if($username=='') { ?>
                            
                            	Please enter a username for your database<br />
                            
                            <?php } ?>     
                              
							<?php if($password=='') { ?>
                            
                            	Please enter a password for your database<br />
                            
                            <?php } ?>   
                              
							<?php if($db_location=='') { ?>
                            
                            	Please enter the name of your database<br />
                            
                            <?php } ?>   
                            
                            <?php if($username!='' && $password!='' && db_location!='') { ?>
                            
                            	Connection error, please double check your settings
                            
                            <? } ?>    
                            
                             <br /><a href="http://www.youtube.com/watch?v=nfM0xNwkAMA">Click here</a> to watch a video on how to setup a mysql database through cpanel
                     </div>
                  
                  
                  
                  <?php } else { ?>
                  
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Connected to MySQL database successfully</strong></div>                             
                  
                  
                  <?php } ?>
                  
                  <form action="?step=2" method="post">
                  
                  		<input type="hidden" name="config" value="1" />
                  
                  		<div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                  			<strong>Tracker Login Information</strong>
							<div class="smallText">This username and password is used to login to tracker on your server</div>
                  		</div>
                        
                        
                        <br />

                  
                  <table cellpadding="5">
                  
                  	<tr>
                    	<td align="right" width="120">
                        	Username
                        </td>
                        <td>
                        	<input type="text" name="main_username" value="<?php echo $main_username ?>" />
                        </td>
                        <td class="smallText">
                        	The username you'll use to login to tracker
                        </td>
                   	</tr>
                    
                  
                  	<tr>
                    	<td align="right">
                        	Password
                        </td>
                        <td>
                        	<input type="text" name="main_password" value="<?php echo $main_password ?>" />
                        </td>
                        <td class="smallText">
                        	The password you'll use to login to tracker
                        </td>
                   	</tr>
                  
                  </table>

                  <div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                  		<strong>MySQL Database Connection</strong>
						<div class="smallText">This information is used to connect to your MySQL Database</div>
                  </div>
                  
                  <br />
                  
                  <table cellpadding="5">
                  
                  	<tr>
                    	<td align="right" width="120">
                        	Host Name
                        </td>
                        <td>
                        	<input type="text" name="db_location" value="<?php echo $db_location ?>" />
                        </td>
                        <td class="smallText">
                        	Your database host name (Usually: <strong>localhost</strong>)
                        </td>
                   	</tr>
                  
                  	<tr>
                    	<td align="right">
                        	Username
                        </td>
                        <td>
                        	<input type="text" name="username" value="<?php echo $username ?>" />
                        </td>
                        <td class="smallText">
                        	Your database username, usually setup in your cpanel.
                        </td>
                   	</tr>
                    
                  
                  	<tr>
                    	<td align="right">
                        	Password
                        </td>
                        <td>
                        	<input type="text" name="password" value="<?php echo $password ?>" />
                        </td>
                        <td class="smallText">
                        	Your database username password, usually setup in your cpanel.                        
                        </td>
                   	</tr>
                  
                  
                  	<tr>
                    	<td align="right">
                        	Database Name
                        </td>
                        <td>
                        	<input type="text" name="database" value="<?php echo $database ?>" />
                        </td>
                        <td class="smallText">
                        	The name of the database that your tracker will be installed in.
                        </td>
                   	</tr>
                 
                 
                 </table>
                  
                  <div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                  
                        	<strong>PPC Coach Account Information</strong>
							<div class="smallText">This is used to validate your tracker installation</div>
    			  </div>
                  
                  <br />
                  
                  <table cellpadding="5">
                  
                  	<tr>
                    	<td align="right" width="120">
                        	Username
                        </td>
                        <td>
                        	<input type="text" name="ppc_coach_username" value="<?php echo $ppc_coach_username ?>" />
                        </td>
                        <td class="smallText">
                        	PPC Coach Username
                        </td>
                   	</tr>
                    
                  
                  	<tr>
                    	<td align="right">
                        	Password
                        </td>
                        <td>
                        	<input type="text" name="ppc_coach_password" value="<?php echo $ppc_coach_password ?>" />
                        </td>
                        <td class="smallText">
                        	PPC Coach Password                        
                        </td>
                   	</tr>
                    
                  
                  	<tr>
                    	<td align="right">
                        	Email
                        </td>
                        <td>
                        	<input type="text" name="ppc_coach_email" value="<?php echo $ppc_coach_email ?>" />
                        </td>
                        <td class="smallText">
                        	The email you registered with PPC Coach
                        </td>
                   	</tr>
                  
                    
                 </table>
                 
                 <div style="padding-top:20px; padding-bottom:20px;">
                 	<input type="submit" value="Update Config File" style="font-size:18px;" />                 
                 </div>
                 
            </form>   

<?php

/*------------------------------------------------------------------

Step 3 - Check if tables exist, if they don't, create them.  If they do, report the number of fields they have. Manual reinstall simply drops all the tables and the script just installs them like it normally would
	
------------------------------------------------------------------*/   

?>         
            
            <?php } elseif ($_GET['step']==3) { ?>      
            
                                        
                  <h1 class="title">Step 3 - Create Tracker Tables In The Database</h1>
                  
                  	<?php $errorCheck=''; $result = mysql_query('SELECT * FROM `kw_log`'); $errorCheck .= mysql_error();  $result = mysql_query('SELECT * FROM `cost`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `offers`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `landing_pages`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `rotations`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `lp_rotations`'); $errorCheck .= mysql_error(); ?>
                  
                  
                  	<?php if($errorCheck=='') { ?>
                  
                    <div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>Step 3 complete! <a href="?step=4">Click here to setup your google api integration</a></strong>
                    
            		</div>

                  	<?php } ?>
                  
                  <?php
				  
				  
				   if(!$link) { 
				   
				   ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                     	   <strong>MySQL connection failed - <a href="?step=2">Please Return To Step One To Fix It</a></strong><br /> 
                            
                             <br /><a href="http://www.youtube.com/watch?v=nfM0xNwkAMA">Click here</a> to watch a video on how to setup a mysql database through cpanel
                     </div>
                  
                  
                  
                  <?php } else { ?>
                  
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Connected to MySQL database successfully</strong></div>                             
                  
                  
                  <?php } ?>
            
            		<div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>This step is completely automated as long as you can connect to a database. Your tables will be automatically created, verified and/or repaired if they already exist. Below is a summary of what the installer script just did.</strong>
            		</div>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `cost` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `cost`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `cost` already exists and has <? echo mysql_num_fields($result) ?>/17 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `cost` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE IF NOT EXISTS `cost` (
							  `id` int(11) NOT NULL auto_increment,
							  `int_date` int(11) NOT NULL,
							  `pretty_date` datetime NOT NULL,
							  `engine` varchar(200) NOT NULL,
							  `network` varchar(150) NOT NULL,
							  `campaign` varchar(200) NOT NULL,
							  `adgroup` varchar(200) NOT NULL,
							  `keyword` varchar(200) NOT NULL,
							  `keyword_status` varchar(50) NOT NULL,
							  `keyword_mincpc` decimal(10,7) NOT NULL,
							  `keyword_desturl` varchar(200) NOT NULL,
							  `match_type` varchar(50) NOT NULL,
							  `impressions` int(16) NOT NULL,
							  `clicks` int(16) NOT NULL,
							  `cpc` decimal(10,7) NOT NULL,
							  `total_cost` decimal(10,7) NOT NULL,
							  `position` decimal(10,7) NOT NULL,
							  PRIMARY KEY  (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `cost` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `cost`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}		
						
					
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `cost` table check
					
					---------------------------------------------------------------------------*/
					
					?>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `kw_log` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `kw_log`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `kw_log` already exists and has <? echo mysql_num_fields($result) ?>/39 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `kw_log` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE IF NOT EXISTS `kw_log` (
							  `id` bigint(16) NOT NULL auto_increment,
							  `adgroup` varchar(150) NOT NULL default '',
							  `keyword` varchar(150) NOT NULL default '',
							  `match_type` varchar(50) NOT NULL default '',
							  `ad` varchar(100) NOT NULL default '',
							  `website` text NOT NULL,
							  `direct_link` varchar(5) NOT NULL default '',
							  `referer` text NOT NULL,
							  `timestamp` int(16) NOT NULL default '0',
							  `browser` varchar(150) NOT NULL default '',
							  `ip_address` varchar(100) NOT NULL default '',
							  `query_string` text NOT NULL,
							  `aff_link` varchar(200) NOT NULL default '',
							  `network_name` varchar(150) NOT NULL default '',
							  `network_offer` varchar(150) NOT NULL default '',
							  `network_payout` decimal(10,2) NOT NULL default '0.00',
							  `click_cost` decimal(10,2) NOT NULL default '0.00',
							  `timestamp_left` int(16) NOT NULL default '0',
							  `convert` varchar(100) NOT NULL default '',
							  `network` varchar(150) NOT NULL default '',
							  `clicks` int(2) NOT NULL default '0',
							  `leads` int(2) NOT NULL default '0',
							  `s/u` int(8) NOT NULL default '0',
							  `payout` decimal(10,2) NOT NULL default '0.00',
							  `epc` decimal(10,2) NOT NULL default '0.00',

							  `cpc` decimal(10,2) NOT NULL default '0.00',
							  `revenue` decimal(10,2) NOT NULL default '0.00',
							  `cost` decimal(10,2) NOT NULL default '0.00',
							  `net` decimal(10,2) NOT NULL default '0.00',
							  `gm` decimal(10,2) NOT NULL default '0.00',
							  `roi` decimal(10,2) NOT NULL default '0.00',
							  `campaign` varchar(250) NOT NULL default '',
							  `ppcengine` varchar(200) NOT NULL default '',
							  `source` varchar(100) NOT NULL default '',
							  `cost_updated` int(16) NOT NULL default '0',
							  `revenue_updated` int(16) NOT NULL default '0',
							  `all_updated` int(16) NOT NULL default '0',
							  `rotation_name` varchar(250) NOT NULL default '',
							  `lp_id` INT( 6 ) NOT NULL default '0',
							  PRIMARY KEY  (`id`),
							  KEY `campaign` (`campaign`),
							  KEY `campaign_2` (`campaign`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `kw_log` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `kw_log`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}							
										
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `kw_log` table check
					
					---------------------------------------------------------------------------*/
					
					?>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `landing_pages` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `landing_pages`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `landing_pages` already exists and has <? echo mysql_num_fields($result) ?>/4 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `landing_pages` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE IF NOT EXISTS `landing_pages` (
							  `id` int(15) NOT NULL auto_increment,
							  `link` text NOT NULL,
							  `nickname` varchar(150) NOT NULL default '',
							  `timestamp` int(16) NOT NULL default '0',
							  PRIMARY KEY  (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `landing_pages` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `landing_pages`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}							
										
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `landing_pages` table check
					
					---------------------------------------------------------------------------*/
					
					?>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `offers` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `offers`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `offers` already exists and has <? echo mysql_num_fields($result) ?>/7 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `offers` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE IF NOT EXISTS `offers` (
							  `id` int(8) NOT NULL auto_increment,
							  `network_id` int(10) NOT NULL default '0',
							  `aff_link` text NOT NULL,
							  `network_name` varchar(150) NOT NULL default '',
							  `network_offer` varchar(150) NOT NULL default '',
							  `network_payout` decimal(10,2) NOT NULL default '0.00',
							  `comments` varchar(250) NOT NULL default '',
							  PRIMARY KEY  (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
							";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `offers` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `offers`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}							
										
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `offers` table check
					
					---------------------------------------------------------------------------*/
					
					?>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `rotations` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `rotations`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `rotations` already exists and has <? echo mysql_num_fields($result) ?>/8 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `rotations` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE IF NOT EXISTS `rotations` (
							  `id` int(16) NOT NULL auto_increment,
							  `rotation_id` int(6) NOT NULL default '0',
							  `rotation_name` varchar(150) NOT NULL default '',
							  `offer_id` int(6) NOT NULL default '0',
							  `order` int(6) NOT NULL default '0',
							  `last` varchar(5) NOT NULL default '',
							  `number_of_offers` int(6) NOT NULL default '0',
							  `status` varchar(100) NOT NULL default '',
							  PRIMARY KEY  (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `rotations` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `rotations`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}							
										
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `rotations` table check
					
					---------------------------------------------------------------------------*/
					
					?>
            
            		<?php
            
					/*-------------------------------------------------------------------------
					
					Check The `lp_rotations` table
					
					---------------------------------------------------------------------------*/
			
					$result = mysql_query('SELECT * FROM `lp_rotations`');
					
					if (mysql_error()=='') {
					
					?>
                    
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `lp_rotations` already exists and has <? echo mysql_num_fields($result) ?>/8 fields</strong></div>     
                    
                    <?php
					
					} else {
					
					
					?>
                    
                    <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Table `lp_rotations` does not exist</strong></div>
                    
                    <?php
			
					$query= "CREATE TABLE `lp_rotations` (
							`id` int( 16 ) NOT NULL AUTO_INCREMENT ,
							`rotation_id` int( 6 ) NOT NULL default '0',
							`rotation_name` varchar( 150 ) NOT NULL default '',
							`offer_id` int( 6 ) NOT NULL default '0',
							`order` int( 6 ) NOT NULL default '0',
							`last` varchar( 5 ) NOT NULL default '',
							`number_of_offers` int( 6 ) NOT NULL default '0',
							`status` varchar( 100 ) NOT NULL default '',
							PRIMARY KEY ( `id` )
							) ENGINE = MYISAM DEFAULT CHARSET = latin1;";
					
					mysql_query($query);
					
					if(mysql_error()=='')
						{
							?>
						
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Table `lp_rotations` has been succesfully created</strong></div>     
                    		
							<?php
						}
					else
						{
							?>
                    
                   			 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>There was an error creating table `lp_rotations`:</strong><br />
                                                
                            <?php echo mysql_error().'</div>';
						}							
										
					}
					
            
					/*-------------------------------------------------------------------------
					
					End of `lp_rotations` table check
					
					---------------------------------------------------------------------------*/
					
					?>
                  
                  	<?php $errorCheck=''; $result = mysql_query('SELECT * FROM `kw_log`'); $errorCheck .= mysql_error();  $result = mysql_query('SELECT * FROM `cost`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `offers`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `landing_pages`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `rotations`'); $errorCheck .= mysql_error(); $result = mysql_query('SELECT * FROM `lp_rotations`'); $errorCheck .= mysql_error(); ?>
                  
                  
                  	<?php if($errorCheck=='') { ?>
                  
                    <div style="padding:15px; line-height:26px; margin-top:10px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>Step 3 complete! <a href="?step=4">Click here to setup your google api integration</a></strong>
                    
            		</div>

                  	<?php } ?>
					
                    
                    <div style="padding-top:150px; padding-bottom:30px;">
                    
      				<h1 class="title">Manually Reinstall Tables</h1>
                    
                    <div style="color:#ff0000"><strong>WARNING: Clicking the button below will erase ALL TABLES AND DATA and reinstall them from scratch. Only do this if the field counts on your database are off! DO NOT refresh the page after you push the button, this may cause your database to get wiped again.</strong></div>              	
                    
                    <div style="padding:40px;">
                    
                    	<form action="?step=3" method="post">
                        	
                            <input type="hidden" value="1" name="reset-table" />
                            
                            <input type="submit" value="Erase All Data and Tables" />
                            
                        </form>
                    
                    </div>
                    
                    
                    </div>
                    				
<?php 

/*------------------------------------------------------------------------------------------------------------------

Step 4 - Take inputs and write them to the google api file, pretty straightforward

------------------------------------------------------------------------------------------------------------------*/

} elseif ($_GET['step']==4) { 

?>
            
            	<h1 class="title">Step 4 - Configure Your Google API Information</h1>
                
                  <?php
				  
				  $orig_db_username = $username;
				  $orig_db_password = $password;
				  $orig_db_database = $database;
				  
				  ?>
                
                  <?php include('api/config.php') ?>
                
                  <?php if(is_writable('api/config.php')) { ?>
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>api/config.php is writeable</strong></div>     
                        
                  <?php }else{ ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                        <strong>api/config.php is NOT writeable</strong><br />
						<a href="http://www.youtube.com/watch?v=VfKTvYFGEvs">Click here</a> to see how to make files writeable using <a href="http://filezilla-project.org/">FileZilla (Freeware FTP Client)</a>
                     </div>               
                  
                               
                  <?php } ?>
                  
                  
                  <?php
				  
				  	$link = mysql_connect($db_location,$db_username,$db_password);
				  
				   if(!$link) { 
				   
				   ?>
                                    
                     <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;">
                     	   <strong>MySQL connection failed</strong><br />
							<?php if($db_username=='') { ?>
                            
                            	Please enter a username for your database<br />
                            
                            <?php } ?>     
                              
							<?php if($db_password=='') { ?>
                            
                            	Please enter a password for your database<br />
                            
                            <?php } ?>   
                              
							<?php if($db_location=='') { ?>
                            
                            	Please enter the name of your database<br />
                            
                            <?php } ?>   
                            
                            <?php if($db_username!='' && $db_password!='' && db_location!='') { ?>
                            
                            	Connection error, please double check your settings
                            
                            <? } ?>    
                            
                             <br /><a href="http://www.youtube.com/watch?v=nfM0xNwkAMA">Click here</a> to watch a video on how to setup a mysql database through cpanel
                     </div>
                  
                  
                  
                  <?php } else { ?>
                  
					
                    <div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Connected to MySQL database successfully</strong></div>                             
                  
                  
                  <?php } ?>
                  
                  
                  
                  <?php                  
                  
                    require_once('api/soapclientfactory.php');  
					
					function show_xml($service) {
					  echo $service->request;
					  echo $service->response;
					  echo "\n";
					}
					
					function show_fault($service) {
					  echo "\n";
					  echo 'Fault: ' . $service->fault . "\n";
					  echo 'Code: ' . $service->faultcode . "\n";
					  echo 'String: ' . $service->faultstring . "\n";
					  echo 'Detail: ' . $service->faultdetail . "\n";
					}
					
					
                    $headers =
                      '<email>' . $email . '</email>'.
                      '<password>' . $password . '</password>' .
                      '<clientEmail>' . $client_email . '</clientEmail>' .
                      '<useragent>' . $useragent . '</useragent>' .
                      '<developerToken>' . $developer_token . '</developerToken>' .
                      '<applicationToken>' . $application_token . '</applicationToken>';
                  
				  	$namespace = 'https://adwords.google.com/api/adwords/v13';
					$account_service =
					  SoapClientFactory::GetClient($namespace . '/AccountService?wsdl', 'wsdl');
					$account_service->setHeaders($headers);
					$debug = 0;
				  
					$account_info = $account_service->call('getAccountInfo');
					$account_info = $account_info['getAccountInfoReturn'];
					
					if($account_info['customerId']!='')
						{
							?>
					
                    			<div style="display:block; background-color:#DDFFDD; color:#00AA00; padding:5px;"><strong>Connected to Google API Successfully</strong></div>     
                            
                            <?php						
						}
					else
						{
							?>
                                 
                                 <div style="display:block; background-color:#FFDDDD; color:#AA0000; padding:5px;"><strong>Could Not Connect To Google API:</strong><br />								
                                		<?php show_fault($account_service); ?>
                                 </div>
                            
                            <?php						
						}					
					
				  ?>
                  
                  
                  <?php if($link && $account_info['customerId']!='' && $email!='' && $password!='' && $client_email!='' && $useragent!='' && $developer_token!='' && $application_token!='' && $application_token!='' && $db_location!='' && $db_username!='' && $db_password!='' && $db_database!='') { ?>
                                    
                    <div style="padding:15px; line-height:26px; margin-top:10px; margin-bottom:20px; background-color:#FFFFCC; border:2px dotted #CCCC33;">
            
 					<strong>Step 4 complete! If all 4 steps are complete, you can now login to tracker by <a href="index.php">clicking here</a></strong>
                    
            		</div>
                  
                  <?php } ?>
                                      
                  <form action="?step=4" method="post">
                  
                  		<input type="hidden" name="api-config" value="1" />
                  
    
                      <div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                            <strong>MySQL Database Connection</strong>
                            <div class="smallText">This information is used to connect to your MySQL Database (<em>Automatically populated from config.php if empty</em>)</div>
                      </div>
                      
                      <br />
                      
                      <table cellpadding="5">
                      
                        <tr>
                            <td align="right" width="130">
                                Host Name
                            </td>
                            <td>
                                <input type="text" name="db_location" value="<?php echo $db_location ?>" />
                            </td>
                            <td class="smallText">
                                Your database host name (Usually: <strong>localhost</strong>)
                            </td>
                        </tr>
                      
                        <tr>
                            <td align="right">
                                Username
                            </td>
                            <td>
                                <input type="text" name="db_username" value="<?php if ($db_username!='') { echo $db_username; } else { echo $orig_db_username;} ?>" />
                            </td>
                            <td class="smallText">
                                Your database username, usually setup in your cpanel.
                            </td>
                        </tr>
                        
                      
                        <tr>
                            <td align="right">
                                Password
                            </td>
                            <td>
                                <input type="text" name="db_password" value="<?php if ($db_password!='') { echo $db_password; } else { echo $orig_db_password;} ?>" />
                            </td>
                            <td class="smallText">
                                Your database username password, usually setup in your cpanel.                        
                            </td>
                        </tr>
                      
                      
                        <tr>
                            <td align="right">
                                Database Name
                            </td>
                            <td>
                                <input type="text" name="db_database" value="<?php if ($db_database!='') { echo $db_database; } else { echo $orig_db_database;} ?>" />
                            </td>
                            <td class="smallText">
                                The name of the database that your tracker will be installed in.
                            </td>
                        </tr>
                     
                     
                     </table>
                  		<div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                  			<strong>Adwords Login Information</strong>
							<div class="smallText">The email and password used to login to your adwords account</div>
                  		</div>
                        
                        
                        <br />

                  
                      <table cellpadding="5">
                      
                        <tr>
                            <td align="right" width="140">
                                Adwords Email
                            </td>
                            <td>
                                <input type="text" name="email" value="<?php echo $email ?>" />
                            </td>
                            <td class="smallText">
                                
                            </td>
                        </tr>
                        
                      
                        <tr>
                            <td align="right">
                                Adwords Password
                            </td>
                            <td>
                                <input type="text" name="password" value="<?php echo $password ?>" />
                            </td>
                            <td class="smallText">
                                
                            </td>
                        </tr>
                      
                      </table>
                      
                      <div style="padding:5px; margin-top:20px; border-bottom:2px dotted #999999; background-color:#EEEEEE;">
                      
                                <strong>Adwords API Developer Information</strong>
                                <div class="smallText">This information allows you to connect to the adwords api. <a href="http://code.google.com/apis/adwords/docs/developer/index.html"><strong>Read the adwords api getting started guide here</strong></a></div>
                      </div>
                      
                      <br />
                      
                      <table cellpadding="5">
                      
                        <tr>
                            <td align="right" width="130">
                                Client Email
                            </td>
                            <td>
                                <input type="text" name="client_email" value="<?php echo $client_email ?>" />
                            </td>
                            <td class="smallText">
                                The client email added to your developer account, should always be the same as your account email above. (Not your developer email)
                            </td>
                        </tr>
                        
                      
                        <tr>
                            <td align="right">
                                User Agent
                            </td>
                            <td>
                                <input type="text" name="useragent" value="<?php echo $useragent ?>" />
                            </td>
                            <td class="smallText">
                                Just some text to identify your application, something like 'Click Tracker' is good.                 
                            </td>
                        </tr>
                        
                      
                        <tr>
                            <td align="right">
                                Developer Token
                            </td>
                            <td>
                                <input type="text" name="developer_token" value="<?php echo $developer_token ?>" />
                            </td>
                            <td class="smallText">
                                You have to request one of these from google first.
                            </td>
                        </tr>
                        
                      
                        <tr>
                            <td align="right">
                                Application Token
                            </td>
                            <td>
                                <input type="text" name="application_token" value="<?php echo $application_token ?>" />
                            </td>
                            <td class="smallText">
                                Request one of these after you have a developer token approved.
                            </td>
                        </tr>
                      
                        
                     </table>
                     
                     <div style="padding-top:20px; padding-bottom:20px;">
                        <input type="submit" value="Update Config File" style="font-size:18px;" />                 
                     </div>
                     
            </form>     
            
            
            <?php } ?>
        </td> 
    </tr>
    <tr>
    	<td style="border-right:1px solid #999999;">&nbsp;
        
        	
        
        </td>    
    </tr>

    
    
</table>



</body>
</html>