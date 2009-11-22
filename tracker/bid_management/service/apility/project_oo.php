<?php header('Content-Type: text/html; Charset=utf-8'); ?>
<?php
  // include the APIlity library
  include('apility.php');
  
  // check whether APIlity can be included safely without leaving traces
  if (session_start()) {
    echo ("Test Session started: " . session_id() . "<br>\n");
  }
  else {
    echo ("Test Session could not be started<br>\n");
  }  
  
  // create an APIlity user based on the information in the authentication.ini file
  // XOR use data from authentication.ini
  $apilityUser = new APIlityUser();

  // XOR use data from authentication.ini
  // $apilityManager = new APIlityManager();
  
  // XOR directly provide credentials
  // $apilityUser = new APIlityUser('login@email.tld', 'p4ssw0rD', 'client@email.tld', 'dev3lOperT0ken', '4pplic4ti0Ntok3n');
  
  // XOR directly provide credentials
  // $apilityManager = new APIlityManager('login@email.tld', 'p4ssw0rD', 'dev3lOperT0ken', '4pplic4ti0Ntok3n');

  // in case of sandbox usage, make sure the clients get created
  $apilityUser->getManagersClientAccounts();      
?>

<html>
<body>
 <h3>Sample use of APIlity</h3>
 <small>Using AdWords API <?php echo API_VERSION; ?></small><br />&nbsp;<br />
