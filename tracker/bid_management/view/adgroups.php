<?php
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$dao = new AdgroupDAO();
$adgroups = $dao->loadAll($_REQUEST["campaignId"]);
$campaignDAO = new CampaignDAO();
$campaign = $campaignDAO->load($_REQUEST["campaignId"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Ad Groups</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h2>Ad Groups</h2>
        <h3><?php print "{$campaign->engine} >> {$campaign->name}";?></h3>
        <table>
            <thead>
                <tr>
                    <th>Ad Group</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adgroups as $adgroup) {?>
                <tr>
                    <td><a href="keywords.php?adgroupId=<?php print $adgroup->id;?>"><?php print $adgroup->name;?></a></td>
                    <td><a href="adgroup_bid_rules.php?adgroupId=<?php print $adgroup->id;?>">Edit Rules</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
</html>