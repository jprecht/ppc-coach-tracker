<?php
require_once dirname(__FILE__).'/apility/apility.php';
require_once dirname(__FILE__).'/../entity/APIAccount.php';

define("MICRONS", 1000000);

class AdwordsService {
    function getUser($masterAccount, $clientAccount) {
        $apilityUser = new APIlityUser();
        $apilityUser->setEmail($masterAccount->user);
        $apilityUser->setPassword($masterAccount->password);
        $apilityUser->setDeveloperToken($masterAccount->developerToken);
        $apilityUser->setApplicationToken($masterAccount->applicationToken);
        $apilityUser->setClientEmail($clientAccount);
        return $apilityUser;
    }

    function updateKeywords($masterAccount, $clientAccount, $keywords) {
        $apilityUser = $this->getUser($masterAccount, $clientAccount);
        $criteria = array();
        $updatedKeywords = array();
        foreach ($keywords as $keyword) {
            if($keyword->adgroup->campaign->engine == "adwords") {
                $criteria[] = array(
                    "id" => $keyword->keywordId,
                    "belongsToAdGroupId" => $keyword->adgroup->adgroupId,
                    "criterionType" => "Keyword",
                    "isPaused" => $keyword->newStatus == "Paused",
                    "maxCpc" => $keyword->newBid,
                    "destinationUrl" => $keyword->newUrl
                );
                $updatedKeywords[] = $keyword;
            }
        }

        $chunks = array_chunk($criteria, 100);
        foreach ($chunks as $chunk) {
            $apilityUser->updateCriterionList($chunk);
        }
        return $updatedKeywords;
    }

    function updateAdgroups($masterAccount, $clientAccount, $adgroups) {
        $apilityUser = $this->getUser($masterAccount, $clientAccount);

        $uploadAdgroups = array();
        foreach ($adgroups as $adgroup) {
            if($adgroup->campaign->engine == "adwords") {
                $uploadAdgroups[] = array(
                    "id" => $adgroup->adgroupId,
                    "campaignId" => $adgroup->campaign->campaignId,
                    "status" => $adgroup->newStatus,
                    "contentMaxCpc" => $adgroup->newBid
                );
            }
        }

        $apilityUser->updateAdgroupList($uploadAdgroups);
    }

    function updateCampaigns($masterAccount, $clientAccount, $campaigns) {
        $apilityUser = $this->getUser($masterAccount, $clientAccount);

        $uploadCamapigns = array();
        foreach ($campaigns as $campaign) {
            if($campaign->engine == "adwords") {
                $uploadCamapigns[] = array(
                    "id" => $campaign->campaignId,
                    "status" => $campaign->newStatus,
                    "budget" => $campaign->newBid
                );
            }
        }
        $apilityUser->updateCampaignList($uploadCamapigns);
    }

    function getKeywordStructureReport($masterAccount, $clientAccount, $reportWaitTime) {
        $apilityUser = $this->getUser($masterAccount, $clientAccount);

        $start = gmdate("Y-m-d",0);
        $end = gmdate("Y-m-d",time());
        $report = $apilityUser->getAccountStructureTsvReport(
            'Keyword Structure Report',
            $start,
            $end,
            array('Campaign', 'CampaignId', 'AdGroup', 'AdGroupId', 'Keyword', 'KeywordId',' KeywordTypeDisplay', 'MaximumCPC', 'KeywordStatus', 'KeywordDestUrlDisplay'),
            array('Keyword'),
            array(),
            array('Active', 'Paused'),
            array(),
            array('Enabled', 'Paused'),
            array(),
            array('Active', 'Paused'),
            '',
            '',
            false,
            array(),
            false,
            $reportWaitTime,
            true,
            false
        );

        return $report;
    }

    function getAdStructureReport($masterAccount, $clientAccount, $reportWaitTime) {
        $apilityUser = $this->getUser($masterAccount, $clientAccount);

        $start = gmdate("Y-m-d",0);
        $end = gmdate("Y-m-d",time());
        $report = $apilityUser->getAccountStructureTsvReport(
            'Ad Structure Report',
            $start,
            $end,
            array('Campaign', 'CampaignId', 'DailyBudget', 'CampaignStatus', 'AdGroup', 'AdGroupId', 'AdGroupMaxCpc', 'AdGroupMaxContentCpc', 'AdGroupStatus', 'CreativeId', 'AdStatus', 'DescriptionLine1', 'CreativeDestUrl'),
            array('Creative'),
            array(),
            array('Active', 'Paused'),
            array(),
            array('Enabled', 'Paused'),
            array(),
            array(),
            '',
            '',
            false,
            array(),
            false,
            $reportWaitTime,
            true,
            false
        );

        return $report;
    }
}
?>