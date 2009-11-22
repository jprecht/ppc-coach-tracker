<?php
require_once dirname(__FILE__).'/utils/StringUtils.php';
require_once dirname(__FILE__).'/PPCImporter.php';
require_once dirname(__FILE__).'/YahooService.php';

abstract class AbstractAdcenterImporter extends PPCImporter {

    protected function getCampaignStructureFields($row) {
        $row = explode(",", $row);
        if (isset($row[0]) && $row[0] == "Campaign") {
            $fields["engine"] = "adcenter";
            $fields["account"] = "";
            $fields["masterAccount"] = "";
            $fields["campaignName"] = $row[2];
            $fields["campaignId"] = $fields["campaignName"];
            $fields["status"] = $row[1];
            $fields["budget"] = ($row[24] > 0) ? $row[24] : 0;
            return $fields;
        }
        return false;
    }

    protected function getAdgroupStructureFields($row) {
        $row = explode(",", $row);
        if (isset($row[0]) && $row[0] == "AdGroup") {
            $fields["campaignId"] = $row[2];
            $fields["adgroupName"] = $row[3];
            $fields["adgroupId"] = $fields["adgroupName"];
            $fields["status"] = $row[1];
            $fields["searchMaxCpc"] = ($row[20] > 0) ? $row[20] : 0;
            $fields["contentMaxCpc"] = ($row[19] > 0) ? $row[19] : 0;
            return $fields;
        }
        return false;
    }

    protected function getKeywordStructureFields($row) {
        $row = explode(",", $row);
        if (isset($row[0]) && $row[0] == "Keyword") {
            $fields["adgroupId"] = $row[3];
            $fields["text"] = $row[4];
            $fields["matchType"] = $row[29];
            $fields["keywordId"] = $fields["text"].$fields["matchType"];
            $fields["status"] = $row[1];
            $fields["searchMaxCpc"] = ($row[20] > 0) ? $row[20] : 0;
            $fields["destinationUrl"] = $row[26];
            return $fields;
        }
        return false;
    }

    protected function getAdStructureFields($row) {
        $row = explode(",", $row);
        if (isset($row[0]) && $row[0] == "TextAd") {
            $fields["adgroupId"] = $row[3];
            $fields["adName"] = $row[7];
            $fields["adId"] = $row[6];
            $fields["status"] = $row[1];
            $fields["destinationUrl"] = $row[10];
            return $fields;
        }
        return false;
    }
}

class LiveAdcenterImporterImpl extends AbstractAdcenterImporter {
    private $report;
    private $filename;

    public function  __construct($filename) {
        $this->filename = $filename;
    }

    public function importStructure($masterAccount, $clientAccount) {
        parent::importStructure($masterAccount, $clientAccount);
        $campaignDAO = new CampaignDAO;
        $campaigns = $campaignDAO->loadAll("adcenter");
        $campaignDAO->saveCampaigns($campaigns);
    }

    private function downloadReport() {
        if($this->report == null) {
            $stringUtils = new StringUtils;
            $this->report = file_get_contents($this->filename);
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