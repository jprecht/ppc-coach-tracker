<?php
class PPCEntity {
    var $id = 0;
    var $currentBid = 0;
    var $newBid = 0;
    var $currentStatus = "";
    var $newStatus = "";
    
    function isChanged() {
        return ($this->currentBid != $this->newBid || $this->currentStatus != $this->newStatus);
    }

    function setUpdated() {
        $this->currentBid = $this->newBid;
        $this->currentStatus = $this->newStatus;
    }

    function isApplyBidRule() {
        return false;
    }

    function getCostThreshold() {
        return 0;
    }

    function getIncreasePercent() {
        return 0;
    }

    function getDecreasePercent() {
        return 0;
    }
}

class Campaign extends PPCEntity {
    var $campaignId = "";
    var $name = "";
    var $engine = "";
    var $account  = "";
    var $masterAccount  = "";
    var $campaignBidRule = null;
    var $adgroupBidRule = null;
    var $keywordBidRule = null;

    function isApplyBidRule() {
        return $this->isApplyCampaignBidRule();
    }

    function getCostThreshold() {
        return $this->getCampaignCostThreshold();
    }

    function getIncreasePercent() {
        return $this->getCampaignIncreasePercent();
    }

    function getDecreasePercent() {
        return $this->getCampaignDecreasePercent();
    }

    function isApplyCampaignBidRule() {
        if($this->campaignBidRule != null) {
            return $this->campaignBidRule->apply;
        }
        return false;
    }

    function getCampaignCostThreshold() {
        if($this->isApplyCampaignBidRule()) {
            return $this->campaignBidRule->cost_threshold;
        }
        return 0;
    }

    function getCampaignIncreasePercent() {
        if($this->isApplyCampaignBidRule()) {
            return $this->campaignBidRule->increase_percent;
        }
        return 0;
    }

    function getCampaignIncreaseDays() {
        if($this->isApplyCampaignBidRule()) {
            return $this->campaignBidRule->increase_days;
        }
        return 0;
    }

    function getCampaignDecreasePercent() {
        if($this->isApplyCampaignBidRule()) {
            return $this->campaignBidRule->decrease_percent;
        }
        return 0;
    }

    function getCampaignDecreaseDays() {
        if($this->isApplyCampaignBidRule()) {
            return $this->campaignBidRule->decrease_days;
        }
        return 0;
    }

    function isApplyAdgroupBidRule() {
        if($this->adgroupBidRule != null) {
            return $this->adgroupBidRule->apply;
        }
        return false;
    }

    function getAdgroupCostThreshold() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->cost_threshold;
        }
        return 0;
    }

    function getAdgroupIncreasePercent() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->increase_percent;
        }
        return 0;
    }

    function getAdgroupIncreaseDays() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->increase_days;
        }
        return 0;
    }

    function getAdgroupDecreasePercent() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->decrease_percent;
        }
        return 0;
    }

    function getAdgroupDecreaseDays() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->decrease_days;
        }
        return 0;
    }

    function isApplyKeywordBidRule() {
        if($this->keywordBidRule != null) {
            return $this->keywordBidRule->apply;
        }
        return false;
    }

    function getKeywordCostThreshold() {
        if($this->isApplyKeywordBidRule()) {
            return $this->keywordBidRule->cost_threshold;
        }
        return 0;
    }

    function getKeywordIncreasePercent() {
        if($this->isApplyKeywordBidRule()) {
            return $this->keywordBidRule->increase_percent;
        }
        return 0;
    }

    function getKeywordIncreaseDays() {
        if($this->isApplyKeywordBidRule()) {
            return $this->keywordBidRule->increase_days;
        }
        return 0;
    }

    function getKeywordDecreasePercent() {
        if($this->isApplyKeywordBidRule()) {
            return $this->keywordBidRule->decrease_percent;
        }
        return 0;
    }

    function getKeywordDecreaseDays() {
        if($this->isApplyKeywordBidRule()) {
            return $this->keywordBidRule->decrease_days;
        }
        return 0;
    }
}

class Adgroup extends PPCEntity {
    var $campaign;
    var $adgroupId = "";
    var $name = "";
    var $searchMaxCpc = 0;
    var $defaultUrl = "";
    var $adgroupBidRule = null;
    var $keywordBidRule = null;

    function isApplyBidRule() {
        return $this->isApplyAdgroupBidRule() || $this->campaign->isApplyAdgroupBidRule();
    }

    function getCostThreshold() {
        return $this->getAdgroupCostThreshold();
    }

