<?php
/*
 * Copyright (c) 2006-2007, Yahoo! Inc. All rights reserved.
 * Code licensed under the BSD License:
 * http://developer.yahoo.com/yui/license.txt
 * version: 2.0.0
 */
require_once("lib/nusoap.php");
include ('basicReport.php');
include ('fileOutputFormat.php');

//=================define constants and endpoints===============//
/*
 * The SOAP endpoint URL for location service.
 sandbox.marketing.ews.yahooapis.com/services
 */
 
define("EWS_LOCATION_SERVICE_ENDPOINT",  "marketing.ews.yahooapis.com");

/*
 * Protocol used to access.
 */
define("EWS_ACCESS_HTTP_PROTOCOL",  "https");

/*
 * Controls the debug messages
 */
define("EWS_DEBUG",             false);

/*
 * EWS Version
 */
define("EWS_VERSION",   "V4");

/*
 * Namespace to be used for the headers
 */
define("EWS_NAMESPACE",         "http://marketing.ews.yahooapis.com/".EWS_VERSION);

/*
 * Directory where location cache files are stored.
 */
define("TEMP_FILES_DIR",        ".");


/*
 * Market
 */
define("MARKET",		"US");

/*
 * Name and location for sample data file
 */
define("SAMPLE_DATA_DIR",	"..");
define("SAMPLE_DATA_FILENAME",	"sample_data_");

/*
 * Headers that will be passed as SOAP headers in each request
 */
$EWS_HEADERS = array(
    "username"          => "beejeebers",
    "password"          => "bab00n",
    "masterAccountID"   => "656088",
    "accountID"         => "9024865880",
    "license"		    => "LCqnXATV34GHD_2QCLDtcw_xPm9zROalxagONxL_NNf7lqeL_TmslCaoH8i68Q--C"
);

/*
 * NOTE: remember to switch to the timezone of your master account if it's different
 *       from the server timezone
 *
 * Example: date_default_timezone_set( "America/Los_Angeles" );
 */

/*
 * Array for sample data
 */
$SAMPLE_DATA = array();

/*
 * Locations gotten from location service are cached to avoid
 * calling it for every request and incurring quota penalties.
 */
$ACCOUNT_LOCATION_CACHE = array();

//===================main function calls=========================//

debug("Yahoo! EWS PHP Client");

/*
 * Load sample data
 */
//loadSampleData();

/*
 * Load all cached location entries
 */
//loadLocationCache();

 $accountID            = $EWS_HEADERS['accountID'];

/*
 * Initialize SOAP clients for different services
 */

 $basicReportService       = createClient(EWS_VERSION."/BasicReportService",  $accountID);

//start  Report object creation
 $dateRange   				= 'Yesterday';
 $reportName  				= 'campaign name';
 $reportType  				= 'KeywordSummaryByDay';

 $reportRequestObj 			= new ewsBasicReportRequest($dateRange,'',$reportName,$reportType,'');
 $reportId					= addReportRequestForAccountID($accountID,$reportRequestObj);
 $onlyCompleted 			= true; 
 $reportList				= getReportList($onlyCompleted);
 $fileFormatObj 			= new ewsFileOutputFormat('XML',false);
 // ReportOutputUrl stored the url of getReportOutputURL , It  stores the value in array.
 $ReportOutputUrl 			= getCustomUrl($reportList,$fileFormatObj);
 
 displayUrl ($ReportOutputUrl);
 
 function displayUrl($ReportOutputUrl)
 {
   echo "<br><h1> KeywordSummaryByDay reportOutputUrl in XML format. </h1> </br>";
   echo "<p> Here display the url which are stored in ReportOutputUrl variable  </p>";
   foreach ($ReportOutputUrl as $value )
	{	
		echo "<br> Url : $value  </br>";
	}	
 }
 


/**
 * Request a report for an account (specified by accountID)
 *
 * @param string $accountID
 * @param object $reportRequestObj [BasicReportRequest]
 * @return int - The report ID
 */

