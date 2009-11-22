<?php
  /*
    GENERIC CLASS FUNCTIONS FOR BOTH IMAGE AND TEXT ADS
  */

  function createAdObject($givenAdGroupId, $givenAdId) {
    // this creates a local ad object
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    // prepare soap parameters
    $soapParameters = "<getAd>
                         <adGroupId>" . $givenAdGroupId . "</adGroupId>
                         <adId>" . $givenAdId . "</adId>
                       </getAd>";
    // execute soap call
    $someAd = $someSoapClient->call("getAd", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":createAdObject()", $soapParameters);
      return false;
    }
    // invalid ids are silently ignored. this is not what we want so put out a
    // warning and return without doing anything.
    if (empty($someAd)) {
      if (!SILENCE_STEALTH_MODE) {
        trigger_error("<b>APIlity PHP library => Warning: </b>Invalid Ad ID. No Ads found", E_USER_WARNING);
      }
      return null;
    }
    return receiveAd($someAd['getAdReturn']);
  }

  // add a ad to the server and create local object
  function addTextAd(
      $belongsToAdGroupId,
      $headline,
      $description1,
      $description2,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                       <headline>" . $headline . "</headline>
                       <description1>" . $description1 . "</description1>
                       <description2>" . $description2 . "</description2>
                       <status>" . $status . "</status>
                       <displayUrl>" . $displayUrl . "</displayUrl>
                       <destinationUrl>" . $destinationUrl . "</destinationUrl>
                       <adType>TextAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";
    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addTextAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  function addTextAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<addAds>";
    foreach ($ads as $ad) {
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <headline>" . $ad['headline'] . "</headline>
                            <description1>" . $ad['description1'] . "</description1>
                            <description2>" . $ad['description2'] . "</description2>
                            <status>" . $ad['status'] . "</status>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <adType>TextAd</adType>";
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {                            
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters .= "</addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addTextAdList()", $soapParameters);
      return false;
    }
    // when we have only one ad return a (one ad element) array  anyway
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addTextAdsOneByOne($ads) {
    // this is just a wrapper to the addTextAdd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        // with exemption request
        $adObject = addTextAd(
            $ad['belongsToAdGroupId'],
            $ad['headline'],
            $ad['description1'],
            $ad['description2'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        // without exemption request
        $adObject = addTextAd(
            $ad['belongsToAdGroupId'],
            $ad['headline'],
            $ad['description1'],
            $ad['description2'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  // add a ad to the server and create local object
  function addMobileAd(
      $belongsToAdGroupId,
      $businessName,
      $countryCode,
      $description,
      $headline,
      $markupLanguages,
      $mobileCarriers,
      $phoneNumber,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    // mobile carriers xml
    $mobileCarriersXml = "";
    foreach($mobileCarriers as $mobileCarrier) {
      $mobileCarriersXml .= "<mobileCarriers>" . $mobileCarrier . "</mobileCarriers>";
    }
    // mark-up languages
    $markupLanguagesXml = "";
    foreach($markupLanguages as $markupLanguage) {
      $markupLanguagesXml .= "<markupLanguages>" . $markupLanguage . "</markupLanguages>";
    }
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                       <businessName>" . $businessName . "</businessName>
                       <countryCode>" . $countryCode . "</countryCode>
                       <description>" . $description . "</description>
                       <headline>" . $headline . "</headline>" . 
                       $markupLanguagesXml.
                       $mobileCarriersXml . "
                       <phoneNumber>" . $phoneNumber . "</phoneNumber>
                       <status>" . $status . "</status>
                       <displayUrl>" . $displayUrl . "</displayUrl>
                       <destinationUrl>" . $destinationUrl . "</destinationUrl>
                       <adType>MobileAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";
    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addMobileAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addMobileAdsOneByOne($ads) {
    // this is just a wrapper to the addMobileAd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $adObject = addMobileAd(
            $ad['belongsToAdGroupId'],
            $ad['businessName'],
            $ad['countryCode'],
            $ad['description'],
            $ad['headline'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['phoneNumber'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        $adObject = addMobileAd(
            $ad['belongsToAdGroupId'],
            $ad['businessName'],
            $ad['countryCode'],
            $ad['description'],
            $ad['headline'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['phoneNumber'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );        
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  function addMobileAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<addAds>";
    foreach ($ads as $ad) {
      // mobile carriers
      $mobileCarriersXml = "";
      foreach($ad['mobileCarriers'] as $mobileCarrier) {
        $mobileCarriersXml .= "<mobileCarriers>" . $mobileCarrier . "</mobileCarriers>";
      }
      // mark-up languages
      $markupLanguagesXml = "";
      foreach($ad['markupLanguages'] as $markupLanguage) {
        $markupLanguagesXml .= "<markupLanguages>" . $markupLanguage . "</markupLanguages>";
      }
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <businessName>" . $ad['businessName'] . "</businessName>
                            <countryCode>" . $ad['countryCode'] . "</countryCode>
                            <description>" . $ad['description'] . "</description>
                            <headline>" . $ad['headline'] . "</headline>" . 
                            $markupLanguagesXml.
                            $mobileCarriersXml . "
                            <phoneNumber>" . $ad['phoneNumber'] . "</phoneNumber>
                            <status>" . $ad['status'] . "</status>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <adType>MobileAd</adType>";
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters .= "</addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addMobileAdList()", $soapParameters);
      return false;
    }
    // when we have only one ad return a (one ad element) array  anyway
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  // add a ad to the server and create local object
  function addMobileImageAd(
      $belongsToAdGroupId,
      $imageLocation,
      $markupLanguages,
      $mobileCarriers,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    // mobile carriers xml
    $mobileCarriersXml = "";
    foreach($mobileCarriers as $mobileCarrier) {
      $mobileCarriersXml .= "<mobileCarriers>" . $mobileCarrier . "</mobileCarriers>";
    }
    // mark-up languages
    $markupLanguagesXml = "";
    foreach($markupLanguages as $markupLanguage) {
      $markupLanguagesXml .= "<markupLanguages>" . $markupLanguage . "</markupLanguages>";
    }
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                       <image>
                         <data xsi:type=\"xsd:base64Binary\">" . img2base64($imageLocation) . "</data>
                         <name>mobile_image_ad</name>
                       </image>" .    
                       $markupLanguagesXml .
                       $mobileCarriersXml . "
                       <status>" . $status . "</status>
                       <displayUrl>" . $displayUrl . "</displayUrl>
                       <destinationUrl>" . $destinationUrl . "</destinationUrl>
                       <adType>MobileImageAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";
    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addMobileImageAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addMobileImageAdsOneByOne($ads) {
    // this is just a wrapper to the addMobileAd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $adObject = addMobileAd(
            $ad['belongsToAdGroupId'],
            $ad['image'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        $adObject = addMobileAd(
            $ad['belongsToAdGroupId'],
            $ad['image'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );        
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  function addMobileImageAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<addAds>";
    foreach ($ads as $ad) {
      // mobile carriers
      $mobileCarriersXml = "";
      foreach($ad['mobileCarriers'] as $mobileCarrier) {
        $mobileCarriersXml .= "<mobileCarriers>" . $mobileCarrier . "</mobileCarriers>";
      }
      // mark-up languages
      $markupLanguagesXml = "";
      foreach($ad['markupLanguages'] as $markupLanguage) {
        $markupLanguagesXml .= "<markupLanguages>" . $markupLanguage . "</markupLanguages>";
      }
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <image>
                              <data xsi:type=\"xsd:base64Binary\">" . img2base64($ad['imageLocation']) . "</data>
                              <name>Mobile Image Ad</name>
                            </image>" .                                
                            $markupLanguagesXml .
                            $mobileCarriersXml . "
                            <status>" . $ad['status'] . "</status>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <adType>MobileImageAd</adType>";
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters .= "</addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addMobileAdList()", $soapParameters);
      return false;
    }
    // when we have only one ad return a (one ad element) array  anyway
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  // add a ad to the server and create local object
  function addLocalBusinessAd(
      $belongsToAdGroupId,
      $address,
      $businessImageLocation,
      $businessKey,
      $businessName,
      $city,
      $countryCode,
      $customIconLocation,
      $customIconId,
      $description1,
      $description2,
      $phoneNumber,
      $postalCode,
      $region,
      $stockIcon,
      $targetRadiusInKm,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    // One of the three fields stockIcon, customIconId, and customIcon can be
    // used to indicate the image used to identify this ad on maps. These three
    // fields are mutually exclusive
    $iconXml = "";
    if ($customIconId) {
      $iconXml .= "<customIconId>" . $customIconId . "</customIconId>";
    }
    else if ($stockIcon) {
      $iconXml .= "<stockIcon>" . $stockIcon . "</stockIcon>";
    }
    else if ($customIconLocation) {
      $iconXml .= "<customIcon>
                     <data xsi:type=\"xsd:base64Binary\">" . img2base64($customIconLocation) . "</data>
                     <name>Custom Icon</name>
                   </customIcon>";
    }
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                       <address>" . $address . "</address>
                       <businessImage>
                         <data xsi:type=\"xsd:base64Binary\">" . img2base64($businessImageLocation) . "</data>
                         <name>Business Image</name>
                       </businessImage>
                       <businessKey>" . $businessKey . "</businessKey>
                       <businessName>" . $businessName . "</businessName>
                       <city>" . $city . "</city>
                       <countryCode>" . $countryCode . "</countryCode>" . 
                       $iconXml . "
                       <description1>" . $description1 . "</description1>
                       <description2>" . $description2 . "</description2>
                       <phoneNumber>" . $phoneNumber . "</phoneNumber>
                       <postalCode>" . $postalCode . "</postalCode>
                       <region>" . $region . "</region>
                       <targetRadiusInKm>" . $targetRadiusInKm . "</targetRadiusInKm>
                       <status>" . $status . "</status>
                       <displayUrl>" . $displayUrl . "</displayUrl>
                       <destinationUrl>" . $destinationUrl . "</destinationUrl>
                       <adType>LocalBusinessAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";

    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addLocalBusinessAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addLocalBusinessAdsOneByOne($ads) {
    // this is just a wrapper to the addMobileAd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $adObject = addLocalBusinessAd(
            $ad['belongsToAdGroupId'],
            $ad['address'],
            $ad['businessImageLocation'],
            $ad['businessKey'],
            $ad['businessName'],
            $ad['city'],
            $ad['countryCode'],
            $ad['customIconLocation'],
            $ad['customIconId'],
            $ad['description1'],
            $ad['description2'],
            $ad['phoneNumber'],
            $ad['postalCode'],
            $ad['region'],
            $ad['stockIcon'],
            $ad['targetRadiusInKm'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        $adObject = addLocalBusinessAd(
            $ad['belongsToAdGroupId'],
            $ad['address'],
            $ad['businessImageLocation'],
            $ad['businessKey'],
            $ad['businessName'],
            $ad['city'],
            $ad['countryCode'],
            $ad['customIconLocation'],
            $ad['customIconId'],
            $ad['description1'],
            $ad['description2'],
            $ad['phoneNumber'],
            $ad['postalCode'],
            $ad['region'],
            $ad['stockIcon'],
            $ad['targetRadiusInKm'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );        
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  function addLocalBusinessAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<addAds>";
    foreach ($ads as $ad) {
      // One of the three fields stockIcon, customIconId, and customIcon can be
      // used to indicate the image used to identify this ad on maps. These three
      // fields are mutually exclusive
      $iconXml = "";
      if ($ad['customIconId']) {
        $iconXml .= "<customIconId>" . $ad['customIconId'] . "</customIconId>";
      }
      else if ($ad['stockIcon']) {
        $iconXml .= "<stockIcon>" . $ad['stockIcon'] . "</stockIcon>";
      }
      else if ($ad['customIconLocation']) {
        $iconXml .= "<customIcon>
                       <data xsi:type=\"xsd:base64Binary\">" . img2base64($ad['customIconLocation']) . "</data>
                       <name>Custom Icon</name>
                     </customIcon>";
      }
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <address>" . $ad['address'] . "</address>
                            <businessImage>
                              <data xsi:type=\"xsd:base64Binary\">" . img2base64($ad['businessImageLocation']) . "</data>
                              <name>Business Image</name>
                            </businessImage>" . 
                            $iconXml . "
                            <businessKey>" . $ad['businessKey'] . "</businessKey>
                            <businessName>" . $ad['businessName'] . "</businessName>
                            <city>" . $ad['city'] . "</city>
                            <countryCode>" . $ad['countryCode'] . "</countryCode>
                            <description1>" . $ad['description1'] . "</description1>
                            <description2>" . $ad['description2'] . "</description2>
                            <phoneNumber>" . $ad['phoneNumber'] . "</phoneNumber>
                            <postalCode>" . $ad['postalCode'] . "</postalCode>
                            <region>" . $ad['region'] . "</region>
                            <targetRadiusInKm>" . $ad['targetRadiusInKm'] . "</targetRadiusInKm>
                            <status>" . $ad['status'] . "</status>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <adType>LocalBusinessAd</adType>";
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {                             
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters .= "</addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addLocalBusinessAdList()", $soapParameters);
      return false;
    }
    // when we have only one ad return a (one ad element) array  anyway
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  function removeAd(&$adObject) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<updateAds>
                         <ads>
                           <adGroupId>" . $adObject->getBelongsToAdGroupId() . "</adGroupId>
                           <id>" . $adObject->getId() . "</id>
                           <status>Disabled</status>
                           <adType>" . $adObject->getAdType() . "</adType>
                          </ads>
                       </updateAds>";
    // delete the ad on the google servers
    $someSoapClient->call("updateAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":removeAd()", $soapParameters);
      return false;
    }
    // set status of the local object to "Disabled"
    $adObject->status =  "Disabled";
    // delete remote calling object
    $adObject = @$GLOBALS['adObject'];
    unset($adObject);
    return true;
  }

  function getAllAds($adGroupIds) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<getAllAds>";
    foreach($adGroupIds as $adGroupId) {
      $soapParameters .= "<adGroupIds>" . $adGroupId . "</adGroupIds>";
    }
    $soapParameters .= "</getAllAds>";
    // query the google servers for all ads
    $allAds = array();
    $allAds = $someSoapClient->call("getAllAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAllAds()", $soapParameters);
      return false;
    }
    // if only one ad then copy and create (one element) array of ads
    $allAds = makeNumericArray($allAds);
    $allAdObjects = array();
    if (!isset($allAds['getAllAdsReturn'])) {
      return $allAdObjects;
    }
    foreach($allAds['getAllAdsReturn'] as $ad) {
      $adObject = receiveAd($ad);
      if (isset($adObject)) {
        array_push($allAdObjects, $adObject);
      }
    }
    return $allAdObjects;
  }

  function getActiveAds($adGroupIds) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<getActiveAds>";
    foreach($adGroupIds as $adGroupId) {
      $soapParameters .= "<adGroupIds>" . $adGroupId . "</adGroupIds>";
    }
    $soapParameters .= "</getActiveAds>";
    // query the google servers for all ads
    $allAds = array();
    $allAds = $someSoapClient->call("getActiveAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getActiveAds()", $soapParameters);
      return false;
    }

    // if only one ad then copy and create (one element) array of ads
    $allAds = makeNumericArray($allAds);
    $allAdObjects = array();
    if (isset($allAds['getActiveAdsReturn'])) foreach($allAds['getActiveAdsReturn'] as $ad) {
      $adObject = receiveAd($ad);
      if (isset($adObject)) {
        array_push($allAdObjects, $adObject);
      }
    }
    return $allAdObjects;
  }

  // add an image ad to the server and create local object
  function addImageAd(
      $belongsToAdGroupId,
      $imageLocation,
      $name,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false, 
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                        <image>
                          <data xsi:type=\"xsd:base64Binary\">" . img2base64($imageLocation) . "</data>
                          <name>" . $name . "</name>
                        </image>
                        <status>" . $status . "</status>
                        <destinationUrl>" . $destinationUrl . "</destinationUrl>
                        <displayUrl>" . $displayUrl . "</displayUrl>
                        <adType>ImageAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";    
    }
    if ($checkOnly) return $soapParameters;
    // add the ad to the google servers
    $soapParameters = "<addAds>
                         <ads>" . 
                            $soapParameters . "
                         </ads>
                       </addAds>";
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addImageAd()", $soapParameters);
      return false;
    }
    // populate object attributes
    return receiveAd($someAd['addAdsReturn']);
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addImageAdsOneByOne($ads) {
    // this is just a wrapper to the addImageAd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $adObject = addImageAd(
            $ad['belongsToAdGroupId'],
            $ad['imageLocation'],
            $ad['name'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        $adObject = addImageAd(
            $ad['belongsToAdGroupId'],
            $ad['imageLocation'],
            $ad['name'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );        
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  function addImageAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "";
    foreach ($ads as $ad) {
      $exemptionRequestXml = '';
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <image>
                              <data xsi:type=\"xsd:base64Binary\">" . img2base64($ad['imageLocation']) . "</data>
                              <name>" . $ad['name'] . "</name>
                            </image>
                            <status>" . $ad['status'] . "</status>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <adType>ImageAd</adType>";
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {                            
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters = "<addAds>" . 
                         $soapParameters . "                        
                       </addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addImageAdList()", $soapParameters);
      return false;
    }
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  // add a commerce ad to the server and create local object
  function addCommerceAd(
      $belongsToAdGroupId,
      $description1,
      $description2,
      $headline,
      $prePriceAnnotation,
      $postPriceAnnotation,
      $priceString,
      $productImageLocation,
      $status,
      $displayUrl,
      $destinationUrl,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                        <description1>" . $description1 . "</description1>
                        <description2>" . $description2 . "</description2>
                        <headline>" . $headline . "</headline>
                        <postPriceAnnotation>" . $postPriceAnnotation . "</postPriceAnnotation>
                        <prePriceAnnotation>" . $prePriceAnnotation . "</prePriceAnnotation>
                        <priceString>" . $priceString . "</priceString>
                        <productImage>
                          <data xsi:type=\"xsd:base64Binary\">" . img2base64($productImageLocation) . "</data>
                          <name>Product Image</name>
                        </productImage>
                        <status>" . $status . "</status>
                        <destinationUrl>" . $destinationUrl . "</destinationUrl>
                        <displayUrl>" . $displayUrl . "</displayUrl>
                        <adType>CommerceAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";    
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";
    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addCommerceAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  // this won't fail completely if only one ad fails but will cause a lot
  // of soap overhead
  function addCommerceAdsOneByOne($ads) {
    // this is just a wrapper to the addImageAd function
    $adObjects = array();
    foreach ($ads as $ad) {
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $adObject = addCommerceAd(
            $ad['belongsToAdGroupId'],
            $ad['description1'],
            $ad['description2'],
            $ad['headline'],
            $ad['postPriceAnnotation'],
            $ad['prePriceAnnotation'],
            $ad['priceString'],
            $ad['productImageLocation'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['exemptionRequest']
        );
      }
      else {
        $adObject = addCommerceAd(
            $ad['belongsToAdGroupId'],
            $ad['description1'],
            $ad['description2'],
            $ad['headline'],
            $ad['postPriceAnnotation'],
            $ad['prePriceAnnotation'],
            $ad['priceString'],
            $ad['productImageLocation'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl']
        );        
      }
      array_push($adObjects, $adObject);
    }
    return $adObjects;
  }

  function addCommerceAdList($ads) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<addAds>";
    foreach ($ads as $ad) {
      $soapParameters .= "<ads>
                            <adGroupId>" . $ad['belongsToAdGroupId'] . "</adGroupId>
                            <description1>" . $ad['description1'] . "</description1>
                            <description2>" . $ad['description2'] . "</description2>
                            <headline>" . $ad['headline'] . "</headline>
                            <postPriceAnnotation>" . $ad['postPriceAnnotation'] . "</postPriceAnnotation>
                            <prePriceAnnotation>" . $ad['prePriceAnnotation'] . "</prePriceAnnotation>
                            <priceString>" . $ad['priceString'] . "</priceString>
                            <productImage>
                              <data xsi:type=\"xsd:base64Binary\">" . img2base64($ad['productImageLocation']) . "</data>
                              <name>Product Image</name>
                            </productImage>
                            <status>" . $ad['status'] . "</status>
                            <destinationUrl>" . $ad['destinationUrl'] . "</destinationUrl>
                            <displayUrl>" . $ad['displayUrl'] . "</displayUrl>
                            <adType>CommerceAd</adType>";      
      if (isset($ad['exemptionRequest']) && $ad['exemptionRequest']) {
        $soapParameters .= "<exemptionRequest>" . $ad['exemptionRequest'] . "</exemptionRequest>";
      }
      $soapParameters .= "</ads>";
    }
    $soapParameters .= "</addAds>";
    // add the ads to the google servers
    $someAds = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addCommerceAdList()", $soapParameters);
      return false;
    }
    $someAds = makeNumericArray($someAds);
    // create local objects
    $adObjects = array();
    foreach($someAds['addAdsReturn'] as $someAd) {
      $adObject = receiveAd($someAd);
      if (isset($adObject)) {
        array_push($adObjects, $adObject);
      }
    }
    return $adObjects;
  }

  function addVideoAd(
      $belongsToAdGroupId,
      $imageLocation,
      $name,
      $video,
      $displayUrl,
      $destinationUrl,
      $status,
      $exemptionRequest = false,
      $checkOnly = false
  ) {
    // update the google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<adGroupId>" . $belongsToAdGroupId . "</adGroupId>
                        <image>
                          <data xsi:type=\"xsd:base64Binary\">" . img2base64($imageLocation) . "</data>
                          <name>Still Image</name>
                        </image>
                        <name>" . $name . "</name>
                        <video>
                          <videoId>" . $video['videoId'] . "</videoId>
                        </video>
                        <status>" . $status . "</status>
                        <destinationUrl>" . $destinationUrl . "</destinationUrl>
                        <displayUrl>" . $displayUrl . "</displayUrl>
                        <adType>VideoAd</adType>";
    if (isset($exemptionRequest) && $exemptionRequest) {                        
      $soapParameters .= "<exemptionRequest>" . $exemptionRequest . "</exemptionRequest>";
    }
    if ($checkOnly) return $soapParameters;
    $soapParameters = "<addAds>
                         <ads>" . 
                           $soapParameters . "
                         </ads>
                       </addAds>";
    // add the ad to the google servers
    $someAd = $someSoapClient->call("addAds", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addVideoAd()", $soapParameters);
      return false;
    }
    return receiveAd($someAd['addAdsReturn']);
  }

  function getMyBusinesses() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<getMyBusinesses></getMyBusinesses>";
    $myBusinesses = $someSoapClient->call("getMyBusinesses", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getMyBusinesses()", $soapParameters);
      return false;
    }
    return $myBusinesses;
  }

  function findBusinesses($name, $address, $countryCode) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<findBusinesses>
                         <name>" . $name . "</name>
                         <address>" . $address . "</address>
                         <countryCode>" . $countryCode . "</countryCode>
                       </findBusinesses>";
    $businesses = $someSoapClient->call("findBusinesses", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":findBusinesses()", $soapParameters);
      return false;
    }
    return $businesses;
  }

  function receiveAd($someAd) {
    // populate class attributes
    $id = $someAd['id'];
    $belongsToAdGroupId = $someAd['adGroupId'];
    $destinationUrl = $someAd['destinationUrl'];
    $status = $someAd['status'];
    $isDisapproved = $someAd['disapproved'];
    $displayUrl = $someAd['displayUrl'];
    // video ad
    if (strcasecmp($someAd['adType'], 'VideoAd') == 0) {
      $image = $someAd['image'];
      $name = $someAd['name'];
      $video = $someAd['video'];
      $adObject = new APIlityVideoAd(
          $id,
          $belongsToAdGroupId,
          $image,
          $name,
          $video,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    // these attributes apply just to image ads, so just assign these
    // attributes if we have an image ad
    else if (strcasecmp($someAd['adType'], 'ImageAd') == 0) {
      $image = array();
      $image['name'] = $someAd['image']['name'];
      $image['width'] = $someAd['image']['width'];
      $image['height'] = $someAd['image']['height'];
      $image['imageUrl'] = $someAd['image']['imageUrl'];
      $image['thumbnailUrl'] = $someAd['image']['thumbnailUrl'];
      $image['mimeType'] = $someAd['image']['mimeType'];
      $image['type'] = $someAd['image']['type'];
      $adObject = new APIlityImageAd(
          $id,
          $belongsToAdGroupId,
          $image,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    // mobile ad
    else if (strcasecmp($someAd['adType'], 'MobileAd') == 0) {
      $businessName = isset($someAd['businessName']) ? $someAd['businessName'] : '';
      $countryCode = $someAd['countryCode'];
      $description = $someAd['description'];
      $headline = $someAd['headline'];
      $markupLanguages = $someAd['markupLanguages'];
      $mobileCarriers = $someAd['mobileCarriers'];
      $phoneNumber = $someAd['phoneNumber'];
      $adObject = new APIlityMobileAd(
          $id,
          $belongsToAdGroupId,
          $businessName,
          $countryCode,
          $description,
          $headline,
          $markupLanguages,
          $mobileCarriers,
          $phoneNumber,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    // mobile image ad
    else if (strcasecmp($someAd['adType'], 'MobileImageAd') == 0) {
      $markupLanguages = $someAd['markupLanguages'];
      $mobileCarriers = $someAd['mobileCarriers'];
      $image = $someAd['image'];
      $adObject = new APIlityMobileImageAd(
          $id,
          $belongsToAdGroupId,
          $image,
          $markupLanguages,
          $mobileCarriers,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    // local business ad
    else if (strcasecmp($someAd['adType'], 'LocalBusinessAd') == 0) {
      $address = $someAd['address'];
      $businessImage = isset($someAd['businessImage'])? $someAd['businessImage'] : NULL;
      $businessKey = isset($someAd['businessKey'])? $someAd['businessKey'] : NULL;
      $businessName = $someAd['businessName'];
      $fullBusinessName = $someAd['fullBusinessName'];      
      $city = $someAd['city'];
      $countryCode = $someAd['countryCode'];
      $customIcon = isset($someAd['customIcon'])? $someAd['customIcon'] : NULL;
      $customIconId = isset($someAd['customIconId'])? $someAd['customIconId'] : NULL;
      $description1 = $someAd['description1'];
      $description2 = $someAd['description2'];
      $phoneNumber = $someAd['phoneNumber'];
      $postalCode = $someAd['postalCode'];
      $region = isset($someAd['region'])? $someAd['region'] : NULL;
      $stockIcon = isset($someAd['stockIcon'])? $someAd['stockIcon'] : NULL;
      $targetRadiusInKm = $someAd['targetRadiusInKm'];
      $latitude = $someAd['latitude'];
      $longitude = $someAd['longitude'];
      $adObject = new APIlityLocalBusinessAd(
          $id,
          $belongsToAdGroupId,
          $address,
          $businessImage,
          $businessKey,
          $businessName,
          $fullBusinessName,
          $city,
          $countryCode,
          $customIcon,
          $customIconId,
          $description1,
          $description2,
          $phoneNumber,
          $postalCode,
          $region,
          $stockIcon,
          $targetRadiusInKm,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved,
          $latitude,
          $longitude
      );
    }
    // commerce ad
    else if (strcasecmp($someAd['adType'], 'CommerceAd') == 0) {
      $description1 = $someAd['description1'];
      $description2 = $someAd['description2'];
      $headline = $someAd['headline'];
      $postPriceAnnotation = $someAd['postPriceAnnotation'];
      $prePriceAnnotation = $someAd['prePriceAnnotation'];
      $priceString = $someAd['priceString'];
      $productImage = $someAd['productImage'];
      $adObject = new APIlityCommerceAd(
          $id,
          $belongsToAdGroupId,
          $description1,
          $description2,
          $headline,
          $postPriceAnnotation,
          $prePriceAnnotation,
          $priceString,
          $productImage,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    // text ad
    else if (strcasecmp($someAd['adType'], 'TextAd') == 0) {
      $headline = $someAd['headline'];
      $description1 = $someAd['description1'];
      $description2 = $someAd['description2'];
      $adObject = new APIlityTextAd(
          $id,
          $belongsToAdGroupId,
          $headline,
          $description1,
          $description2,
          $displayUrl,
          $destinationUrl,
          $status,
          $isDisapproved
      );
    }
    return $adObject;
  }

  function checkAdList($ads, $languages, $newGeoTargets) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<checkAds>";
    foreach ($ads as $ad) {
      $soapParameters .= "<ads>";
      if ((array_key_exists('mobileCarriers', $ad)) && 
          (!array_key_exists('imageLocation', $ad))) {
        $soapParameters .= addImageAd(
            0,
            $ad['imageLocation'],
            $ad['name'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if ((array_key_exists('mobileCarriers', $ad)) &&
               (!array_key_exists('imageLocation', $ad))) {
        $soapParameters .= addMobileAd(
            0,
            $ad['businessName'],
            $ad['countryCode'],
            $ad['description'],
            $ad['headline'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['phoneNumber'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if ((array_key_exists('mobileCarriers', $ad)) &&
               (array_key_exists('imageLocation', $ad))) {
        $soapParameters .= addMobileImageAd(
            0,
            $ad['imageLocation'],
            $ad['markupLanguages'],
            $ad['mobileCarriers'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if (array_key_exists('targetRadiusInKm', $ad)) {
        $soapParameters .= addLocalBusinessAd(
            0,
            $ad['address'],
            $ad['businessImageLocation'],
            $ad['businessKey'],
            $ad['businessName'],
            $ad['city'],
            $ad['countryCode'],
            $ad['customIconLocation'],
            $ad['customIconId'],
            $ad['description1'],
            $ad['description2'],
            $ad['phoneNumber'],
            $ad['postalCode'],
            $ad['region'],
            $ad['stockIcon'],
            $ad['targetRadiusInKm'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if (array_key_exists('postPriceAnnotation', $ad)) {
        $soapParameters .= addCommerceAd(
            0,
            $ad['description1'],
            $ad['description2'],
            $ad['headline'],
            $ad['postPriceAnnotation'],
            $ad['prePriceAnnotation'],
            $ad['priceString'],
            $ad['productImageLocation'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if (array_key_exists('headline', $ad)) {
        $soapParameters .= addTextAd(
            0,
            $ad['headline'],
            $ad['description1'],
            $ad['description2'],
            $ad['status'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            false,
            true
        );
      }
      else if (array_key_exists('video', $ad)) {
        $soapParameters .= addVideoAd(
            0,
            $ad['image'],
            $ad['name'],
            $ad['video'],
            $ad['displayUrl'],
            $ad['destinationUrl'],
            $ad['status'],
            false,
            true
        );
      }
      $soapParameters .= "</ads>";
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
      foreach($newGeoTargets['countryTargets']['countries'] as $country) {
        $newGeoTargetsXml .= "<countries>" . trim($country) . "</countries>";
      }
    }
    $newGeoTargetsXml .= "</countryTargets><regionTargets>";
    if (isset($newGeoTargets['regionTargets']['regions'])) {
      foreach($newGeoTargets['regionTargets']['regions'] as $region) {
        $newGeoTargetsXml .= "<regions>" . trim($region) . "</regions>";
      }
    }
    $newGeoTargetsXml .= "</regionTargets><metroTargets>";
    if (isset($newGeoTargets['metroTargets']['metros'])) {
      foreach($newGeoTargets['metroTargets']['metros'] as $metro) {
        $newGeoTargetsXml .= "<metros>" . trim($metro) . "</metros>";
      }
    }
    $newGeoTargetsXml .= "</metroTargets><cityTargets>";
    if (isset($newGeoTargets['cityTargets']['cities'])) {
      foreach($newGeoTargets['cityTargets']['cities'] as $city) {
        $newGeoTargetsXml .= "<cities>" . trim($city) . "</cities>";
      }
    }
    $newGeoTargetsXml .= "</cityTargets><proximityTargets>";
    if (isset($newGeoTargets['proximityTargets']['circles'])) {
        foreach($newGeoTargets['proximityTargets']['circles'] as $circle) {
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

    $soapParameters .= "<geoTarget>" . $newGeoTargetsXml . "</geoTarget></checkAds>";

     // query the google servers
     $adsCheck = $someSoapClient->call('checkAds', $soapParameters);
     $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault)  {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'].':checkAdList()', $soapParameters);
      return false;
    }
    return makeNumericArray($adsCheck);
  }

  function getMyVideos() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAdClient();
    $soapParameters = "<getMyVideos></getMyVideos>";
    $videos = $someSoapClient->call("getMyVideos", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getMyVideos()", $soapParameters);
      return false;
    }
    return makeNumericArray($videos);
  }
?>