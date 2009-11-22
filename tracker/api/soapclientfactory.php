<?php

// Copyright 2008, Google Inc. All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


/**
 * This class will create an instance of nusoap soapclient for making SOAP 
 * calls PHP5 introduced php_soap extension, which introduced a new class 
 * called soapclient. This caused nusoap library to rename their classes to 
 *nusoap_client. SoapClientFactory will hide this complexity from the user.
 */

//require_once('/usr/lib/php/nusoap.php');
require_once('lib/nusoap.php');

class SoapClientFactory{
  public static function GetClient(
    $endpoint, $wsdl = false, $proxyhost = false, $proxyport = false, 
    $proxyusername = false, $proxypassword = false, $timeout = 0, 
    $response_timeout = 30) {
    if (!extension_loaded('soap')) {
      return new soapclient($endpoint, $wsdl, $proxyhost, $proxyport, 
        $proxyusername, $proxypassword, $timeout, $response_timeout);
    } else {
      return new nusoap_client($endpoint, $wsdl, $proxyhost, $proxyport, 
        $proxyusername, $proxypassword, $timeout, $response_timeout);
    }
  }
}
?>

