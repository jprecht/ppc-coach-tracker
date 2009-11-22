<?php
  function xmlEscape($xml) {
    $xml = str_replace("<", "&lt;", $xml);	
    $xml = str_replace(">", "&gt;", $xml);	
    $xml = str_replace("&", "&amp;", $xml);	
    $xml = str_replace("'", "&apos;", $xml);	
    $xml = str_replace('"', "&quot;", $xml);		
	  return $xml;
  }

  // this function extracts the amount of units and operations and the response
  // time from the returned SOAP header
  function extractSoapHeaderInfo($soapHeaderXml) {
    $units = substr(
        $soapHeaderXml['headers'],
        0,
        strpos($soapHeaderXml['headers'], "</units>")
    );
    $units = substr($units, strrpos($units, ">")+1);

    $operations = substr(
        $soapHeaderXml['headers'],
        0,
        strpos($soapHeaderXml['headers'], "</operations>")
    );
    $operations = substr($operations, strrpos($operations, ">")+1);

    $responseTime = substr(
        $soapHeaderXml['headers'],
        0,
        strpos($soapHeaderXml['headers'], "</responseTime>")
    );
    $responseTime = substr($responseTime, strrpos($responseTime, ">")+1);

    return array(
        'responseTime' => $responseTime,
        'operations' => $operations,
        'units' => $units,
        'request' => $soapHeaderXml['request'],
        'response' => $soapHeaderXml['response']
    );
  }

  function checkApilityRequirements() {
    $errorMessage = '';
    $requirementsFulfilled = false;

    // check if curl exists
    if (function_exists("curl_version")) {
      $requirementsFulfilled = true;
    }
    else $errorMessage .= "<b>APIlity PHP library => Warning:</b> APIlity requires <b>cURL</b>. Please make sure it is installed. <a href='http://google-apility.sourceforge.net/index.html#Requirements' target='_blank'>Instructions</a> here. This warning can be ignored if you do not need any of the <b>Report functionalities</b>";

    // PHP version is < 5, check if DOM_XML exists
    if (version_compare(phpversion(), "5.0.0", "<"))  {
      if (!function_exists("domxml_open_mem")) {
        $requirementsFulfilled = false;
        $errorMessage .= "<b>APIlity PHP library => Warning:</b> APIlity requires <b>DOM XML</b> when you are running PHP4.x. Please make sure it is installed. <a href='http://google-apility.sourceforge.net/index.html#Requirements' target='_blank'>Instructions</a> here. This warning can be ignored if you do not need any of the <b>Report functionalities</b>";
      }
      if (IS_ENABLED_OO_MODE) {
        $requirementsFulfilled = false;
        $errorMessage .= "<b>APIlity PHP library => Warning:</b> APIlity <b>does not work in Object-Oriented Mode</b> when you are running PHP4.x. Either turn Object-Oriented Mode off (<a href='http://google-apility.sourceforge.net/index.html#Object_Orientation' target='_blank'>Instructions</a> here), or upgrade to PHP5";
      }      
    }
    return array(
      'requirementsFulfilled' => $requirementsFulfilled,
      'errorMessage' => $errorMessage
    );
  }

  // this function accepts an image file name and returns the base64 encoded
  // version of the file
  function img2base64($fileName) {
    if (file_exists($fileName)) {
      $handle = fopen($fileName,'rb');
      $fileContent = fread($handle, filesize($fileName));
      fclose($handle);
      return chunk_split(base64_encode($fileContent));
    }
    else {
      if (!SILENCE_STEALTH_MODE) trigger_error('<b>APIlity PHP library => Warning:</b> Sorry, but the specified file '.addslashes($fileName).' does not exist', E_USER_WARNING);
      return false;
    }
  }

  // PHP converts any non-empty string to true. even "false" is converted to
  // true. Additionally false doesn't print to the screen but results in an
  // empty string. We don't want all this so we convert "false" and false to 0
  // and everything else to 1.
  function convertBool($bool) {
    // make sure strings ("true", "false") are converted to boolean
    if (is_string($bool)) {
      if (strcasecmp(trim($bool), "false") == 0) $bool = 0; else $bool = 1;
    }
    // make sure booleans are converted to integers because the boolean false
    // is not printed to the screen
    if (is_bool($bool)) {
      if ($bool) $bool = 1; else $bool = 0;
    }
    return $bool;
  }

  function convertToArray(&$wannabeArray) {
    if (!is_array($wannabeArray)) {
      $saveValue = $wannabeArray;
      unset($wannabeArray);
      $wannabeArray = array();
      if ($saveValue != '') array_push($wannabeArray, $saveValue);
    }
    return $wannabeArray;
  }

  function makeNumericArray($someArray) {
    if (empty($someArray)) {
      return $someArray;
    }
    $apiArrayKeys = array_keys($someArray);
    if (is_array($apiArrayKeys) && isset($apiArrayKeys[0])) {
      $apiArrayKey = $apiArrayKeys[0];
      if (!array_key_exists(0, $someArray[$apiArrayKey])) {
        $saveArray = $someArray[$apiArrayKey];
        unset($someArray);
        $someArray[$apiArrayKey][0] = $saveArray;
      }
    }
    return $someArray;
  }
?>