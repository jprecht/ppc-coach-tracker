<?php
require_once dirname(__FILE__).'/service/URLUpdateService.php';
require_once dirname(__FILE__).'/../config.php';

if(isset ($_REQUEST["action"])) {
    $service = new URLUpdateService();

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

    if($_REQUEST["action"] == "adcenter_download") {
        $service->adcenterDownload($_FILES['adcenterfile']['tmp_name']);
    }

    if($_REQUEST["action"] == "adwords_upload") {
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $service->adwordsUpload($adwordsMaster, $adwordsClient, $keywords);
    }

    if($_REQUEST["action"] == "yahoo_upload") {
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $service->yahooUpload($ysmMaster, $ysmClient, $keywords);
    }

    if($_REQUEST["action"] == "adcenter_upload") {
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
        $filename = "adcenter-".date("Y-m-d_H-i-s").".csv";
        $service->adcenterUpload(dirname(__FILE__)."/adcenter_temp/$filename", $keywords);
        header("Content-type: text/csv");
        header("Content-disposition: attachment; filename=$filename");
        print file_get_contents(dirname(__FILE__)."/adcenter_temp/$filename");
        die;
    }

    if($_REQUEST["action"] == "update") {
        $service->updateUrls();
        $dao = new KeywordDAO();
        $keywords = $dao->loadAll(false, true);
    }
}

?>
<html>
    <head>
        <title>URL Update</title>
    </head>
    <body>
        <h1>URL Update</h1>
        <ul>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=adwords_download">AdWords URL Download</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=yahoo_download">Yahoo URL Download</a></li>            
            <li>
                <form action="<?php print $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="action" value="adcenter_download" />
                    Acenter Editor File:
                    <input type="file" name="adcenterfile"/>
                    <input type="submit" value="Import"/>
                </form>
            </li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=update">Run Update</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=adwords_upload">Upload Changes to AdWords</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=yahoo_upload">Upload Changes to Yahoo</a></li>
            <li><a href="<?php print $_SERVER["PHP_SELF"];?>?action=adcenter_upload">Upload Changes to AdCenter</a></li>
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
                <th>Old URL</th>
                <th>New URL</th>
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
                    print "<td>{$keyword->currentUrl}</td>";
                    print "<td>{$keyword->newUrl}</td>";
                    print "</tr>";
                }
                ?>
        </tbody>
    </table>
    <?php }?>
</html>