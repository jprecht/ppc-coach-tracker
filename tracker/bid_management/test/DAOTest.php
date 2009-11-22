<?php
include_once '../database/PPCEntityDAO.php';

$d = new CampaignDAO();

//$c = new Campaign();
//$d->saveCampaigns(array($c));
$c = $d->load(1);
//print_r($c);
//print $c->campaignBidRule->id;
//$r1 = $c->campaignBidRule;

$r1 = new BidRule();
$r1->cost_threshold = 1;
$r1->decrease_days =2;
$r1->decrease_percent = 3;
$r1->increase_days=4;
$r1->increase_percent=5;
$c->campaignBidRule = $r1;

$r2 = new BidRule();
$r2->cost_threshold = 6;
$r2->decrease_days =7;
$r2->decrease_percent = 8;
$r2->increase_days=9;
$r2->increase_percent=10;
$c->adgroupBidRule = $r2;

$r3 = new BidRule();
$r3->cost_threshold = 11;
$r3->decrease_days =12;
$r3->decrease_percent = 13;
$r3->increase_days=14;
$r3->increase_percent=15;
$c->keywordBidRule = $r3;


$d->saveCampaigns(array($c));

?>