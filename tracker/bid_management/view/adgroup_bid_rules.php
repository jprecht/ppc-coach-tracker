<?php
require_once dirname(__FILE__).'/../database/BidRuleDAO.php';
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$ruleDAO = new BidRuleDAO();
$adgroupDAO = new AdgroupDAO();
$adgroup = $adgroupDAO->load($_REQUEST["adgroupId"]);

if($adgroup->keywordBidRule == null) {
    $adgroup->keywordBidRule = new BidRule();
    $adgroup->keywordBidRule->entityType = 2;
    $adgroup->keywordBidRule->ruleType = 1;
}

if($adgroup->adgroupBidRule == null) {
    $adgroup->adgroupBidRule = new BidRule();
    $adgroup->adgroupBidRule->entityType = 2;
    $adgroup->adgroupBidRule->ruleType = 2;
}

if(isset($_REQUEST["keyword_cost_threshold"])) {
    $adgroup->keywordBidRule->cost_threshold = strip_tags($_REQUEST["keyword_cost_threshold"]);
    $adgroup->keywordBidRule->increase_percent = strip_tags($_REQUEST["keyword_bid_increase_percent"]);
    $adgroup->keywordBidRule->increase_days = strip_tags($_REQUEST["keyword_bid_increase_days"]);
    $adgroup->keywordBidRule->decrease_percent = strip_tags($_REQUEST["keyword_bid_decrease_percent"]);
    $adgroup->keywordBidRule->decrease_days = strip_tags($_REQUEST["keyword_bid_decrease_days"]);
    $adgroup->keywordBidRule->apply = $_REQUEST["apply_keyword_rule"] == "on";

    $adgroup->adgroupBidRule->cost_threshold = strip_tags($_REQUEST["adgroup_cost_threshold"]);
    $adgroup->adgroupBidRule->increase_percent = strip_tags($_REQUEST["adgroup_bid_increase_percent"]);
    $adgroup->adgroupBidRule->increase_days = strip_tags($_REQUEST["adgroup_bid_increase_days"]);
    $adgroup->adgroupBidRule->decrease_percent = strip_tags($_REQUEST["adgroup_bid_decrease_percent"]);
    $adgroup->adgroupBidRule->decrease_days = strip_tags($_REQUEST["adgroup_bid_decrease_days"]);
    $adgroup->adgroupBidRule->apply = $_REQUEST["apply_adgroup_rule"] == "on";

    $adgroupDAO->saveAdgroups(array($adgroup));
    header("Location: adgroups.php?campaignId={$adgroup->campaign->id}");
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title><?php print $adgroup->name;?> Bid Management Rules</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1><?php print $adgroup->name;?> Bid Management Rules</h1>
        <h3><?php print "{$adgroup->campaign->engine} >> {$adgroup->campaign->name} >> {$adgroup->name}";?></h3>
        <form action="<?php print $_SERVER['PHP_SELF'];?>?adgroupId=<?php print $adgroup->id;?>" method="POST">
            <h2><input type="checkbox" id="apply_keyword_rule" name="apply_keyword_rule" <?php if($adgroup->keywordBidRule->apply) {print "checked='checked'";}?> />Search Network Settings</h2>
            <ul>
                <li>
                    Pause keywords that aren't profitable and total costs exceed
                    $<input type="text" name="keyword_cost_threshold" value="<?php print $adgroup->keywordBidRule->cost_threshold;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />
                </li>
                <li>
                    Increase keyword bids by
                    <input type="text" name="keyword_bid_increase_percent" value="<?php print $adgroup->keywordBidRule->increase_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they're profitable and have been for the past
                    <input type="text" name="keyword_bid_increase_days" value="<?php print $adgroup->keywordBidRule->increase_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease keyword bids by
                    <input type="text" name="keyword_bid_decrease_percent" value="<?php print $adgroup->keywordBidRule->decrease_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they have been loosing for the past
                    <input type="text" name="keyword_bid_decrease_days" value="<?php print $adgroup->keywordBidRule->decrease_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
            </ul>


            <h2><input type="checkbox" id="apply_adgroup_rule" name="apply_adgroup_rule" <?php if($adgroup->adgroupBidRule->apply) {print "checked='checked'";}?>/>Content Network Settings</h2>
            <ul>
                <li>
                    Pause Ad Groups that are not profitable and costs exceed
                    $<input type="text" name="adgroup_cost_threshold" value="<?php print $adgroup->adgroupBidRule->cost_threshold;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />
                </li>
                <li>
                    Increase Ad Group bids by
                    <input type="text" name="adgroup_bid_increase_percent" value="<?php print $adgroup->adgroupBidRule->increase_percent;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />%
                    if they're profitable and have been for the past
                    <input type="text" name="adgroup_bid_increase_days" value="<?php print $adgroup->adgroupBidRule->increase_days;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease Ad Group bids by
                    <input type="text" name="adgroup_bid_decrease_percent" value="<?php print $adgroup->adgroupBidRule->decrease_percent;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" />%
                    if they have been loosing for the past
                    <input type="text" name="adgroup_bid_decrease_days" value="<?php print $adgroup->adgroupBidRule->decrease_days;?>" onchange="document.getElementById('apply_adgroup_rule').checked='checked'" /> days
                </li>
            </ul>
            <input type="submit" value="Update Rules"/>
        </form>
    </body>
</html>