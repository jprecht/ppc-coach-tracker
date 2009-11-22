<?php
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';
require_once dirname(__FILE__).'/AdwordsService.php';
require_once dirname(__FILE__).'/AdwordsImporter.php';
require_once dirname(__FILE__).'/YahooService.php';
require_once dirname(__FILE__).'/YahooImporter.php';
require_once dirname(__FILE__).'/AdcenterImporter.php';
require_once dirname(__FILE__).'/AdcenterService.php';

ini_set("max_execution_time", 6000);

class URLUpdateService {

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

    function adcenterDownload($filename) {
        $adcenterImporter = new LiveAdcenterImporterImpl($filename);
        $adcenterImporter->importStructure(null, null);
    }

    function adwordsUpload($masterAccount, $clientAccount, $keywords) {
        if(count($keywords) > 0) {
            $adwordsService = new AdwordsService;
            $updatedKeywords = $adwordsService->updateKeywords($masterAccount, $clientAccount, $keywords);
            $this->saveUpdatedKeywords($updatedKeywords);
        }
    }

    function yahooUpload($masterAccount, $clientAccount, $keywords) {
        if(count($keywords) > 0) {
            $yahooService = new YahooService;
            $updatedKeywords = $yahooService->upload($masterAccount, $clientAccount, array(), array(), $keywords);
            $this->saveUpdatedKeywords($updatedKeywords);
        }
    }

    function adcenterUpload($filename, $keywords) {
        if(count($keywords) > 0) {
            $adcenterService = new AdcenterService();
            $updatedKeywords = $adcenterService->uploadKeywords($filename, $keywords);
            $this->saveUpdatedKeywords($updatedKeywords);
        }
    }

    function saveUpdatedKeywords($keywords) {
        $dao = new KeywordDAO();
        $dao->saveKeywords($this->setEntityUpdated($keywords));
    }

    function setEntityUpdated($entities) {
        $newEntities = array();
        foreach ($entities as $entity) {
            $entity->setUpdated();
            $newEntities[] = $entity;
        }
        return $newEntities;
    }

    function updateUrls() {
        $updatedKeywords = array();
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll();
        foreach ($keywords as $keyword) {
            $keyword = $this->updateURL($keyword);
            if($keyword->isChanged()) {
                $updatedKeywords[] = $keyword;
            }
        }
        // Save changes
        $dao = new KeywordDAO();
        $dao->saveKeywords($updatedKeywords);
        return $updatedKeywords;
    }

    function updateURL($keyword) {
        // If the current url is just the default then change it to the adgroup default
        if($keyword->currentUrl  == "" || $keyword->currentUrl  == "default URL") {
            $keyword->currentUrl = $keyword->adgroup->defaultUrl;
        }
        
        // Remove the query string from the url
        $url = $keyword->getBaseUrl();
        $keywordId = $keyword->id;
        $source = $keyword->adgroup->campaign->engine;
        $z = $this->extractParameter("z", $keyword->currentUrl);
        $b = $this->extractParameter("b", $keyword->currentUrl);
        $network = "";
        if($source == "adwords") {
            $network = "{ifsearch:search}{ifcontent:content}";
        }
        if($source == "yahoo") {
            $network = "{OVMTC}";
        }
        if($source == "adcenter") {
            $network = $this->extractParameter("network", $keyword->currentUrl);
        }
        // Put the new url together
        $keyword->newUrl = "$url?source=$source&keywordId=$keywordId&z=$z&b=$b&network=$network";
        return $keyword;
    }

    function extractParameter($name, $url) {
        $param = "";
        $search = "$name=";
        $pos = strpos($url, $search);
        if($pos > -1) {
            $newUrl = substr($url, $pos+strlen($search));
            $pos = strpos($newUrl, "&");
            if($pos > -1) {
                $param = substr($newUrl, 0, $pos);
            } else {
                $param = $newUrl;
            }
        }
        return $param;
    }
}
?>