In order for apility to work correctly the following modifications to the file nusoap.php have been made:

1) Class and object name "soapclient" have been changed to "soapclientNusoap"
This was made in order to avoid namespace problems when PHP's native SOAP is enabled

2) The original setting "var $soap_defencoding = 'ISO-8859-1';" has been changed to "var $soap_defencoding = 'UTF-8';"
This was made in order to avoid problems with uploading the Euro sign (€)

3) The original setting "var $decode_utf8 = true;" has been changed to "var $decode_utf8 = false;"
This was made because we want raw UTF-8 that, e.g., might not map to iso-8859-1 like the Euro sign (€)

4) Some modifications to the serializeEnvelope function have been made
    // Begin modification for APIlity
        $body = str_replace("&", "&amp;", $body);
        $body = str_replace("€", "&#8364;", $body);    	
        $body = utf8_encode($body);
    // End modification
This was made as a work-around of several PHP Unicode-related problems and the way the API accepts data.

5) The original getHeaders() function was renamed to getHeadersOriginal and the modified
getHeaders() function was modified in order to return not only the headers, but also the request and the
response.
  // was function getHeaders()
    function getHeadersOriginal(){
      return $this->responseHeaders;
    }
  // modified getHeaders()
    function getHeaders(){
      return array('headers' => $this->responseHeaders, 'request' => $this->request, 'response' => $this->response);
    }