<?php
  function getKeywordsFromSite(
      $url,
      $includeLinkedPages,
      $languages,
      $countries
  ) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getKeywordToolClient();

    $languagesXml = "";
    $countriesXml = "";
    // process languages
    if ($languages[0] == "all") {
      $languagesXml = "";
    }
    else {
      foreach($languages as $language) {
        $languagesXml .= "<languages>" . trim($language) . "</languages>";
      }
    }
    // process countries
    if ($countries[0] == "all") {
      $countriesXml = "";
    }
    else {
      foreach($countries as $country) {
        $countriesXml .= "<countries>" . trim($country) . "</countries>";
      }
    }
    if ($includeLinkedPages) {
      $includeLinkedPages = "true";
    }
    else {
      $includeLinkedPages = "false";
    }
    $soapParameters = "<getKeywordsFromSite>
                         <url>" . $url . "</url>
                         <includeLinkedPages>" . $includeLinkedPages . "</includeLinkedPages>" . 
                         $languagesXml.
                         $countriesXml . "
                       </getKeywordsFromSite>";
    // query the google servers for the keywords from a site
    $keywordsFromSite =
        $someSoapClient->call("getKeywordsFromSite", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getKeywordsFromSite()", $soapParameters);
      return false;
    }

    if (isset($keywordsFromSite['getKeywordsFromSiteReturn']['groups'])) {
      if (!isset($keywordsFromSite['getKeywordsFromSiteReturn']['groups'][0])) {
        $saveArray = $keywordsFromSite['getKeywordsFromSiteReturn']['groups'];
        unset($keywordsFromSite['getKeywordsFromSiteReturn']['groups']);
        if (!empty($saveArray)) {
          $keywordsFromSite['getKeywordsFromSiteReturn']['groups'][0] = $saveArray;
        }
      }
    }
    else {
      $keywordsFromSite['getKeywordsFromSiteReturn']['groups'] = array();
    }

    if (isset($keywordsFromSite['getKeywordsFromSiteReturn']['keywords'])) {
      if (!isset($keywordsFromSite['getKeywordsFromSiteReturn']['keywords'][0])) {
        $saveArray = $keywordsFromSite['getKeywordsFromSiteReturn']['keywords'];
        unset($keywordsFromSite['getKeywordsFromSiteReturn']['keywords']);
        if (!empty($saveArray)) {
          $keywordsFromSite['getKeywordsFromSiteReturn']['keywords'][0] = $saveArray;
        }
      }
    }
    else {
      $keywordsFromSite['getKeywordsFromSiteReturn']['keywords'] = array();
    }
    return $keywordsFromSite['getKeywordsFromSiteReturn'];
  }

  function getKeywordVariations($seedKeywords, $useSynonyms, $languages, $countries) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getKeywordToolClient();

    $languagesXml = "";
    $countriesXml = "";
    $seedKeywordsXml = "";
    // process seed keywords
    foreach($seedKeywords as $seedKeyword) {
      if ($seedKeyword['isNegative']) {
        $seedKeyword['isNegative'] = "true";
      }
      else {
        $seedKeyword['isNegative'] = "false";
      }
      $seedKeywordsXml .= "<seedKeywords>
                             <negative>" . trim($seedKeyword['isNegative']) . "</negative>
                             <text>" . trim($seedKeyword['text']) . "</text>
                             <type>" . trim($seedKeyword['type']) . "</type>
                           </seedKeywords>";
    }
    // process languages
    if ($languages[0] == "all") {
      $languagesXml = "";
    }
    else {
      foreach($languages as $language) {
        $languagesXml .= "<languages>" . trim($language) . "</languages>";
      }
    }
    // process countries
    if ($countries[0] == "all") {
      $countriesXml = "";
    }
    else {
      foreach($countries as $country) {
        $countriesXml .= "<countries>" . trim($country) . "</countries>";
      }
    }
    // make sure boolean gets transferred to string correctly
    if ($useSynonyms) $useSynonyms = "true"; else $useSynonyms = "false";

    $soapParameters = "<getKeywordVariations>" . 
                         $seedKeywordsXml . "
                         <useSynonyms>" . $useSynonyms . "</useSynonyms>" . 
                         $languagesXml.
                         $countriesXml . "
                       </getKeywordVariations>";
    $keywordVariations =
        $someSoapClient->call("getKeywordVariations", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getKeywordVariations()", $soapParameters);
      return false;
    }

    if (isset($keywordVariations['getKeywordVariationsReturn']['additionalToConsider'])) {
      if (!isset($keywordVariations['getKeywordVariationsReturn']['additionalToConsider'][0])) {
        $saveArray = $keywordVariations['getKeywordVariationsReturn']['additionalToConsider'];
        unset($keywordVariations['getKeywordVariationsReturn']['additionalToConsider']);
        if (!empty($saveArray)) {
          $keywordVariations['getKeywordVariationsReturn']['additionalToConsider'][0] = $saveArray;
        }
      }
    }
    else {
      $keywordVariations['getKeywordVariationsReturn']['additionalToConsider'] = array();
    }

    if (isset($keywordVariations['getKeywordVariationsReturn']['moreSpecific'])) {
      if (!isset($keywordVariations['getKeywordVariationsReturn']['moreSpecific'][0])) {
        $saveArray = $keywordVariations['getKeywordVariationsReturn']['moreSpecific'];
        unset($keywordVariations['getKeywordVariationsReturn']['moreSpecific']);
        if (!empty($saveArray)) {
          $keywordVariations['getKeywordVariationsReturn']['moreSpecific'][0] = $saveArray;
        }
      }
    }
    else {
      $keywordVariations['getKeywordVariationsReturn']['moreSpecific'] = array();
    }
    return $keywordVariations['getKeywordVariationsReturn'];
  }
?>