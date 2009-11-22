<?php
require_once dirname(__FILE__).'/utils/StringUtils.php';
require_once dirname(__FILE__).'/PPCImporter.php';
require_once dirname(__FILE__).'/YahooService.php';

abstract class AbstractYahooImporter extends PPCImporter {

    protected function getCampaignStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[2]) && $row[2] == "Campaign") {
            $fields["engine"] = "yahoo";
            $fields["account"] = $this->clientAccount->masterAccountId;
            $fields["masterAccount"] = $this->clientAccount->accountId;
            $fields["campaignName"] = $row[0];
            $fields["campaignId"] = substr($row[24], 1);
            $fields["status"] = ($row[3] == "On") ? "Active" : "Paused";
            $fields["budget"] = 0; // Note: Not available in report
            return $fields;
        }
        return false;
    }

    protected function getAdgroupStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[2]) && $row[2] == "Ad Group") {
            $fields["campaignId"] = substr($row[24], 1);
            $fields["adgroupName"] = $row[1];
            $fields["adgroupId"] = substr($row[28], 1);
            $fields["status"] = ($row[3] == "On") ? "Active" : "Paused";
            $fields["searchMaxCpc"] = ($row[8] == "Default") ? -1 : ($row[8] == "" ? 0 : $row[8]);
            $fields["searchMaxCpc"] = str_replace("\"", "", $fields["searchMaxCpc"]);
            $fields["contentMaxCpc"] = ($row[13] == "Default") ? -1 : ($row[13] == "" ? 0 : $row[13]);
            $fields["contentMaxCpc"] = str_replace("\"", "", $fields["contentMaxCpc"]);
            return $fields;
        }
        return false;
    }

    protected function getKeywordStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[2]) && $row[2] == "Keyword") {
            $fields["adgroupId"] = substr($row[28], 1);
            $fields["text"] = $row[5];
            $fields["keywordId"] = substr($row[31], 1);
            $fields["matchType"] = $row[12];
            $fields["status"] = ($row[3] == "On") ? "Active" : "Paused";
            $fields["searchMaxCpc"] = ($row[8] == "Default") ? -1 : ($row[8] == "" ? 0 : $row[8]);
            $fields["searchMaxCpc"] = str_replace("\"", "", $fields["searchMaxCpc"]);
            $fields["destinationUrl"] = $row[7];
            return $fields;
        }
        return false;
    }

    protected function getAdStructureFields($row) {
        $row = explode("\t", $row);
        if (isset($row[2]) && $row[2] == "Ad") {
            $fields["adgroupId"] = substr($row[28], 1);
            $fields["adName"] = $row[17];
            $fields["adId"] = substr($row[30], 1);
            $fields["status"] = ($row[3] == "On") ? "Active" : "Paused";
            $fields["destinationUrl"] = $row[22];
            return $fields;
        }
        return false;
    }

}

class TestYahooImporterImpl extends AbstractYahooImporter {
    var $filename = "E:\\clients\\ppccoach\\reports\\ppcbullet.csv";

    protected function getCampaignReport() {
        $stringUtils = new StringUtils;
        return $stringUtils->unicodeToAscii(file_get_contents($this->filename));
    }

    protected function getAdgroupReport() {
        $stringUtils = new StringUtils;
        return $stringUtils->unicodeToAscii(file_get_contents($this->filename));
    }

    protected function getKeywordReport() {
        $stringUtils = new StringUtils;
        return $stringUtils->unicodeToAscii(file_get_contents($this->filename));
    }

    protected function getAdReport() {
        $stringUtils = new StringUtils;
        return $stringUtils->unicodeToAscii(file_get_contents($this->filename));
    }
}

class LiveYahooImporterImpl extends AbstractYahooImporter {
    private $report;
    private $yahooService;

    public function  __construct($yahooService) {
        $this->yahooService = $yahooService;
    }

    public function importStructure($masterAccount, $clientAccount) {
        parent::importStructure($masterAccount, $clientAccount);
        $campaignDAO = new CampaignDAO;
        $campaigns = $campaignDAO->loadAll("yahoo");
        $this->yahooService->downloadCampaignBudgets($masterAccount, $clientAccount, $campaigns);
        $campaignDAO->saveCampaigns($campaigns);
    }

    private function downloadReport() {
        if($this->report == null) {
            $stringUtils = new StringUtils;
            $this->report = $stringUtils->unicodeToAscii($this->yahooService->getYahooBulkReport($this->masterAccount, $this->clientAccount));
        }
        return $this->report;
    }

    protected function getCampaignReport() {
        return $this->downloadReport();
    }

    protected function getAdgroupReport() {
        return $this->downloadReport();
    }

    protected function getKeywordReport() {
        return $this->downloadReport();
    }

    protected function getAdReport() {
        return $this->downloadReport();
    }
}
?>