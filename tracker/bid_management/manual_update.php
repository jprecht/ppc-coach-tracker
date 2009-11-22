<?php
require_once dirname(__FILE__).'/service/BidManagementService.php';
require_once dirname(__FILE__).'/../config.php';

if(isset ($_REQUEST["action"])) {
    $service = new BidManagementService;

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

    if($_REQUEST["action"] == "adwords_download") {
        $service->adwordsDownload($adwordsMaster, $adwordsClient);
    }

    if($_REQUEST["action"] == "yahoo_download") {
        $service->yahooDownload($ysmMaster, $ysmClient);
    }

    if($_REQUEST["action"] == "adwords_upload") {
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $dao = new AdgroupDAO();
        $adgroups = $dao->loadAll(false, true);
        $dao = new CampaignDAO();
        $campaigns = $dao->loadAll(false, true);
        $service->adwordsUpload($adwordsMaster, $adwordsClient, $keywords, $adgroups, $campaigns);
    }

    if($_REQUEST["action"] == "yahoo_upload") {
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $dao = new AdgroupDAO();
        $adgroups = $dao->loadAll(false, true);
        $dao = new CampaignDAO();
        $campaigns = $dao->loadAll(false, true);
        $service->yahooUpload($ysmMaster, $ysmClient, $keywords, $adgroups, $campaigns);
    }

    if($_REQUEST["action"] == "update") {
        $service->calculateUpdates();
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $dao = new AdgroupDAO();
        $adgroups = $dao->loadAll(false, true);
        $dao = new CampaignDAO();
        $campaigns = $dao->loadAll(false, true);
    }
}

?>
<html>
    <head>
        <title>Bid Management Update</title>
    </head>
    <body>
        <h1>Bid Management Update</h1>
        <ul>
            <li><a
                    href="<?php print $_SERVER["PHP_SELF"];?>?action=adwords_download">AdWords
	Bids Download</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=yahoo_download">Yahoo
	Bids Download</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=update">Run
	Update</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=adwords_upload">Upload
	Changes to AdWords</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=yahoo_upload">Upload
	Changes to Yahoo</a></li>
        </ul>
    </body>
    <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "update") {?>
    <h2>Updated Keywords</h2>
    <table border="solid">
        <thead>
            <tr>
                <th>Engine</th>
                <th>Campaign</th>
                <th>Adgroup</th>
                <th>Keyword</th>
                <th>Match Type</th>
                <th>Current Bid</th>
                <th>New Bid</th>
                <th>Current Status</th>
                <th>New Status</th>
            </tr>
        </thead>
        <tbody>
                <?php
                foreach ($keywords as $keyword) {
                    print "<tr>";
                    print "<td>{$keyword->adgroup->campaign->engine}</td>";
                    print "<td>{$keyword->adgroup->campaign->name}</td>";
                    print "<td>{$keyword->adgroup->name}</td>";
                    print "<td>{$keyword->text}</td>";
                    print "<td>{$keyword->matchType}</td>";
                    print "<td>{$keyword->currentBid}</td>";
                    print "<td>{$keyword->newBid}</td>";
                    print "<td>{$keyword->currentStatus}</td>";
                    print "<td>{$keyword->newStatus}</td>";
                    print "</tr>";
                }
                ?>
        </tbody>
    </table>

    <h2>Updated Ad Groups</h2>
    <table border="solid">
        <thead>
            <tr>
                <th>Engine</th>
                <th>Campaign</th>
                <th>Adgroup</th>
                <th>Current Bid</th>
                <th>New Bid</th>
                <th>Current Status</th>
                <th>New Status</th>
            </tr>
        </thead>
        <tbody>
                <?php
                foreach ($adgroups as $adgroup) {
                    print "<tr>";
                    print "<td>{$adgroup->campaign->engine}</td>";
                    print "<td>{$adgroup->campaign->name}</td>";
                    print "<td>{$adgroup->name}</td>";
                    print "<td>{$adgroup->currentBid}</td>";
                    print "<td>{$adgroup->newBid}</td>";
                    print "<td>{$adgroup->currentStatus}</td>";
                    print "<td>{$adgroup->newStatus}</td>";
                    print "</tr>";
                }
                ?>
        </tbody>
    </table>

    <h2>Updated Campaigns</h2>
    <table border="solid">
        <thead>
            <tr>
                <th>Engine</th>
                <th>Campaign</th>
                <th>Current Daily Budget</th>
                <th>New Daily Budget</th>
                <th>Current Status</th>
                <th>New Status</th>
            </tr>
        </thead>
        <tbody>
                <?php
                foreach ($campaigns as $campaign) {
                    print "<tr>";
                    print "<td>{$campaign->engine}</td>";
                    print "<td>{$campaign->name}</td>";
                    print "<td>{$campaign->currentBid}</td>";
                    print "<td>{$campaign->newBid}</td>";
                    print "<td>{$campaign->currentStatus}</td>";
                    print "<td>{$campaign->newStatus}</td>";
                    print "</tr>";
                }
                ?>
        </tbody>
    </table>
    <?php }?>
</html>