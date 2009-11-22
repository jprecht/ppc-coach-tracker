<?php
class BidRule {
    var $id = 0;
    var $entityId = 0;
    var $entityType = 0; // 1 = Keyword, 2 = Ad Group, 3 = Campaign
    var $ruleType = 0; // 1 = Keyword, 2 = Ad Group, 3 = Campaign
    var $cost_threshold = 0;
    var $increase_percent = 0;
    var $increase_days = 0;
    var $decrease_percent = 0;
    var $decrease_days = 0;
    var $apply = 0;
}
?>
