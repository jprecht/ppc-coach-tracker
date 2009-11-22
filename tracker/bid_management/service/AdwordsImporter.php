<?php
require_once dirname(__FILE__).'/PPCImporter.php';
require_once dirname(__FILE__).'/AdwordsService.php';

abstract class AbstractAdwordsImporter extends PPCImporter {
    protected function getCampaignStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[0]) && is_numeric($row[0])) {
            $fields = array();
            $fields["engine"] = "adwords";
            $fields["account"] = $this->clientAccount;
            $fields["masterAccount"] = $this->masterAccount->user;
            $fields["campaignName"] = $row[1];
            $fields["campaignId"] = $row[0];
            $fields["status"] = $row[12];
            $fields["budget"] = $row[7];
            return $fields;
        }
        return false;
    }

    protected function getAdgroupStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[0]) && is_numeric($row[0])) {
            $fields = array();
            $fields["campaignId"] = $row[0];
            $fields["adgroupName"] = $row[3];
            $fields["adgroupId"] = $row[2];
            $fields["status"] = ($row[10] == "Enabled") ? "Active" : "Paused";
            $fields["searchMaxCpc"] = $row[8]/MICRONS;
            $fields["contentMaxCpc"] = $row[9]/MICRONS;
            return $fields;
        }
        return false;
    }

    protected function getKeywordStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[0]) && is_numeric($row[0])) {
            $fields = array();
            $fields["adgroupId"] = $row[2];
            $fields["text"] = $row[5];
            $fields["keywordId"] = $row[4];
            $fields["matchType"] = $row[6];
            $fields["status"] = $row[7];
            $fields["searchMaxCpc"] = $row[8];
            $fields["destinationUrl"] = $row[9];
            return $fields;
        }
        return false;
    }

    protected function getAdStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[0]) && is_numeric($row[0])) {
            $fields = array();
            $fields["adgroupId"] = $row[2];
            $fields["adName"] = $row[4];
            $fields["adId"] = $row[5];
            $fields["status"] = ($row[6] == "Enabled") ? "Active" : "Paused";
            $fields["destinationUrl"] = $row[11];
            return $fields;
        }
        return false;
    }
}

class TestAdwordsImporterImpl extends AbstractAdwordsImporter {
    protected function getCampaignReport() {
        return $this->getAdReport();
    }

    protected function getAdgroupReport() {
        return $this->getAdReport();
    }

    protected function getKeywordReport() {
        return file_get_contents("E:\\clients\\ppccoach\\reports\\adwords_keywords.csv");
    }

    protected function getAdReport() {
        return file_get_contents("E:\\clients\\ppccoach\\reports\\adwords_ads.csv");
    }
}

class LiveAdwordsImporterImpl extends AbstractAdwordsImporter {
    private $adwordsService;
    private $adReport;
    private $keywordReport;

    public function __construct($adwordsService) {
        $this->adwordsService = $adwordsService;
    }
    
    private function downloadKeywordReport() {
        $this->keywordReport = $this->adwordsService->getKeywordStructureReport($this->masterAccount, $this->clientAccount, 30);
    }

    private function downloadAdReport() {
        $this->adReport = $this->adwordsService->getAdStructureReport($this->masterAccount, $this->clientAccount, 30);
    }

    protected function getCampaignReport() {
        return $this->getAdReport();
    }

    protected function getAdgroupReport() {
        return $this->getAdReport();
    }

    protected function getKeywordReport() {
        if($this->keywordReport == null) {
            $this->downloadKeywordReport();
        }
        return $this->keywordReport;
    }

    protected function getAdReport() {
        if($this->adReport == null) {
            $this->downloadAdReport();
        }
        return $this->adReport;
    }
}
?>