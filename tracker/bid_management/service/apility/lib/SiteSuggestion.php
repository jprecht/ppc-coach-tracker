<?php
  function getSitesByCategoryName($categoryName, $targeting) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getSiteSuggestionClient();
    $soapParameters = "<getSitesByCategoryName>
                         <categoryName>" . $categoryName . "</categoryName>" . 
                         getTargetingXml($targeting) . "
                       </getSitesByCategoryName>";
    // talk to the google server
    $siteSuggestion =
        $someSoapClient->call("getSitesByCategoryName", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getSitesByCategoryName()", $soapParameters);
      return false;
    }
    return $siteSuggestion['getSitesByCategoryNameReturn'];
  }

  function getSitesByDemographics($demographics, $targeting) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getSiteSuggestionClient();
    $soapParameters = "<getSitesByDemographics>
                         <demo>
                           <childrenTarget>" . $demographics['childrenTarget'] . "</childrenTarget>
                           <ethnicityTarget>" . $demographics['ethnicityTarget'] . "</ethnicityTarget>
                           <genderTarget>" . $demographics['genderTarget'] . "</genderTarget>
                           <minAgeRange>" . $demographics['minAgeRange'] . "</minAgeRange>
                           <maxAgeRange>" . $demographics['maxAgeRange'] . "</maxAgeRange>
                           <minHouseholdIncomeRange>" . $demographics['minHouseholdIncomeRange'] . "</minHouseholdIncomeRange>
                           <maxHouseholdIncomeRange>" . $demographics['maxHouseholdIncomeRange'] . "</maxHouseholdIncomeRange>
                         </demo>" . 
                         getTargetingXml($targeting) . "
                       </getSitesByDemographics>";
    // talk to the google server
    $siteSuggestion =
        $someSoapClient->call("getSitesByDemographics", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getSitesByDemographics()", $soapParameters);
      return false;
    }
    return $siteSuggestion['getSitesByDemographicsReturn'];
  }

  function getSitesByTopics($topics, $targeting) {
    $soapClients = &APIlityClients::getClients();
    $topicsXml = '';
    foreach ($topics as $topic) {
      $topicsXml .= "<topics>" . trim($topic) . "</topics>";
    }
    $someSoapClient = $soapClients->getSiteSuggestionClient();
    $soapParameters = "<getSitesByTopics>" . 
                         $topicsXml.
                         getTargetingXml($targeting) . "
                       </getSitesByTopics>";
    // talk to the google server
    $siteSuggestion =
        $someSoapClient->call("getSitesByTopics", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getSitesByTopics()", $soapParameters);
      return false;
    }
    return $siteSuggestion['getSitesByTopicsReturn'];
  }

  function getSitesByUrls($urls, $targeting) {
    $soapClients = &APIlityClients::getClients();
    $urlsXml = '';
    foreach ($urls as $url) {
      $urlsXml .= "<urls>" . trim($url) . "</urls>";
    }
    $someSoapClient = $soapClients->getSiteSuggestionClient();
    $soapParameters = "<getSitesByUrls>" . 
                         $urlsXml.
                         getTargetingXml($targeting) . "
                       </getSitesByUrls>";
    // talk to the google server
    $siteSuggestion =
        $someSoapClient->call("getSitesByUrls", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getSitesByUrls()", $soapParameters);
      return false;
    }
    return $siteSuggestion['getSitesByUrlsReturn'];
  }

  function getTargetingXml($targeting) {
    $targetingXml = '';
    foreach($targeting['countries'] as $country) {
      $targetingXml .= "<countries>" . trim($country) . "</countries>";
    }
    foreach($targeting['regions'] as $region) {
      $targetingXml .= "<regions>" . trim($region) . "</regions>";
    }
    foreach($targeting['metros'] as $metro) {
      $targetingXml .= "<metros>" . trim($metro) . "</metros>";
    }
    foreach($targeting['languages'] as $language) {
      $targetingXml .= "<languages>" . trim($language) . "</languages>";
    }
    return "<targeting>" . $targetingXml . "</targeting>";
  }
?>