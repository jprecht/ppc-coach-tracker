<?php header('Content-Type: text/html; Charset=utf-8'); ?>
<?php
  // include the APIlity library
  include('apility.php');

  // XOR use data from authentication.ini
  $apilityUser = new APIlityUser();
  
  // XOR use data from authentication.ini
  // $apilityManager = new APIlityManager();
  
  // XOR directly provide credentials
  // $apilityUser = new APIlityUser('login@email.tld', 'p4ssw0rD', 'client@email.tld', 'dev3lOperT0ken', '4pplic4ti0Ntok3n');
  
  // XOR directly provide credentials
  // $apilityManager = new APIlityManager('login@email.tld', 'p4ssw0rD', 'dev3lOperT0ken', '4pplic4ti0Ntok3n');
  
  // check whether APIlity can be included safely without leaving traces
  if (session_start()) {
    echo ("Test Session started: " . session_id() . "<br>\n");
  }
  else {
    echo ("Test Session could not be started.<br>\n");
  }  
  // in case of sandbox usage, make sure the clients get created
  getManagersClientAccounts();
?>

<html>
<body>
 <h3>Sample use of APIlity</h3>
 <small>Using AdWords API <?php echo API_VERSION; ?></small><br />&nbsp;<br />
<?php
  // get all campaigns of the client
  $allCampaigns = getAllCampaigns();
  
  if (sizeOf($allCampaigns)==0) exit("<p>No Campaigns Found.<p>");

  // use only the first one for this demo
  $campaign = $allCampaigns[0];

  // get all adgroups of the client
  $adGroups = $campaign->getAllAdGroups();
  if (sizeOf($adGroups)==0) exit("<p>No AdGroups Found.<p>");

  // use only the first one for this demo
  $adGroup = $adGroups[0];

  // get all creatives of the client
  $creatives = $adGroup->getAllAds();
  if (sizeOf($creatives)==0) exit("<p>No Ads Found.<p>");

  // use only the first one for this demo
  $creative = $creatives[0];

  // get all criteria of the client
  $criteria = $adGroup->getAllCriteria();
  if (sizeOf($criteria)==0) exit("<p>No Criteria Found.<p>");