function addReportRequestForAccountID($accountID, $reportRequestObj){
	global $basicReportService;
	
	$params['dateRange'] = $reportRequestObj->getDateRange();
	$params['reportName'] = $reportRequestObj->getReportName();
	$params['reportType'] = $reportRequestObj->getReportType();
	
	
	
	$retArray = execute($basicReportService, 'addReportRequestForAccountID', array('accountID' => $accountID, 'reportRequest' => $params));
	return $retArray->out;
	
}
/**
   * This fucntion return the getOutputUrl 
   * return url  list 
*/ 
 
function getCustomUrl($reports,$fileFormatObj)
{
		foreach ($reports as $key => $value)
		{
		  $retArray[$key] =  getReportOutputUrl($value->reportID,$fileFormatObj);
		}
		
		return $retArray;
}
 
 
 function getReportList($onlyCompleted){
	global $basicReportService;
	
	$retArray = execute($basicReportService, 'getReportList', array('onlyCompleted' => $onlyCompleted));
	return $retArray->out->ReportInfo;
}
 
 /*
 * @param int $reportID
 * @param object $fileFormatObj [FileOutputFormat]
 * @return string - A URL you can use to retrieve the report using the HTTP GET method
 */
function getReportOutputUrl($reportID, $fileFormatObj){
	
	global $basicReportService;
	
	$fileFormatArr['fileOutputType'] = $fileFormatObj->getFileOutputType();
	$fileFormatArr['zipped'] = $fileFormatObj->getZipped();
	$retArray = execute($basicReportService, 'getReportOutputUrl', array('reportID' => $reportID, 'fileFormat' => $fileFormatArr));
	
	return $retArray->out;
}

 /*
 * @param int $reportID
 * @param object $fileFormatObj [FileOutputFormat]
 * @return string - A URL you can use to retrieve the report using the HTTP GET method
 */
function getReportOutputUrls($reportID, $fileFormatObj){
	
	global $basicReportService;
	
	$fileFormatArr['fileOutputType'] = $fileFormatObj->getFileOutputType();
	$fileFormatArr['zipped'] = $fileFormatObj->getZipped();
	$retArray = execute($basicReportService, 'getReportOutputUrl', array('reportID' => $reportID, 'fileFormat' => $fileFormatArr));
	
	return $retArray->out;
}

/**
 * Delete a report (specified by reportID) that you have requested. If necessary, first use getReportList to obtain the report ID.
 *
 * @param int $reportID
 * @return void
 */
function deleteReport($reportID){
	global $basicReportService;
	$retArray = execute($basicReportService, 'deleteReport', array('reportID' => $reportID));
	return $retArray['out'];
}


//===================sample data functions===========================//
/**
 * loads sample data file
 */
function loadSampleData()
{
  global $SAMPLE_DATA;

    $sample_data_file = SAMPLE_DATA_DIR . '/' . SAMPLE_DATA_FILENAME . MARKET . '.properties';
	//$sample_data_file ='./test.txt';
	debug( "Opening sample data file $sample_data_file" );
	$lines = file( $sample_data_file );
	

	
  if( !$lines )
  {
    trigger_error("Could not open sample data file $sample_data_file",E_USER_ERROR);
  }

  foreach( $lines as $line )
  {
    $line = mb_trim( $line );

    // skip comments
    if( mb_strpos($line, "#") === 0 ) continue;

    // skip empty lines
    if( mb_strlen($line) == 0 ) continue;

    // split line on the equal sign
    list( $name, $value ) = mb_split( "=", $line, 2 ); 

    $name = mb_trim( $name );
    $value = mb_trim( $value );

    // add config to the array
    $SAMPLE_DATA[$name] = $value;
  }

  debug( "Successfully loaded sample data file" );

}

/**
 * multibyte version of trim() function
 */
function mb_trim( $str )
{
  $space_pattern = "[[:space:]\n\t]";

  $str = mb_ereg_replace( "^$space_pattern", "", $str ); // left trim
  $str = mb_ereg_replace( "$space_pattern$", "", $str ); // right trim

  return $str;
}


//===================add functions===========================//
/**
 * function that creates a campaign and adds to the
 * account.
 */
