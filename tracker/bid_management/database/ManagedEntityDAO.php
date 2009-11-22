<?php
require_once dirname(__FILE__).'/database_connect.php';
require_once dirname(__FILE__).'/../entity/ManagedEntities.php';

define("SECONDS_IN_DAY", 60*60*24);

class ManagedEntityDAO {

    function prepareData() {
        $conn = get_conn();
        $query = file_get_contents(dirname(__FILE__)."/sql/prepare_data.sql");
        mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        close_conn($conn);
    }

    function loadCampaigns() {
        $campaignDAO = new CampaignDAO();
        $campaigns = $campaignDAO->loadAll();
        $managedCampaigns = array();

        $todayStart = mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
        $end = gmdate("Y-m-d",$todayStart-1);

        foreach ($campaigns as $campaign) {
            if($campaign->isApplyBidRule()) {
                $increaseStart = gmdate("Y-m-d",$todayStart - ($campaign->getCampaignIncreaseDays()*SECONDS_IN_DAY));
                $decreaseStart = gmdate("Y-m-d",$todayStart - ($campaign->getCampaignDecreaseDays()*SECONDS_IN_DAY));

                $managedCampaign = $this->getManagedEntity($campaign->id, "campaign_id", $increaseStart, $decreaseStart, $end);
                $managedCampaign->ppcEntity = $campaign;
                $managedCampaigns[] = $managedCampaign;
            }
        }
        return $managedCampaigns;
    }

    function loadAdgroups() {
        $adgroupDAO = new AdgroupDAO();
        $adgroups = $adgroupDAO->loadAll();
        $managedAdgroups = array();

        $todayStart = mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
        $end = gmdate("Y-m-d",$todayStart-1);

        foreach ($adgroups as $adgroup) {
            if($adgroup->isApplyBidRule()) {
                $increaseStart = gmdate("Y-m-d",$todayStart - ($adgroup->getAdgroupIncreaseDays()*SECONDS_IN_DAY));
                $decreaseStart = gmdate("Y-m-d",$todayStart - ($adgroup->getAdgroupDecreaseDays()*SECONDS_IN_DAY));

                $managedAdgroup = $this->getManagedEntity($adgroup->id, "adgroup_id", $increaseStart, $decreaseStart, $end);
                $managedAdgroup->ppcEntity = $adgroup;
                $managedAdgroups[] = $managedAdgroup;
            }
        }
        return $managedAdgroups;
    }

    function loadKeywords() {
        $keywordDAO = new KeywordDAO();
        $keywords = $keywordDAO->loadAll();
        $managedKeywords = array();

        $todayStart = mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
        $end = gmdate("Y-m-d",$todayStart-1);

        foreach ($keywords as $keyword) {
            if($keyword->isApplyBidRule()) {
                $increaseStart = gmdate("Y-m-d",$todayStart - ($keyword->getKeywordIncreaseDays()*SECONDS_IN_DAY));
                $decreaseStart = gmdate("Y-m-d",$todayStart - ($keyword->getKeywordDecreaseDays()*SECONDS_IN_DAY));

                $managedKeyword = $this->getManagedEntity($keyword->id, "keyword_id", $increaseStart, $decreaseStart, $end);
                $managedKeyword->ppcEntity = $keyword;
                $managedKeywords[] = $managedKeyword;
            }
        }
        return $managedKeywords;
    }

    function getManagedEntity($ppcEntityId, $idField, $increaseStart, $decreaseStart, $end) {
        $query = "SELECT SUM(b1.cost) AS total_cost, SUM(b1.revenue)-SUM(b1.cost) AS total_profit, COALESCE(increase.profit,0) AS increase_profit, COALESCE(decrease.profit,0) AS decrease_profit
                  FROM bid_management_data b1 LEFT JOIN
                        (SELECT b2.$idField, SUM(b2.revenue)-SUM(b2.cost) AS profit
                         FROM bid_management_data b2
                         WHERE b2.$idField = $ppcEntityId AND b2.data_date BETWEEN '$increaseStart' AND '$end'
                         GROUP BY b2.$idField) AS increase ON increase.$idField = b1.$idField LEFT JOIN
                        (SELECT b3.$idField, SUM(b3.revenue)-SUM(b3.cost) AS profit
                         FROM bid_management_data b3
                         WHERE b3.$idField = $ppcEntityId AND b3.data_date BETWEEN '$decreaseStart' AND '$end'
                         GROUP BY b3.$idField) AS decrease ON decrease.$idField = b1.$idField ";
        $conn = get_conn();
        $result = mysql_query($query, $conn) or die (__CLASS__.__FUNCTION__.'I cannot execute the query because: ' . mysql_error());

        $row = mysql_fetch_array($result);
        $managedEntity = $this->instantiateManagedEntity($row);
        close_conn($conn);
        return $managedEntity;
    }

    function instantiateManagedEntity($row) {
        $managedEntity = new ManagedEntity();
        $managedEntity->cost = $row["total_cost"];
        $managedEntity->totalProfit = $row["total_profit"];
        $managedEntity->increasePeriodProfit = $row["increase_profit"];
        $managedEntity->decreasePeriodProfit = $row["decrease_profit"];
        return $managedEntity;
    }
}

?>