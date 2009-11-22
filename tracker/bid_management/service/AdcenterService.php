<?php
class AdcenterService {
    function uploadKeywords($filename, $keywords) {
    // Write headers
        $this->writeBulkHeaderRow($filename);

        // Add Keywords
        return $this->writeBulkKeywordRows($filename, $keywords);
    }

    function writeBulkHeaderRow($filename) {
        $row = array();
        $row[0] = "Type";
        $row[1] = "Status";
        $row[2] = "Campaign";
        $row[3] = "Ad group";
        $row[4] = "Keyword";
        $row[5] = "Match type";
        $row[6] = "Bid amount";
        $row[7] = "Destination URL";
        $this->writeBulkRow($row, $filename);
    }

    function writeBulkKeywordRows($filename, $keywords) {
        $updatedKeywords = array();
        foreach ($keywords as $keyword) {
            if($keyword->adgroup->campaign->engine == "adcenter") {
                $this->writeBulkKeywordRow($keyword, $filename);
                $updatedKeywords[] = $keyword;
            }
        }
        return $updatedKeywords;
    }

    function writeBulkKeywordRow($keyword, $filename) {
        $row = array();
        $row[0] = "Keyword";
        $row[1] = $keyword->newStatus;
        $row[2] = $keyword->adgroup->campaign->name;
        $row[3] = $keyword->adgroup->name;
        $row[4] = $keyword->text;
        $row[5] = $keyword->matchType;
        $row[6] = $keyword->newBid;
        $row[7] = $keyword->newUrl;
        $this->writeBulkRow($row, $filename);
    }

    function writeBulkRow($row, $filename) {
    // Open file
        $fp = fopen($filename, "a");
        foreach ($row as $field) {
            fwrite($fp, $field.",");
        }
        // Write newline
        fwrite($fp, "\n");
    }
}
?>