function addCampaign($campaignService, $accountID)
{
    global $SAMPLE_DATA;

    $now = time();

    $campaign = createCampaign(
         NULL,                                  /* ID */
        $SAMPLE_DATA['CAMPAIGN_NAME'],          /* name */
        $SAMPLE_DATA['CAMPAIGN_DESCRIPTION'],   /* description */
        $accountID,                             /* accountID */
        'On',                                   /* status */
        true,                                   /* sponsored search */
        true,                                   /* advanced match */
        false,                                  /* campaign optimization */
        true,                                   /* content match */
        date( "c", $now + 60*60*24),    /* start date - tomorrow */
        date( "c", $now + 60*60*24*365) /* end date - one year from now */
        );

    debug("Calling addCampaign");

    $retObj = execute($campaignService,
                'addCampaign',
                array( 'campaign' => $campaign )
                );

    checkResponse($retObj);
    debug("------> Campaign ID: ".$retObj->out->campaign->ID);

    return $retObj->out->campaign;
}

/**
 * function that creates an adgroup and adds it to the
 * campaign
 */
function addAdGroup($adGroupService,$accountID,$campaignID)
{
    global $SAMPLE_DATA;

    $adGroup = createAdGroup(
         NULL,                              /* ID                      */
        $accountID,                         /* account                 */
        $SAMPLE_DATA['ADGROUP_NAME'],       /* name                    */
        false,                              /* auto optimization       */
        false,                              /* advanced match          */
        $campaignID,                        /* campaign                */
        $SAMPLE_DATA['ADGROUP_CM_MAX_BID'], /* content match maxbid    */
        true,                               /* content match           */
        $SAMPLE_DATA['ADGROUP_SS_MAX_BID'], /* sponsored search maxbid */
        true,                               /* sponsored search        */
        'On',                               /* status                  */
        false);                             /* watch                   */

    debug("Calling addAdGroup");

    $retObj = execute(
            $adGroupService,
            'addAdGroup',
            array( 'adGroup' => $adGroup )
            );

    checkResponse($retObj);
    debug("------> AdGroup ID: ".$retObj->out->adGroup->ID);

    return $retObj->out->adGroup;
}

/**
 * function that creates individual ads and adds it to the
 * adgroup.
 */
function addAds($adService,$adGroupID)
{
    global $SAMPLE_DATA;

    $ad1 = createAd(
         NULL,                           /* ID */
        $adGroupID,
	$SAMPLE_DATA['AD1_DESC'],        /* description */
	$SAMPLE_DATA['AD1_DISPLAY_URL'], /* display URL */
	$SAMPLE_DATA['AD1_NAME'],        /* name */
	$SAMPLE_DATA['AD1_SHORT_DESC'],  /* short description */
        'On',                            /* status */
	$SAMPLE_DATA['AD1_TITLE'],       /* title */
	$SAMPLE_DATA['AD1_URL']          /* URL */
    );

    $ad2 = createAd(
         NULL,                           /* ID */
        $adGroupID,
	$SAMPLE_DATA['AD2_DESC'],        /* description */
	$SAMPLE_DATA['AD2_DISPLAY_URL'], /* display URL */
	$SAMPLE_DATA['AD2_NAME'],        /* name */
	$SAMPLE_DATA['AD2_SHORT_DESC'],  /* short description */
        'On',                            /* status */
	$SAMPLE_DATA['AD2_TITLE'],       /* title */
	$SAMPLE_DATA['AD2_URL']          /* URL */
    );

    debug("Calling addAds");

    //PHP NOTE: In case of nested arrays, only the outer-most
    //element needs to be keyed with the correct param name.
    $adsParam = array( 'ads' => array($ad1, $ad2) );

    $retObj = execute($adService, 'addAds', $adsParam);

    //IMPL NOTE: PHP will treat single element as an object and not an array

    //PHP NOTE: Return values of arrays are structured differently than
    //as described in wsdl.
    if(isset($retObj->out->AdResponse[0]->ad))
    {
        checkResponse($retObj->out->AdResponse[0]);
        debug("------> Ad ID: ".$retObj->out->AdResponse[0]->ad->ID);
        checkForEditorialReason($adService,$retObj->out->AdResponse[0]->ad);
    }

    if(isset($retObj->out->AdResponse[1]->ad))
    {
        checkResponse($retObj->out->AdResponse[1]);
        debug("------> Ad ID: ".$retObj->out->AdResponse[1]->ad->ID);
        checkForEditorialReason($adService,$retObj->out->AdResponse[1]->ad);
    }

    return $retObj->out;
}

