<?php
// creates a local campaign object we can play with
function createCampaignObject($givenCampaignId) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    // prepare soap parameters
    $soapParameters = "<getCampaign>
                         <id>" . $givenCampaignId . "</id>
                       </getCampaign>";
    // execute soap call
    $someCampaign = $someSoapClient->call("getCampaign", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":createCampaignObject()", $soapParameters);
        return false;
    }
    // invalid ids are silently ignored. this is not what we want so put out a
    // warning and return without doing anything.
    if (empty($someCampaign)) {
        if (!SILENCE_STEALTH_MODE) {
            trigger_error("<b>APIlity PHP library => Warning: </b>Invalid Campaign ID. No Campaign with the ID " . $givenCampaignId . " found", E_USER_WARNING);
        }
        return null;
    }
    return receiveCampaign($someCampaign['getCampaignReturn'], 'createCampaignObject');
}

function getAllCampaigns() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    // just need a dummy argument here. don't tell this to the real world and
    // just keep it inside
    $soapParameters = "<getAllAdWordsCampaigns>
                         <dummy>0</dummy>
                       </getAllAdWordsCampaigns>";
    // query the google server for all campaigns
    $allCampaigns = array();
    $allCampaigns = $someSoapClient->call("getAllAdWordsCampaigns", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAllCampaigns()", $soapParameters);
        return false;
    }
    $allCampaigns = makeNumericArray($allCampaigns);
    $allCampaignObjects = array();
    // return only active or paused campaigns
    if (!isset($allCampaigns['getAllAdWordsCampaignsReturn'])) {
        return $allCampaignObjects;
    }
    foreach ($allCampaigns['getAllAdWordsCampaignsReturn'] as $campaign) {
        $campaignObject = receiveCampaign($campaign, 'getAllAdWordsCampaigns');
        if (isset($campaignObject)) {
            array_push($allCampaignObjects, $campaignObject);
        }
    }
    return $allCampaignObjects;
}

function getActiveCampaigns() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    $soapParameters = "<getActiveAdWordsCampaigns>
                       </getActiveAdWordsCampaigns>";
    // query the google server for all active campaigns
    $allCampaigns = array();
    $allCampaigns = $someSoapClient->call("getActiveAdWordsCampaigns", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getActiveCampaigns()", $soapParameters);
        return false;
    }
    $allCampaigns = makeNumericArray($allCampaigns);
    $allCampaignObjects = array();
    // return only active or paused campaigns
    if (!isset($allCampaigns['getActiveAdWordsCampaignsReturn'])) {
        return $allCampaignObjects;
    }
    foreach ($allCampaigns['getActiveAdWordsCampaignsReturn'] as $campaign) {
        $campaignObject = receiveCampaign($campaign, 'getActiveAdWordsCampaigns');
        if (isset($campaignObject)) {
            array_push($allCampaignObjects, $campaignObject);
        }
    }
    return $allCampaignObjects;
}  

function getCampaignList($campaignIds) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    $soapParameters = "<getCampaignList>";
    foreach($campaignIds as $campaignId) {
        $soapParameters .= "<ids>" . $campaignId . "</ids>";
    }
    $soapParameters .= "</getCampaignList>";
    // query the google server for all campaigns
    $campaigns = array();
    $campaigns = $someSoapClient->call("getCampaignList", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getCampaignList()", $soapParameters);
        return false;
    }
    $campaigns = makeNumericArray($campaigns);
    $campaignObjects = array();
    // return only active or paused campaigns
    if (!isset($campaigns['getCampaignListReturn'])) {
        return $campaignObjects;
    }
    foreach ($campaigns['getCampaignListReturn'] as $campaign) {
        $campaignObject = receiveCampaign($campaign, 'getCampaignList');
        if (isset($campaignObject)) {
            array_push($campaignObjects, $campaignObject);
        }
    }
    return $campaignObjects;
}

function removeCampaign(&$campaignObject) {
    // update google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    // danger! think in micros
    $soapParameters = "<updateCampaign>
                          <campaign>
                            <id>" . $campaignObject->getId() . "</id>
                            <status>Deleted</status>
                          </campaign>
                       </updateCampaign>";
    // delete the campaign on the google servers
    $someSoapClient->call("updateCampaign", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":removeCampaign()", $soapParameters);
        return false;
    }
    // delete remote calling object
    $campaignObject = @$GLOBALS['campaignObject'];
    unset($campaignObject);
    return true;
}

