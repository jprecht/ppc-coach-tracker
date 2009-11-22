<?php
require_once dirname(__FILE__).'/database_connect.php';
require_once dirname(__FILE__).'/../entity/PPCEntities.php';
require_once dirname(__FILE__).'/BidRuleDAO.php';

define("MAX_INSERT_ROWS", 1000);

class CampaignDAO {
    function load($id) {
        $conn = get_conn();

        $query = "SELECT * FROM ppc_campaigns WHERE id = $id";
        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $row = mysql_fetch_array($result);
        $campaign = $this->instantiateCampaign($row);
        close_conn($conn);
        return $campaign;
    }

    function loadAll($engine = false, $new = false) {
        $conn = get_conn();

        if($engine) {
            $query = "SELECT * FROM ppc_campaigns WHERE engine = '$engine' ";
            $query .= ($new == true) ? "AND (status != new_status OR budget != new_budget) " : "";
        }
        else {
            $query = "SELECT * FROM ppc_campaigns ";
            $query .= ($new == true) ? "WHERE (status != new_status OR budget != new_budget) " : "";
        }

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $campaigns = array();
        while($row = mysql_fetch_array($result)) {
            $campaigns[] = $this->instantiateCampaign($row);
        }
        close_conn($conn);
        return $campaigns;
    }

    function instantiateCampaign($row) {
        $campaign = new Campaign;
        $campaign->id = $row["id"];
        $campaign->engine = $row["engine"];
        $campaign->masterAccount = $row["master_account"];
        $campaign->account = $row["account"];
        $campaign->campaignId = $row["campaign_id"];
        $campaign->name = $row["campaign_name"];
        $campaign->currentBid = $row["budget"];
        $campaign->newBid = $row["new_budget"];
        $campaign->currentStatus = $row["status"];
        $campaign->newStatus = $row["new_status"];
        $dao = new BidRuleDAO();

        if(isset($row["campaign_bid_rule_id"]) && $row["campaign_bid_rule_id"] > 0) {
            $campaign->campaignBidRule = $dao->load($row["campaign_bid_rule_id"]);
        }
        if(isset($row["adgroup_bid_rule_id"]) && $row["adgroup_bid_rule_id"] > 0) {
            $campaign->adgroupBidRule = $dao->load($row["adgroup_bid_rule_id"]);
        }
        if(isset($row["keyword_bid_rule_id"]) && $row["keyword_bid_rule_id"] > 0) {
            $campaign->keywordBidRule = $dao->load($row["keyword_bid_rule_id"]);
        }

        return $campaign;
    }

    function saveCampaigns($campaigns) {
        if(count($campaigns) > 0) {
            $chunks = array_chunk($campaigns, MAX_INSERT_ROWS);
            foreach ($chunks as $chunk) {
            $conn = get_conn();
                $query = "INSERT INTO ppc_campaigns (
                campaign_id,
                campaign_name,
                master_account,
                account,
                engine,
                status,
                budget,
                new_status,
                new_budget,
                campaign_bid_rule_id,
                adgroup_bid_rule_id,
                keyword_bid_rule_id
                ) VALUES";
                foreach ($chunk as $campaign) {
                    $name = mysql_real_escape_string($campaign->name, $conn);
                    $bidRuleDAO = new BidRuleDAO();
                    $campaignBidRuleId = 0;
                    if($campaign->campaignBidRule != null) {
                        $campaign->campaignBidRule->entityId = $campaign->id;
                        $campaign->campaignBidRule = $bidRuleDAO->save($campaign->campaignBidRule);
                        $campaignBidRuleId = $campaign->campaignBidRule->id;
                    }

                    $adgroupBidRuleId = 0;
                    if($campaign->adgroupBidRule != null) {
                        $campaign->adgroupBidRule->entityId = $campaign->id;
                        $campaign->adgroupBidRule = $bidRuleDAO->save($campaign->adgroupBidRule);
                        $adgroupBidRuleId = $campaign->adgroupBidRule->id;
                    }

                    $keywordBidRuleId = 0;
                    if($campaign->keywordBidRule != null) {
                        $campaign->keywordBidRule->entityId = $campaign->id;
                        $campaign->keywordBidRule = $bidRuleDAO->save($campaign->keywordBidRule);
                        $keywordBidRuleId = $campaign->keywordBidRule->id;
                    }

                    $query .= "(
                '{$campaign->campaignId}',
                '$name',
                '{$campaign->masterAccount}',
                '{$campaign->account}',
                '{$campaign->engine}',
                '{$campaign->currentStatus}',
                        {$campaign->currentBid},
                '{$campaign->newStatus}',
                        {$campaign->newBid},
                        $campaignBidRuleId,
                        $adgroupBidRuleId,
                        $keywordBidRuleId
                ),";
                }
                $query = substr($query, 0, strlen($query)-1);
                $query .= " ON DUPLICATE KEY UPDATE
                campaign_id = VALUES(campaign_id),
                campaign_name = VALUES(campaign_name),
                master_account = VALUES(master_account),
                account = VALUES(account),
                engine = VALUES(engine),
                status = VALUES(status),
                budget = VALUES(budget),
                new_status = VALUES(new_status),
                new_budget = VALUES(new_budget),
                campaign_bid_rule_id = VALUES(campaign_bid_rule_id),
                adgroup_bid_rule_id = VALUES(adgroup_bid_rule_id),
                keyword_bid_rule_id = VALUES(keyword_bid_rule_id)
                ";

                mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
                close_conn($conn);
            }
        }
    }
}