/**
 * function that creates keywords and add it to the adgroup.
 */
function addKeywords($keywordService, $adGroupID)
{
    global $SAMPLE_DATA;

    $keyword1 = createKeyword(
	NULL,                                /* ID */
	$adGroupID,
	true,                                /* advanced match */
	NULL,                                /* alt text */
	NULL,                                /* ss max bid */
	'On',                                /* status */
	$SAMPLE_DATA['KEYWORD1_TEXT'],       /* text */
	NULL,                                /* URL */
	NULL                                 /* watch */
    );

    $keyword2 = createKeyword(
	NULL,                                /* ID */
	$adGroupID,
	true,                                /* advanced match */
	NULL,                                /* alt text */
	$SAMPLE_DATA['KEYWORD2_SS_MAX_BID'], /* ss max bid */
	'On',                                /* status */
	$SAMPLE_DATA['KEYWORD2_TEXT'],       /* text */
	NULL,                                /* URL */
	NULL                                 /* watch */
    );

    $keyword3 = createKeyword(
	NULL,                                /* ID */
	$adGroupID,
	true,                                /* advanced match */
	NULL,                                /* alt text */
	$SAMPLE_DATA['KEYWORD3_SS_MAX_BID'], /* ss max bid */
	'On',                                /* status */
	$SAMPLE_DATA['KEYWORD3_TEXT'],       /* text */
	NULL,                                /* URL */
	NULL                                 /* watch */
    );


    $keyword4 = createKeyword(
	NULL,                                /* ID */
	$adGroupID,
	true,                                /* advanced match */
	NULL,                                /* alt text */
	$SAMPLE_DATA['KEYWORD4_SS_MAX_BID'], /* ss max bid */
	'On',                                /* status */
	$SAMPLE_DATA['KEYWORD4_TEXT'],       /* text */
	NULL,                                /* URL */
	NULL                                 /* watch */
    );

    debug("Calling addKeywords");

    $keywordsParam = array( 'keywords' =>
        array($keyword1, $keyword2, $keyword3, $keyword4)
        );

    $retObj = execute($keywordService, 'addKeywords', $keywordsParam);

    return $retObj->out;
}

/**
 * function that creates words that should be excluded from
 * the adgroup.
 */
function addExcludedWords($excludedWordsService, $adGroupID)
{
    global $SAMPLE_DATA;

    $excludedWord1 = createExcludedWord(
	NULL,                              /* ID */
	$adGroupID,
	$SAMPLE_DATA['EXCLUDEDWORD1_TEXT'] /* text */
    );

    $excludedWord2 = createExcludedWord(
	NULL,                              /* ID */
	$adGroupID,
	$SAMPLE_DATA['EXCLUDEDWORD2_TEXT'] /* text */
    );

    $excludedWord3 = createExcludedWord(
	NULL,                              /* ID */
	$adGroupID,
	$SAMPLE_DATA['EXCLUDEDWORD3_TEXT'] /* text */
    );

    debug("Calling addExcludedWordsToAdGroup");

    $keywordsParam = array( 'excludedWords' =>
        array($excludedWord1, $excludedWord2, $excludedWord3)
        );

    $retObj = execute($excludedWordsService, 'addExcludedWordsToAdGroup', $keywordsParam);

    return $retObj->out;
}

//==================data type create functions=====================//
/**
 * low-level function that creates an individual excluded word data structure.
 * The object is created with a container of the object type.
 */
function createExcludedWord($ID,$adGroupID,$text)
{
    $excludedWord = array (
        'ID'        => $ID,
        'adGroupID'    => $adGroupID,
        'text'      => $text
    );

    return $excludedWord;
}

/**
 * low-level function that creates an individual keyword data structure.
 * The object is created with a container of the object type.
 */