    function getIncreasePercent() {
        return $this->getAdgroupIncreasePercent();
    }

    function getDecreasePercent() {
        return $this->getAdgroupDecreasePercent();
    }

    function isApplyAdgroupBidRule() {
        if($this->adgroupBidRule != null) {
            return $this->adgroupBidRule->apply;
        }
        return false;
    }

    function getAdgroupCostThreshold() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->cost_threshold;
        } else {
            return $this->campaign->getAdgroupCostThreshold();
        }
    }

    function getAdgroupIncreasePercent() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->increase_percent;
        } else {
            return $this->campaign->getAdgroupIncreasePercent();
        }
    }

    function getAdgroupIncreaseDays() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->increase_days;
        } else {
            return $this->campaign->getAdgroupIncreaseDays();
        }
    }

    function getAdgroupDecreasePercent() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->decrease_percent;
        } else {
            return $this->campaign->getAdgroupDecreasePercent();
        }
    }

    function getAdgroupDecreaseDays() {
        if($this->isApplyAdgroupBidRule()) {
            return $this->adgroupBidRule->decrease_days;
        } else {
            return $this->campaign->getAdgroupDecreaseDays();
        }
    }

    function isApplyCurrentKeywordBidRule() {
        if($this->keywordBidRule != null) {
            return $this->keywordBidRule->apply;
        }
        return false;
    }

    function isApplyKeywordBidRule() {
        return $this->isApplyCurrentKeywordBidRule() || $this->campaign->isApplyKeywordBidRule();
    }

    function getKeywordCostThreshold() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->cost_threshold;
        } else {
            return $this->campaign->getKeywordCostThreshold();
        }
    }

    function getKeywordIncreasePercent() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->increase_percent;
        } else {
            return $this->campaign->getKeywordIncreasePercent();
        }
    }

    function getKeywordIncreaseDays() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->increase_days;
        } else {
            return $this->campaign->getKeywordIncreaseDays();
        }
    }

    function getKeywordDecreasePercent() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->decrease_percent;
        } else {
            return $this->campaign->getKeywordDecreasePercent();
        }
    }

    function getKeywordDecreaseDays() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->decrease_days;
        } else {
            return $this->campaign->getKeywordDecreaseDays();
        }
    }
}

class Ad extends PPCEntity {
    var $adgroup;
    var $adId = "";
    var $name = "";
    var $currentUrl = "";
}

class Keyword extends PPCEntity {
    var $adgroup;
    var $keywordId = "";
    var $text = "";
    var $matchType = "";
    var $currentUrl = "";
    var $newUrl = "";
    var $keywordBidRule = null;

    function isChanged() {
        return (parent::isChanged() || $this->currentUrl != $this->newUrl);
    }

    function setUpdated() {
        parent::setUpdated();
        $this->currentUrl = $this->newUrl;
    }
    
    function getBaseUrl() {
        $pos = strpos($this->currentUrl, "?");
        if($pos > -1) {
            return substr($this->currentUrl, 0, $pos);
        }
        return $this->currentUrl;
    }
    
    function isApplyBidRule() {
        return $this->isApplyCurrentKeywordBidRule() || $this->adgroup->isApplyKeywordBidRule();
    }

    function getCostThreshold() {
        return $this->getKeywordCostThreshold();
    }

    function getIncreasePercent() {
        return $this->getKeywordIncreasePercent();
    }

    function getDecreasePercent() {
        return $this->getKeywordDecreasePercent();
    }

    function isApplyCurrentKeywordBidRule() {
        if($this->keywordBidRule != null) {
            return $this->keywordBidRule->apply;
        }
        return false;
    }

    function getKeywordCostThreshold() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->cost_threshold;
        } else {
            return $this->adgroup->getKeywordCostThreshold();
        }
    }

    function getKeywordIncreasePercent() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->increase_percent;
        } else {
            return $this->adgroup->getKeywordIncreasePercent();
        }
    }

    function getKeywordIncreaseDays() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->increase_days;
        } else {
            return $this->adgroup->getKeywordIncreaseDays();
        }
    }

    function getKeywordDecreasePercent() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->decrease_percent;
        } else {
            return $this->adgroup->getKeywordDecreasePercent();
        }
    }

    function getKeywordDecreaseDays() {
        if($this->isApplyCurrentKeywordBidRule()) {
            return $this->keywordBidRule->decrease_days;
        } else {
            return $this->adgroup->getKeywordDecreaseDays();
        }
    }
}
?>