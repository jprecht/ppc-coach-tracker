<?php
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$dao = new KeywordDAO();
$keywords = $dao->loadAll($_REQUEST["adgroupId"]);
$adgroupDAO = new AdgroupDAO();
$adgroup = $adgroupDAO->load($_REQUEST["adgroupId"]);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Keywords</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h2>Keywords</h2>
        <h3><?php print "{$adgroup->campaign->engine} >> {$adgroup->campaign->name} >> {$adgroup->name}";?></h3>
        <table>
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Match Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keywords as $keyword) {?>
                <tr>
                    <td><?php print $keyword->text;?></td>
                    <td><?php print $keyword->matchType;?></td>
                    <td><a href="keyword_bid_rules.php?keywordId=<?php print $keyword->id;?>">Edit Rules</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
</html>