function createKeyword($ID,$adGroupID,$advancedMatchON,$alternateText,$sponsoredSearchMaxBid,$status,$text,$url,$watchON)
{
    $keyword = array (
        'ID'                    => $ID,
        'adGroupID'             => $adGroupID,
        'advancedMatchON'       => $advancedMatchON,
        'alternateText'         => $alternateText,
        'sponsoredSearchMaxBid' => $sponsoredSearchMaxBid,
        'status'                => $status,
        'text'                  => $text,
        'url'                   => $url,
        'watchON'               => $watchON
    );

    return $keyword;
}

/**
 * low-level function that creates an individual ad data structure.
 * The object is created with a container of the object type.
 */
function createAd($ID,$adGroupID,$description,$displayUrl,$name,$shortDescription,$status,$title,$url)
{
    $ad = array (
        'ID'               => $ID,
        'adGroupID'        => $adGroupID,
        'description'      => $description,
        'displayUrl'       => $displayUrl,
        'name'             => $name,
        'shortDescription' => $shortDescription,
        'status'           => $status,
        'title'            => $title,
        'url'              => $url
    );

    return $ad;
}

/**
 * low-level function that creates an individual campaign data structure.
 * The object is created with a container of the object type.
 */
function createCampaign($ID, $name,$description,$accountID,$status,$sponsoredSearchON, $advancedMatchON, $campaignOptimizationON, $contentMatchON, $startDate, $endDate)
{
    $campaign = array(
        'ID'                 => $ID,
        'name'               => $name,
        'description'        => $description,
        'accountID'          => $accountID,
        'status'             => $status,
        'sponsoredSearchON'  => $sponsoredSearchON,
        'advancedMatchON'    => $advancedMatchON,
        'campaignOptimizationON' => $campaignOptimizationON,
        'contentMatchON'     => $contentMatchON,
        'startDate'          => $startDate,
        'endDate'            => $endDate
        );

    return $campaign;
}

/**
 * low-level function that creates an individual adGroup data structure.
 * The object is created with a container of the object type.
 */
function createAdGroup($ID,$accountID,$name,$adAutoOptimizationON,$advancedMatchON,$campaignID,$contentMatchMaxBid,
$contentMatchON,$sponsoredSearchMaxBid,$sponsoredSearchON,$status,$watchON)
{
    $adGroup = array (
        'ID'                    => $ID,
        'accountID'             => $accountID,
        'name'                  => $name,
        'adAutoOptimizationON'  => $adAutoOptimizationON,
        'advancedMatchON'       => $advancedMatchON,
        'campaignID'            => $campaignID,
        'contentMatchMaxBid'    => $contentMatchMaxBid,
        'contentMatchON'        => $contentMatchON,
        'sponsoredSearchMaxBid' => $sponsoredSearchMaxBid,
        'sponsoredSearchON'     => $sponsoredSearchON,
        'status'                => $status,
        'watchON'               => $watchON
    );

    return $adGroup;
}

//==========================SOAP utility methods=================//
/**
 * low-level function that creates a SOAP client given the service
 * name and accountID.
 *
 * $service - service name of the form Version/Name. Ex: V4/CampaignService
 *
 * $accountID - accountID for which the current operation is performed.
 *
 * $useLocationService (optional) - true to ignore location service usage.
 * useful only when creatinng client for LocationService itself.
 */
function createClient($service, $accountID, $useLocationService=true)
{
    if($useLocationService) $wsdlEndPointURL = getEndPointFromLocationService($service, $accountID);
    else $wsdlEndPointURL = EWS_ACCESS_HTTP_PROTOCOL."://".EWS_LOCATION_SERVICE_ENDPOINT."/services/".$service;

    debug("Creating $service client");

    $client = new SoapClient(
            "$wsdlEndPointURL?wsdl",
        array(  'trace'      => true,
                'exceptions' => true,
                'location'   => $wsdlEndPointURL,
                'uri'        => EWS_NAMESPACE,
                'connection_timeout'=>10)
    );

    $headers = createHeaders();

    debug("Setting header");

    $client->__setSoapHeaders( $headers );

    return $client;
}

/**
 * Creates an associative array of header names that can be
 * used in the request.
 */
