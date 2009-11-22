<?php
require_once dirname(__FILE__).'/../database/BidRuleDAO.php';
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$ruleDAO = new BidRuleDAO();
$keywordDAO = new KeywordDAO();
$keyword = $keywordDAO->load($_REQUEST["keywordId"]);

if($keyword->keywordBidRule == null) {
    $keyword->keywordBidRule = new BidRule();
    $keyword->keywordBidRule->entityType = 1;
    $keyword->keywordBidRule->ruleType = 1;
}

if(isset($_REQUEST["keyword_cost_threshold"])) {
    $keyword->keywordBidRule->cost_threshold = strip_tags($_REQUEST["keyword_cost_threshold"]);
    $keyword->keywordBidRule->increase_percent = strip_tags($_REQUEST["keyword_bid_increase_percent"]);
    $keyword->keywordBidRule->increase_days = strip_tags($_REQUEST["keyword_bid_increase_days"]);
    $keyword->keywordBidRule->decrease_percent = strip_tags($_REQUEST["keyword_bid_decrease_percent"]);
    $keyword->keywordBidRule->decrease_days = strip_tags($_REQUEST["keyword_bid_decrease_days"]);
    $keyword->keywordBidRule->apply = $_REQUEST["apply_keyword_rule"] == "on";

    $keywordDAO->saveKeywords(array($keyword));
    header("Location: keywords.php?adgroupId={$keyword->adgroup->id}");
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title><?php print $keyword->name;?> Bid Management Rules</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1><?php print $keyword->text;?> Bid Management Rules</h1>
        <h3><?php print "{$keyword->adgroup->campaign->engine} >> {$keyword->adgroup->campaign->name} >> {$keyword->adgroup->name} >> {$keyword->text}";?></h3>
        <form action="<?php print $_SERVER['PHP_SELF'];?>?keywordId=<?php print $keyword->id;?>" method="POST">
            <h2><input type="checkbox" id="apply_keyword_rule" name="apply_keyword_rule" <?php if($keyword->keywordBidRule->apply) {print "checked='checked'";}?> />Search Network Settings</h2>
            <ul>
                <li>
                    Pause keywords that aren't profitable and total costs exceed
                    $<input type="text" name="keyword_cost_threshold" value="<?php print $keyword->keywordBidRule->cost_threshold;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />
                </li>
                <li>
                    Increase keyword bids by
                    <input type="text" name="keyword_bid_increase_percent" value="<?php print $keyword->keywordBidRule->increase_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they're profitable and have been for the past
                    <input type="text" name="keyword_bid_increase_days" value="<?php print $keyword->keywordBidRule->increase_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
                <li>
                    Decrease keyword bids by
                    <input type="text" name="keyword_bid_decrease_percent" value="<?php print $keyword->keywordBidRule->decrease_percent;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" />%
                    if they have been loosing for the past
                    <input type="text" name="keyword_bid_decrease_days" value="<?php print $keyword->keywordBidRule->decrease_days;?>" onchange="document.getElementById('apply_keyword_rule').checked='checked'" /> days
                </li>
            </ul>
            <input type="submit" value="Update Rules"/>
        </form>
    </body>
</html>