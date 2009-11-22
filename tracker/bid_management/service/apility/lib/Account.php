<?php
  function getManagersClientAccounts() {
    $authenticationContext = &APIlityManager::getContext();
    // we want to get the current manager's account clients so temporarily unset
    // any eventually existing clientEmail setting
    if (is_a($authenticationContext, 'APIlityUser')) {
      $savedClientEmail = $authenticationContext->getClientEmail();
      $authenticationContext->setClientEmail("");
    }
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAccountClient();
    // prepare soap parameters
    $soapParameters = "<getClientAccounts></getClientAccounts>";
    // execute soap call
    $clientAccounts = $someSoapClient->call("getClientAccounts", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getManagersClientAccounts()", $soapParameters);
      // in case of an error make sure that at least the clientEmail gets restored
      if (is_a($authenticationContext, 'APIlityUser')) {      
        $authenticationContext->setClientEmail($savedClientEmail);
      }
      return false;
    }
    // restore clientEmail
    if (is_a($authenticationContext, 'APIlityUser')) {    
      $authenticationContext->setClientEmail($savedClientEmail);
    }
    // make sure we really return an array
    if (isset($clientAccounts['getClientAccountsReturn'])) {
      $clientAccounts = makeNumericArray($clientAccounts);
      return $clientAccounts['getClientAccountsReturn'];
    }
    else {
      return array();
    }
  }

  function getClientsClientAccounts() {
    $soapClients = &APIlityClients::getClients();
    $authenticationContext = &APIlityManager::getContext();
    $someSoapClient = $soapClients->getAccountClient();
    // we want to get the current client's account clients so make sure that the
    // clientEmail is set at all
    if ((is_a($authenticationContext, 'APIlityUser')) &&
        ($authenticationContext->getClientEmail())) {
      // prepare soap parameters
      $soapParameters = "<getClientAccounts></getClientAccounts>";
      // execute soap call
      $clientAccounts = $someSoapClient->call("getClientAccounts", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getClientsClientAccounts()", $soapParameters);
        return false;
      }
      // make sure we really return an array    
      if (isset($clientAccounts['getClientAccountsReturn'])) {        
        $clientAccounts = makeNumericArray($clientAccounts);          
        return $clientAccounts['getClientAccountsReturn'];
      }
      else {
        return array();
      }
    }
    else return false;
  }

  function getClientsClientAccountInfos() {
    $soapClients = &APIlityClients::getClients();
    $authenticationContext = &APIlityManager::getContext();
    $someSoapClient = $soapClients->getAccountClient();
    // we want to get the current client's account clients so make sure that the
    // clientEmail is set at all
    if ((is_a($authenticationContext, 'APIlityUser')) &&
        ($authenticationContext->getClientEmail())) {
      // prepare soap parameters
      $soapParameters = "<getClientAccountInfos></getClientAccountInfos>";
      // execute soap call
      $clientAccounts = $someSoapClient->call("getClientAccountInfos", $soapParameters);
      $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
      if ($someSoapClient->fault) {
        pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getClientsClientAccountInfos()", $soapParameters);
        return false;
      }
      // make sure we really return an array      
      if ((isset($clientAccounts['getClientAccountInfosReturn'])) &&
          (is_array($clientAccounts['getClientAccountInfosReturn']))) {
        return $clientAccounts['getClientAccountInfosReturn'];
      }
      else {
        return array();  
      }          
    }
    else return false;
  }

  function getManagersClientAccountInfos() {
    $authenticationContext = &APIlityManager::getContext();
    // we want to get the current manager's account clients so temporarily unset
    // any eventually existing clientEmail setting
    if (is_a($authenticationContext, 'APIlityUser')) {
      $savedClientEmail = $authenticationContext->getClientEmail();
      $authenticationContext->setClientEmail("");
    }
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAccountClient();
    // prepare soap parameters
    $soapParameters = "<getClientAccountInfos></getClientAccountInfos>";
    // execute soap call
    $clientAccounts = $someSoapClient->call("getClientAccountInfos", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getManagersClientAccountInfos()", $soapParameters);
      // in case of an error make sure that at least the clientEmail gets restored
      if (is_a($authenticationContext, 'APIlityUser')) {      
        $authenticationContext->setClientEmail($savedClientEmail);
      }
      return false;
    }
    // restore clientEmail
    if (is_a($authenticationContext, 'APIlityUser')) {    
      $authenticationContext->setClientEmail($savedClientEmail);
    }
    // make sure we really return an array
    if ((isset($clientAccounts['getClientAccountInfosReturn'])) &&
        (is_array($clientAccounts['getClientAccountInfosReturn']))) {
      return $clientAccounts['getClientAccountInfosReturn'];
    }
    else {
      return array();  
    }
  }

  function getAccountInfo() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAccountClient();
    // prepare soap parameters
    $soapParameters = "<getAccountInfo></getAccountInfo>";
    // execute soap call
    $accountInfo = $someSoapClient->call("getAccountInfo", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getAccountInfo()", $soapParameters);
      return false;
    }
    return $accountInfo['getAccountInfoReturn'];
  }

  function getMccAlerts() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAccountClient();
    // prepare soap parameters
    $soapParameters = "<getMccAlerts></getMccAlerts>";
    // execute soap call
    $mccAlerts = $someSoapClient->call("getMccAlerts", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getMccAlerts()", $soapParameters);
      return false;
    }
    // make sure we return an array
    if (isset($mccAlerts['getMccAlertsReturn'])) {
      $mccAlerts['getMccAlertsReturn'] = makeNumericArray($mccAlerts['getMccAlertsReturn']);  
      return $mccAlerts['getMccAlertsReturn'];      
    }
    return array();
  }  

  function updateAccountInfo(
    $defaultNetworkTargeting,
    $descriptiveName,
    $emailPromotionsPreferences,
    $languagePreference,
    $primaryBusinessCategory
  ) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getAccountClient();
    // prepare soap parameters
    if ($defaultNetworkTargeting) {
      $defaultNetworkTargetingXml =
          "<defaultNetworkTargeting>" . $defaultNetworkTargeting . "</defaultNetworkTargeting>";
    }
    else {
      $defaultNetworkTargetingXml = '';
    }
    if ($descriptiveName) {
      $descriptiveNameXml =
          "<descriptiveName>" . $descriptiveName . "</descriptiveName>";
    }
    else {
      $descriptiveNameXml = '';
    }
    if ($emailPromotionsPreferences) {
      $emailPromotionsPreferencesXml =
          "<emailPromotionsPreferences>" . $emailPromotionsPreferences . "</emailPromotionsPreferences>";
    }
    else {
      $emailPromotionsPreferencesXml = '';
    }
    if ($languagePreference) {
      $languagePreferenceXml =
          "<languagePreference>" . $languagePreference . "</languagePreference>";
    }
    else {
      $languagePreferenceXml = '';
    }
    if ($primaryBusinessCategory) {
      $primaryBusinessCategoryXml =
          "<primaryBusinessCategory>" . $primaryBusinessCategory . "</primaryBusinessCategory>";
    }
    else {
      $primaryBusinessCategoryXml = '';
    }
    $soapParameters = "<updateAccountInfo>
                          <account>" . 
                            $defaultNetworkTargetingXml.
                            $descriptiveNameXml.
                            $emailPromotionsPreferencesXml.
                            $languagePreferenceXml.
                            $primaryBusinessCategoryXml . "
                         </account>
                       </updateAccountInfo>";
    // execute soap call
    $accountInfo = $someSoapClient->call("updateAccountInfo", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":updateAccountInfo()", $soapParameters);
      return false;
    }
    return true;
  }

  function createEmailPreferencesXml(
    $marketResearchEnabled,
    $newsletterEnabled,
    $promotionsEnabled,
    $accountPerformanceEnabled,
    $disapprovedAdsEnabled
  ) {
    if ($marketResearchEnabled) {
      $marketResearchEnabled = "true";
    }
    else {
      $marketResearchEnabled = "false";
    }
    if ($newsletterEnabled) {
      $newsletterEnabled = "true";
    }
    else {
      $newsletterEnabled = "false";
    }
    if ($promotionsEnabled) {
      $promotionsEnabled = "true";
    }
    else {
      $promotionsEnabled = "false";
    }
    if ($accountPerformanceEnabled) {
      $accountPerformanceEnabled = "true";
    }
    else {
      $accountPerformanceEnabled = "false";
    }
    if ($disapprovedAdsEnabled) {
      $disapprovedAdsEnabled = "true";
    }
    else {
      $disapprovedAdsEnabled = "false";
    }
    return   "<marketResearchEnabled>" . $marketResearchEnabled . "</marketResearchEnabled>
              <newsletterEnabled>" . $newsletterEnabled . "</newsletterEnabled>
              <promotionsEnabled>" . $promotionsEnabled . "</promotionsEnabled>
              <accountPerformanceEnabled>" . $accountPerformanceEnabled . "</accountPerformanceEnabled>
              <disapprovedAdsEnabled>" . $disapprovedAdsEnabled . "</disapprovedAdsEnabled>";
  }

  function createDefaultNetworkTargetingXml($defaultNetworkTargeting) {
    $defaultNetworkTargetingXml = '';
    foreach($defaultNetworkTargeting as $networkTarget) {
      $defaultNetworkTargetingXml .= "<networkTypes>" . $networkTarget . "</networkTypes>";
    }
    return $defaultNetworkTargetingXml;
  }
?>