?>

 <b>Retrieve all Campaigns</b><br />
 <pre>$allCampaigns = getAllCampaigns();</pre><br />
 <b>For this demo we only want the first Campaign</b><br />
 <pre>$campaign = $allCampaigns[0];</pre><br />
 <h4>Some properties of the Campaign Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre>$campaign->getName();</pre></td><td><?php echo $campaign->getName(); ?></td></tr>
  <tr><td><pre>$campaign->getId();</pre></td><td><?php echo $campaign->getId(); ?></td></tr>
  <tr><td><pre>$campaign->getBudgetAmount();</pre></td><td><?php echo $campaign->getBudgetAmount(); ?></td></tr>
  <tr><td><pre>$campaign->getStartDate();</pre></td><td><?php echo $campaign->getStartDate(); ?></td></tr>
  <tr><td><pre>$campaign->getEndDate();</pre></td><td><?php echo $campaign->getEndDate(); ?></td></tr>
  <tr><td><pre>$campaign->getLanguages();</pre></td><td><pre><?php print_r ($campaign->getLanguages()); ?></pre></td></tr>
 </table>
 <br />
 <b>Retrieve all AdGroups</b><br />
 <pre>$adGroups = $campaign->getAllAdGroups();</pre><br />
 <b>For this demo we only want the first AdGroup</b><br />
 <pre>$adGroup = $adGroups[0];</pre>
 <h4>Some properties of the AdGroup Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre>$adGroup->getName();</pre></td><td><?php echo $adGroup->getName(); ?></td></tr>
  <tr><td><pre>$adGroup->getId();</pre></td><td><?php echo $adGroup->getId();?></td></tr>
  <tr><td><pre>$adGroup->getBelongsToCampaignId();</pre></td><td><?php echo $adGroup->getBelongsToCampaignId(); ?></td></tr>
  <tr><td><pre>$adGroup->getKeywordMaxCpc();</pre></td><td><?php echo $adGroup->getKeywordMaxCpc(); ?></td></tr>
  <tr><td><pre>$adGroup->getStatus();</pre></td><td><?php echo $adGroup->getStatus(); ?></td></tr>
  <tr><td><pre>$adGroup->toXml();</pre></td><td><?php echo ("<pre><small>".htmlspecialchars($adGroup->toXml())."</small></pre>"); ?></td></tr>
 </table>
  <br />
 <b>Retrieve all Creatives</b><br />
 <pre>$creatives = $adGroup->getAllAds();</pre><br />
 <b>For this demo we only want the first Creative</b><br />
 <pre>$creative = $creatives[0];</pre></br>
 <h4>Some properties of the Creative Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre>$creative->getHeadline();</pre></td><td><?php echo $creative->getHeadline(); ?></td></tr>
  <tr><td><pre>$creative->getDestinationUrl();</pre></td><td><?php echo $creative->getDestinationUrl(); ?></td></tr>
  <tr><td><pre>$creative->getDisplayUrl();</pre></td><td><?php echo $creative->getDisplayUrl(); ?></td></tr>
  <tr><td><pre>$creative->getId();</pre></td><td><?php echo $creative->getId(); ?></td></tr>
  <tr><td><pre>$creative->getBelongsToAdGroupId();</pre></td><td><?php echo $creative->getBelongsToAdGroupId() ?></td></tr>
  <tr><td><pre>$creative->getStatus();</pre></td><td><?php echo $creative->getStatus(); ?></td></tr>
  <tr><td><pre>$creative->getIsDisapproved();</pre></td><td><?php echo $creative->getIsDisapproved(); ?></td></tr>
 </table>
 <br />
 <b>Retrieve all Criteria</b><br />
 <pre>$criteria = $adGroup->getAllCriteria();</pre><br />
 <b>We are going to loop through our $criteria array</b><br />
 <pre>foreach($criteria as $criterion){...}</pre>

 <?php
  for($i=0; $i<5; $i++) {
   if (sizeof($criteria)-1>=$i) $criterion = $criteria[$i]; else break;
 ?>

 <h4>Some Properties of the current Criterion Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr>
    <td><pre><?php if (strcasecmp($criterion->getCriterionType(), "Keyword") == 0) echo "\$criterion->getText();"; else echo "\$criterion->getUrl();" ?></pre></td>
    <td><?php if (strcasecmp($criterion->getCriterionType(), "Keyword") == 0) echo $criterion->getText(); else echo $criterion->getUrl(); ?></td>
  </tr>
  <tr><td><pre>$criterion->getCriterionType();</pre></td><td><?php echo $criterion->getCriterionType(); ?></td></tr>
  <tr><td><pre>$criterion->getId();</pre></td><td><?php echo $criterion->getId(); ?></td></tr>
  <tr><td><pre>$criterion->getBelongsToAdGroupId();</pre></td><td><?php echo $criterion->getBelongsToAdGroupId(); ?></td></tr>
  <tr><td><pre>$criterion->getStatus();</pre></td><td><?php echo $criterion->getStatus(); ?></td></tr>
  <tr><td><pre>$criterion->getIsNegative();</pre></td><td><?php echo $criterion->getIsNegative(); ?></td></tr>
 </table>
 <br />

 <?php } ?>
 <?php
  // view some quota details
  $soapClients = &APIlityClients::getClients();
  echo "<br /><b>Overall Consumed Units:</b> ".$soapClients->getOverallConsumedUnits()."<br />";
  echo "<br /><b>Overall Performed Operations: </b>".$soapClients->getOverallPerformedOperations()."<br />";
  echo "<br /><b>The Response Times of the Last SOAP Requests</b> (Max. ".N_LAST_RESPONSE_TIMES.", Oldest to Youngest):<br />";
  foreach($soapClients->getLastResponseTimes() as $lastResponseTime) {
    echo "&nbsp;&nbsp;".$lastResponseTime;
  }
 ?>
</body>
</html>