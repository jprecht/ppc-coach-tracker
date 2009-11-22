<?php
  // no time limit for PHP operations
  set_time_limit(0);  

  // find out where we are
  $currentWorkingDirectory = dirname(__FILE__);
  require_once($currentWorkingDirectory.'/lib/APIlityUser.php');

  function apilityBootstrap($path) {
    // parse the settings.ini file
    $settingsIni = parse_ini_file($path);
   
    // this constant controls whether APIlity should play in the sandbox
    define ("USE_SANDBOX", $settingsIni['Use_Sandbox']);
    // this constant controls which currency APIlity pays with in the sandbox
    // it is only required when APIlity plays in the sandbox
    if (USE_SANDBOX) {
      define ("CURRENCY_FOR_SANDBOX", $settingsIni['Currency_For_Sandbox']);
    }
     
    // this constant controls whether APIlity will be verbose or silent
    define ("SILENCE_STEALTH_MODE", $settingsIni['Silence_Stealth_Mode']);
    // if we are in the silence stealth mode do not report any errors
    if (SILENCE_STEALTH_MODE) error_reporting(0);
     
    // this constant controls the way apility treats currencies
    // ("1$ is 1" or "1$ is 1000000")  
    define ("EXCHANGE_RATE", $settingsIni['Exchange_Rate']);
     
    // this constant controls apility's error reporting behaviour  
    define ("DISPLAY_ERROR_STYLE", $settingsIni['Display_Error_Style']);
   
    // this constant controls whether apility returns deleted objects 
    define ("RETURN_DELETED_OBJECTS", $settingsIni['Return_Deleted_Objects']);
     
    // this constant controls whether the Campaign Negative Criteria will be
    // included as an object attribute or not
    define (
      "INCLUDE_CAMPAIGN_NEGATIVE_CRITERIA",
      $settingsIni['Include_Campaign_Negative_Criteria']
    );
     
    // this constant controls whether the isOptimizeAdServing attribute of
    // Campaigns will be used or not
    define (
      "IS_ENABLED_OPTIMIZED_AD_SERVING_ATTRIBUTE",
      $settingsIni['Is_Enabled_Optimized_Ad_Serving_Attribute']
    );
     
    // this controls the wsdl cache behaviour
    define ("WSDL_CACHE_ENABLED", $settingsIni['WSDL_Cache_Enabled']);
    // this set time in seconds to use cache version of WSDL file
    define("WSDL_CACHE_TIME", $settingsIni['WSDL_Cache_Time']);
    // this defines which directory to cache WSDL file to
    define("WSDL_CACHE_DIR", $settingsIni['WSDL_Cache_Directory']);
   
    // this defines the version of the native API we are using
    define("API_VERSION", $settingsIni['Use_API_Version']);
   
    // this defines whether the debug mode is enabled or not
    define("IS_ENABLED_DEBUG_MODE", $settingsIni['Enable_Debug_Mode']);
   
    // this defines whether the object-oriented mode is enabled or not
    define("IS_ENABLED_OO_MODE", $settingsIni['Enable_APIlity_Object_Oriented_Mode']);
     
    // set up a potential HTTP proxy
    define("HTTP_PROXY_HOST", $settingsIni['HTTP_Proxy_Host']);
    define("HTTP_PROXY_PORT", $settingsIni['HTTP_Proxy_Port']);
    define("HTTP_PROXY_USER", $settingsIni['HTTP_Proxy_User']);
    define("HTTP_PROXY_PASSWORD", $settingsIni['HTTP_Proxy_Password']);        
    
    // find out where we are
    $currentWorkingDirectory = dirname(__FILE__);       
  
    // import error reporting class
    require_once($currentWorkingDirectory.'/lib/Fault.php');
    // import the wsdl clients
    require_once($currentWorkingDirectory.'/lib/Clients.php');
    // include some useful utilities
    require_once($currentWorkingDirectory.'/lib/Utilities.php');  
    // import each service depending on the api version indicated in settings.ini
  
    if (strcasecmp(API_VERSION, "v13") == 0) {
      // always import these files, whether in OO mode or not    
      require_once($currentWorkingDirectory.'/lib/Campaign.php');
      require_once($currentWorkingDirectory.'/lib/AdGroup.php');
      require_once($currentWorkingDirectory.'/lib/Criterion.php');
      require_once($currentWorkingDirectory.'/lib/Ad.php');
      // import these files only when we are not in OO mode
      // however, if we are in OO mode, the import happens in APIlityUser.php
      if (!IS_ENABLED_OO_MODE) {    
        require_once($currentWorkingDirectory.'/lib/Report.php');
        require_once($currentWorkingDirectory.'/lib/TrafficEstimate.php');
        require_once($currentWorkingDirectory.'/lib/Info.php');
        require_once($currentWorkingDirectory.'/lib/Account.php');
        require_once($currentWorkingDirectory.'/lib/KeywordTool.php');
        require_once($currentWorkingDirectory.'/lib/SiteSuggestion.php');
      }
    }
    // report invalid API version exception
    else {
      if (!SILENCE_STEALTH_MODE) {
        trigger_error("<b>APIlity PHP library => Warning: </b>The API version '".API_VERSION."' does not exist. Please update your <b>settings.ini</b> file", E_USER_WARNING);
      }
    }
  
    // check if we are coolio to run apility
    $apilityRequirements = checkApilityRequirements();
    if (!$apilityRequirements['requirementsFulfilled']) {
      if (!SILENCE_STEALTH_MODE) {
        trigger_error($apilityRequirements['errorMessage'], E_USER_WARNING);
      }
    }
  }    
?>