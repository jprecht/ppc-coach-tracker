<?php
  // thanks Dietrich Ayala and Scott Nichol for nusoap. this is great!
  require_once('nusoap.php');

  // this is the url to all WSDL files
  if (!USE_SANDBOX) {
    define ("WSDL_URL", "https://adwords.google.com/api/adwords/" . API_VERSION);
  }
  else {
    define ("WSDL_URL", "https://sandbox.google.com/api/adwords/" . API_VERSION);
  }
  define ("REPORT_WSDL_URL", "https://adwords.google.com/api/adwords/" . API_VERSION);
  // this defines the amount n of reponse times in the lastResponseTimes array
  define("N_LAST_RESPONSE_TIMES", 10);
  // this defines the amount n of soap requests in the lastSoapRequests array
  define("N_LAST_SOAP_REQUESTS", 1);
  // this defines the amount n of soap responses in the lastSoapResponses array
  define("N_LAST_SOAP_RESPONSES", 1);

  class APIlityClients {
    // create an array where all SOAP clients and soap-related data will reside
    var $soapClients = array();

    // used to store current head so lazy loading can work properly
    var $soapHeader;

    // Soap Client name to service url mapping
    var $_clients;

    // constructor
    function APIlityClients() {
      // create these clients
      // singleton design pattern
      // allow only one instance
      // whereas headers are flexible
      static $soapClients;

      if (!isset($soapClients)) {
        $this->_clients = array(
            'campaignClient' => WSDL_URL . "/CampaignService?wsdl",
            'adGroupClient' => WSDL_URL . "/AdGroupService?wsdl",
            'criterionClient' => WSDL_URL . "/CriterionService?wsdl",
            'adClient' => WSDL_URL . "/AdService?wsdl",
            'reportClient' =>   WSDL_URL . "/ReportService?wsdl",
            'infoClient' =>   WSDL_URL . "/InfoService?wsdl",
            'trafficEstimatorClient' => WSDL_URL . "/TrafficEstimatorService?wsdl",
            'accountClient' => WSDL_URL . "/AccountService?wsdl",
            'keywordToolClient' => WSDL_URL . "/KeywordToolService?wsdl",
            'siteSuggestionClient' => WSDL_URL . "/SiteSuggestionService?wsdl");
        
        // these attributes will be updated after each soapclient->call operation
        $this->soapClients['overallConsumedUnits'] = 0;
        $this->soapClients['overallPerformedOperations'] = 0;
        $this->soapClients['lastResponseTimes'] = array();

        // this is for debugging purposes
        $this->soapClients['lastSoapRequests'] = array();
        $this->soapClients['lastSoapResponses'] = array();
        $this->soapClients['lastSoapRequestIds'] = array();
        
        $soapClients = $this;
        return $soapClients;
      }
    }

    // get functions for accessing the various clients
    function getCampaignClient() {
      return $this->_getClient('campaignClient');
    }

    function getAdGroupClient() {
      return $this->_getClient('adGroupClient');
    }

    function getAdClient() {
      return $this->_getClient('adClient');
    }

    function getCriterionClient(){
      return $this->_getClient('criterionClient');
    }

    function getSiteSuggestionClient(){
      return $this->_getClient('siteSuggestionClient');
    }

    function getReportClient() {
      return $this->_getClient('reportClient');
    }

    function getInfoClient() {
      return $this->_getClient('infoClient');
    }

    function getTrafficEstimatorClient(){
      return $this->_getClient('trafficEstimatorClient');
    }

    function getAccountClient() {
      return $this->_getClient('accountClient');
    }

    function getKeywordToolClient() {
      return $this->_getClient('keywordToolClient');
    }

    function getOverallConsumedUnits() {
      return $this->soapClients['overallConsumedUnits'];
    }

    function getOverallPerformedOperations() {
      return $this->soapClients['overallPerformedOperations'];
    }

    function getLastResponseTimes() {
      return $this->soapClients['lastResponseTimes'];
    }

    function getLastSoapRequests() {
      if (strcmp(DISPLAY_ERROR_STYLE, "HTML") == 0) {
        $style = "<style type='text/css'>
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
        echo "<h1>Requests</h1>";
        $requests = $this->soapClients['lastSoapRequests'];
        $requests = preg_replace('/(&quot;.*?&quot;)/s', '<span class="string">\1</span>', $requests);
        $requests = preg_replace('/(&lt;.*?&gt;)/s', '<span class="tag">\1</span>', $requests);
        $requests = preg_replace('/\n/s', '<br />\1', $requests);
        return $requests;
      }
      else {
        return $this->soapClients['lastSoapRequests'];
      }
    }

    function getLastSoapResponses() {
      if (strcmp(DISPLAY_ERROR_STYLE, "HTML") == 0) {
        $style = "<style type='text/css'>
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
        echo "<h1>Responses</h1>";
        $responses = $this->soapClients['lastSoapResponses'];
        $responses = preg_replace('/(&quot;.*?&quot;)/s', '<span class="string">\1</span>', $responses);
        $responses = preg_replace('/(&lt;.*?&gt;)/s', '<span class="tag">\1</span>', $responses);
        $responses = preg_replace('/\n/s', '<br />\1', $responses);
        return $responses;
      }
      else {
        return $this->soapClients['lastSoapResponses'];
      }
    }

    // lazy loading and cache functioning for all Soap Clients
    // private
    function _getClient($client) {
      // client has already been created
      if (isset($this->soapClients[$client])) {
        return $this->soapClients[$client];
      }
      // we require the client for the first time
      else {
        // get the wsdl service url
        $service = $this->_clients[$client];
        if (WSDL_CACHE_ENABLED) {
          require_once('WsdlCache.php');
          // create a cache object for the current url
          $wsdlCache = new APIlityWsdlCache($service, WSDL_CACHE_DIR, WSDL_CACHE_TIME);
          // cache file is valid
          if ($wsdlCache->isValid()) {
            $localFile = $wsdlCache->getFilePath();
            $this->soapClients[$client] = new soapclientNusoap($localFile, 'wsdl');
            // update client with most current headers
            $this->soapClients[$client]->setHeaders($this->soapHeader);
            if (HTTP_PROXY_HOST) {
              $this->soapClients[$client]->setHTTPProxy(
                  HTTP_PROXY_HOST,
                  HTTP_PROXY_PORT,
                  HTTP_PROXY_USER,
                  HTTP_PROXY_PASSWORD);
            }
            return $this->soapClients[$client];
          }
        }
        // if caching is disabled or caching failed
        $this->soapClients[$client] = new soapclientNusoap($service, 'wsdl');
        // update client with most current headers
        $this->soapClients[$client]->setHeaders($this->soapHeader);
        return $this->soapClients[$client];
      }
    }

    // on context switch we call setHeaders
    function setSoapHeaders($soapHeader) {
      $this->soapHeader = $soapHeader;
      // we cannot iterate over objects in PHP4, so just hard-wire this
      if (isset($this->soapClients['campaignClient']))
          $this->soapClients['campaignClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['adGroupClient']))
          $this->soapClients['adGroupClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['criterionClient']))
          $this->soapClients['criterionClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['siteSuggestionClient']))
          $this->soapClients['siteSuggestionClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['adClient']))
          $this->soapClients['adClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['reportClient']))
          $this->soapClients['reportClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['infoClient']))
          $this->soapClients['infoClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['trafficEstimatorClient']))
          $this->soapClients['trafficEstimatorClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['accountClient']))
          $this->soapClients['accountClient']->setHeaders($this->soapHeader);
      if (isset($this->soapClients['keywordToolClient']))
          $this->soapClients['keywordToolClient']->setHeaders($this->soapHeader);
    }

    function updateSoapRelatedData($soapRelatedData) {
      // units and operations are just summed up so that we can see the overall data
      $this->soapClients['overallConsumedUnits'] += $soapRelatedData['units'];
      $this->soapClients['overallPerformedOperations'] += $soapRelatedData['operations'];
      // the last n response times are kept. n is defined by
      // N_LAST_RESPONSE_TIMES. the lastResponseTimes array is a queue (FIFO)
      while (sizeof($this->soapClients['lastResponseTimes']) >= N_LAST_RESPONSE_TIMES) {
        array_shift($this->soapClients['lastResponseTimes']);
      }
      array_push($this->soapClients['lastResponseTimes'], $soapRelatedData['responseTime']);
      if (IS_ENABLED_DEBUG_MODE) {
        $this->debugSoapRequest($soapRelatedData['request']);
        $this->debugSoapResponse($soapRelatedData['response']);
      }
    }

    function debugSoapRequest($request) {
      array_push(
        $this->soapClients['lastSoapRequests'],
        htmlspecialchars($request, ENT_QUOTES)
      );
      // the last n soap requests are kept. n is defined by
      // N_LAST_SOAP_REQUESTS. the lastSoapRequests array is a queue (FIFO)
      if (sizeof($this->soapClients['lastSoapRequests']) <=  N_LAST_SOAP_REQUESTS) {
        return;
      }
      while (sizeof($this->soapClients['lastSoapRequests']) > N_LAST_SOAP_REQUESTS) {
        array_shift($this->soapClients['lastSoapRequests']);
      }
    }

    function debugSoapResponse($response) {
      array_push(
          $this->soapClients['lastSoapResponses'],
          htmlspecialchars($response, ENT_QUOTES));
      // the last n soap responses are kept. n is defined by
      // N_LAST_SOAP_RESPONSES. the lastSoapResponses array is a queue (FIFO)
      if (sizeof($this->soapClients['lastSoapResponses']) <= N_LAST_SOAP_RESPONSES) {
        return;
      }
      while (sizeof($this->soapClients['lastSoapResponses']) > N_LAST_SOAP_RESPONSES) {
        array_shift($this->soapClients['lastSoapResponses']);
      }
    }

    /**
     * If initialized, gets SOAP clients, 
     * else initialzes new clients object and returns it
     * 
     * @return APIlityClients object
     * @static static
     * @author Yury Ksenevich
     */
    function &getClients() {
      static $soapClients = null;
      if (isset($soapClients)) {
        return $soapClients;
      }        
      $soapClients = new APIlityClients();
      return $soapClients;
    }
  }
?>