<?php
require_once dirname(__FILE__).'/database_connect.php';

$dbh = get_conn();

/* ---------------------------- bid_rules ------------------------------ */
$query = "DROP TABLE IF EXISTS bid_rule";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE bid_rule (
                            id INT NOT NULL AUTO_INCREMENT,
                            ppc_entity_id INT NOT NULL,
                            ppc_entity_type SMALLINT NOT NULL DEFAULT 0,
                            rule_type SMALLINT NOT NULL DEFAULT 0,
                            cost_threshold FLOAT NOT NULL DEFAULT 0,
                            increase_percent FLOAT NOT NULL DEFAULT 0,
                            increase_days FLOAT NOT NULL DEFAULT 0,
                            decrease_percent FLOAT NOT NULL DEFAULT 0,
                            decrease_days FLOAT NOT NULL DEFAULT 0,
                            apply TINYINT NOT NULL DEFAULT 0,
                            PRIMARY KEY(id),
                            UNIQUE KEY(ppc_entity_id, ppc_entity_type, rule_type)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

/* ---------------------------- ppc_keywords ------------------------------ */
$query = "DROP TABLE IF EXISTS ppc_keywords";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE ppc_keywords (
                            id INT NOT NULL AUTO_INCREMENT,
                            adgroup_id INT NOT NULL DEFAULT 0,
                            keyword_id VARCHAR(255) NOT NULL DEFAULT '',
                            text VARCHAR(255),
                            match_type VARCHAR(255),
                            status VARCHAR(255),
                            search_max_cpc FLOAT NOT NULL DEFAULT 0,
                            new_status VARCHAR(255),
                            new_search_max_cpc FLOAT NOT NULL DEFAULT 0,
                            current_url TEXT,
                            new_url TEXT,
                            keyword_bid_rule_id INT NOT NULL DEFAULT 0,
                            PRIMARY KEY(id),
                            UNIQUE KEY(adgroup_id, keyword_id)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

/* ---------------------------- ppc_ads ------------------------------ */
$query = "DROP TABLE IF EXISTS ppc_ads";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE ppc_ads (
                            id INT NOT NULL AUTO_INCREMENT,
                            adgroup_id INT NOT NULL DEFAULT 0,
                            ad_id VARCHAR(255) NOT NULL DEFAULT '',
                            ad_name VARCHAR(255),
                            current_url TEXT,
                            PRIMARY KEY(id),
                            UNIQUE KEY(adgroup_id, ad_id)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

/* ---------------------------- ppc_adgroups ------------------------------ */
$query = "DROP TABLE IF EXISTS ppc_adgroups";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE ppc_adgroups (
                            id INT NOT NULL AUTO_INCREMENT,
                            adgroup_id VARCHAR(255) NOT NULL DEFAULT '',
                            campaign_id INT NOT NULL DEFAULT 0,
                            adgroup_name VARCHAR(255),
                            status VARCHAR(255),
                            content_max_cpc FLOAT NOT NULL DEFAULT 0,
                            search_max_cpc FLOAT NOT NULL DEFAULT 0,
                            new_status VARCHAR(255),
                            new_content_max_cpc FLOAT NOT NULL DEFAULT 0,
                            default_url TEXT,
                            adgroup_bid_rule_id INT NOT NULL DEFAULT 0,
                            keyword_bid_rule_id INT NOT NULL DEFAULT 0,
                            PRIMARY KEY(id),
                            UNIQUE KEY(campaign_id, adgroup_id)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

/* ---------------------------- ppc_campaigns ------------------------------ */
$query = "DROP TABLE IF EXISTS ppc_campaigns";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE ppc_campaigns (
                            id INT NOT NULL AUTO_INCREMENT,
                            campaign_id VARCHAR(255) NOT NULL DEFAULT '',
                            campaign_name VARCHAR(255),
                            master_account VARCHAR(255),
                            account VARCHAR(255) NOT NULL DEFAULT '',
                            engine VARCHAR(255) NOT NULL DEFAULT '',
                            status VARCHAR(255),
                            budget FLOAT NOT NULL DEFAULT 0,
                            new_status VARCHAR(255),
                            new_budget FLOAT NOT NULL DEFAULT 0,
                            campaign_bid_rule_id INT NOT NULL DEFAULT 0,
                            adgroup_bid_rule_id INT NOT NULL DEFAULT 0,
                            keyword_bid_rule_id INT NOT NULL DEFAULT 0,
                            PRIMARY KEY(id),
                            UNIQUE KEY(engine, account, campaign_id)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

/* ---------------------------- bid_management_data ------------------------------ */
$query = "DROP TABLE IF EXISTS bid_management_data";
mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

$query = "CREATE TABLE bid_management_data (
                            id INT NOT NULL AUTO_INCREMENT,
                            campaign_id INT,
                            adgroup_id INT,
                            keyword_id INT,
                            data_date DATE,
                            revenue FLOAT,
                            cost FLOAT,
                            PRIMARY KEY(id),
                            UNIQUE KEY(keyword_id, data_date)
                            ) ENGINE = MyISAM ";

mysql_query($query, $dbh) or die ('I cannot execute the query because: ' . mysql_error());

print "Database Created";
mysql_close($dbh);
?>