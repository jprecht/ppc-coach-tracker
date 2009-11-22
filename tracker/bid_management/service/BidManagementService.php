<?php
require_once dirname(__FILE__).'/../database/ManagedEntityDAO.php';
require_once dirname(__FILE__).'/AdwordsService.php';
require_once dirname(__FILE__).'/AdwordsImporter.php';
require_once dirname(__FILE__).'/YahooService.php';
require_once dirname(__FILE__).'/YahooImporter.php';

ini_set("max_execution_time", 6000);

class BidManagementService {
    function update($adwordsMasterAccount, $adwordsClientAccount, $ysmMasterAccount, $ysmClientAccount) {
        // Download latest bids and status from engines
        $this->adwordsDownload($adwordsMasterAccount, $adwordsClientAccount);
        $this->yahooDownload($ysmMasterAccount, $ysmClientAccount);

        // Update campaigns, adgroups and keywords
        list($keywords, $adgroups, $campaigns) = $this->calculateUpdates();

        // Upload changes
        $this->adwordsUpload($adwordsMasterAccount, $adwordsClientAccount, $keywords, $adgroups, $campaigns);
        $this->yahooUpload($ysmMasterAccount, $ysmClientAccount, $keywords, $adgroups, $campaigns);
    }

    function adwordsDownload($masterAccount, $clientAccount) {
        $adwordsService = new AdwordsService;
        $adwordsImporter = new LiveAdwordsImporterImpl($adwordsService);
        $adwordsImporter->importStructure($masterAccount, $clientAccount);
    }

    function yahooDownload($masterAccount, $clientAccount) {
        $yahooService = new YahooService;
        $yahooImporter = new LiveYahooImporterImpl($yahooService);
        $yahooImporter->importStructure($masterAccount, $clientAccount);
    }

    function adwordsUpload($masterAccount, $clientAccount, $keywords, $adgroups, $campaigns) {
        $updatedKeywords = $this->adwordsKeywordUpload($masterAccount, $clientAccount, $keywords);
        $updatedAdgroups = $this->adwordsAdgroupUpload($masterAccount, $clientAccount, $adgroups);
        $updatedCampaigns = $this->adwordsCampaignUpload($masterAccount, $clientAccount, $campaigns);

        $this->saveUpdatedEntities($updatedKeywords, $updatedAdgroups, $updatedCampaigns);
    }

    function yahooUpload($masterAccount, $clientAccount, $keywords, $adgroups, $campaigns) {
        $yahooService = new YahooService;

        $uploadKeywords = $this->getEngineEntities($keywords, "yahoo");
        $uploadAdgroups = $this->getEngineEntities($adgroups, "yahoo");
        $uploadCampaigns = $this->getEngineEntities($campaigns, "yahoo");

        $yahooService->upload($masterAccount, $clientAccount, $uploadCampaigns, $uploadAdgroups, $uploadKeywords);
        $this->saveUpdatedEntities($uploadKeywords, $uploadAdgroups, $uploadCampaigns);
    }

    function adwordsKeywordUpload($masterAccount, $clientAccount, $keywords) {
        $uploadKeywords = $this->getEngineKeywords($keywords, "adwords");

        if(count($uploadKeywords) > 0) {
            $adwordsService = new AdwordsService;
            $adwordsService->updateKeywords($masterAccount, $clientAccount, $uploadKeywords);
        }
        return $uploadKeywords;
    }

    function adwordsAdgroupUpload($masterAccount, $clientAccount, $adgroups) {
        $uploadAdgroups = $this->getEngineAdgroups($adgroups, "adwords");

        if(count($uploadAdgroups) > 0) {
            $adwordsService = new AdwordsService;
            $adwordsService->updateAdgroups($masterAccount, $clientAccount, $uploadAdgroups);
        }
        return $uploadAdgroups;
    }

    function adwordsCampaignUpload($masterAccount, $clientAccount, $campaigns) {
        $uploadCampaigns = $this->getEngineCampaigns($campaigns, "adwords");

        if(count($uploadCampaigns) > 0) {
            $adwordsService = new AdwordsService;
            $adwordsService->updateCampaigns($masterAccount, $clientAccount, $uploadCampaigns);
        }

        return $uploadCampaigns;
    }

    function getEngineKeywords($keywords, $engine) {
        $newKeywords = array();
        foreach ($keywords as $keyword) {
            if($keyword->adgroup->campaign->engine == $engine) {
                $newKeywords[] = $keyword;
            }
        }
        return $newKeywords;
    }

    function getEngineAdgroups($adgroups, $engine) {
        $newAdgroups = array();
        foreach ($adgroups as $adgroup) {
            if($adgroup->campaign->engine == $engine) {
                $newAdgroups[] = $adgroup;
            }
        }
        return $newAdgroups;
    }

    function getEngineCampaigns($campaigns, $engine) {
        $newCampaigns = array();
        foreach ($campaigns as $campaign) {
            if($campaign->engine == $engine) {
                $newCampaigns[] = $campaign;
            }
        }
        return $newCampaigns;
    }

    function saveUpdatedEntities($keywords, $adgroups, $campaigns) {
        $dao = new KeywordDAO();
        $dao->saveKeywords($this->setEntityUpdated($keywords));
        $dao = new AdgroupDAO();
        $dao->saveAdgroups($this->setEntityUpdated($adgroups));
        $dao = new CampaignDAO();
        $dao->saveCampaigns($this->setEntityUpdated($campaigns));
    }

    function setEntityUpdated($entities) {
        $newEntities = array();
        foreach ($entities as $entity) {
            $entity->setUpdated();
            $newEntities[] = $entity;
        }
        return $newEntities;
    }

    function calculateUpdates() {
        $dao = new ManagedEntityDAO;
        $dao->prepareData();
        // Update keywords
        $keywords = $this->updateKeywords();
        // Update adgroups
        $adgroups = $this->updateAdgroups();
        // Update campaigns
        $campaigns = $this->updateCampaigns();

        return array($keywords, $adgroups, $campaigns);
    }

    function updateKeywords() {
        $updatedKeywords = array();
        $dao = new ManagedEntityDAO;
        $keywords = $dao->loadKeywords();
        foreach ($keywords as $keyword) {
            $keyword->update();
            if($keyword->ppcEntity->isChanged()) {
                $updatedKeywords[] = $keyword->ppcEntity;
            }
        }
        // Save changes
        $dao = new KeywordDAO();
        $dao->saveKeywords($updatedKeywords);
        return $updatedKeywords;
    }

    function updateAdgroups() {
        $updatedAdgroups = array();
        $dao = new ManagedEntityDAO;
        $adgroups = $dao->loadAdgroups();
        foreach ($adgroups as $adgroup) {
            $adgroup->update();
            if($adgroup->ppcEntity->isChanged()) {
                $updatedAdgroups[] = $adgroup->ppcEntity;
            }
        }
        // Save changes
        $dao = new AdgroupDAO();
        $dao->saveAdgroups($updatedAdgroups);
        return $updatedAdgroups;
    }

    function updateCampaigns() {
        $updatedCampaigns = array();
        $dao = new ManagedEntityDAO;
        $campaigns = $dao->loadCampaigns($engine);
        foreach ($campaigns as $campaign) {
            $campaign->update();
            if($campaign->ppcEntity->isChanged()) {
                $updatedCampaigns[] = $campaign->ppcEntity;
            }
        }
        // Save changes
        $dao = new CampaignDAO();
        $dao->saveCampaigns($updatedCampaigns);
        return $updatedCampaigns;
    }

}
?>