class AdgroupDAO {
    function load($id) {
        $conn = get_conn();
        $query = "SELECT * FROM ppc_adgroups WHERE id=$id";
        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
        $row = mysql_fetch_array($result);
        $adgroup = $this->instantiateAdgroup($row);
        return $adgroup;
    }

    function loadAll($campaignId = false, $new = false) {
        $conn = get_conn();
        if($campaignId) {
            $query = "SELECT * FROM ppc_adgroups WHERE campaign_id = $campaignId ";
            $query .= ($new == true) ? "AND (status != new_status OR content_max_cpc != new_content_max_cpc) " : "";
        }
        else {
            $query = "SELECT * FROM ppc_adgroups ";
            $query .= ($new == true) ? "WHERE (status != new_status OR content_max_cpc != new_content_max_cpc) " : "";
        }
        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
        $adgroups = array();
        while($row = mysql_fetch_array($result)) {
            $adgroups[] = $this->instantiateAdgroup($row);
        }
        close_conn($conn);
        return $adgroups;
    }

    function instantiateAdgroup($row) {
        $adgroup = new Adgroup;
        $adgroup->id = $row["id"];
        $campaignDAO = new CampaignDAO;
        $adgroup->campaign = $campaignDAO->load($row["campaign_id"]);
        $adgroup->adgroupId = $row["adgroup_id"];
        $adgroup->name = $row["adgroup_name"];
        $adgroup->searchMaxCpc = $row["search_max_cpc"];
        $adgroup->currentBid = $row["content_max_cpc"];
        $adgroup->newBid = $row["new_content_max_cpc"];
        $adgroup->currentStatus = $row["status"];
        $adgroup->newStatus = $row["new_status"];
        $adgroup->defaultUrl = $row["default_url"];
        $dao = new BidRuleDAO();
        if(isset($row["adgroup_bid_rule_id"]) && $row["adgroup_bid_rule_id"] > 0) {
            $adgroup->adgroupBidRule = $dao->load($row["adgroup_bid_rule_id"]);
        }
        if(isset($row["keyword_bid_rule_id"]) && $row["keyword_bid_rule_id"] > 0) {
            $adgroup->keywordBidRule = $dao->load($row["keyword_bid_rule_id"]);
        }

        return $adgroup;
    }

