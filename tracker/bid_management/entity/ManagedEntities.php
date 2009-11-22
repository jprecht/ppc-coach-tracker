<?php
class ManagedEntity {
    var $ppcEntity = 0;
    var $cost = 0;
    var $totalProfit = 0;
    var $increasePeriodProfit = 0;
    var $decreasePeriodProfit = 0;

    function isProfitable() {
        return $this->totalProfit > 0;
    }

    function isIncreasePeriodProfitable() {
        return $this->increasePeriodProfit > 0;
    }

    function isDecreasePeriodLoss() {
        return $this->decreasePeriodProfit < 0;
    }

    function costThresholdExceeded() {
        if($this->cost > $this->ppcEntity->getCostThreshold()) {
            return true;
        }
        return false;
    }

    function changeBid($updatePercent) {
    // Calculate update amount
        $updateAmount = (abs($updatePercent)/100)*$this->ppcEntity->currentBid;

        // If the update percentage is positive then increase - otherwise decrease
        if($updatePercent > 0) {
            $newBid = $this->ppcEntity->currentBid + $updateAmount;
        } else {
            $newBid = $this->ppcEntity->currentBid - $updateAmount;
        }

        // If the new bid is zero or less then it needs to be paused
        if($newBid > 0) {
            $this->ppcEntity->newBid = $newBid;
        } else {
            $this->ppcEntity->newStatus = "Paused";
        }
    }

    function update() {
        if($this->ppcEntity->isApplyBidRule()) {
            $this->updateStatus();
            $this->updateBid();
        }
    }

    function updateStatus() {
        // If the entity isn't profitable and has exceeded it's cost threshold then pause it
        if(!$this->isProfitable() && $this->costThresholdExceeded()) {
            $this->ppcEntity->newStatus = "Paused";
        } else {
            $this->ppcEntity->newStatus = $this->ppcEntity->currentStatus;
        }
    }

    function updateBid() {
        $this->ppcEntity->newBid = $this->ppcEntity->currentBid;

        if($this->ppcEntity->currentStatus != "Paused" && $this->ppcEntity->newStatus != "Paused") {
        // If the entity is profitable and has been during the increase period then increase the bid by the specified amount
            if($this->isProfitable() && $this->isIncreasePeriodProfitable()) {
                $this->changeBid($this->ppcEntity->getIncreasePercent());
            }

            // If the entity has been making a loss during the decrease period then decrease the bid by the specified amount
            if($this->isDecreasePeriodLoss()) {
                $this->changeBid(-$this->ppcEntity->getDecreasePercent());
            }
        }
    }
}
?>