function updateCampaignList($campaigns) {
    // update google servers
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();

    $soapParameters = "<updateCampaignList>";

    foreach ($campaigns as $campaign) {
        $statusXml = "";
        $budgetXml = "";

        if (isset($campaign['status'])) {
            $status = $campaign['status'];
            $statusXml = "<status>$status</status>";
        }
        // think in micros
        if (isset($campaign['budget'])) {
            $budgetXml = "<budgetAmount>" . $campaign['budget'] * EXCHANGE_RATE . "</budgetAmount>";
        }

        $soapParameters .= "<changedData>
                            <id>".$campaign['id']."</id>
        $statusXml
        $budgetXml
                          </changedData>";

    }

    $soapParameters .= "</updateCampaignList>";

    // updatethe campaign on the google servers
    $someSoapClient->call("updateCampaignList", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":updateCampaignList()", $soapParameters);
        return false;
    }
    return true;
}

function addCampaign(
    $name,
    $status,
    $startDate,
    $endDate,
    $budgetAmount,
    $budgetPeriod,
    $networkTargeting,
    $languages,
    $newGeoTargets,
    $adScheduling = false,
    $budgetOptimizerSettings = false
) {
    // update the google server
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();

    // expecting array("target1", "target2")
    $networkTargetingXml = "";
    foreach($networkTargeting as $networkTarget)  {
        $networkTargetingXml .=
        "<networkTypes>" . trim($networkTarget) . "</networkTypes>";
    }

    $newGeoTargetsXml ="";
    $newGeoTargetsXml .= "<countryTargets>";
    if (isset($newGeoTargets['countryTargets']['countries'])) {
        foreach ($newGeoTargets['countryTargets']['countries'] as $country) {
            $newGeoTargetsXml .= "<countries>" . trim($country) . "</countries>";
        }
    }
    if (isset($newGeoTargets['countryTargets']['excludedCountries'])) {
        foreach ($newGeoTargets['countryTargets']['excludedCountries'] as $excludedCountry) {
            $newGeoTargetsXml .= "<excludedCountries>" . trim($excludedCountry) . "</excludedCountries>";
        }
    }
    $newGeoTargetsXml .= "</countryTargets><regionTargets>";
    if (isset($newGeoTargets['regionTargets']['regions'])) {
        foreach ($newGeoTargets['regionTargets']['regions'] as $region) {
            $newGeoTargetsXml .= "<regions>" . trim($region) . "</regions>";
        }
    }
    if (isset($newGeoTargets['regionTargets']['excludedRegions'])) {
        foreach ($newGeoTargets['regionTargets']['excludedRegions'] as $excludedRegion) {
            $newGeoTargetsXml .= "<excludedRegions>" . trim($excludedRegion) . "</excludedRegions>";
        }
    }
    $newGeoTargetsXml .= "</regionTargets><metroTargets>";
    if (isset($newGeoTargets['metroTargets']['metros'])) {
        foreach ($newGeoTargets['metroTargets']['metros'] as $metro) {
            $newGeoTargetsXml .= "<metros>" . trim($metro) . "</metros>";
        }
    }
    if (isset($newGeoTargets['metroTargets']['excludedMetros'])) {
        foreach ($newGeoTargets['metroTargets']['excludedMetros'] as $excludedMetro) {
            $newGeoTargetsXml .= "<excludedMetros>" . trim($excludedMetro) . "</excludedMetros>";
        }
    }
    $newGeoTargetsXml .= "</metroTargets><cityTargets>";
    if (isset($newGeoTargets['cityTargets']['cities'])) {
        foreach ($newGeoTargets['cityTargets']['cities'] as $city) {
            $newGeoTargetsXml .= "<cities>" . trim($city) . "</cities>";
        }
    }
    if (isset($newGeoTargets['cityTargets']['excludedCities'])) {
        foreach ($newGeoTargets['cityTargets']['excludedCities'] as $excludedCity) {
            $newGeoTargetsXml .= "<excludedCities>" . trim($excludedCity) . "</excludedCities>";
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
    if (isset($newGeoTargets['targetAll']) && $newGeoTargets['targetAll']) {
        $newGeoTargetsXml .= "<targetAll>true</targetAll>";
    }

    // expecting array("en", "fr", "gr")
    $languagesXml = "";
    if (strcasecmp ($languages[0], "all") == 0) {
        $languagesXml = "";
    }
    else {
        foreach ($languages as $language) {
            $languagesXml .= "<languages>" . trim($language) . "</languages>";
        }
    }
    // only send a start day if it is necessary
    $startDateXml = "";
    if ($startDate) {
        $startDateXml = "<startDay>" . $startDate . "</startDay>";
    }
    else {
        $startDateXml = "";
    }

    // only send an end day if it is necessary
    $endDateXml = "";
    if ($endDate) {
        $endDateXml = "<endDay>" . $endDate . "</endDay>";
    }
    else {
        $endDateXml = "";
    }

    $adSchedulingXml = "";
    if ($adScheduling) {
        $adSchedulingXml .=
          "<schedule><status>" . $adScheduling['status'] . "</status>";
        foreach ($adScheduling['intervals'] as $interval) {
            $adSchedulingXml .= "<intervals>
                               <multiplier>" . $interval['multiplier'] . "</multiplier>
                               <day>" . $interval['day'] . "</day>
                               <startHour>" . $interval['startHour'] . "</startHour>
                               <startMinute>" . $interval['startMinute'] . "</startMinute>
                               <endHour>" . $interval['endHour'] . "</endHour>
                               <endMinute>" . $interval['endMinute'] . "</endMinute>
                             </intervals>";
        }
        $adSchedulingXml .= "</schedule>";
    }

    // think in micros
    $budgetAmount = $budgetAmount * EXCHANGE_RATE;

    $budgetOptimizerSettingsXml = "";
    if ($budgetOptimizerSettings) {
        if ($budgetOptimizerSettings['enabled']) {
            $budgetOptimizerSettings['enabled'] = "true";
        }
        else {
            $budgetOptimizerSettings['enabled'] = "false";
        }
        if ($budgetOptimizerSettings['takeOnOptimizedBids']) {
            $budgetOptimizerSettings['takeOnOptimizedBids'] = "true";
        }
        else {
            $budgetOptimizerSettings['takeOnOptimizedBids'] = "false";
        }
        $budgetOptimizerSettingsXml .= "<budgetOptimizerSettings>
                                        <bidCeiling>" .
        $budgetOptimizerSettings['bidCeiling'] * EXCHANGE_RATE . "
                                        </bidCeiling>
                                        <enabled>" .
        $budgetOptimizerSettings['enabled'] . "
                                        </enabled>
                                        <takeOnOptimizedBids>" .
        $budgetOptimizerSettings['takeOnOptimizedBids'] . "
                                        </takeOnOptimizedBids>
                                      </budgetOptimizerSettings>";
    }
    $soapParameters = "<addCampaign>
                         <campaign>
                           <budgetAmount>" . $budgetAmount . "</budgetAmount>
                           <budgetPeriod>" . $budgetPeriod . "</budgetPeriod>
                           <name>" . $name . "</name>
                           <status>" . $status . "</status>" .
    $startDateXml .
    $endDateXml . "
                           <networkTargeting>" .
    $networkTargetingXml . "
                           </networkTargeting>
                           <languageTargeting>" .
    $languagesXml . "
                           </languageTargeting>
                           <geoTargeting>" . $newGeoTargetsXml . "</geoTargeting>" .
    $adSchedulingXml .
    $budgetOptimizerSettingsXml . "
                        </campaign>
                      </addCampaign>";
    // add the campaign to the google servers
    $someCampaign = $someSoapClient->call("addCampaign", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addCampaign()", $soapParameters);
        return false;
    }
    return receiveCampaign($someCampaign['addCampaignReturn'], 'addCampaign');
}

function addCampaignList($campaigns) {
    // update the google server
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCampaignClient();
    $soapParameters = "<addCampaignList>";
    foreach ($campaigns as $campaign) {
        $newGeoTargetsXml = "";
        $languagesXml = "";
        $networkTargetingXml = "";
        $newGeoTargetsXml .= "<countryTargets>";
        if ($campaign['geoTargets']['countryTargets']['countries']) {
            foreach ($campaign['geoTargets']['countryTargets']['countries'] as $country) {
                $newGeoTargetsXml .= "<countries>" . trim($country) . "</countries>";
            }
        }
        if ((isset($campaign['geoTargets']['countryTargets']['excludedCountries'])) &&
            ($campaign['geoTargets']['countryTargets']['excludedCountries'])) {
            foreach ($campaign['geoTargets']['countryTargets']['excludedCountries'] as $excludedCountry) {
                $newGeoTargetsXml .= "<excludedCountries>" . trim($excludedCountry) . "</excludedCountries>";
            }
        }
        $newGeoTargetsXml .= "</countryTargets><regionTargets>";
        if ($campaign['geoTargets']['regionTargets']['regions']) {
            foreach ($campaign['geoTargets']['regionTargets']['regions'] as $region) {
                $newGeoTargetsXml .= "<regions>" . trim($region) . "</regions>";
            }
        }
        if ((isset($campaign['geoTargets']['regionTargets']['excludedRegions'])) &&
            ($campaign['geoTargets']['regionTargets']['excludedRegions'])) {
            foreach ($campaign['geoTargets']['regionTargets']['excludedRegions'] as $excludedRegion) {
                $newGeoTargetsXml .= "<excludedRegions>" . trim($excludedRegion) . "</excludedRegions>";
            }
        }
        $newGeoTargetsXml .= "</regionTargets><metroTargets>";
        if ($campaign['geoTargets']['metroTargets']['metros']) {
            foreach ($campaign['geoTargets']['metroTargets']['metros'] as $metro) {
                $newGeoTargetsXml .= "<metros>" . trim($metro) . "</metros>";
            }
        }
        if ((isset($campaign['geoTargets']['metroTargets']['excludedMetros']) &&
                ($campaign['geoTargets']['metroTargets']['excludedMetros']))) {
            foreach ($campaign['geoTargets']['metroTargets']['excludedMetros'] as $excludedMetro) {
                $newGeoTargetsXml .= "<excludedMetros>" . trim($excludedMetro) . "</excludedMetros>";
            }
        }
        $newGeoTargetsXml .= "</metroTargets><cityTargets>";
        if ($campaign['geoTargets']['cityTargets']['cities']) {
            foreach ($campaign['geoTargets']['cityTargets']['cities'] as $city) {
                $newGeoTargetsXml .= "<cities>" . trim($city) . "</cities>";
            }
        }
        if ((isset($campaign['geoTargets']['cityTargets']['excludedCities'])) &&
            ($campaign['geoTargets']['cityTargets']['excludedCities'])) {
            foreach ($campaign['geoTargets']['cityTargets']['excludedCities'] as $excludedCity) {
                $newGeoTargetsXml .= "<excludedCities>" . trim($excludedCity) . "</excludedCities>";
            }
        }
        $newGeoTargetsXml .= "</cityTargets><proximityTargets>";
        if ($campaign['geoTargets']['proximityTargets']['circles']) {
            foreach ($campaign['geoTargets']['proximityTargets']['circles'] as $circle) {
                $newGeoTargetsXml .= "<circles>";
                $newGeoTargetsXml .= "<latitudeMicroDegrees>" . $circle['latitudeMicroDegrees'] . "</latitudeMicroDegrees>";
                $newGeoTargetsXml .= "<longitudeMicroDegrees>" . $circle['longitudeMicroDegrees'] . "</longitudeMicroDegrees>";
                $newGeoTargetsXml .= "<radiusMeters>" . $circle['radiusMeters'] . "</radiusMeters>";
                $newGeoTargetsXml .= "</circles>";
            }
        }
        $newGeoTargetsXml .= "</proximityTargets>";
        if (!empty($campaign['targetAll'])) {
            $newGeoTargetsXml .= "<targetAll>true</targetAll>";
        }

        // expecting array("en", "fr", "gr")
        if (strcasecmp($campaign['languages'][0], "all") != 0) {
            foreach ($campaign['languages'] as $language) {
                $languagesXml .= "<languages>" . trim($language) . "</languages>";
            }
        }
        foreach($campaign['networkTargeting'] as $networkTargeting) {
            $networkTargetingXml .=
            "<networkTypes>" . trim($networkTargeting) . "</networkTypes>";
        }

        $adSchedulingXml = "";
        if (!empty($campaign['adScheduling'])) {
            $adSchedulingXml .=
            "<schedule><status>" . $campaign['adScheduling']['status'] . "</status>";
            foreach ($campaign['adScheduling']['intervals'] as $interval) {
                $adSchedulingXml .= "<intervals>
                                 <multiplier>" . $interval['multiplier'] . "</multiplier>
                                 <day>" . $interval['day'] . "</day>
                                 <startHour>" . $interval['startHour'] . "</startHour>
                                 <startMinute>" . $interval['startMinute'] . "</startMinute>
                                 <endHour>" . $interval['endHour'] . "</endHour>
                                 <endMinute>" . $interval['endMinute'] . "</endMinute>
                               </intervals>";
            }
            $adSchedulingXml .= "</schedule>";
        }

        $budgetOptimizerSettingsXml = "";
        if (!empty($campaign['budgetOptimizerSettings'])) {
            if ($campaign['budgetOptimizerSettings']['enabled']) {
                $campaign['budgetOptimizerSettings']['enabled'] = "true";
            }
            else {
                $campaign['budgetOptimizerSettings']['enabled'] = "false";
            }
            if ($campaign['budgetOptimizerSettings']['takeOnOptimizedBids']) {
                $campaign['budgetOptimizerSettings']['takeOnOptimizedBids'] = "true";
            }
            else {
                $campaign['budgetOptimizerSettings']['takeOnOptimizedBids'] = "false";
            }
            $budgetOptimizerSettingsXml .= "<budgetOptimizerSettings>
                                          <bidCeiling>" .
            $campaign['budgetOptimizerSettings']['bidCeiling'] * EXCHANGE_RATE . "
                                          </bidCeiling>
                                          <enabled>" .
            $campaign['budgetOptimizerSettings']['enabled'] . "
                                          </enabled>
                                          <takeOnOptimizedBids>" .
            $campaign['budgetOptimizerSettings']['takeOnOptimizedBids'] . "
                                          </takeOnOptimizedBids>
                                        </budgetOptimizerSettings>";
        }
        // only send a start day if it is necessary
        $startDateXml = "";
        if (!empty($campaign['startDate'])) {
            $startDateXml = "<startDay>" . $campaign['startDate'] . "</startDay>";
        }
        else {
            $startDateXml = "";
        }

        // only send an end day if it is necessary
        $endDateXml = "";
        if (!empty($campaign['endDate'])) {
            $endDateXml = "<endDay>" . $campaign['endDate'] . "</endDay>";
        }
        else {
            $endDateXml = "";
        }

        // think in micros
        $campaign['budgetAmount'] = $campaign['budgetAmount'] * EXCHANGE_RATE;
        $soapParameters .= "<campaigns>
                            <budgetAmount>" . $campaign['budgetAmount'] . "</budgetAmount>
                            <budgetPeriod>" . $campaign['budgetPeriod'] . "</budgetPeriod>
                            <name>" . $campaign['name'] . "</name>
                            <status>" . $campaign['status'] . "</status>" .
        $startDateXml.
        $endDateXml . "
                            <networkTargeting>" .
        $networkTargetingXml . "
                            </networkTargeting>
                            <languageTargeting>" . $languagesXml . "</languageTargeting>
                            <geoTargeting>" . $newGeoTargetsXml . "</geoTargeting>" .
        $adSchedulingXml . "
                          </campaigns>";
    }
    $soapParameters .= "</addCampaignList>";
    // add the campaigns to the google servers
    $someCampaigns = $someSoapClient->call("addCampaignList", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":addCampaignList()", $soapParameters);
        return false;
    }
    $someCampaigns = makeNumericArray($someCampaigns);
    // create local objects
    $campaignObjects = array();
    foreach($someCampaigns['addCampaignListReturn'] as $someCampaign) {
        $campaignObject = receiveCampaign($someCampaign, 'addCampaignList');
        if (isset($campaignObject)) {
            array_push($campaignObjects, $campaignObject);
        }
    }
    return $campaignObjects;
}

function addCampaignsOneByOne($campaigns) {
    // this is just a wrapper to the addCampaign function
    $campaignObjects = array();

    foreach ($campaigns as $campaign) {
        $campaignObject = addCampaign(
            $campaign['name'],
            $campaign['status'],
            $campaign['startDate'],
            $campaign['endDate'],
            $campaign['budgetAmount'],
            $campaign['budgetPeriod'],
            $campaign['networkTargeting'],
            $campaign['languages'],
            $campaign['geoTargets'],
            isset($campaign['adScheduling']) ? $campaign['adScheduling'] : array(),
            isset($campaign['budgetOptimizerSettings']) ? $campaign['budgetOptimizerSettings'] : array()
        );
        array_push($campaignObjects, $campaignObject);
    }
    return $campaignObjects;
}

function getExplicitCampaignNegativeWebsiteCriteria($id) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<getCampaignNegativeCriteria>
                         <campaignId>" . $id . "</campaignId>
                       </getCampaignNegativeCriteria>";
    $allCampaignNegativeCriteria =
    $someSoapClient->call("getCampaignNegativeCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getExplicitCampaignNegativeWebsiteCriteria()", $soapParameters);
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
            $allCampaignNegativeCriteria[0] = array('url' => $saveNegativeCriteria['url']);
        }
    }
    else {
        unset($allCampaignNegativeCriteria);
        $allCampaignNegativeCriteria = array();
        foreach ($saveNegativeCriteria as $negativeCriterion) {
            if (isset($negativeCriterion['url'])) {
                array_push($allCampaignNegativeCriteria, array('url' => $negativeCriterion['url']));
            }
        }
    }
    return $allCampaignNegativeCriteria;
}