    function saveAdgroups($adgroups) {
        if(count($adgroups) > 0) {
            $chunks = array_chunk($adgroups, MAX_INSERT_ROWS);
            foreach ($chunks as $chunk) {
                $conn = get_conn();
                $query = "INSERT INTO ppc_adgroups (
                id,
                adgroup_id,
                campaign_id,
                adgroup_name,
                status,
                search_max_cpc,
                content_max_cpc,
                new_status,
                new_content_max_cpc,
               	default_url,
                adgroup_bid_rule_id,
                keyword_bid_rule_id
                ) VALUES";
                foreach ($chunk as $adgroup) {
                    $name = mysql_real_escape_string($adgroup->name, $conn);
                    $bidRuleDAO = new BidRuleDAO();
                    $adgroupBidRuleId = 0;
                    if($adgroup->adgroupBidRule != null) {
                        $adgroup->adgroupBidRule->entityId = $adgroup->id;
                        $adgroup->adgroupBidRule = $bidRuleDAO->save($adgroup->adgroupBidRule);
                        $adgroupBidRuleId = $adgroup->adgroupBidRule->id;
                    }

                    $keywordBidRuleId = 0;
                    if($adgroup->keywordBidRule != null) {
                        $adgroup->keywordBidRule->entityId = $adgroup->id;
                        $adgroup->keywordBidRule = $bidRuleDAO->save($adgroup->keywordBidRule);
                        $keywordBidRuleId = $adgroup->keywordBidRule->id;
                    }

                    $query .= "(
                        {$adgroup->id},
                '{$adgroup->adgroupId}',
                        {$adgroup->campaign->id},
                '$name',
                '{$adgroup->currentStatus}',
                        {$adgroup->searchMaxCpc},
                        {$adgroup->currentBid},
                '{$adgroup->newStatus}',
                        {$adgroup->newBid},
                    '{$adgroup->defaultUrl}',
                        $adgroupBidRuleId,
                        $keywordBidRuleId
                ),";
                }
                $query = substr($query, 0, strlen($query)-1);
                $query .= " ON DUPLICATE KEY UPDATE
                id = VALUES(id),
                adgroup_id = VALUES(adgroup_id),
                campaign_id = VALUES(campaign_id),
                adgroup_name = VALUES(adgroup_name),
                status = VALUES(status),
                search_max_cpc = VALUES(search_max_cpc),
                content_max_cpc = VALUES(content_max_cpc),
                new_status = VALUES(new_status),
                new_content_max_cpc = VALUES(new_content_max_cpc),
                default_url = VALUES(default_url),
                adgroup_bid_rule_id = VALUES(adgroup_bid_rule_id),
                keyword_bid_rule_id = VALUES(keyword_bid_rule_id)
                    ";

                mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
                close_conn($conn);
            }
        }
    }
}

class KeywordDAO {
    function load($id) {
        $conn = get_conn();

        $query = "SELECT * FROM ppc_keywords WHERE id = $id";

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $row = mysql_fetch_array($result);
        $keyword = $this->instantiateKeyword($row);

        close_conn($conn);
        return $keyword;
    }

    function loadAll($adgroupId = false, $new = false) {
        $conn = get_conn();

        if($adgroupId) {
            $query = "SELECT * FROM ppc_keywords WHERE adgroup_id = $adgroupId ";
            $query .= ($new == true) ? "AND (status != new_status OR search_max_cpc != new_search_max_cpc OR current_url != new_url) " : "";
        }
        else {
            $query = "SELECT * FROM ppc_keywords ";
            $query .= ($new == true) ? "WHERE (status != new_status OR search_max_cpc != new_search_max_cpc OR current_url != new_url) " : "";
        }

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $keywords = array();
        while($row = mysql_fetch_array($result)) {
            $keywords[] = $this->instantiateKeyword($row);
        }
        close_conn($conn);
        return $keywords;
    }

    function instantiateKeyword($row) {
        $keyword = new Keyword;
        $keyword->id = $row["id"];
        $adgroupDAO = new AdgroupDAO;
        $keyword->adgroup = $adgroupDAO->load($row["adgroup_id"]);
        $keyword->keywordId = $row["keyword_id"];
        $keyword->text = $row["text"];
        $keyword->matchType = $row["match_type"];
        $keyword->currentBid = round($row["search_max_cpc"],2);
        $keyword->newBid = $row["new_search_max_cpc"];
        $keyword->currentStatus = $row["status"];
        $keyword->newStatus = $row["new_status"];
        $keyword->currentUrl = $row["current_url"];
        $keyword->newUrl = $row["new_url"];
        $dao = new BidRuleDAO();
        if(isset($row["keyword_bid_rule_id"]) && $row["keyword_bid_rule_id"] > 0) {
            $keyword->keywordBidRule = $dao->load($row["keyword_bid_rule_id"]);
        }
        return $keyword;
    }