<?php
  // get all campaigns of the client
  $allCampaigns = $apilityUser->getActiveCampaigns();  
  if (sizeOf($allCampaigns)==0) exit("<p>No Campaigns Found.<p>");

  // use only the first one for this demo
  $campaign = $allCampaigns[0];

  // get all adgroups of the client
  $adGroups = $campaign->getActiveAdGroups();
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

 <b>Create an APIlity user</b>
 <pre><?php echo removeTags(highlight_string('<?php $apilityUser = new APIlityUser(); ?>', true)); ?></pre>   
 <b>Retrieve all Campaigns</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $allCampaigns = $apilityUser->getActiveCampaigns(); ?>', true)); ?></pre>
 <b>For this demo we only want the first Campaign</b><br /> 
 <pre><?php echo removeTags(highlight_string('<?php $campaign = $allCampaigns[0]; ?>', true)); ?></pre>
 <h4>Some properties of the Campaign Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getName(); ?>', true)); ?></pre></td><td><?php echo $campaign->getName(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getId(); ?>', true)); ?></pre></td><td><?php echo $campaign->getId(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getBudgetAmount(); ?>', true)); ?></pre></td><td><?php echo $campaign->getBudgetAmount(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getStartDate(); ?>', true)); ?></pre></td><td><?php echo $campaign->getStartDate(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getEndDate(); ?>', true)); ?></pre></td><td><?php echo $campaign->getEndDate(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $campaign->getLanguages(); ?>', true)); ?></pre></td><td><pre><?php print_r ($campaign->getLanguages()); ?></pre></td></tr>
 </table>
 <br />
 <b>Retrieve all AdGroups</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $adGroups = $campaign->getActiveAdGroups(); ?>', true)); ?></pre><br />
 <b>For this demo we only want the first AdGroup</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $adGroup = $adGroups[0]; ?>', true)); ?></pre>
 <h4>Some properties of the AdGroup Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->getName(); ?>', true)); ?></pre></td><td><?php echo $adGroup->getName(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->getId(); ?>', true)); ?></pre></td><td><?php echo $adGroup->getId();?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->getBelongsToCampaignId(); ?>', true)); ?></pre></td><td><?php echo $adGroup->getBelongsToCampaignId(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->getKeywordMaxCpc(); ?>', true)); ?></pre></td><td><?php echo $adGroup->getKeywordMaxCpc(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->getStatus(); ?>', true)); ?></pre></td><td><?php echo $adGroup->getStatus(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $adGroup->toXml(); ?>', true)); ?></pre></td><td><?php echo ("<pre><small>".htmlspecialchars($adGroup->toXml())."</small></pre>"); ?></td></tr>
 </table>
  <br />
 <b>Retrieve all Creatives</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $creatives = $adGroup->getAllAds(); ?>', true)); ?></pre><br />
 <b>For this demo we only want the first Creative</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $creative = $creatives[0]; ?>', true)); ?></pre></br>
 <h4>Some properties of the Creative Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getHeadline(); ?>', true)); ?></pre></td><td><?php echo $creative->getHeadline(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getDestinationUrl(); ?>', true)); ?></pre></td><td><?php echo $creative->getDestinationUrl(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getDisplayUrl(); ?>', true)); ?></pre></td><td><?php echo $creative->getDisplayUrl(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getId(); ?>', true)); ?></pre></td><td><?php echo $creative->getId(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getBelongsToAdGroupId(); ?>', true)); ?></pre></td><td><?php echo $creative->getBelongsToAdGroupId() ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getStatus(); ?>', true)); ?></pre></td><td><?php echo $creative->getStatus(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $creative->getIsDisapproved(); ?>', true)); ?></pre></td><td><?php echo $creative->getIsDisapproved(); ?></td></tr>
 </table>
 <br />
 <b>Retrieve all Criteria</b><br />
 <pre><?php echo removeTags(highlight_string('<?php $criteria = $adGroup->getAllCriteria(); ?>', true)); ?></pre><br />
 <b>We are going to loop through our $criteria array</b><br />
 <pre><?php echo removeTags(highlight_string('<?php foreach($criteria as $criterion){...} ?>', true)); ?></pre>

 <?php
  for($i=0; $i<5; $i++) {
   if (sizeof($criteria)-1>=$i) $criterion = $criteria[$i]; else break;
 ?>

 <h4>Some Properties of the current Criterion Object</h4>
 <table border="1">
  <tr><th>Method Call</th><th>Return Value</th></tr>
  <tr>
    <td><pre><?php if (strcasecmp($criterion->getCriterionType(), "Keyword") == 0) echo removeTags(highlight_string('<?php $criterion->getText(); ?>', true)); else echo removeTags(highlight_string('<?php $criterion->getUrl(); ?>', true)); ?></pre></td>
    <td><?php if (strcasecmp($criterion->getCriterionType(), "Keyword") == 0) echo $criterion->getText(); else echo $criterion->getUrl(); ?></td>    
  </tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $criterion->getCriterionType(); ?>', true)); ?></pre></td><td><?php echo $criterion->getCriterionType(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $criterion->getId(); ?>', true)); ?></pre></td><td><?php echo $criterion->getId(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $criterion->getBelongsToAdGroupId(); ?>', true)); ?></pre></td><td><?php echo $criterion->getBelongsToAdGroupId(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $criterion->getStatus(); ?>', true)); ?></pre></td><td><?php echo $criterion->getStatus(); ?></td></tr>
  <tr><td><pre><?php echo removeTags(highlight_string('<?php $criterion->getIsNegative(); ?>', true)); ?></pre></td><td><?php echo $criterion->getIsNegative(); ?></td></tr>
 </table>
 <br />

 <?php } ?>
 <?php
  // view some quota details
  echo "<br /><b>Overall Consumed Units:</b> " . $apilityUser->getOverallConsumedUnits()."<br />";
  echo "<br /><b>Overall Performed Operations: </b>" . $apilityUser->getOverallPerformedOperations()."<br />";
  echo "<br /><b>The Response Times of the Last SOAP Requests</b> (Max. ".N_LAST_RESPONSE_TIMES.", Oldest to Youngest):<br />";
  foreach($apilityUser->getLastResponseTimes() as $lastResponseTime) {
    echo "&nbsp;&nbsp;".$lastResponseTime;
  }
  
  function removeTags($code) {
  	return str_replace('&lt;?php&nbsp;', '', str_replace('<span style="color: #0000BB">php', '<span style="color: #0000BB">', str_replace('<span style="color: #0000BB">?&gt;</span>', '', str_replace('<span style="color: #007700">&lt;?</span>', '', $code))));
  }
  
 ?>
</body>
</html>