<?php
  // import this file only when we are not in OO mode
  // however, if we are in OO mode, the import happens in APIlityUser.php  
  if (!IS_ENABLED_OO_MODE) {
    require_once('Campaign.inc.php');  
  }
  
  class APIlityCampaign {
    // class attributes
    var $name;
    var $id;
    var $status;
    var $startDate;
    var $endDate;
    var $budgetAmount;
    var $budgetPeriod;
    var $languages = array();
    var $geoTargets = array();
    var $isEnabledOptimizedAdServing;
    var $networkTargeting = array();
    var $adScheduling = array();
    var $budgetOptimizerSettings = array();
    var $campaignNegativeKeywordCriteria = array();
    var $campaignNegativeWebsiteCriteria = array();
    var $belongsToClientEmail;
    var $conversionOptimizerSettings = array();

    // constructor
    function APIlityCampaign (
        $name,
        $id,
        $status,
        $startDate,
        $endDate,
        $budgetAmount,
        $budgetPeriod,
        $networkTargeting,
        $languages,
        $geoTargets,
        $isEnabledOptimizedAdServing,
        $campaignNegativeKeywordCriteria = array(),
        $campaignNegativeWebsiteCriteria = array(),
        $adScheduling,
        $budgetOptimizerSettings = array(),
        $conversionOptimizerSettings = array()
    ) {
      $this->name = $name;
      $this->id = $id;
      $this->status = $status;
      $this->startDate = $startDate;
      $this->endDate = $endDate;
      $this->budgetAmount = $budgetAmount;
      $this->budgetPeriod = $budgetPeriod;
      $this->networkTargeting = convertToArray($networkTargeting);
      $this->languages = convertToArray($languages);
      $this->geoTargets = $geoTargets;
      $this->campaignNegativeKeywordCriteria =
          convertToArray($campaignNegativeKeywordCriteria);
      $this->campaignNegativeWebsiteCriteria =
          convertToArray($campaignNegativeWebsiteCriteria);
      $this->isEnabledOptimizedAdServing =
          convertBool($isEnabledOptimizedAdServing);
      $this->adScheduling = $adScheduling;
      $this->budgetOptimizerSettings = $budgetOptimizerSettings;
      $authenticationContext = &APIlityManager::getContext();              
      $this->belongsToClientEmail = is_a($authenticationContext, 'APIlityUser')? $authenticationContext->getClientEmail() : '';        
      $this->conversionOptimizerSettings = $conversionOptimizerSettings;
    }

    // XML output
    function toXml() {
      $adSchedulingXml = "";
      $adScheduling = $this->getAdScheduling();
      $adSchedulingXml .= "\t<status>" . $adScheduling['status'] . "</status>\n";
      if ( strcasecmp($adScheduling['status'], "Disabled") != 0 ) {
        foreach ($adScheduling['intervals'] as $interval) {
          $adSchedulingXml .= "\t<intervals>\n
                               \t\t<multiplier>" . $interval['multiplier'] . "</multiplier>\n
                               \t\t<day>" . $interval['day'] . "</day>\n
                               \t\t<startHour>" . $interval['startHour'] . "</startHour>\n
                               \t\t<startMinute>" . $interval['startMinute'] . "</startMinute>\n
                               \t\t<endHour>" . $interval['endHour'] . "</endHour>\n
                               \t\t<endMinute>" . $interval['endMinute'] . "</endMinute>\n
                               \t</intervals>\n";
        }
      }

      $networkTargetingXml = "";
      foreach ($this->getNetworkTargeting() as $networkTarget) {
        $networkTargetingXml .= "\t\t<networkTarget>" . $networkTarget . "</networkTarget>\n";
      }

      $languagesXml = "";
      foreach ($this->getLanguages() as $language) {
        $languagesXml .= "\t\t<language>" . $language . "</language>\n";
      }

      $geoTargetsXml = "";
      $geoTargets = $this->getGeoTargets();
      // countries      
      $geoTargetsXml .= "\t<countryTargets>\n";
      if (isset($geoTargets['countryTargets']['countries'])) {
        foreach ($geoTargets['countryTargets']['countries'] as $country) {
          $geoTargetsXml .= "\t\t<countries>" . $country . "</countries>\n";
        }
      }
      if (isset($geoTargets['countryTargets']['excludedCountries'])) {
        foreach ($geoTargets['countryTargets']['excludedCountries'] as $excludedCountry) {
          $geoTargetsXml .= "\t\t<excludedCountries>" . $excludedCountry . "</excludedCountries>\n";
        }
      }
      $geoTargetsXml .= "\t</countryTargets>\n";
      // regions
      $geoTargetsXml .= "\t<regionTargets>";
      if (isset($geoTargets['regionTargets']['regions'])) {
        foreach ($geoTargets['regionTargets']['regions'] as $region) {
          $geoTargetsXml .= "\t\t<regions>" . $region . "</regions>\n";
        }
      }
      if (isset($geoTargets['regionTargets']['excludedRegions'])) {
        foreach ($geoTargets['regionTargets']['excludedRegions'] as $excludedRegion) {
          $geoTargetsXml .= "\t\t<excludedRegions>" . $excludedRegion . "</excludedRegions>\n";
        }
      }
      $geoTargetsXml .= "\t</regionTargets>\n";
      // metros
      $geoTargetsXml .= "\t<metroTargets>";
      if (isset($geoTargets['metroTargets']['metros'])) {
        foreach ($geoTargets['metroTargets']['metros'] as $metro) {
          $geoTargetsXml .= "\t\t<metros>" . $metro . "</metros>\n";
        }
      }
      if (isset($geoTargets['metroTargets']['excludedMetros'])) {
        foreach ($geoTargets['metroTargets']['excludedMetros'] as $excludedMetro) {
          $geoTargetsXml .= "\t\t<excludedMetros>" . $excludedMetro . "</excludedMetros>\n";
        }
      }
      $geoTargetsXml .= "\t</metroTargets>\n";
      // cities
      $geoTargetsXml .= "\t<cityTargets>";
      if (isset($geoTargets['cityTargets']['cities'])) {
        foreach ($geoTargets['cityTargets']['cities'] as $city) {
          $geoTargetsXml .= "\t\t<cities>" . $city . "</cities>\n";
        }
      }
      if (isset($geoTargets['cityTargets']['excludedCities'])) {
        foreach ($geoTargets['cityTargets']['excludedCities'] as $excludedCity) {
          $geoTargetsXml .= "\t\t<excludedCities>" . $excludedCity . "</excludedCities>\n";
        }
      }      
      $geoTargetsXml .= "\t</cityTargets>\n";
      $geoTargetsXml .= "\t<proximityTargets>";
      if (isset($geoTargets['proximityTargets']['circles'])) {
        foreach ($geoTargets['proximityTargets']['circles'] as $circle) {
          $geoTargetsXml .= "\t\t<circles>\n";
          $geoTargetsXml .= "\t\t\t<latitudeMicroDegrees>" . $circle['latitudeMicroDegrees'] . "</latitudeMicroDegrees>\n";
          $geoTargetsXml .= "\t\t\t<longitudeMicroDegrees>" . $circle['longitudeMicroDegrees'] . "</longitudeMicroDegrees>\n";
          $geoTargetsXml .= "\t\t\t<radiusMeters>" . $circle['radiusMeters'] . "</radiusMeters>\n";
          $geoTargetsXml .= "\t\t</circles>\n";
        }
      }
      $geoTargetsXml .= "\t</proximityTargets>\n";
      if (isset($geoTargets['targetAll']) && $geoTargets['targetAll']) {
        $geoTargets['targetAll'] = "true";
      }
      else {
        $geoTargets['targetAll'] = "false";
      }
      $geoTargetsXml .= "\t<targetAll>" . $geoTargets['targetAll'] . "</targetAll>\n";

      $negativeWebsiteCriteriaXml = "";
      foreach ($this->getCampaignNegativeWebsiteCriteria() as $criterion) {
        $negativeWebsiteCriteriaXml .=
            "\t\t<negativeKeywordCriterion>\n\t\t\t<url>" . 
            $criterion['url'] . "</url>\n\t\t</negativeKeywordCriterion>\n";
      }

      $negativeKeywordCriteriaXml = "";
      foreach ($this->getCampaignNegativeKeywordCriteria() as $criterion) {
        $negativeKeywordCriteriaXml .=
          "\t\t<negativeKeywordCriterion>\n\t\t\t<text>" . 
          $criterion['text'] . "</text>\n\t\t\t<type>" . 
          $criterion['type'] . "</type>\n\t\t</negativeKeywordCriterion>\n";
      }

      $conversionOptimizerSettingsXml = "";
      $conversionOptimizerSettings = $this->getConversionOptimizerSettings();
      if ($conversionOptimizerSettings['enabled']) {
        $conversionOptimizerSettings['enabled'] = "true";
      }
      else {
        $conversionOptimizerSettings['enabled'] = "false";
      }
      $conversionOptimizerSettingsXml .=
          "\t\t<maxCpaBidForAllAdGroups>" . $conversionOptimizerSettings['maxCpaBidForAllAdGroups'] . 
          "</maxCpaBidForAllAdGroups>\n\t\t<enabled>" . 
          $conversionOptimizerSettings['enabled'] . "</enabled>\n";

      $budgetOptimizerSettingsXml = "";
      $budgetOptimizerSettings = $this->getBudgetOptimizerSettings();
      if ($budgetOptimizerSettings['enabled']) {
        $budgetOptimizerSettings['enabled'] = "true";
      }
      else {
        $budgetOptimizerSettings['enabled'] = "false";
      }
      $budgetOptimizerSettingsXml .=
          "\t\t<bidCeiling>" . $budgetOptimizerSettings['bidCeiling'] . 
          "</bidCeiling>\n\t\t<enabled>" . 
          $budgetOptimizerSettings['enabled'] . "</enabled>\n";

      $xml = "<Campaign>
  <name>" . xmlEscape($this->getName()) . "</name>
  <id>" . $this->getId() . "</id>
  <status>" . $this->getStatus() . "</status>
  <startDate>" . $this->getStartDate() . "</startDate>
  <endDate>" . $this->getEndDate() . "</endDate>
  <budgetAmount>" . $this->getBudgetAmount() . "</budgetAmount>
  <budgetPeriod>" . $this->getBudgetPeriod() . "</budgetPeriod>  
  <networkTargeting>\n" . $networkTargetingXml . "\t</networkTargeting>
  <languages>\n" . $languagesXml . "\t</languages>
  <geoTargets>\n" . $geoTargetsXml . "\t</geoTargets>
  <negativeKeywordCriteria>\n" . $negativeKeywordCriteriaXml . "\t</negativeKeywordCriteria>
  <negativeWebsiteCriteria>\n" . $negativeWebsiteCriteriaXml . "\t</negativeWebsiteCriteria>
  <adScheduling>\n" . $adSchedulingXml . "\t</adScheduling>
  <budgetOptimizerSettings>\n" . $budgetOptimizerSettingsXml . "\t</budgetOptimizerSettings>
  <conversionOptimizerSettings>\n" . $conversionOptimizerSettingsXml . "\t</conversionOptimizerSettings>  
  <belongsToClientEmail>" . $this->getBelongsToClientEmail() . "</belongsToClientEmail>
</Campaign>";
      return $xml;
    }

    // get functions    
    function getBelongsToClientEmail() {
      return $this->belongsToClientEmail;  
    }
    
    function getName() {
      return $this->name;
    }

    function getId() {
      return $this->id;
    }

    function getStatus() {
      return $this->status;
    }

    function getStartDate() {
      return $this->startDate;
    }

    function getEndDate() {
      return $this->endDate;
    }

    function getAdScheduling() {
      return $this->adScheduling;
    }

    function getBudgetOptimizerSettings() {
      return $this->budgetOptimizerSettings;
    }

    function getConversionOptimizerSettings() {
      return $this->conversionOptimizerSettings;
    }

    function getBudgetAmount() {
      // thinking in currency units here
      return $this->budgetAmount;
    }

    function getBudgetPeriod() {
      return $this->budgetPeriod;
    }

    function getNetworkTargeting() {
      return $this->networkTargeting;
    }

    function getLanguages() {
      return $this->languages;
    }

    function getGeoTargets() {
      return $this->geoTargets;
    }

    function getIsEnabledOptimizedAdServing() {
      return $this->isEnabledOptimizedAdServing;
    }

    function getEstimate() {
      // this function is located in TrafficEstimate.php
      return getCampaignEstimate($this);
    }

    // report function
    function getCampaignData() {
      $campaignData = array(
        'name' => $this->getName(),
        'id' => $this->getId(),
        'belongsToClientEmail' => $this->getBelongsToClientEmail(),
        'status' => $this->getStatus(),
        'startDate' => $this->getStartDate(),
        'endDate' => $this->getEndDate(),
        'budgetAmount' => $this->getBudgetAmount(),
        'budgetPeriod' => $this->getBudgetPeriod(),        
        'networkTargeting' => $this->getNetworkTargeting(),
        'languages' => $this->getLanguages(),
        'geoTargets' => $this->getGeoTargets(),
        'isEnabledOptimizedAdServing' => $this->getIsEnabledOptimizedAdServing(),
        'campaignNegativeWebsiteCriteria' => $this->getCampaignNegativeWebsiteCriteria(),
        'campaignNegativeKeywordCriteria' => $this->getCampaignNegativeKeywordCriteria(),
        'adScheduling' => $this->getAdScheduling()
      );
      return $campaignData;
    }

    function getCampaignStats($startDate, $endDate) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      $soapParameters = "<getCampaignStats>
                           <campaignIds>" . $this->getId() . "</campaignIds>
                           <startDay>" . $startDate . "</startDay>
                           <endDay>" . $endDate . "</endDay>
                         </getCampaignStats>";
      // query the google servers for the campaign stats
      $campaignStats = $someSoapClient->call("getCampaignStats", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getCampaignStats()", $soapParameters);
        return false;
      }
      // add campaign name to the stats for the sake of clarity
      $campaignStats['getCampaignStatsReturn']['name'] = $this->getName();
      // think in currency units
      $campaignStats['getCampaignStatsReturn']['cost'] =
          ((double) $campaignStats['getCampaignStatsReturn']['cost']) / EXCHANGE_RATE;
      return $campaignStats['getCampaignStatsReturn'];
    }

    function getRecommendedBudget() {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      $soapParameters = "<getRecommendedBudgetList>
                           <campaignIds>" . $this->getId() . "</campaignIds>
                         </getRecommendedBudgetList>";
      // query the google servers for the campaign stats
      $recommendedBudget = $someSoapClient->call("getRecommendedBudgetList", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getRecommendedBudget()", $soapParameters);
        return false;
      }
      return $recommendedBudget['getRecommendedBudgetListReturn'];
    }

    function getAllAdGroups() {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdGroupClient();
      $soapParameters = "<getAllAdGroups>
                           <campaignID>" . $this->getId() . "</campaignID>
                         </getAllAdGroups>";
      // query the google servers for all adgroups of the campaign
      $allAdGroups = array();
      $allAdGroups = $someSoapClient->call("getAllAdGroups", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAllAdGroups()", $soapParameters);
        return false;
      }
      $allAdGroups = makeNumericArray($allAdGroups);
      // return only paused and active adgroups
      $allAdGroupObjects = array();
      if (!isset($allAdGroups['getAllAdGroupsReturn'])) {
        return $allAdGroupObjects;
      }
      foreach($allAdGroups['getAllAdGroupsReturn'] as $adGroup) {
        $adGroupObject = receiveAdGroup($adGroup);
        if (isset($adGroupObject)) {
          array_push($allAdGroupObjects, $adGroupObject);
        }
      }
      return $allAdGroupObjects;
    }

    function getActiveAdGroups() {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getAdGroupClient();
      $soapParameters = "<getActiveAdGroups>
                           <campaignID>" . $this->getId() . "</campaignID>
                         </getActiveAdGroups>";
      // query the google servers for all adgroups of the campaign
      $allAdGroups = array();
      $allAdGroups = $someSoapClient->call("getActiveAdGroups", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getActiveAdGroups()", $soapParameters);
        return false;
      }
      $allAdGroups = makeNumericArray($allAdGroups);
      // return only paused and active adgroups
      $allAdGroupObjects = array();
      if (!isset($allAdGroups['getActiveAdGroupsReturn'])) {
        return $allAdGroupObjects;
      }
      foreach($allAdGroups['getActiveAdGroupsReturn'] as $adGroup) {
        $adGroupObject = receiveAdGroup($adGroup);
        if (isset($adGroupObject)) {
          array_push($allAdGroupObjects, $adGroupObject);
        }
      }
      return $allAdGroupObjects;
    }

    function getCampaignNegativeWebsiteCriteria() {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      $soapParameters = "<getCampaignNegativeCriteria>
                           <campaignId>" . $this->getId() . "</campaignId>
                         </getCampaignNegativeCriteria>";
      $allCampaignNegativeCriteria =
          $someSoapClient->call("getCampaignNegativeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getCampaignNegativeWebsiteCriteria()", $soapParameters);
        return false;
      }
      // if we have only one campaign negative criterion return a one-element array anyway
      if (isset($allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn'])) {
        $saveNegativeCriteria = $allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn'];
      }
      else {
        $saveNegativeCriteria = array();
      }
      if (isset($allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn']['id'])) {
        unset($allCampaignNegativeCriteria);
        $allCampaignNegativeCriteria = array();
        if (isset($saveNegativeCriteria['url'])) {
          $allCampaignNegativeCriteria[0] =
            array('url' => $saveNegativeCriteria['url']);
        }
      }
      else {
        unset($allCampaignNegativeCriteria);
        $allCampaignNegativeCriteria = array();
        foreach ($saveNegativeCriteria as $negativeCriterion) {
          if (isset($negativeCriterion['url'])) {
            array_push(
              $allCampaignNegativeCriteria,
              array('url' => $negativeCriterion['url'])
            );
          }
        }
      }
      return $allCampaignNegativeCriteria;
    }

    function getCampaignNegativeKeywordCriteria() {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();
      $soapParameters = "<getCampaignNegativeCriteria>
                           <campaignId>" . $this->getId() . "</campaignId>
                         </getCampaignNegativeCriteria>";
      $allCampaignNegativeCriteria =
          $someSoapClient->call("getCampaignNegativeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getCampaignNegativeKeywordCriteria()", $soapParameters);
        return false;
      }
      // if we have only one campaign negative criterion return a one-element array anyway
      if (isset($allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn'])) {
        $saveNegativeCriteria =
            $allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn'];
      }
      else {
        $saveNegativeCriteria = array();
      }
      if (isset($allCampaignNegativeCriteria['getCampaignNegativeCriteriaReturn']['id'])) {
        unset($allCampaignNegativeCriteria);
        $allCampaignNegativeCriteria = array();
        if (isset($saveNegativeCriteria['text'])) {
          $allCampaignNegativeCriteria[0] = array(
              'text' => $saveNegativeCriteria['text'],
              'type' => $saveNegativeCriteria['type']
          );
        }
      }
      else {
        unset($allCampaignNegativeCriteria);
        $allCampaignNegativeCriteria = array();
        foreach ($saveNegativeCriteria as $negativeCriterion) {
          if (isset($negativeCriterion['text'])) {
            array_push(
              $allCampaignNegativeCriteria,
              array(
                  'text' => $negativeCriterion['text'],
                  'type' => $negativeCriterion['type']
              )
            );
          }
        }
      }
      return $allCampaignNegativeCriteria;
    }

    // set functions
    function setConversionOptimizerSettings($conversionOptimizerSettings) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();      
      if ((isset($conversionOptimizerSettings['enabled'])) &&
          ($conversionOptimizerSettings['enabled'])) {
        $soapParameters = "<getConversionOptimizerEligibility>
                             <campaignIds>" . $this->getId() . "</campaignIds>
                           </getConversionOptimizerEligibility>";
        $conversionOptimizerEligibility =
            $someSoapClient->call("getConversionOptimizerEligibility", $soapParameters);
        $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
        if ($someSoapClient->fault) {
          pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setConversionOptimizerSettings()", $soapParameters);
          return false;
        }        
        if ((isset($conversionOptimizerEligibility['getConversionOptimizerEligibilityReturn']['eligibleToSwitchOn'])) &&
            (convertBool($conversionOptimizerEligibility['getConversionOptimizerEligibilityReturn']['eligibleToSwitchOn']))) {
          $conversionOptimizerSettings['enabled'] = 'true';
          $soapParameters = "<updateCampaign>
                               <campaign>
                                 <conversionOptimizerSettings>
                                   <enabled>" . $conversionOptimizerSettings['enabled'] . "</enabled>
                                   <maxCpaBidForAllAdGroups>" . 
                                     $conversionOptimizerSettings['maxCpaBidForAllAdGroups'] * EXCHANGE_RATE . "
                                   </maxCpaBidForAllAdGroups>
                                 </conversionOptimizerSettings> 
                                 <id>" . $this->getId() . "</id>
                               </campaign>
                             </updateCampaign>";
          $someSoapClient->call("updateCampaign", $soapParameters);
          $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
          if ($someSoapClient->fault) {
            pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setConversionOptimizerSettings()", $soapParameters);
            return false;
          }
          $this->conversionOptimizerSettings = 
                array(
                    'enabled' => true,
                    'maxCpaBidForAllAdGroups' => $conversionOptimizerSettings['maxCpaBidForAllAdGroups'] * EXCHANGE_RATE
                );
        }
        else {
          return false;  
        }        
      }
      else {
        $soapParameters = "<updateCampaign>
                             <campaign>
                               <conversionOptimizerSettings>
                                 <enabled>false</enabled>
                                 <maxCpaBidForAllAdGroups></maxCpaBidForAllAdGroups>
                               </conversionOptimizerSettings> 
                               <id>" . $this->getId() . "</id>
                             </campaign>
                           </updateCampaign>";
        $someSoapClient->call("updateCampaign", $soapParameters);
        $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
        if ($someSoapClient->fault) {
          pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setConversionOptimizerSettings()", $soapParameters);
          return false;
        }
        $this->conversionOptimizerSettings = 
            array(
                'enabled' => false,
                'maxCpaBidForAllAdGroups' => null
            );
      }    
    }

    function setCampaignNegativeWebsiteCriteria($newCampaignNegativeCriteria) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();

      // we need to save potentially existing negative KEYWORD criteria
      // as they will be deleted when we set new negative WEBSITE criteria
      $saveNegativeKeywordCriteria = array();
      $saveNegativeKeywordCriteria = $this->getCampaignNegativeKeywordCriteria();
      $saveNegativeKeywordCriteriaXml = "";
      if (!empty($saveNegativeKeywordCriteria)) {
        foreach ($saveNegativeKeywordCriteria as $saveNegativeKeywordCriterion) {
          $saveNegativeKeywordCriteriaXml .= "<criteria>
                                                <criterionType>Keyword</criterionType>
                                                <id>0</id>
                                                <adGroupId>0</adGroupId>
                                                <language></language>
                                                <maxCpc>0</maxCpc>
                                                <negative>true</negative>
                                                <type>" . trim($saveNegativeKeywordCriterion['type']) . "</type>
                                                <text>" . 
                                                  trim($saveNegativeKeywordCriterion['text']) . "
                                                </text>
                                              </criteria>";
        }
      }
      // end of saving negative KEYWORD criteria

      // expecting array('url' => "none.de", 'url' => "of.com", 'url' => "these.net")
      $newCampaignNegativeCriteriaXml = "";
      $soapParameters = "<setCampaignNegativeCriteria>
                           <campaignId>" . $this->getId() . "</campaignId>";
      if (!empty($newCampaignNegativeCriteria)) {
        foreach ($newCampaignNegativeCriteria as $newCampaignNegativeCriterion) {
          // update google servers
          $newCampaignNegativeCriteriaXml .= "<criteria>
                                                <criterionType>Website</criterionType>
                                                <id>0</id>
                                                <adGroupId>0</adGroupId>
                                                <language></language>
                                                <maxCpm>0</maxCpm>
                                                <negative>true</negative>
                                                <url>" . 
                                                  trim($newCampaignNegativeCriterion['url']) . "
                                                </url>
                                              </criteria>";
        }
      }
      // attach saved negative KEYWORD criteria
      $soapParameters .= $saveNegativeKeywordCriteriaXml;
      // close soap parameters
      $soapParameters .=
          $newCampaignNegativeCriteriaXml . "</setCampaignNegativeCriteria>";
      $someSoapClient->call("setCampaignNegativeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setCampaignNegativeWebsiteCriteria()", $soapParameters);
        return false;
      }
      return true;
    }

    function setCampaignNegativeKeywordCriteria($newCampaignNegativeCriteria) {
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCriterionClient();

      // we need to save potentially existing negative WEBSITE criteria
      // as they will be deleted when we set new negative KEYWORD criteria
      $saveNegativeWebsiteCriteria = array();
      $saveNegativeWebsiteCriteria = $this->getCampaignNegativeWebsiteCriteria();
      $saveNegativeWebsiteCriteriaXml = "";
      if (!empty($saveNegativeWebsiteCriteria)) {
        foreach ($saveNegativeWebsiteCriteria as $saveNegativeWebsiteCriterion) {
          $saveNegativeWebsiteCriteriaXml .= "<criteria>
                                                <criterionType>Website</criterionType>
                                                <id>0</id>
                                                <adGroupId>0</adGroupId>
                                                <language></language>
                                                <maxCpm>0</maxCpm>
                                                <negative>true</negative>
                                                <url>" . 
                                                  trim($saveNegativeWebsiteCriterion['url']) . "
                                                </url>
                                              </criteria>";
        }
      }
      // end of saving negative WEBSITE criteria

      // expecting
      // array(
      //   array('text' => "none", 'type' => "Phrase"),
      //   array('text' => "of", 'type' => "Exact"),
      //   array('text' => "these", 'type' => "Broad")
      // )
      $newCampaignNegativeCriteriaXml = "";
      $soapParameters = "<setCampaignNegativeCriteria>
                           <campaignId>" . $this->getId() . "</campaignId>";
      if (!empty($newCampaignNegativeCriteria)) {
        foreach ($newCampaignNegativeCriteria as $newCampaignNegativeCriterion) {
          // update google servers
          $newCampaignNegativeCriteriaXml .= "<criteria>
                                                <criterionType>Keyword</criterionType>
                                                <id>0</id>
                                                <adGroupId>0</adGroupId>
                                                <language></language>
                                                <type>" . trim($newCampaignNegativeCriterion['type']) . "</type>
                                                <maxCpc>0</maxCpc>
                                                <negative>true</negative>
                                                <text>" . trim($newCampaignNegativeCriterion['text']) . "</text>
                                              </criteria>";
        }
      }
      // attach saved negative WEBSITE criteria
      $soapParameters .= $saveNegativeWebsiteCriteriaXml;
      // close soap parameters
      $soapParameters .= $newCampaignNegativeCriteriaXml . "</setCampaignNegativeCriteria>";
      $someSoapClient->call("setCampaignNegativeCriteria", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setCampaignNegativeKeywordCriteria()", $soapParameters);
        return false;
      }
      return true;
    }

    function setName ($newName) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! think in micros
      $soapParameters = "<updateCampaign>
                            <campaign>
                              <id>" . $this->getId() . "</id>
                              <name>" . $newName . "</name>
                            </campaign>
                          </updateCampaign>";
      // set the new name on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setName()", $soapParameters);
        return false;
      }
      // update local object
      $this->name = $newName;
      return true;
    }

    function setEndDate ($newEndDate) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <endDay>" . $newEndDate . "</endDay>
                           </campaign>
                         </updateCampaign>";
      // set the new end date on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setEndDate()", $soapParameters);
        return false;
      }
      // update local object
      $this->endDate = $newEndDate;
      return true;
    }

    function setAdScheduling ($newAdScheduling) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      $intervalsXml = "";
      foreach ($newAdScheduling['intervals'] as $interval) {
        $intervalsXml .= "<intervals>
                            <multiplier>" . $interval['multiplier'] . "</multiplier>
                            <day>" . $interval['day'] . "</day>
                            <startHour>" . $interval['startHour'] . "</startHour>
                            <startMinute>" . $interval['startMinute'] . "</startMinute>
                            <endHour>" . $interval['endHour'] . "</endHour>
                            <endMinute>" . $interval['endMinute'] . "</endMinute>
                          </intervals>";
      }
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <schedule>
                               <status>" . $newAdScheduling['status'] . "</status>" . 
                               $intervalsXml . "
                             </schedule>
                           </campaign>
                         </updateCampaign>";
      // set the new end date on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setAdScheduling()", $soapParameters);
        return false;
      }
      // update local object
      $this->adScheduling = $newAdScheduling;
      return true;
    }

    function setBudgetOptimizerSettings ($newBudgetOptimizerSettings) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      if ($newBudgetOptimizerSettings['enabled']) {
        $newBudgetOptimizerSettings['enabled'] = "true";
      }
      else {
        $newBudgetOptimizerSettings['enabled'] = "false";
      }
      if ($newBudgetOptimizerSettings['takeOnOptimizedBids']) {
        $newBudgetOptimizerSettings['takeOnOptimizedBids'] = "true";
      }
      else {
        $newBudgetOptimizerSettings['takeOnOptimizedBids'] = "false";
      }
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <budgetOptimizerSettings>
                               <bidCeiling>" . 
                                 $newBudgetOptimizerSettings['bidCeiling'] * EXCHANGE_RATE . "
                               </bidCeiling>
                               <enabled>" . 
                                 $newBudgetOptimizerSettings['enabled'] . "
                               </enabled>
                               <takeOnOptimizedBids>" . 
                                 $newBudgetOptimizerSettings['takeOnOptimizedBids'] . "
                               </takeOnOptimizedBids>
                             </budgetOptimizerSettings>
                           </campaign>
                         </updateCampaign>";
      // set the new end date on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setBudgetOptimizerSettings()", $soapParameters);
        return false;
      }
      // update local object
      $this->budgetOptimizerSettings = $newBudgetOptimizerSettings;
      return true;
    }

    function setBudgetAmount ($newBudgetAmount) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <budgetAmount>" . ($newBudgetAmount * EXCHANGE_RATE) . "</budgetAmount>
                           </campaign>
                         </updateCampaign>";
      // set the new name on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setBudgetAmount()", $soapParameters);
        return false;
      }
      // update local object
      $this->budgetAmount = $newBudgetAmount;
      return true;
    }

    function setBudgetPeriod ($newBudgetPeriod) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <budgetPeriod>" . $newBudgetPeriod . "</budgetPeriod>
                           </campaign>
                         </updateCampaign>";
      // set the new name on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setBudgetPeriod()", $soapParameters);
        return false;
      }
      // update local object
      $this->budgetPeriod = $newBudgetPeriod;
      return true;
    }

    function setNetworkTargeting($networkTargeting) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <networkTargeting>";
      foreach($networkTargeting as $networkTarget) {
        $soapParameters .= "<networkTypes>" . trim($networkTarget) . "</networkTypes>";
      }
      $soapParameters .= "</networkTargeting>
                          <id>" . $this->getId() . "</id>
                        </campaign>
                      </updateCampaign>";
      // set the network targets on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setNetworkTargeting()", $soapParameters);
        return false;
      }
      // update local object
      $this->networkTargeting = $networkTargeting;
      return true;
    }

    function setLanguages ($newLanguages) {
      // expecting languages as array("en", "de", "fr")
      // update google servers
      $newLanguagesXml = "";
      if (strcasecmp(trim($newLanguages[0]), "all") != 0) {
        foreach ($newLanguages as $newLanguage) {
          // build the new languages xml
          $newLanguagesXml .= "<languages>" . trim($newLanguage) . "</languages>";
        }
      }
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <languageTargeting>" . 
                               $newLanguagesXml . "
                             </languageTargeting>
                           </campaign>
                         </updateCampaign>";
      // set the new languages on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setLanguages()", $soapParameters);
        return false;
      }
      // update local object
      if (strcasecmp(trim($newLanguages[0]), "all") != 0) {
        $this->languages = $newLanguages;
      }
      else {
        $this->languages = array();
      }
      return true;
    }

    function setGeoTargets ($newGeoTargets) {
      // expecting geoTargets as
      // array(
      //   ['countryTargets']['countries'] => array(),
      //                     ['excludedCountries'] => array(),
      //   ['regionTargtes']['regions'] => array(),
      //                    ['excludedRegions'] => array(), 
      //   ['metroTargets']['metros'] => array(),
      //                   ['excludedMetros'] => array(),
      //   ['cityTargets']['cities'] => array()
      //                  ['excludedCities'] => array()
      //   ['targetAll'] => boolean
      // )
      $newGeoTargetsXml = "";
      $newGeoTargetsXml .= "<countryTargets>";
      if (isset($newGeoTargets['countryTargets']['countries'])) {
        foreach($newGeoTargets['countryTargets']['countries'] as $country) {
          $newGeoTargetsXml .= "<countries>" . trim($country) . "</countries>";
        }
      }
      if (isset($newGeoTargets['countryTargets']['excludedCountries'])) {
        foreach($newGeoTargets['countryTargets']['excludedCountries'] as $excludedCountry) {
          $newGeoTargetsXml .= "<excludedCountries>" . trim($excludedCountry) . "</excludedCountries>";
        }
      }
      $newGeoTargetsXml .= "</countryTargets><regionTargets>";
      if (isset($newGeoTargets['regionTargets']['regions'])) {
        foreach($newGeoTargets['regionTargets']['regions'] as $region) {
          $newGeoTargetsXml .= "<regions>" . trim($region) . "</regions>";
        }
      }
      if (isset($newGeoTargets['regionTargets']['excludedRegions'])) {
        foreach($newGeoTargets['regionTargets']['excludedRegions'] as $excludedRegion) {
          $newGeoTargetsXml .= "<excludedRegions>" . trim($excludedRegion) . "</excludedRegions>";
        }
      }      
      $newGeoTargetsXml .= "</regionTargets><metroTargets>";
      if (isset($newGeoTargets['metroTargets']['metros'])) {
        foreach($newGeoTargets['metroTargets']['metros'] as $metro) {
          $newGeoTargetsXml .= "<metros>" . trim($metro) . "</metros>";
        }
      }
      if (isset($newGeoTargets['metroTargets']['excludedMetros'])) {
        foreach($newGeoTargets['metroTargets']['excludedMetros'] as $excludedMetro) {
          $newGeoTargetsXml .= "<excludedMetros>" . trim($excludedMetro) . "</excludedMetros>";
        }
      }
      $newGeoTargetsXml .= "</metroTargets><cityTargets>";
      if (isset($newGeoTargets['cityTargets']['cities'])) {
        foreach($newGeoTargets['cityTargets']['cities'] as $city) {
          $newGeoTargetsXml .= "<cities>" . trim($city) . "</cities>";
        }
      }
      if (isset($newGeoTargets['cityTargets']['excludedCities'])) {
        foreach($newGeoTargets['cityTargets']['excludedCities'] as $excludedCity) {
          $newGeoTargetsXml .= "<excludedCities>" . trim($excludedCity) . "</excludedCities>";
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
      if (isset($newGeoTargets['targetAll']) && $newGeoTargets['targetAll']) {
        $newGeoTargetsXml .= "<targetAll>true</targetAll>";
      }

      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! think in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <geoTargeting>" . $newGeoTargetsXml . "</geoTargeting>
                           </campaign>
                         </updateCampaign>";
      // set the new geo targets on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setGeoTargets()", $soapParameters);
        return false;
      }
      // update local object
      $this->geoTargets = $newGeoTargets;
      return true;
    }

    function setIsEnabledOptimizedAdServing($newFlag) {
      // update google servers
      // make sure bool gets transformed to string correctly
      if ($newFlag) $newFlag = "true"; else $newFlag = "false";
      $soapParameters = "<setOptimizeAdServing>
                           <campaignId>" . $this->getId() . "</campaignId>
                           <enable>" . $newFlag . "</enable>
                         </setOptimizeAdServing>";
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // set the new optimize adserving flag on the server
      $someSoapClient->call("setOptimizeAdServing", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setIsEnabledOptimizedAdServing()", $soapParameters);
        return false;
      }
      // update local object
      $this->isEnabledOptimizedAdServing = convertBool($newFlag);
      return true;
    }

    function setStatus($newStatus) {
      // update google servers
      $soapClients = &APIlityClients::getClients();
      $someSoapClient = $soapClients->getCampaignClient();
      // danger! thinking in micros
      $soapParameters = "<updateCampaign>
                           <campaign>
                             <id>" . $this->getId() . "</id>
                             <status>" . $newStatus . "</status>
                           </campaign>
                         </updateCampaign>";
      // set the new status on the google servers
      $someSoapClient->call("updateCampaign", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":setStatus()", $soapParameters);
        return false;
      }
      // update local object
      $this->status = $newStatus;
      return true;
    }
  }
?>