    function saveKeywords($keywords) {
        if(count($keywords) > 0) {
            $chunks = array_chunk($keywords, MAX_INSERT_ROWS);
            foreach ($chunks as $chunk) {
                $conn = get_conn();
                $query = "INSERT INTO ppc_keywords (
                id,
                keyword_id,
                adgroup_id,
                text,
                match_type,
                status,
                search_max_cpc,
                new_status,
                new_search_max_cpc,
                current_url,
                new_url,
                keyword_bid_rule_id
                ) VALUES";
                foreach ($chunk as $keyword) {
                    $text = mysql_real_escape_string($keyword->text, $conn);
                    $bidRuleDAO = new BidRuleDAO();
                    $keywordBidRuleId = 0;
                    if($keyword->keywordBidRule != null) {
                        $keyword->keywordBidRule->entityId = $keyword->id;
                        $keyword->keywordBidRule = $bidRuleDAO->save($keyword->keywordBidRule);
                        $keywordBidRuleId = $keyword->keywordBidRule->id;
                    }

                    $query .= "(
                        {$keyword->id},
                '{$keyword->keywordId}',
                        {$keyword->adgroup->id},
                '$text',
                '{$keyword->matchType}',
                '{$keyword->currentStatus}',
                        {$keyword->currentBid},
                '{$keyword->newStatus}',
                        {$keyword->newBid},
                '{$keyword->currentUrl}',
                '{$keyword->newUrl}',
                        $keywordBidRuleId
                ),";
                }
                $query = substr($query, 0, strlen($query)-1);
                $query .= " ON DUPLICATE KEY UPDATE
                id = VALUES(id),
                keyword_id = VALUES(keyword_id),
                adgroup_id = VALUES(adgroup_id),
                text = VALUES(text),
                match_type = VALUES(match_type),
                status = VALUES(status),
                search_max_cpc = VALUES(search_max_cpc),
                new_status = VALUES(new_status),
                new_search_max_cpc = VALUES(new_search_max_cpc),
                current_url = VALUES(current_url),
                new_url = VALUES(new_url),
                keyword_bid_rule_id = VALUES(keyword_bid_rule_id)
                ";

                mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
                close_conn($conn);
            }
        }
    }
}

class AdDAO {
    function load($id) {
        $conn = get_conn();

        $query = "SELECT * FROM ppc_ads WHERE id = $id";

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $row = mysql_fetch_array($result);
        $ad = $this->instantiateAd($row);

        close_conn($conn);
        return $ad;
    }

    function loadAll($adgroupId = false, $new = false) {
        $conn = get_conn();

        if($adgroupId) {
            $query = "SELECT * FROM ppc_ads WHERE adgroup_id = $adgroupId ";
            $query .= ($new == true) ? "AND (current_url != new_url) " : "";
        }
        else {
            $query = "SELECT * FROM ppc_ads ";
            $query .= ($new == true) ? "WHERE (current_url != new_url) " : "";
        }

        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $ads = array();
        while($row = mysql_fetch_array($result)) {
            $ads[] = $this->instantiateAd($row);
        }
        close_conn($conn);
        return $ads;
    }

    function instantiateAd($row) {
        $ad = new Ad;
        $ad->id = $row["id"];
        $adgroupDAO = new AdgroupDAO;
        $ad->adgroup = $adgroupDAO->load($row["adgroup_id"]);
        $ad->adId = $row["ad_id"];
        $ad->name = $row["ad_name"];
        $ad->currentUrl = $row["current_url"];
        return $ad;
    }

    function saveAds($ads) {
        if(count($ads) > 0) {
            $chunks = array_chunk($ads, MAX_INSERT_ROWS);
            foreach ($chunks as $chunk) {
                $query = "INSERT INTO ppc_ads (
                id,
                ad_id,
                adgroup_id,
                ad_name,
                current_url
                ) VALUES";
                foreach ($chunk as $ad) {
                    $conn = get_conn();
                    $name = mysql_real_escape_string($ad->name, $conn);
                    $query .= "(
                        {$ad->id},
                    '{$ad->adId}',
                        {$ad->adgroup->id},
                    '$name',
                    '{$ad->currentUrl}'
                    ),";
                }
                $query = substr($query, 0, strlen($query)-1);
                $query .= " ON DUPLICATE KEY UPDATE
                id = VALUES(id),
                ad_id = VALUES(ad_id),
                adgroup_id = VALUES(adgroup_id),
                ad_name = VALUES(ad_name),
                current_url = VALUES(current_url)
                ";

                mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());
                close_conn($conn);
            }
        }
    }
}

?>