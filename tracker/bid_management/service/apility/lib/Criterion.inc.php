<?php
  /*
    GENERIC CLASS FUNCTIONS FOR BOTH KEYWORD AND WEBSITE CRITERIONS
  */

  // add keyword criterion on google servers and create local object
  function addKeywordCriterion(
      $text,
      $belongsToAdGroupId,
      $type,
      $isNegative,
      $maxCpc,
      $language,
      $destinationUrl,
      $exemptionRequest = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    // populate variables with function arguments

    // make sure bool gets transformed to string correctly
    if ($isNegative) $isNegative = "true"; else $isNegative = "false";
    $exemptionRequestXml = '';
    if ($exemptionRequest) {
      $exemptionRequestXml = '<exemptionRequest>' . $exemptionRequest . '</exemptionRequest>';
    }

    // when budget optimizer is on the maxcpc needs to be omitted
    $maxCpcXml = "";
    if ($maxCpc) {
      $maxCpcXml = "<maxCpc>" . $maxCpc * EXCHANGE_RATE . "</maxCpc>";
    }
    $soapParameters = "<addCriteria>
                         <criteria>
                           <adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                           <criterionType>Keyword</criterionType>
                           <type>" . $type . "</type>
                           <text>" . $text . "</text>
                           <negative>" . $isNegative . "</negative>" . 
                           $maxCpcXml . "
                           <language>" . $language . "</language>
                           <destinationUrl>" . $destinationUrl . "</destinationUrl>" . 
                           $exemptionRequestXml . "
                         </criteria>
                       </addCriteria>";
    // add criterion to the google servers
    $someCriterion = $someSoapClient->call("addCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addKeywordCriterion()", $soapParameters);
      return false;
    }
    return receiveCriterion($someCriterion['addCriteriaReturn']);
  }

  function addKeywordCriterionList($criteria) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<addCriteria>";
    foreach ($criteria as $criterion) {
      // make sure integer is transformed to string correctly
      if ($criterion['isNegative']) {
        $criterion['isNegative'] = "true";
      }
      else {
        $criterion['isNegative'] = "false";
      }
      // think in micros
      // when budget optimizer is on the maxcpc needs to be omitted
      $maxCpcXml = "";
      if ($criterion['maxCpc']) {
        $maxCpcXml = "<maxCpc>" . $criterion['maxCpc'] * EXCHANGE_RATE . "</maxCpc>";
      }
      $soapParameters .= "<criteria>
                            <adGroupId>" . 
                              $criterion['belongsToAdGroupId'] . "
                            </adGroupId>
                            <type>" . $criterion['type'] . "</type>
                            <criterionType>Keyword</criterionType>
                            <text>" . $criterion['text'] . "</text>
                            <negative>" . $criterion['isNegative'] . "</negative>" . 
                            $maxCpcXml . "
                            <language>" . $criterion['language'] . "</language>
                            <destinationUrl>" . $criterion['destinationUrl'] . "</destinationUrl>";
      if (isset($criterion['exemptionRequest'])) {                            
        $soapParameters .= "<exemptionRequest>" . 
                              $criterion['exemptionRequest'] . "
                            </exemptionRequest>";
      }
      $soapParameters .= "</criteria>";
    }
    $soapParameters .= "</addCriteria>";
    // add criteria to the google servers
    $someCriteria = $someSoapClient->call("addCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addKeywordCriterionList()", $soapParameters);
      return false;
    }
    // when we have only one keyword return a (one keyword element) array anyway
    $someCriteria = makeNumericArray($someCriteria);
    // create local objects
    $criterionObjects = array();
    foreach($someCriteria['addCriteriaReturn'] as $someCriterion) {
      $criterionObject = receiveCriterion($someCriterion);
      if (isset($criterionObject)) {
        array_push($criterionObjects, $criterionObject);
      }
    }
    return $criterionObjects;
  }

  // this won't fail completely if only one criterion fails
  // but causes a lot soap overhead
  function addKeywordCriteriaOneByOne($criteria) {
    // this is basically just a wrapper to the addKeywordCriterion function
    $criterionObjects = array();
    foreach ($criteria as $criterion) {
      if (isset($criterion['exemptionRequest'])) {
        // with exemption request
        $criterionObject = addKeywordCriterion(
            $criterion['text'],
            $criterion['belongsToAdGroupId'],
            $criterion['type'],
            $criterion['isNegative'],
            $criterion['maxCpc'],
            $criterion['language'],
            $criterion['destinationUrl'],
            $criterion['exemptionRequest']
        );
      }
      else {
        // without exemption request
        $criterionObject = addKeywordCriterion(
            $criterion['text'],
            $criterion['belongsToAdGroupId'],
            $criterion['type'],
            $criterion['isNegative'],
            $criterion['maxCpc'],
            $criterion['language'],
            $criterion['destinationUrl']
        );
      }
      array_push($criterionObjects, $criterionObject);
    }
    return $criterionObjects;
  }

  function getAllCriteria($adGroupId) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<getAllCriteria>
                         <adGroupId>" . $adGroupId . "</adGroupId>
                       </getAllCriteria>";
    // query the google servers for all criteria
    $allCriteria = array();
    $allCriteria = $someSoapClient->call("getAllCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAllCriteria()", $soapParameters);
      return false;
    }
    // when we have only one criterion in the adgroup return a (one criterion
    // element) array  anyway
    $allCriteria = makeNumericArray($allCriteria);
    $allCriterionObjects = array();
    if (!isset($allCriteria['getAllCriteriaReturn'])) {
      return $allCriterionObjects;
    }
    foreach ($allCriteria['getAllCriteriaReturn'] as $criterion) {
      $criterionObject = receiveCriterion($criterion);
      if (isset($criterionObject)) {
        array_push($allCriterionObjects, $criterionObject);
      }
    }
    return $allCriterionObjects;
  }

  // remove criterion on google servers and delete local object
  function removeCriterion(&$criterionObject) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<removeCriteria>
                          <adGroupId>" . 
                            $criterionObject->getBelongsToAdGroupId() . "
                          </adGroupId>
                          <criterionIds>" . 
                            $criterionObject->getId() . "
                          </criterionIds>
                       </removeCriteria>";
    // talk to the google servers
    $someSoapClient->call("removeCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":removeCriterion()", $soapParameters);
      return false;
    }
    // delete remote calling object
    $criterionObject = @$GLOBALS['criterionObject'];
    unset($criterionObject);
    return true;
  }
  
  // remove criterion on google servers and delete local object
  function removeCriterionList($criterionObjects) {
    if ((!is_array($criterionObjects)) &&
        (!(sizeOf($criterionObjects) > 0))) {
      return false;
    }
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<removeCriteria>
                          <adGroupId>" . 
                            $criterionObjects[0]->getBelongsToAdGroupId() . "
                          </adGroupId>";
    foreach ($criterionObjects as $criterionObject) {                          
      $soapParameters .= "<criterionIds>" . 
                            $criterionObject->getId() . "
                          </criterionIds>";
    }
    $soapParameters .= "</removeCriteria>";
    // talk to the google servers
    $someSoapClient->call("removeCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":removeCriterionList()", $soapParameters);
      return false;
    }
    return true;
  }  

  function createCriterionObject($givenAdGroupId, $givenCriterionId) {
    // this will create a local criterion object that we can play with
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    // prepare soap parameters
    $soapParameters = "<getCriteria>
                         <adGroupId>" . $givenAdGroupId . "</adGroupId>
                         <criterionIds>" . $givenCriterionId . "</criterionIds>
                       </getCriteria>";
    // execute soap call
    $someCriterion = $someSoapClient->call("getCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":createCriterionObject()", $soapParameters);
      return false;
    }
    // invalid ids are silently ignored. this is not what we want so put out a
    // warning and return without doing anything.
    if (empty($someCriterion)) {
      if (!SILENCE_STEALTH_MODE) {
        trigger_error("<b>APIlity PHP library => Warning: </b>Invalid Criterion ID or AdGroup ID. No Criterion found", E_USER_WARNING);
      }
      return null;
    }
    return receiveCriterion($someCriterion['getCriteriaReturn']);
  }

  // add keyword criterion on google servers and create local object
  function addWebsiteCriterion(
      $url,
      $belongsToAdGroupId,
      $isNegative,
      $maxCpm,
      $maxCpc,
      $destinationUrl
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();

    // thinking in micros here
    $maxCpm = $maxCpm * EXCHANGE_RATE;
    $maxCpc = $maxCpc * EXCHANGE_RATE;
    // make sure bool gets transformed to string correctly
    if ($isNegative) $isNegative = "true"; else $isNegative = "false";

    $soapParameters = "<addCriteria>
                         <criteria>
                           <adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                           <url>" . $url . "</url>
                           <criterionType>Website</criterionType>
                           <negative>" . $isNegative . "</negative>
                           <maxCpm>" . $maxCpm . "</maxCpm>
                           <maxCpc>" . $maxCpc . "</maxCpc>
                           <destinationUrl>" . $destinationUrl . "</destinationUrl>
                         </criteria>
                       </addCriteria>";
    // add criterion to the google servers
    $someCriterion = $someSoapClient->call("addCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addWebsiteCriterion()", $soapParameters);
      return false;
    }
    return receiveCriterion($someCriterion['addCriteriaReturn']);
  }

  function addWebsiteCriteriaOneByOne($criteria) {
    // this is basically just a wrapper to the addWebsiteCriterion function
    $criterionObjects = array();
    foreach ($criteria as $criterion) {
      $criterionObject = addWebsiteCriterion(
          $criterion['url'],
          $criterion['belongsToAdGroupId'],
          $criterion['isNegative'],
          $criterion['maxCpm'],
          $criterion['maxCpc'],
          $criterion['destinationUrl']
      );
      array_push($criterionObjects, $criterionObject);
    }
    return $criterionObjects;
  }

  function addWebsiteCriterionList($criteria) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $criterionObjects = array();
    // prepare soap parameters
    $soapParameters = "<addCriteria>";
    foreach ($criteria as $criterion) {
      // update the google servers
      // thinking in micros here
      $maxCpm = '';
      if (isset($criterion['maxCpm'])) {
        $maxCpm = $criterion['maxCpm'] * EXCHANGE_RATE;
      }
      $maxCpc = '';
      if (isset($criterion['maxCpc'])) {
        $maxCpc = $criterion['maxCpc'] * EXCHANGE_RATE;      
      }
      // make sure bool gets transformed to string correctly
      if ($criterion['isNegative']) {
        $criterion['isNegative'] = "true";
      }
      else {
        $criterion['isNegative'] = "false";
      }
      $soapParameters .= "<criteria>
                            <adGroupId>" . 
                              $criterion['belongsToAdGroupId'] . "
                            </adGroupId>
                            <url>" . $criterion['url'] . "</url>
                            <criterionType>Website</criterionType>
                            <negative>" . $criterion['isNegative'] . "</negative>
                            <maxCpm>" . $maxCpm . "</maxCpm>
                            <maxCpc>" . $maxCpc . "</maxCpc>
                            <destinationUrl>" . $criterion['destinationUrl'] . "</destinationUrl>
                          </criteria>";
    }
    $soapParameters .= "</addCriteria>";
    // add criteria to the google servers
    $someCriteria = $someSoapClient->call("addCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addWebsiteCriterionList()", $soapParameters);
      return false;
    }
    // when we have only one criterion return a (one criterion element) array
    // anyway
    $someCriteria = makeNumericArray($someCriteria);
    // create local objects
    $criterionObjects = array();
    foreach($someCriteria['addCriteriaReturn'] as $someCriterion) {
      $criterionObject = receiveCriterion($someCriterion);
      if (isset($criterionObject)) {
        array_push($criterionObjects, $criterionObject);
      }
    }
    return $criterionObjects;
  }

  function updateCriterionList($criteria) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<updateCriteria>";
    foreach ($criteria as $criterion) {
      $isNegativeXml = "";
      $isPausedXml = "";
      $maxCpcXml = "";
      $maxCpmXml = "";
      $destinationUrlXml = "";
      $languageXml = "";
      // make sure integer is transformed to string correctly
      if (isset($criterion['isNegative'])) {
        if ($criterion['isNegative']) {
          $criterion['isNegative'] = "true";
        }
        else {
          $criterion['isNegative'] = "false";
        }
        $isNegativeXml = "<negative>" . $criterion['isNegative'] . "</negative>";
      }
      // make sure integer is transformed to string correctly
      if (isset($criterion['isPaused'])) {
        if ($criterion['isPaused']) {
          $criterion['isPaused'] = "true";
        }
        else {
          $criterion['isPaused'] = "false";
        }
        $isPausedXml = "<paused>" . $criterion['isPaused'] . "</paused>";
      }
      // think in micros
      if (isset($criterion['maxCpc'])) {
        $maxCpcXml = "<maxCpc>" . $criterion['maxCpc'] * EXCHANGE_RATE . "</maxCpc>";
      }
      if (isset($criterion['maxCpm'])) {
        $maxCpcXml = "<maxCpm>" . $criterion['maxCpm'] * EXCHANGE_RATE . "</maxCpm>";
      }
      if (isset($criterion['destinationUrl'])) {
        $destinationUrlXml =
            "<destinationUrl>" . $criterion['destinationUrl'] . "</destinationUrl>";
      }
      if (isset($criterion['language'])) {
        $languageXml = "<language>" . $criterion['language'] . "</language>";
      }
      $soapParameters .= "<criteria>
                            <id>" . $criterion['id'] . "</id>
                            <adGroupId>" . 
                              $criterion['belongsToAdGroupId'] . "
                            </adGroupId>" . 
                            $isNegativeXml .
                            $isPausedXml .
                            $maxCpcXml .
                            $destinationUrlXml .
                            $languageXml . "
                            <criterionType>" . $criterion['criterionType'] . "</criterionType>
                          </criteria>";
    }
    $soapParameters .= "</updateCriteria>";
    // update the criteria on the google servers
    $someSoapClient->call("updateCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":updateCriterionList()", $soapParameters);
      return false;
    }
    else {
      return true;
    }
  }

  function getCriterionListStats($adGroupId, $criteriaIds, $startDate, $endDate) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $criteriaIdsXml = '';
    foreach ($criteriaIds as $criterionId) {
      $criteriaIdsXml .= '<criterionIds>' . $criterionId . '</criterionIds>';
    }
    $soapParameters = "<getCriterionStats>
                         <adGroupId>" . $adGroupId . "</adGroupId>" .
                         $criteriaIdsXml . "
                         <startDay>" . $startDate . "</startDay>
                         <endDay>" . $endDate . "</endDay>
                       </getCriterionStats>";
    // query the google servers for all criterion stats
    $criterionListStats = $someSoapClient->call('getCriterionStats', $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault)  {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] .':getCriterionListStats()', $soapParameters);
      return false;
    }
    return $criterionListStats['getCriterionStatsReturn'];
  } 

  function getCriteriaList($adGroupId, $criteriaIds) {
    return getCriterionList($adGroupId, $criteriaIds);
  }

  function getCriterionList($adGroupId, $criteriaIds) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $criteriaIdsXml = '';
    foreach($criteriaIds as $criteriaId) {
      $criteriaIdsXml .= '<criterionIds>' . $criteriaId . '</criterionIds>';
    }
    $soapParameters = '<getCriteria>
                         <adGroupId>' . $adGroupId . '</adGroupId>' . 
                         $criteriaIdsXml . '
                       </getCriteria>';
     // query the google servers for all criteria
     $listCriteria = $someSoapClient->call('getCriteria', $soapParameters);
     $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault)  {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ':getCriteriaList()', $soapParameters);
      return false;
    }
    // when we have only one criterion in the adgroup return a (one criterion
    // element) array  anyway
    $listCriteria = makeNumericArray($listCriteria);
    $listCriterionObjects = array();
    if (isset($listCriteria['getCriteriaReturn'])) {
      foreach ($listCriteria['getCriteriaReturn'] as $criterion) {
        $criterionObject = receiveCriterion($criterion);
        if (isset($criterionObject)) {
          array_push($listCriterionObjects, $criterionObject);
        }
      }
    }
    return $listCriterionObjects;
  }

  function checkCriterionList($criteria, $languages, $newGeoTargets) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();

    $soapParameters = "<checkCriteria>";
    foreach ($criteria as $criterion) {
      // make sure integer is transformed to string correctly
      if ($criterion['isNegative']) {
        $criterion['isNegative'] = "true";
      }
      else {
        $criterion['isNegative'] = "false";
      }
      // think in micros
      $soapParameters .= "<criteria>";
      if (isset($criterion['destinationUrl'])) {
        $soapParameters .= "<destinationUrl>" . $criterion['destinationUrl'] . "</destinationUrl>";
      }
      if (isset($criterion['isNegative'])) {
        $soapParameters .= "<negative>" . $criterion['isNegative'] . "</negative>"; 
      }                            
      if (isset($criterion['text'])) {
        $soapParameters .= "<type>" . $criterion['type'] . "</type>
                            <criterionType>Keyword</criterionType>
                            <text>" . $criterion['text'] . "</text>
                            <maxCpc>" . 
                              $criterion['maxCpc'] * EXCHANGE_RATE . "
                            </maxCpc>
                            <language>" . $criterion['language'] . "</language>";
      }
      else if (isset($criterion['url'])) {
        $soapParameters .= "<url>" . $criterion['url'] . "</url>
                            <criterionType>Website</criterionType>
                            <maxCpm>" . 
                              $criterion['maxCpm'] * EXCHANGE_RATE . "
                            </maxCpm>
                            <maxCpc>" . 
                              $criterion['maxCpc'] * EXCHANGE_RATE . "
                            </maxCpc>";
      }
      $soapParameters .= "</criteria>";
    }

    $languagesXml = "<languageTarget>";
    foreach($languages as $language) {
      $languagesXml .= "<languages>" . $language . "</languages>";
    }
    $languagesXml .= "</languageTarget>";
    $soapParameters .= $languagesXml;

    // expecting geoTargets as
    // array(
    //   ['countryTargets']['countries'] => array(),
    //   ['regionTargtes']['regions'] => array(),
    //   ['metroTargets']['metros'] => array(),
    //   ['cityTargets']['cities'] => array()
    //   ['targetAll'] => boolean
    // )
    $newGeoTargetsXml = "";
    $newGeoTargetsXml .= "<countryTargets>";

    if (isset($newGeoTargets['countryTargets']['countries'])) {
      foreach ($newGeoTargets['countryTargets']['countries'] as $country) {
        $newGeoTargetsXml .= "<countries>" . trim($country) . "</countries>";
      }
    }

    $newGeoTargetsXml .= "</countryTargets><regionTargets>";
    if (isset($newGeoTargets['regionTargets']['regions'])) {
      foreach ($newGeoTargets['regionTargets']['regions'] as $region) {
        $newGeoTargetsXml .= "<regions>" . trim($region) . "</regions>";
      }
    }
    $newGeoTargetsXml .= "</regionTargets><metroTargets>";
    if (isset($newGeoTargets['metroTargets']['metros'])) {
      foreach ($newGeoTargets['metroTargets']['metros'] as $metro) {
        $newGeoTargetsXml .= "<metros>" . trim($metro) . "</metros>";
      }
    }
    $newGeoTargetsXml .= "</metroTargets><cityTargets>";
    if (isset($newGeoTargets['cityTargets']['cities'])) {
      foreach ($newGeoTargets['cityTargets']['cities'] as $city) {
        $newGeoTargetsXml .= "<cities>" . trim($city) . "</cities>";
      }
    }
    $newGeoTargetsXml .= "</cityTargets><proximityTargets>";
    if (isset($newGeoTargets['proximityTargets']['circles'])) {
      foreach ($newGeoTargets['proximityTargets']['circles'] as $circle) {
        $newGeoTargetsXml .= "<circles>";
        $newGeoTargetsXml .= "<latitudeMicroDegrees>" . $circle['latitudeMicroDegrees'] . "</latitudeMicroDegrees>";
        $newGeoTargetsXml .= "<longitudeMicroDegrees>" . $circle['longitudeMicroDegrees'] . "</longitudeMicroDegrees>";
        $newGeoTargetsXml .= "<radiusMeters>" . $circle['radiusMeters'] . "</radiusMeters>";
        $newGeoTargetsXml .= "</circles>";
      }
    }
    $newGeoTargetsXml .= "</proximityTargets>";
    if (!empty($newGeoTargets['targetAll'])) {
      $newGeoTargetsXml .= "<targetAll>true</targetAll>";
    }
    $soapParameters .= "<geoTarget>" . $newGeoTargetsXml . "</geoTarget></checkCriteria>";

    // query the google servers
    $criteriaCheck = $someSoapClient->call('checkCriteria', $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault)  {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ':checkCriterionList()', $soapParameters);
      return false;
    }
    return $criteriaCheck;
  }

  function receiveCriterion($someCriterion) {
    // create generic object attributes
    $belongsToAdGroupId = $someCriterion['adGroupId'];
    $criterionType = $someCriterion['criterionType'];
    $destinationUrl = 
        isset($someCriterion['destinationUrl']) ? $someCriterion['destinationUrl'] : '';
    $id = $someCriterion['id'];
    $language = 
        isset($someCriterion['language'])? $someCriterion['language'] : '';
    $isNegative = isset($someCriterion['negative'])? $someCriterion['negative'] : false;
    $isPaused = isset($someCriterion['paused'])? $someCriterion['paused'] : false;
    $status = $someCriterion['status'];

    // if we have a keyword criterion create its object attributes
    if (strcasecmp($someCriterion['criterionType'], "Keyword") == 0) {
      if (isset($someCriterion['maxCpc'])) {
        $maxCpc = $someCriterion['maxCpc'];
      }
      else {
        $maxCpc = null;
      }
      if (isset($someCriterion['proxyMaxCpc'])) {
        $proxyMaxCpc = $someCriterion['proxyMaxCpc'];
      }
      else {
        $proxyMaxCpc = null;
      }
      if (isset($someCriterion['firstPageCpc'])) {
        $firstPageCpc = $someCriterion['firstPageCpc'];
      }
      else {
        $firstPageCpc = null;
      }
      if (isset($someCriterion['qualityScore'])) {
        $qualityScore = $someCriterion['qualityScore'];
      }
      else {
        $qualityScore = null;
      }      
      $type = $someCriterion['type'];
      $text = $someCriterion['text'];
      $criterionObject = new APIlityKeywordCriterion(
          $text,
          $id,
          $belongsToAdGroupId,
          $type,
          $criterionType,
          $isNegative,
          $isPaused,
          ((double) $maxCpc) / EXCHANGE_RATE,
          ((double) $firstPageCpc) / EXCHANGE_RATE,
          ((double) $proxyMaxCpc) / EXCHANGE_RATE,
          $qualityScore,          
          $status,
          $language,
          $destinationUrl
      );
    }
    // else create the website criterion's object attributes
    else {
      if (isset($someCriterion['maxCpm'])) {
        $maxCpm = $someCriterion['maxCpm'];
      }
      else {
        $maxCpm = null;
      }
      if (isset($someCriterion['maxCpc'])) {
        $maxCpc = $someCriterion['maxCpc'];
      }
      else {
        $maxCpc = null;
      }

      $url = $someCriterion['url'];
      $criterionObject = new APIlityWebsiteCriterion(
          $url,
          $id,
          $belongsToAdGroupId,
          $criterionType,
          $isNegative,
          $isPaused,
          ((double) $maxCpm) / EXCHANGE_RATE,
          ((double) $maxCpc) / EXCHANGE_RATE,        
          $status,
          $language,
          $destinationUrl
      );
    }
    return $criterionObject;
  }
?>