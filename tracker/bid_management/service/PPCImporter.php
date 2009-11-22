<?php
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

abstract class PPCImporter {
    protected $masterAccount;
    protected $clientAccount;

    public function importStructure($masterAccount, $clientAccount) {
        $this->masterAccount = $masterAccount;
        $this->clientAccount = $clientAccount;
        $campaigns = $this->importCampaigns();
        $adgroups = $this->importAdgroups($campaigns);
        $this->importAds($adgroups);
        $this->importKeywords($adgroups);
    }

    private function importCampaigns() {
        $campaignDAO = new CampaignDAO;

        $report = $this->getCampaignReport();
        $oldCampaigns = $campaignDAO->loadAll();
        $campaigns = array();
        foreach ($oldCampaigns as $campaign) {
            $campaigns[$campaign->campaignId] = $campaign;
        }

        $rows = explode("\n", $report);
        foreach ($rows as $row) {
            $campaign = $this->importCampaignStructureRow($row, $campaigns);
            if($campaign) {
                $campaigns[$campaign->campaignId] = $campaign;
            }
        }
        $campaignDAO->saveCampaigns($campaigns);

        $oldCampaigns = $campaignDAO->loadAll();
        $campaigns = array();
        foreach ($oldCampaigns as $campaign) {
            $campaigns[$campaign->campaignId] = $campaign;
        }
        return $campaigns;
    }

    private function importCampaignStructureRow($row, $campaigns) {
        $fields = $this->getCampaignStructureFields($row);
        if ($fields) {
            if(isset($campaigns[$fields["campaignId"]])) {
                $campaign = $campaigns[$fields["campaignId"]];
            } else {
                $campaign = new Campaign;
                $campaign->campaignId = $fields["campaignId"];
            }
            $campaign->engine = $fields["engine"];
            $campaign->name = $fields["campaignName"];
            $campaign->masterAccount = $fields["masterAccount"];
            $campaign->account = $fields["account"];
            $campaign->currentBid = $fields["budget"];
            $campaign->newBid = $fields["budget"];
            $campaign->currentStatus = $fields["status"];
            $campaign->newStatus = $fields["status"];
            return $campaign;
        }
        return false;
    }

    protected abstract function getCampaignReport();

    protected abstract function getCampaignStructureFields($row);

    private function importAdgroups($campaigns) {
        $adgroupDAO = new AdgroupDAO;

        $report = $this->getAdgroupReport();
        $oldAdgroups = $adgroupDAO->loadAll();
        $adgroups = array();
        foreach ($oldAdgroups as $adgroup) {
            $adgroups[$adgroup->adgroupId] = $adgroup;
        }

        $rows = explode("\n", $report);
        foreach ($rows as $row) {
            $adgroup = $this->importAdgroupStructureRow($row, $campaigns, $adgroups);
            if($adgroup) {
                $adgroups[$adgroup->adgroupId] = $adgroup;
            }
        }
        $adgroupDAO->saveAdgroups($adgroups);
        $oldAdgroups = $adgroupDAO->loadAll();
        $adgroups = array();
        foreach ($oldAdgroups as $adgroup) {
            $adgroups[$adgroup->adgroupId] = $adgroup;
        }
        return $adgroups;
    }

    private function importAdgroupStructureRow($row, $campaigns, $adgroups) {
        $fields = $this->getAdgroupStructureFields($row);
        if ($fields) {
            if(isset($adgroups[$fields["adgroupId"]])) {
                $adgroup = $adgroups[$fields["adgroupId"]];
            } else {
                $adgroup = new Adgroup;
                $adgroup->adgroupId = $fields["adgroupId"];
            }
            $adgroup->campaign = $campaigns[$fields["campaignId"]];
            $adgroup->name = $fields["adgroupName"];
            $adgroup->currentBid = $fields["contentMaxCpc"];
            $adgroup->newBid = $fields["contentMaxCpc"];
            $adgroup->currentStatus = $fields["status"];
            $adgroup->newStatus = $fields["status"];
            $adgroup->searchMaxCpc = $fields["searchMaxCpc"];
            return $adgroup;
        }
        return false;
    }

    protected abstract function getAdgroupReport();

    protected abstract function getAdgroupStructureFields($row);

    private function importAds($adgroups) {
        $adDAO = new AdDAO;

        $report = $this->getAdReport();
        $oldAds = $adDAO->loadAll();
        $ads = array();
        foreach ($oldAds as $ad) {
            $ads[$ad->adId] = $ad;
        }

        $rows = explode("\n", $report);
        foreach ($rows as $row) {
            $ad = $this->importAdStructureRow($row, $adgroups, $ads);
            if($ad) {
                $ads[$ad->adId] = $ad;
            }
        }
        $adDAO->saveAds($ads);
        return $ads;
    }

    private function importAdStructureRow($row, $adgroups, $ads) {
        $fields = $this->getAdStructureFields($row);
        if ($fields) {
            if(isset($ads[$fields["adId"]])) {
                $ad = $ads[$fields["adId"]];
            } else {
                $ad = new Ad;
                $ad->adId = $fields["adId"];
            }
            $ad->adgroup = $adgroups[$fields["adgroupId"]];
            $ad->name = $fields["adName"];
            $ad->currentStatus = $fields["status"];
            $ad->newStatus = $fields["status"];
            $ad->currentUrl = $fields["destinationUrl"];
            if($ad->currentUrl != "") {
                $ad->adgroup->defaultUrl = $ad->currentUrl;
                $adgroupDAO = new AdgroupDAO();
                $adgroupDAO->saveAdgroups(array($ad->adgroup));
            }
            return $ad;
        }
        return false;
    }

    protected abstract function getAdReport();

    protected abstract function getAdStructureFields($row);

    private function importKeywords($adgroups) {
        $keywordDAO = new KeywordDAO;

        $report = $this->getKeywordReport();
        $oldKeywords = $keywordDAO->loadAll();
        $keywords = array();
        foreach ($oldKeywords as $keyword) {
            $keywords[$keyword->keywordId] = $keyword;
        }

        $rows = explode("\n", $report);
        foreach ($rows as $row) {
            $keyword = $this->importKeywordStructureRow($row, $adgroups, $keywords);
            if($keyword) {
                $keywords[$keyword->keywordId] = $keyword;
            }
        }
        $keywordDAO->saveKeywords($keywords);
        return $keywords;
    }

    private function importKeywordStructureRow($row, $adgroups, $keywords) {
        $fields = $this->getKeywordStructureFields($row);
        if ($fields) {
            if(isset($keywords[$fields["keywordId"]])) {
                $keyword = $keywords[$fields["keywordId"]];
            } else {
                $keyword = new Keyword;
                $keyword->keywordId = $fields["keywordId"];
            }

            $keyword->adgroup = $adgroups[$fields["adgroupId"]];
            $keyword->text = $fields["text"];
            $keyword->matchType = $fields["matchType"];
            if($fields["searchMaxCpc"] < 0) {
                $keyword->currentBid = $keyword->adgroup->searchMaxCpc;
            } else {
                $keyword->currentBid = $fields["searchMaxCpc"];
            }
            $keyword->newBid = $keyword->currentBid;
            $keyword->currentStatus = $fields["status"];
            $keyword->newStatus = $fields["status"];
            $keyword->currentUrl = $fields["destinationUrl"];
            $keyword->newUrl = $fields["destinationUrl"];
            return $keyword;
        }
        return false;
    }

    protected abstract function getKeywordReport();

    protected abstract function getKeywordStructureFields($row);
}
?>