function getExplicitCampaignNegativeKeywordCriteria($id) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getCriterionClient();
    $soapParameters = "<getCampaignNegativeCriteria>
                         <campaignId>" . $id . "</campaignId>
                       </getCampaignNegativeCriteria>";
    $allCampaignNegativeCriteria = $someSoapClient->call("getCampaignNegativeCriteria", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getExplicitCampaignNegativeKeywordCriteria()", $soapParameters);
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

function receiveCampaign($someCampaign, $apiOperation, $overrideStatus = false) {
    if (($someCampaign['status'] == "Active") ||
        ($someCampaign['status'] == "Paused") ||
        ($someCampaign['status'] == "Pending") ||
        ($someCampaign['status'] == "Suspended") ||
        ($someCampaign['status'] == "Ended") ||
        ($overrideStatus)) {
        // populate class attributes
        $name = $someCampaign['name'];
        $id = $someCampaign['id'];
        $status = $someCampaign['status'];
        $startDate = $someCampaign['startDay'];
        $endDate = $someCampaign['endDay'];

        // think in currency units
        $budgetAmount = 0;
        if (isset($someCampaign['budgetAmount'])) {
            $budgetAmount = ((double) $someCampaign['budgetAmount']) / EXCHANGE_RATE;
        }
        $budgetPeriod = $someCampaign['budgetPeriod'];

        $budgetOptimizerSettings = array(
          'bidCeiling' => 0.0,
          'enabled' => false);

        if (!empty($someCampaign['budgetOptimizerSettings']['bidCeiling'])) {
            $budgetOptimizerSettings['bidCeiling'] =
            ((double) $someCampaign['budgetOptimizerSettings']['bidCeiling']) / EXCHANGE_RATE;
        }

        if (!empty($someCampaign['budgetOptimizerSettings']['enabled'])) {
            $budgetOptimizerSettings['enabled'] =
            $someCampaign['budgetOptimizerSettings']['enabled'];
        }

        $conversionOptimizerSettings = array(
          'maxCpaBidForAllAdGroups' => 0.0,
          'enabled' => false
        );

        if (!empty($someCampaign['conversionOptimizerSettings']['maxCpaBidForAllAdGroups'])) {
            $conversionOptimizerSettings['maxCpaBidForAllAdGroups'] =
            ((double) $someCampaign['conversionOptimizerSettings']['maxCpaBidForAllAdGroups']) / EXCHANGE_RATE;
        }

        if (!empty($someCampaign['conversionOptimizerSettings']['enabled'])) {
            $conversionOptimizerSettings['enabled'] =
            $someCampaign['conversionOptimizerSettings']['enabled'];
        }

        $networkTargeting = '';
        if (isset($someCampaign['networkTargeting']['networkTypes'])) {
            $networkTargeting =
            $someCampaign['networkTargeting']['networkTypes'];
        }

        $languages = '';
        if (isset($someCampaign['languageTargeting']['languages'])) {
            $languages =
            $someCampaign['languageTargeting']['languages'];
        }

        // determine the geoTargets
        $geoTargets = array(
        'countryTargets' => array('countries' => array(), 'excludedCountries' => array()),
        'regionTargets' => array('regions' => array(), 'excludedRegions' => array()),
        'metroTargets' => array('metros' => array(), 'excludedMetros' => array()),
        'cityTargets' => array('cities' => array(), 'excludedCities' => array()),
        'proximityTargets' => array('circles' => array()),
        'targetAll' => false
        );
        // countries
        if ((isset($someCampaign['geoTargeting']['countryTargets']['countries'])) &&
            (is_array($someCampaign['geoTargeting']['countryTargets']['countries']))) {
            foreach ($someCampaign['geoTargeting']['countryTargets']['countries'] as $country) {
                array_push($geoTargets['countryTargets']['countries'], $country);
            }
        }
        else if (isset($someCampaign['geoTargeting']['countryTargets']['countries'])) {
            array_push($geoTargets['countryTargets']['countries'], $someCampaign['geoTargeting']['countryTargets']['countries']);
        }
        // excludedCountries
        if ((isset($someCampaign['geoTargeting']['countryTargets']['excludedCountries'])) &&
            (is_array($someCampaign['geoTargeting']['countryTargets']['excludedCountries']))) {
            foreach ($someCampaign['geoTargeting']['countryTargets']['excludedCountries'] as $excludedCountry) {
                array_push($geoTargets['countryTargets']['excludedCountries'], $excludedCountry);
            }
        }
        else if (isset($someCampaign['geoTargeting']['countryTargets']['excludedCountries'])) {
            array_push($geoTargets['countryTargets']['excludedCountries'], $someCampaign['geoTargeting']['countryTargets']['excludedCountries']);
        }
        // regions
        if ((isset($someCampaign['geoTargeting']['regionTargets']['regions'])) &&
            (is_array($someCampaign['geoTargeting']['regionTargets']['regions']))) {
            foreach ($someCampaign['geoTargeting']['regionTargets']['regions'] as $region) {
                array_push($geoTargets['regionTargets']['regions'], $region);
            }
        }
        else if (isset($someCampaign['geoTargeting']['regionTargets']['regions'])) {
            array_push($geoTargets['regionTargets']['regions'], $someCampaign['geoTargeting']['regionTargets']['regions']);
        }
        // excludedRegions
        if ((isset($someCampaign['geoTargeting']['regionTargets']['excludedRegions'])) &&
            (is_array($someCampaign['geoTargeting']['regionTargets']['excludedRegions']))) {
            foreach ($someCampaign['geoTargeting']['regionTargets']['excludedRegions'] as $excludedRegion) {
                array_push($geoTargets['regionTargets']['excludedRegions'], $excludedRegion);
            }
        }
        else if (isset($someCampaign['geoTargeting']['regionTargets']['excludedRegions'])) {
            array_push($geoTargets['regionTargets']['excludedRegions'], $someCampaign['geoTargeting']['regionTargets']['excludedRegions']);
        }
        // metros
        if ((isset($someCampaign['geoTargeting']['metroTargets']['metros'])) &&
            (is_array($someCampaign['geoTargeting']['metroTargets']['metros']))) {
            foreach ($someCampaign['geoTargeting']['metroTargets']['metros'] as $metro) {
                array_push($geoTargets['metroTargets']['metros'], $metro);
            }
        }
        else if (isset($someCampaign['geoTargeting']['metroTargets']['metros'])) {
            array_push($geoTargets['metroTargets']['metros'], $someCampaign['geoTargeting']['metroTargets']['metros']);
        }
        // excludedMetros
        if ((isset($someCampaign['geoTargeting']['metroTargets']['excludedMetros'])) &&
            (is_array($someCampaign['geoTargeting']['metroTargets']['excludedMetros']))) {
            foreach ($someCampaign['geoTargeting']['metroTargets']['excludedMetros'] as $excludedMetro) {
                array_push($geoTargets['metroTargets']['excludedMetros'], $excludedMetro);
            }
        }
        else if (isset($someCampaign['geoTargeting']['metroTargets']['excludedMetros'])) {
            array_push($geoTargets['metroTargets']['excludedMetros'], $someCampaign['geoTargeting']['metroTargets']['excludedMetros']);
        }
        // cities
        if ((isset($someCampaign['geoTargeting']['cityTargets']['cities'])) &&
            (is_array($someCampaign['geoTargeting']['cityTargets']['cities']))) {
            foreach ($someCampaign['geoTargeting']['cityTargets']['cities'] as $city) {
                array_push($geoTargets['cityTargets']['cities'], $city);
            }
        }
        else if (isset($someCampaign['geoTargeting']['cityTargets']['cities'])) {
            array_push($geoTargets['cityTargets']['cities'], $someCampaign['geoTargeting']['cityTargets']['cities']);
        }
        // excludedCities
        if ((isset($someCampaign['geoTargeting']['cityTargets']['excludedCities'])) &&
            (is_array($someCampaign['geoTargeting']['cityTargets']['excludedCities']))) {
            foreach ($someCampaign['geoTargeting']['cityTargets']['excludedCities'] as $excludedCity) {
                array_push($geoTargets['cityTargets']['excludedCities'], $excludedCity);
            }
        }
        else if (isset($someCampaign['geoTargeting']['cityTargets']['excludedCities'])) {
            array_push($geoTargets['cityTargets']['excludedCities'], $someCampaign['geoTargeting']['cityTargets']['excludedCities']);
        }
        // circles
        if ((isset($someCampaign['geoTargeting']['proximityTargets']['circles'])) &&
            (is_array($someCampaign['geoTargeting']['proximityTargets']['circles']))) {
            foreach ($someCampaign['geoTargeting']['proximityTargets']['circles'] as $circle) {
                array_push($geoTargets['proximityTargets']['circles'], $circle);
            }
        }
        else if (isset($someCampaign['geoTargeting']['proximityTargets']['circles'])) {
            array_push($geoTargets['proximityTargets']['circles'], $someCampaign['geoTargeting']['proximityTargets']['circles']);
        }
        // targetAll
        if (isset($someCampaign['geoTargeting']['targetAll'])) {
            $geoTargets['targetAll'] = $someCampaign['geoTargeting']['targetAll'];
        }

        $adScheduling = array();
        if (isset($someCampaign['schedule']['status'])) {
            $adScheduling['status'] = $someCampaign['schedule']['status'];
        }
        if (strcasecmp($someCampaign['schedule']['status'], "Disabled") != 0 ) {
            if (!isset($someCampaign['schedule']['intervals']['day'])) {
                $adScheduling['intervals'] = array();
                foreach ($someCampaign['schedule']['intervals'] as $interval) {
                    array_push($adScheduling['intervals'], $interval);
                }
            }
            else if (isset($someCampaign['schedule']['intervals']['day'])) {
                $adScheduling['intervals'] = array();
                array_push($adScheduling['intervals'], $someCampaign['schedule']['intervals']);
            }
        }

        if (IS_ENABLED_OPTIMIZED_AD_SERVING_ATTRIBUTE) {
            // isEnabledOptimizedAdServing?
            // this is not an object attribute but we make it be one. as we can change
            // it we want to see its value
            $soapParameters = "<getOptimizeAdServing>
                             <campaignId>" . $id . "</campaignId>
                           </getOptimizeAdServing>";
            // query the google servers whether the campaign is optimize adserving
            $soapClients = &APIlityClients::getClients();
            $someSoapClient = $soapClients->getCampaignClient();
            $isEnabledOptimizedAdServing = $someSoapClient->call("getOptimizeAdServing", $soapParameters);
            $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
            if ($someSoapClient->fault) {
                pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":" . $apiOperation . "()", $soapParameters);
                return false;
            }

            $isEnabledOptimizedAdServing =
            isset($isEnabledOptimizedAdServing['getOptimizeAdServingReturn']) ?
            $isEnabledOptimizedAdServing['getOptimizeAdServingReturn'] : false;
        }
        else {
            $isEnabledOptimizedAdServing = NULL;
        }
        $campaignNegativeKeywordCriteria = null;
        $campaignNegativeWebsiteCriteria = null;
        if (INCLUDE_CAMPAIGN_NEGATIVE_CRITERIA) {
            $campaignNegativeKeywordCriteria =
            getExplicitCampaignNegativeKeywordCriteria($id);
            $campaignNegativeWebsiteCriteria =
            getExplicitCampaignNegativeWebsiteCriteria($id);
        }
        // end of populate class attributes

        // now we can create the object
        $campaignObject = new APIlityCampaign (
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
            $campaignNegativeKeywordCriteria,
            $campaignNegativeWebsiteCriteria,
            $adScheduling,
            $budgetOptimizerSettings,
            $conversionOptimizerSettings
        );
        return $campaignObject;
    }
    else if (RETURN_DELETED_OBJECTS && $someCampaign['status'] == 'Deleted') {
        return receiveCampaign($someCampaign, $apiOperation, true);
    }
}
?>