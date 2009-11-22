<?php
require_once dirname(__FILE__).'/../database/PPCEntityDAO.php';

$dao = new CampaignDAO();
$campaigns = $dao->loadAll($_REQUEST["engine"]);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Campaigns</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h2>Campaigns</h2>
        <table>
            <thead>
                <tr>
                    <th>Campaign</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $campaign) {?>
                <tr>
                    <td><a href="adgroups.php?campaignId=<?php print $campaign->id;?>"><?php print $campaign->name;?></a></td>
                    <td><a href="campaign_bid_rules.php?campaignId=<?php print $campaign->id;?>">Edit Rules</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
</html>