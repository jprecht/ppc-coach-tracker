<?php
  class APIlityManager {
    var $email;
    var $password;
    var $developerToken;
    var $applicationToken;
    var $userAgent;
    var $customSettingsIni;
    var $customAuthenticationIni;
    
    // constructor
    function APIlityManager(
        $email = null,
        $password = null,
        $developerToken = null,
        $applicationToken = null,
        $customSettingsIni = null,
        $customAuthenticationIni = null
    ) {
      
      // potentially use a custom settings.ini file per APIlityManager
      if (isset($customSettingsIni) && $customSettingsIni) {
        apilityBootstrap($customSettingsIni);
        $this->customSettingsIni = $customSettingsIni;
      }
      else {
        apilityBootstrap(dirname(__FILE__) . '/../settings.ini');    
      }
      
      // all login credentials are directly given
      if ($email && $password && $developerToken && $applicationToken) {
        $this->email = $email;
        $this->password = $password;
        // developer token is different in the sandbox
        $this->developerToken = !USE_SANDBOX? $developerToken : ($email . "++" . CURRENCY_FOR_SANDBOX);
        $this->applicationToken = $applicationToken; 
      }
      // parse the default authentication.ini file
      else {
        if (isset($customAuthenticationIni) && $customAuthenticationIni) {
          $authenticationIni = 
              parse_ini_file($customAuthenticationIni);
          $this->email = $authenticationIni['Email'];
          $this->password = $authenticationIni['Password'];
          // developer token is different in the sandbox
          $this->developerToken = !USE_SANDBOX? $authenticationIni['Developer_Token'] : ($authenticationIni['Email'] . "++" . CURRENCY_FOR_SANDBOX);
          $this->applicationToken = $authenticationIni['Application_Token'];                   
          $this->customAuthenticationIni = $customAuthenticationIni;
        }
        else {        
          $authenticationIni = 
              parse_ini_file(dirname(__FILE__) . '/../authentication.ini');
          $this->email = $authenticationIni['Email'];
          $this->password = $authenticationIni['Password'];
          // developer token is different in the sandbox
          $this->developerToken = !USE_SANDBOX? $authenticationIni['Developer_Token'] : ($authenticationIni['Email'] . "++" . CURRENCY_FOR_SANDBOX);
          $this->applicationToken = $authenticationIni['Application_Token'];         
        }
      }      
      // hard-wire the user agent
      $this->userAgent = 'Google APIlity PHP Library for AdWords';                   
      // set the headers upon authentication context creation if soapclients
      // already exist
      $soapClients = &APIlityClients::getClients();      
      if (isset($soapClients)) $soapClients->setSoapHeaders($this->getHeader());               
      // call the dummy function in order to import external files      
      $this->dummy();
      APIlityManager::setContext($this);
    }
        
    // this dummy function's only purpose is to include the other files  
    function dummy() {    
      if (IS_ENABLED_OO_MODE) {
        // the corresponding .php files contain the classes and have been 
        // imported in apility.php
        $currentWorkingDirectory = dirname(__FILE__);
        require_once($currentWorkingDirectory.'/Campaign.inc.php');
        require_once($currentWorkingDirectory.'/AdGroup.inc.php');
        require_once($currentWorkingDirectory.'/Criterion.inc.php');
        require_once($currentWorkingDirectory.'/Ad.inc.php');
        // these files have no classes, importing them here will put them in
        // the scope of this class
        require_once($currentWorkingDirectory.'/Report.php');
        require_once($currentWorkingDirectory.'/TrafficEstimate.php');
        require_once($currentWorkingDirectory.'/Info.php');
        require_once($currentWorkingDirectory.'/Account.php');
        require_once($currentWorkingDirectory.'/KeywordTool.php');
        require_once($currentWorkingDirectory.'/SiteSuggestion.php');    
      }   
    }
    
    function getOverallPerformedOperations() {
      $soapClients = &APIlityClients::getClients();
      return $soapClients->getOverallPerformedOperations();
    }
    
    function getOverallConsumedUnits() {
      $soapClients = &APIlityClients::getClients();      
      return $soapClients->getOverallConsumedUnits();      
    }
        
    function getLastResponseTimes() {
      $soapClients = &APIlityClients::getClients();      
      return $soapClients->getLastResponseTimes();      
    }    
    
    function getFaultStack() {
      $faultStack = &APIlityFault::getFaultStack();
      return $faultStack;
    }    
    
    function getLastSoapRequests() {
      $soapClients = &APIlityClients::getClients();
      return $soapClients->getLastSoapRequests();      
    }
    
    function getLastSoapResponses() {
      $soapClients = &APIlityClients::getClients();
      return $soapClients->getLastSoapResponses();      
    }
            
    // getters
    function getEmail() {
      return $this->email;  
    }

    function getPassword() {
      return $this->password;  
    }

    function getDeveloperToken() {      
      return !USE_SANDBOX? $this->developerToken : ($this->email . "++" . CURRENCY_FOR_SANDBOX);
    }
    
    function getUserAgent() {
      return $this->userAgent;
    }

    function getApplicationToken() {
      return $this->applicationToken;  
    }

    // this will return a valid header for soap clients
    function getHeader() {
      return "<email>" . $this->getEmail() . "</email>
              <password>" . $this->getPassword() . "</password>
              <developerToken>" . $this->getDeveloperToken() . "</developerToken>
              <applicationToken>" . $this->getApplicationToken() . "</applicationToken>
              <useragent>" . $this->getUserAgent() . "</useragent>";
    }    
    
    function &getContext($instance = null) {
      static $apilityManager;
      if ($instance) {
        $apilityManager = $instance;
      }
      return $apilityManager;
    }
      
    /**
     * Sets Authentification context
     * 
     * @param array $authenticationIni requires the following ini elements: Client_Email, Email, Password, Developer_Token, Application_Token
     * @static static
     * @author Yury Ksenevich
     */
    function setContext($instance) {
      $apilityManager = &APIlityManager::getContext($instance);           
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($apilityManager->getHeader());
    }    
    
    // setters        
    function setEmail($newEmail) {
      $this->email = $newEmail;
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($this->getHeader());
    }
    
    function setPassword($newPassword) {
      $this->password = $newPassword;
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($this->getHeader());      
    }
    
    function setDeveloperToken($newDeveloperToken) {
      $this->developerToken = $newDeveloperToken;
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($this->getHeader());      
    }
    
    function setApplicationToken($newApplicationToken) {
      $this->applicationToken = $newApplicationToken;
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($this->getHeader());      
    }

    function __call($method, $args) {
      return call_user_func_array($method, $args);
    }                            
  }
  
  class APIlityUser extends APIlityManager {  
    var $clientEmail;
    
    // constructor
    function APIlityUser(
        $email = null,
        $password = null,
        $clientEmail = null,
        $developerToken = null,
        $applicationToken = null,
        $customSettingsIni = null,
        $customAuthenticationIni = null
    ) {
      // we need to construct the superclass first, this is php-specific
      // object-oriented behaviour
      APIlityUser::APIlityManager($email, $password, $developerToken, $applicationToken, $customSettingsIni, $customAuthenticationIni);

      // if we have no client email, fall back to the authentication.ini file
      // all other fall backs for the other header fields are in the super class
      if (!$clientEmail) {
        if (isset($customAuthenticationIni) && $customAuthenticationIni) {
          $authenticationIni = 
              parse_ini_file($customAuthenticationIni);            
          $clientEmail = isset($authenticationIni['Client_Email'])? $authenticationIni['Client_Email'] : $clientEmail = '';           
        }
        else {
          $authenticationIni = 
              parse_ini_file(dirname(__FILE__) . '/../authentication.ini');            
          $clientEmail = isset($authenticationIni['Client_Email'])? $authenticationIni['Client_Email'] : ''; 
        }
      }
      $this->setClientEmail($clientEmail);              
      APIlityManager::setContext($this);
    } 
           
    // getters    
    function getClientEmail() {
      return $this->clientEmail;  
    }
        
    // this will return a valid header for soap clients
    function getHeader() {
      return "<email>" . $this->getEmail() . "</email>
              <password>" . $this->getPassword() . "</password>
              <clientEmail>" . $this->getClientEmail() . "</clientEmail>
              <developerToken>" . $this->getDeveloperToken() . "</developerToken>
              <applicationToken>" . $this->getApplicationToken() . "</applicationToken>
              <useragent>" . $this->getUserAgent() . "</useragent>";
    }        
        
    // setters
    function setClientEmail($newEmail) {
      $this->clientEmail = $newEmail;
      $authenticationContext = &APIlityManager::getContext();
      $authenticationContext->clientEmail = $newEmail;
      $soapClients = &APIlityClients::getClients();
      $soapClients->setSoapHeaders($authenticationContext->getHeader());
    }      
  }
?>