function createHeaders()
{
    global $EWS_HEADERS;

    $headers = array();

    foreach($EWS_HEADERS as $aHeaderName => $aHeaderValue)
    {
        debug("Creating $aHeaderName header");

        $aHeader = new SoapHeader(
                    EWS_NAMESPACE,
                    $aHeaderName,
                    $aHeaderValue
                            );

        array_push($headers,$aHeader);
    }


    return $headers;
}

function debug($msg)
{
    if(EWS_DEBUG) echo "[debug] $msg\n";
}


function checkResponse($retObj)
{
    if( isset($retObj->out->operationSucceeded) && !$retObj->out->operationSucceeded )
    {
        $fault = $retObj->out->errors;
        trigger_error("SOAP Fault: (faultcode: {$fault->Error->code}, faultstring: {$fault->Error->message})",E_USER_ERROR);
    }
    else if(isset($retObj->operationSucceeded) && !$retObj->operationSucceeded)
    {
        $fault = $retObj->errors;
        checkForEditorialReason(null,$retObj,$fault->Error->code);
        trigger_error("SOAP Fault: (faultcode: {$fault->Error->code}, faultstring: {$fault->Error->message})",E_USER_ERROR);
    }
}

/**
 * Retrieves the endpoint for the specific service and accountID using the
 * EWS LocationService.
 * The steps to obtain a location are:
 * i)  If location is present in the ACCOUNT_LOCATION_CACHE, return it.
 * ii) If not present in ACCOUNT_LOCATION_CACHE, call location service to
 *     fetch the location. Store the location in the cache file
 *     and ACCOUNT_LOCATION_CACHE before returning it.
 *
 * $serviceName - name of the service of the form Version/Name.
 *
 * $accountID   - accountID for which location is sought.
 */
function getEndPointFromLocationService($serviceName, $accountID)
{
    global $ACCOUNT_LOCATION_CACHE;

    $cachedAccountLocation = array_key_exists($accountID,$ACCOUNT_LOCATION_CACHE) ? $ACCOUNT_LOCATION_CACHE[$accountID] : NULL;

    if(!$cachedAccountLocation)
    {
        $client     = createClient(EWS_VERSION."/LocationService",NULL,false);

        if($client)
        {
            $response = execute($client,"getMasterAccountLocation",NULL);
            if($response)
            {
                $cachedAccountLocation = $response->out;
                persistAccountLocationCache($accountID,$cachedAccountLocation);
            }
        }
    }

    if(!$cachedAccountLocation)
    {
        trigger_error("Service Error: Failed to get account location from server: EWS_LOCATION_SERVICE_ENDPOINT",E_USER_ERROR);
    }

    return $cachedAccountLocation."/".$serviceName;
}

/**
 * Low-level function that executes the SOAP call and performs
 * error handling.
 */
function execute($soapClient, $operation, $params)
{
    try
    {
        if($params)
        {
            $result = $soapClient->__soapCall(
                    $operation,
                    array(
                        $params
                    )
                );
        }
        else
        {
            $result = $soapClient->__soapCall($operation,array());
        }
    }
    catch (SoapFault $fault)
    {
           trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})",E_USER_ERROR);
    }

    //check for faults in result
    if (isset($result) && is_soap_fault($result))
    {
       trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})",E_USER_ERROR);
    }
    else if(isset($result))
    {
        return $result;
    }
    else
    {
        return NULL;
    }
}

//==========account location caching/persistence functions========//
/**
 * Low-level function that stores the specifed location
 * in the permenant cache file as well as in the internal
 * ACCOUNT_LOCATION_CACHE.
 *
 */
function persistAccountLocationCache($accountID, $location)
{
    global $ACCOUNT_LOCATION_CACHE, $CACHE_FILE_NAME;

    //Step 1: Store in the internal data structure
    $ACCOUNT_LOCATION_CACHE[$accountID] = $location;

    //Step 2: Store in a persistent store
    $cacheFile = getLocationCacheFileName();

    if (!$handle = fopen($cacheFile, 'a'))
    {
         echo "Cannot open file ($cacheFile)";
         exit;
    }

    $accountLocationEntry = "\n$accountID = $location";

    // Write $somecontent to our opened file.
    if (fwrite($handle, $accountLocationEntry) === FALSE)
    {
        echo "Cannot write to file ($cacheFile)";
        exit;
    }

    debug("Success, wrote ($accountLocationEntry) to file ($cacheFile)" );

    fclose($handle);
}

