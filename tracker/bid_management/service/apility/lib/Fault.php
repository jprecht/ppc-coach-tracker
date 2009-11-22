<?php
  // this defines the maximum size of the fault stack
  // only the last N fault objects are kept
  define("FAULT_STACK_SIZE", 10);

  class APIlityFault {
    // class attributes
    // supplied by the API
    var $code;
    var $message;
    var $trigger;
    var $errors;
    // supplied by APIlity
    var $faultOrigin;
    var $soapParameters;

    // constructor
    function APIlityFault (
        $code,
        $message,
        $trigger,
        $errors,
        $faultOrigin,
        $soapParameters
    ) {
      $this->code = $code;
      $this->message = $message;
      $this->trigger = $trigger;
      $this->errors = $errors;
      $this->faultOrigin = $faultOrigin;
      $this->soapParameters = preg_replace('/\s\s+/', '', $soapParameters);
    }

    // get functions
    function getCode() {
      return $this->code;
    }

    function getMessage() {
      return $this->message;
    }

    function getTrigger() {
      return $this->trigger;
    }

    function getErrors() {
      return $this->errors;
    }

    function getFaultOrigin() {
      return $this->faultOrigin;
    }

    function getSoapParameters() {
      return $this->soapParameters;
    }

    function printFault() {
      echo $this->getFault();
    }
    
    function &getFaultStack() {
      static $faultStack = array();
      return $faultStack;  
    }

    // this provides error reporting depending on the way errors should be
    // reported (settings.ini)
    function getFault() {
      // return debuggable fault objects
      if (strcmp(DISPLAY_ERROR_STYLE, "Plaintext") == 0) {
        $allErrors = "";
        foreach($this->getErrors() as $error) {
          $allErrors .= "
\tCode: " . (isset($error['code']) ? $error['code'] : '') . "
\tDetail: " . (isset($error['detail']) ? $error['detail'] : '') . "
\tIs Exemptable: " . (isset($error['isExemptable']) ? $error['isExemptable'] : '') . "
\tField: " . (isset($error['field']) ? $error['field'] : '') . "
\tIndex: " . (isset($error['index']) ? $error['index'] : '') . "
\tText Index: " . (isset($error['textIndex']) ? $error['textIndex'] : '') . "
\tText Length: " . (isset($error['textLength']) ? $error['textLength'] : '') . "
\tTrigger: " . (isset($error['trigger']) ? $error['trigger'] : '') . "\n";
        }
        if ($allErrors == "") $allErrors = "\n";
        return "
Code: " . $this->getCode() . "
Message: " . $this->getMessage() . "
Trigger: " . $this->getTrigger() . "
Errors:"
.$allErrors.
"Fault Origin: " . $this->getFaultOrigin() . "
SOAP Parameters: " . $this->getSoapParameters() . "\n\n";
      }

      // return beautiful html errors
      else if (strcmp(DISPLAY_ERROR_STYLE, "HTML") == 0) {
        $faultMessage =
            "<div style='padding:0.5em; margin:0.5em; border:1px dashed gray;'>
            <small>\n<font color='blue'><b>Ouch!</b></font> I am not proud to
            announce the following <font color='maroon'><u>AdWords API Exception</u></font>:\n<br />";
        $faultMessage .=
            "&nbsp;&nbsp;<b>Message:</b> " . $this->getMessage() . "\n<br />\n";
        $faultMessage .=
            "&nbsp;&nbsp;<b>Trigger:</b> " . $this->getTrigger() . "\n<br />\n";
        $faultMessage .=
            "&nbsp;&nbsp;<b>Code:</b> <font color='blue'>"
            . $this->getCode() . "</font>\n<br />\n";
        $faultMessage .=
            "&nbsp;&nbsp;<b>Fault Origin:</b> <font color='blue'>"
            . $this->getFaultOrigin() . "</font>\n<br />";

        // pseudo syntax highlighting of the SOAP parameters
        $style =
            "<style type='text/css'>
               .string {
                 font-family:monospace;
                 color:darkgreen;
               }
               .tag {
                 font-family:monospace;
                 color:blue;
               }
             </style>";
        echo $style;
        $this->soapParameters =
          str_replace("'", "&quot;", htmlspecialchars($this->getSoapParameters()));
        $this->soapParameters =
          preg_replace('/(&quot;.*?&quot;)/s', '<span class="string">\1</span>', $this->getSoapParameters());
        $this->soapParameters =
            preg_replace('/(&lt;[a-zA-Z0-9]*&gt;)/s', '<span class="tag">\1</span>', $this->getSoapParameters());
        $this->soapParameters =
          preg_replace('/(&lt;\/.*?&gt;)/s', '<span class="tag">\1</span><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $this->getSoapParameters());
        $this->soapParameters =
          preg_replace('/\n/s', '<br />\1', $this->getSoapParameters());
        $faultMessage .=
            "&nbsp;&nbsp;<b>SOAP Parameters:</b>\n<br />\n
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
            .utf8_encode(str_replace("€", "&#x20AC;", $this->getSoapParameters()))
             . "\n";
        if (sizeOf($this->getErrors()) > 0) {
          $faultMessage .= "<br />\n<b>AdWords API Error(s):</b>\n<br />\n";
          $allErrors = "";
          $i = 0;
          foreach($this->getErrors() as $error) {
            $allErrors .= "&nbsp;&nbsp;<b>Violation #" . $i . "</b><br />";
            $allErrors .= "
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Code:</b> " . (isset($error['code']) ? $error['code'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail:</b> " . (isset($error['detail']) ? $error['detail'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Is Exemptable:</b> " . (isset($error['isExemptable']) ? $error['isExemptable'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Field:</b> " . (isset($error['field']) ? $error['field'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Index:</b> " . (isset($error['index']) ? $error['index'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Text Index:</b> " . (isset($error['textIndex']) ? $error['textIndex'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Text Length:</b> " . (isset($error['textLength']) ? $error['textLength'] : '') . "<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Trigger:</b> " . (isset($error['trigger']) ? $error['trigger'] : '') . "\n<br />&nbsp;<br />";
            $i++;
          }
          $faultMessage .= $allErrors;
        }
        $faultMessage .= "</small></div>";
        return $faultMessage;
      }

      // return xml fault messages
      else if (strcmp(DISPLAY_ERROR_STYLE, "XML") == 0) {
        $xml =   "
<adWordsApiError>
  <code>" . $this->getCode() . "</code>
  <message>" . $this->getMessage() . "</message>
  <trigger>" . $this->getTrigger() . "</trigger>
  <faultOrigin>" . $this->getFaultOrigin() . "</faultOrigin>
  <soapParameters>\n\t\t"  . str_replace('€', '&#x20AC;', $this->getSoapParameters()) . "\n\t</soapParameters>";
  if (sizeOf($this->getErrors()) > 0) {
    $allErrors = "<errors>\n";
    foreach($this->getErrors() as $error) {
      $allErrors .= "<error>
  <code>" . (isset($error['code']) ? $error['code'] : '') . "</code>\n
  <detail>" . (isset($error['detail']) ? $error['detail'] : '') . "</detail>\n
  <isExemptable>" . (isset($error['isExemptable']) ? $error['isExemptable'] : '') . "</isExemptable>\n
  <field>" . (isset($error['field']) ? $error['field'] : '') . "</field>\n
  <index>" . (isset($error['index']) ? $error['index'] : '') . "</index>\n
  <textIndex>" . (isset($error['textIndex']) ? $error['textIndex'] : '') . "</textIndex>\n
  <textLength>" . (isset($error['textLength']) ? $error['textLength'] : '') . "</textLength>\n
  <trigger>" . (isset($error['trigger']) ? $error['trigger'] : '') . "</trigger>\n</error>\n";
    }
    $xml .= $allErrors . "\n</errors>\n";
  }
  $xml .= "\n</adWordsApiError>\n";
        return utf8_encode($xml);
      }
      return false;
    }    
  }  
  
  function pushFault($someSoapClient, $faultOrigin, $soapParameters) {
    // avoid annoying warnings as not all values are always set
    // API Exception level
    // supplied by the API
    if (!isset($someSoapClient->detail['fault']['code']))
        $someSoapClient->detail['fault']['code'] = "N/A";
    if (!isset($someSoapClient->detail['fault']['message']))
        $someSoapClient->detail['fault']['message'] = $someSoapClient->getError();
    if (!isset($someSoapClient->detail['fault']['trigger']))
        $someSoapClient->detail['fault']['trigger'] = "N/A";
    if (!isset($someSoapClient->detail['fault']['errors']))
        $someSoapClient->detail['fault']['errors'] = array();
    if (isset($someSoapClient->detail['fault']['errors']['code'])) {
      $saveFault = $someSoapClient->detail['fault']['errors'];
      unset($someSoapClient->detail['fault']['errors']);
      $someSoapClient->detail['fault']['errors'][0] = $saveFault;
    }
    // supplied by APIlity
    if (!isset($faultOrigin)) $faultOrigin = "N/A";
    if (!isset($soapParameters)) $soapParameters = "N/A";

    // create a fault object
    $faultObject = new APIlityFault(
        $someSoapClient->detail['fault']['code'],
        $someSoapClient->detail['fault']['message'],
        $someSoapClient->detail['fault']['trigger'],
        $someSoapClient->detail['fault']['errors'],
        $faultOrigin,
        $soapParameters
    );

    // by default print the fault message, else when running in silence
    // stealth mode be quiet
    if (!SILENCE_STEALTH_MODE) $faultObject->printFault();

    // access the error stack
    $faultStack = &APIlityFault::getFaultStack();
    // push the fault object in the fault stack and keep only the last
    // #FAULT_STACK_SIZE error messages
    array_push($faultStack, $faultObject);
    if (sizeof($faultStack) <= FAULT_STACK_SIZE) return;
    while (sizeof($faultStack) > FAULT_STACK_SIZE) {
      array_shift($faultStack);
    }
  }  
?>