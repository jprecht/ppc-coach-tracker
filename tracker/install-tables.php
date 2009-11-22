<?php
include("config.php");
//  Connect to our database
//  ----------------------------------
$dbh = mysql_connect ($db_location, $username, $password); 
mysql_select_db ($database);

$sql = "CREATE TABLE `kw_log` (
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
  PRIMARY KEY  (`id`),
  KEY `campaign` (`campaign`),
  KEY `campaign_2` (`campaign`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12271";
echo "Creating table: kw_log ...<BR>";
mysql_query( $sql, $dbh );

$sql = "CREATE TABLE `landing_pages` (
  `id` int(15) NOT NULL auto_increment,
  `link` text NOT NULL,
  `nickname` varchar(150) NOT NULL default '',
  `timestamp` int(16) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6";
echo "Creating table: landing_pages ...<BR>";
mysql_query( $sql, $dbh );

$sql = "CREATE TABLE `offers` (
  `id` int(8) NOT NULL auto_increment,
  `network_id` int(10) NOT NULL default '0',
  `aff_link` text NOT NULL,
  `network_name` varchar(150) NOT NULL default '',
  `network_offer` varchar(150) NOT NULL default '',
  `network_payout` decimal(10,2) NOT NULL default '0.00',
  `comments` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36";
echo "Creating table: offers ...<BR>";
mysql_query( $sql, $dbh );

$sql = "CREATE TABLE `rotations` (
  `id` int(16) NOT NULL auto_increment,
  `rotation_id` int(6) NOT NULL default '0',
  `rotation_name` varchar(150) NOT NULL default '',
  `offer_id` int(6) NOT NULL default '0',
  `order` int(6) NOT NULL default '0',
  `last` varchar(5) NOT NULL default '',
  `number_of_offers` int(6) NOT NULL default '0',
  `status` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=67";
echo "Creating table: rotations ...<BR>";

$sql = "CREATE TABLE `cost` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`int_date` INT NOT NULL ,
`pretty_date` DATETIME NOT NULL ,
`engine` VARCHAR( 200 ) NOT NULL ,
`network` VARCHAR( 150 ) NOT NULL ,
`campaign` VARCHAR( 200 ) NOT NULL ,
`adgroup` VARCHAR( 200 ) NOT NULL ,
`keyword` VARCHAR( 200 ) NOT NULL ,
`keyword_status` VARCHAR( 50 ) NOT NULL ,
`keyword_mincpc` DECIMAL( 10, 7 ) NOT NULL ,
`keyword_desturl` VARCHAR( 200 ) NOT NULL ,
`match_type` VARCHAR( 50 ) NOT NULL ,
`impressions` INT( 16 ) NOT NULL ,
`clicks` INT( 16 ) NOT NULL ,
`cpc` DECIMAL( 10, 7 ) NOT NULL ,
`total_cost` DECIMAL( 10, 7 ) NOT NULL ,
`position` DECIMAL( 10, 7 ) NOT NULL
) ENGINE = MYISAM ";
mysql_query( $sql, $dbh );

$sql = "ALTER TABLE `cost` ADD INDEX `cost` ( `int_date` , `campaign` , `adgroup` , `keyword` )";
mysql_query( $sql, $dbh );


$sql = "CREATE TABLE `lp_rotations` (
`id` int( 16 ) NOT NULL AUTO_INCREMENT ,
`rotation_id` int( 6 ) NOT NULL default '0',
`rotation_name` varchar( 150 ) NOT NULL default '',
`offer_id` int( 6 ) NOT NULL default '0',
`order` int( 6 ) NOT NULL default '0',
`last` varchar( 5 ) NOT NULL default '',
`number_of_offers` int( 6 ) NOT NULL default '0',
`status` varchar( 100 ) NOT NULL default '',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1";
mysql_query( $sql, $dbh );

$sql = "ALTER TABLE `kw_log` ADD `lp_id` INT( 6 ) NOT NULL AFTER `rotation_name`";

mysql_query( $sql, $dbh );
echo "All tables should have been successfully created, please check them using phpmyadmin or whatever mysql program you use to verify.  You can now delete this file from your server.";
?>