/**
 * Simple algorithm to generate a unique cache file name unique to
 * store location information gotten from the specified
 * EWS_LOCATION_SERVICE_ENDPOINT
 */
function getLocationCacheFileName()
{
    global $CACHE_FILE_NAME;

    if(!isset($CACHE_FILE_NAME))
    {
        $CACHE_FILE_NAME = "ews_cache_";
        $location        = EWS_LOCATION_SERVICE_ENDPOINT;

        for($i=0;$i<strlen(EWS_LOCATION_SERVICE_ENDPOINT);$i++)
        {
            $achar = $location[$i];
            if(ord($achar)>=ord('A') && ord($achar)<=ord('z')) $CACHE_FILE_NAME .= $achar;
        }

        debug("CACHE_FILE_NAME: $CACHE_FILE_NAME");
    }

    return TEMP_FILES_DIR."/".$CACHE_FILE_NAME;
}

/**
 * Called at the script initialization time to load previously
 * cached location entries.
 */
function loadLocationCache()
{
    global $ACCOUNT_LOCATION_CACHE;

    $cacheFile = getLocationCacheFileName();

    if(!file_exists($cacheFile) || !is_file($cacheFile)) return;

    $lines = file($cacheFile);

    $ACCOUNT_LOCATION_CACHE = array();

    foreach($lines as $line_num => $line)
    {
        $line = trim($line);
        if(!$line) continue;
        if(strpos($line,'#')===0) continue;

        list($accountID, $location) = split('=',$line);

        $accountID = trim($accountID);
        $location  = trim($location);

        debug("$accountID, $location;");

        if($accountID && $location)
        {
            $ACCOUNT_LOCATION_CACHE[$accountID] = $location;
        }
    }

    debug(count($ACCOUNT_LOCATION_CACHE)." account location read from cache.");

    if(EWS_DEBUG) print_r($ACCOUNT_LOCATION_CACHE);
}

//===============editorial reasons===================//
function checkForEditorialReason($adService,$ad,$errorcode=NULL)
{
   $editorialReasons = NULL;

   if(isset($ad->editorialStatus) && $ad->editorialStatus == "Pending")
   {
       debug(" Ad in pending state.");
       if($adService)
       {
         $retObj = execute($adService,"getEditorialReasonsForAd",array("adID" => $ad->ID));
         $editorialReasons = $retObj->out;
       }
   }
   else if(isset($errorcode) && $errorcode == "E2014")
   {
     debug(" Ad rejected.");
     $editorialReasons = $ad->editorialReasons;
   }

     //There might not be any editorial reasons yet for a Pending OMO.
   if(isset($editorialReasons->adEditorialReasons))
   {
     printEditorialReason(" Editorial Reason Code - Ad           : ", $editorialReasons->adEditorialReasons);
     printEditorialReason(" Editorial Reason Code - decription   : ", $editorialReasons->descriptionEditorialReasons);
     printEditorialReason(" Editorial Reason Code - display Url  : ", $editorialReasons->displayUrlEditorialReasons);
     printEditorialReason(" Editorial Reason Code - short desc   : ",$editorialReasons->shortDescriptionEditorialReason);
     printEditorialReason(" Editorial Reason Code - title        : ", $editorialReasons->titleEditorialReasons);
     printEditorialReason(" Editorial Reason Code - url content  : ", $editorialReasons->urlContentEditorialReasons);
     printEditorialReason(" Editorial Reason Code - url          : ", $editorialReasons->urlEditorialReasons);
     printEditorialReason(" Editorial Reason Code - url string   : ", $editorialReasons->urlStringEditorialReasons);
   }
}

function printEditorialReason($msg,$reason)
{
  //php treats 1 element vs more than one element differently.
  if(isset($reason->int) && count($reason->int)==1)
  {
       debug($msg.$reason->int);
  }
  else if(isset($reason->int) && count($reason->int)>1)
  {
       debug($msg.implode(",",$reason->int));
  }
}

?>
