<?php
  function getUnitCountForClients($startDate, $endDate, $clientEmails) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $clientEmailsXml = "";
    foreach($clientEmails as $clientEmail) {
      $clientEmailsXml .= "<clientEmails>" . $clientEmail . "</clientEmails>";
    }
    $soapParameters = "<getUnitCountForClients>" . 
                         $clientEmailsXml . "
                         <startDate>" . $startDate . "</startDate>
                         <endDate>" . $endDate . "</endDate>
                       </getUnitCountForClients>";
    // query the google servers for the unit count for clients
    $unitCount = $someSoapClient->call("getUnitCountForClients", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getUnitCountForClients()", $soapParameters);
      return false;
    }
    return $unitCount['getUnitCountForClientsReturn'];
  }

  function getOperationCount($startDate, $endDate) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getOperationCount>
                         <startDate>" . $startDate . "</startDate>
                         <endDate>" . $endDate . "</endDate>
                       </getOperationCount>";
    // query the google servers for the operation count
    $operationCount = $someSoapClient->call("getOperationCount", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getOperationCount()", $soapParameters);
      return false;
    }
    return $operationCount['getOperationCountReturn'];
  }

  function getUnitCount($startDate, $endDate) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getUnitCount>
                         <startDate>" . $startDate . "</startDate>
                         <endDate>" . $endDate . "</endDate>
                        </getUnitCount>";
    // query the google servers for the unit count
    $unitCount = $someSoapClient->call("getUnitCount", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getUnitCount()", $soapParameters);
      return false;
    }
    return $unitCount['getUnitCountReturn'];
  }

  function getOperationsQuotaThisMonth() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getOperationsQuotaThisMonth></getOperationsQuotaThisMonth>";
    // query the google servers for this month's operation quota
    $operationsQuotaThisMonth = $someSoapClient->call("getOperationsQuotaThisMonth", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getOperationsQuotaThisMonth()", $soapParameters);
      return false;
    }
    return $operationsQuotaThisMonth['getOperationsQuotaThisMonthReturn'];
  }

  function getUnitCountForMethod($service, $method, $startDate, $endDate) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getUnitCountForMethod>
                         <service>" . $service . "</service>
                         <method>" . $method . "</method>
                         <startDate>" . $startDate . "</startDate>
                         <endDate>" . $endDate . "</endDate>
                       </getUnitCountForMethod>";
    // query the google servers for the unit count
    $unitCountForMethod = $someSoapClient->call("getUnitCountForMethod", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getUnitCountForMethod()", $soapParameters);
      return false;
    }
    return $unitCountForMethod['getUnitCountForMethodReturn'];
  }

  function getMethodCost($service, $method, $date) {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getMethodCost>
                         <service>" . $service . "</service>
                         <method>" . $method . "</method>
                         <date>" . $date . "</date>
                       </getMethodCost>";
    // query the google servers for the method cost
    $methodCost = $someSoapClient->call("getUnitCountForMethod", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getMethodCost()", $soapParameters);
      return false;
    }
    return $methodCost['getMethodCostReturn'];
  }

  function getUsageQuotaThisMonth() {
    $soapClients = &APIlityClients::getClients();
    $someSoapClient = $soapClients->getInfoClient();
    $soapParameters = "<getUsageQuotaThisMonth></getUsageQuotaThisMonth>";
    $usageQuotaThisMonth = $someSoapClient->call("getUsageQuotaThisMonth", $soapParameters);
    $soapClients->updateSoapRelatedData(extractSoapHeaderInfo($someSoapClient->getHeaders()));
    if ($someSoapClient->fault) {
      pushFault($someSoapClient, $_SERVER['PHP_SELF'] . ":getUsageQuotaThisMonth()", $soapParameters);
      return false;
    }
    return $usageQuotaThisMonth['getUsageQuotaThisMonthReturn'];
  }
?>