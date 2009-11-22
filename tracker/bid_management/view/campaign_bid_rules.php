<?php
require_once dirname(__FILE__).'/../database/BidRuleDAO.php';
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$ruleDAO = new BidRuleDAO();
$campaignDAO = new CampaignDAO();
$campaign = $campaignDAO->load($_REQUEST["campaignId"]);

if($campaign->keywordBidRule == null) {
    $campaign->keywordBidRule = new BidRule();
    $campaign->keywordBidRule->entityType = 3;
    $campaign->keywordBidRule->ruleType = 1;
}

if($campaign->adgroupBidRule == null) {
    $campaign->adgroupBidRule = new BidRule();
    $campaign->adgroupBidRule->entityType = 3;
    $campaign->adgroupBidRule->ruleType = 2;
}

if($campaign->campaignBidRule == null) {
    $campaign->campaignBidRule = new BidRule();
    $campaign->campaignBidRule->entityType = 3;
    $campaign->campaignBidRule->ruleType = 3;
}

if(isset($_REQUEST["keyword_cost_threshold"])) {
    $campaign->keywordBidRule->cost_threshold = strip_tags($_REQUEST["keyword_cost_threshold"]);
    $campaign->keywordBidRule->increase_percent = strip_tags($_REQUEST["keyword_bid_increase_percent"]);
    $campaign->keywordBidRule->increase_days = strip_tags($_REQUEST["keyword_bid_increase_days"]);
    $campaign->keywordBidRule->decrease_percent = strip_tags($_REQUEST["keyword_bid_decrease_percent"]);
    $campaign->keywordBidRule->decrease_days = strip_tags($_REQUEST["keyword_bid_decrease_days"]);
    $campaign->keywordBidRule->apply = $_REQUEST["apply_keyword_rule"] == "on";

    $campaign->adgroupBidRule->cost_threshold = strip_tags($_REQUEST["adgroup_cost_threshold"]);
    $campaign->adgroupBidRule->increase_percent = strip_tags($_REQUEST["adgroup_bid_increase_percent"]);
    $campaign->adgroupBidRule->increase_days = strip_tags($_REQUEST["adgroup_bid_increase_days"]);
    $campaign->adgroupBidRule->decrease_percent = strip_tags($_REQUEST["adgroup_bid_decrease_percent"]);
    $campaign->adgroupBidRule->decrease_days = strip_tags($_REQUEST["adgroup_bid_decrease_days"]);
    $campaign->adgroupBidRule->apply = $_REQUEST["apply_adgroup_rule"] == "on";

    $campaign->campaignBidRule->cost_threshold = strip_tags($_REQUEST["campaign_cost_threshold"]);
    $campaign->campaignBidRule->increase_percent = strip_tags($_REQUEST["campaign_budget_increase_percent"]);
    $campaign->campaignBidRule->increase_days = strip_tags($_REQUEST["campaign_budget_increase_days"]);
    $campaign->campaignBidRule->decrease_percent = strip_tags($_REQUEST["campaign_budget_decrease_percent"]);
    $campaign->campaignBidRule->decrease_days = strip_tags($_REQUEST["campaign_budget_decrease_days"]);
    $campaign->campaignBidRule->apply = $_REQUEST["apply_campaign_rule"] == "on";

    $campaignDAO->saveCampaigns(array($campaign));
    header("Location: campaigns.php?engine={$campaign->engine}");
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title><?php print $campaign->name;?> Bid Management Rules</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1><?php print $campaign->name;?> Bid Management Rules</h1>
        <h3><?php print "{$campaign->engine} >> {$campaign->name}";?></h3>
        <form action="<?php print $_SERVER['PHP_SELF'];?>?campaignId=<?php print $campaign->id;?>" method="POST">
            <h2><input type="checkbox" id="apply_keyword_rule" name="apply_keyword_rule" <?php if($campaign->keywordBidRule->apply) {print "checked='checked'";}?> />Search Network Settings</h2>
            <ul>
                <li>
                    Pause keywords that aren't profitable and total costs exceed
                    $<input type="text" name="keyword_cost_threshold" value="<?php print $campaign->keywordBidRule->cost_threshold;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />
                </li>
                <li>
                    Increase keyword bids by
                    <input type="text" name="keyword_bid_increase_percent" value="<?php print $campaign->keywordBidRule->increase_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they're profitable and have been for the past
                    <input type="text" name="keyword_bid_increase_days" value="<?php print $campaign->keywordBidRule->increase_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease keyword bids by
                    <input type="text" name="keyword_bid_decrease_percent" value="<?php print $campaign->keywordBidRule->decrease_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they have been loosing for the past
                    <input type="text" name="keyword_bid_decrease_days" value="<?php print $campaign->keywordBidRule->decrease_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
            </ul>


            <h2><input type="checkbox" id="apply_adgroup_rule" name="apply_adgroup_rule" <?php if($campaign->adgroupBidRule->apply) {print "checked='checked'";}?>/>Content Network Settings</h2>
            <ul>
                <li>
                    Pause Ad Groups that are not profitable and costs exceed
                    $<input type="text" name="adgroup_cost_threshold" value="<?php print $campaign->adgroupBidRule->cost_threshold;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />
                </li>
                <li>
                    Increase Ad Group bids by
                    <input type="text" name="adgroup_bid_increase_percent" value="<?php print $campaign->adgroupBidRule->increase_percent;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />%
                    if they're profitable and have been for the past
                    <input type="text" name="adgroup_bid_increase_days" value="<?php print $campaign->adgroupBidRule->increase_days;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease Ad Group bids by
                    <input type="text" name="adgroup_bid_decrease_percent" value="<?php print $campaign->adgroupBidRule->decrease_percent;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />%
                    if they have been loosing for the past
                    <input type="text" name="adgroup_bid_decrease_days" value="<?php print $campaign->adgroupBidRule->decrease_days;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" /> days
                </li>
            </ul>

            <h2><input type="checkbox" id="apply_campaign_rule" name="apply_campaign_rule" <?php if($campaign->campaignBidRule->apply) {print "checked='checked'";}?>/>Campaign Budget Settings</h2>
            <ul>
                <li>
                    Pause Campaigns that are not profitable and costs exceed
                    $<input type="text" name="campaign_cost_threshold" value="<?php print $campaign->campaignBidRule->cost_threshold;?>" onchange="document.getElementById('apply_campaign_rule').checked='checked'" />
                </li>
                <li>
                    Increase Campaign budgets by
                    <input type="text" name="campaign_budget_increase_percent" value="<?php print $campaign->campaignBidRule->increase_percent;?>" onchange="document.getElementById('apply_campaign_rule').checked='checked'" />%
                    if they have been gaining money for the past
                    <input type="text" name="campaign_budget_increase_days" value="<?php print $campaign->campaignBidRule->increase_days;?>" onchange="document.getElementById('apply_campaign_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease Campaign budgets by
                    <input type="text" name="campaign_budget_decrease_percent" value="<?php print $campaign->campaignBidRule->decrease_percent;?>" onchange="document.getElementById('apply_campaign_rule').checked='checked'" />%
                    if they have been loosing money for the past
                    <input type="text" name="campaign_budget_decrease_days" value="<?php print $campaign->campaignBidRule->decrease_days;?>" onchange="document.getElementById('apply_campaign_rule').checked='checked'" /> days
                </li>
            </ul>
            <input type="submit" value="Update Rules"/>
        </form>
    </body>
</html>