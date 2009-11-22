<?php
require_once dirname(__FILE__).'/service/BidManagementService.php';
require_once dirname(__FILE__).'/../config.php';

$adwordsMaster = new AdwordsMasterAccount;
$adwordsMaster->user = ADWORDS_MCC_ACCOUNT;
$adwordsMaster->password = ADWORDS_MCC_PASSWORD;
$adwordsMaster->developerToken = ADWORDS_DEVELOPER_TOKEN;
$adwordsMaster->applicationToken = ADWORDS_APPLICATION_TOKEN;
$adwordsClient = ADWORDS_CLIENT_ACCOUNT;

$ysmMaster = new YSMMasterAccount;
$ysmMaster->user = YAHOO_MASTER_USER;
$ysmMaster->password = YAHOO_MASTER_PASSWORD;
$ysmMaster->license = YAHOO_LICENSE;

$ysmClient = new YSMAccount;
$ysmClient->user = YAHOO_USER;
$ysmClient->password = YAHOO_PASSWORD;
$ysmClient->masterAccountId = YAHOO_MASTER_ACCOUNT_ID;
$ysmClient->accountId = YAHOO_ACCOUNT_ID;

$service = new BidManagementService;
$service->update($adwordsMaster, $adwordsClient, $ysmMaster, $ysmClient);
?>