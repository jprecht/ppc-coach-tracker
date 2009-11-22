<?php
require_once dirname(__FILE__).'/utils/CSVParser.php';
require_once dirname(__FILE__).'/utils/StringUtils.php';
require_once dirname(__FILE__).'/utils/HTTPFileUploader.php';
require_once dirname(__FILE__).'/../entity/APIAccount.php';

define("EWS_LOCATION_SERVICE_ENDPOINT",  "global.marketing.ews.yahooapis.com");
//define("EWS_LOCATION_SERVICE_ENDPOINT",  "sandbox.marketing.ews.yahooapis.com");
define("EWS_ACCESS_HTTP_PROTOCOL",  "https");
define("EWS_DEBUG", false);
define("EWS_VERSION", "V5");
define("EWS_NAMESPACE", "http://marketing.ews.yahooapis.com/".EWS_VERSION);
define("MARKET", "US");
define("TEMP_FILES_DIR", dirname(__FILE__)."/../ysm_api_temp/");
define("YSM_BULK_HEADERS", TEMP_FILES_DIR."headers.csv");
define("REPORT_WAIT_TIME", 60);

class YahooService {
    function setUser($masterAccount, $clientAccount) {
        global $EWS_HEADERS;

        $EWS_HEADERS = array(
            "username" => $masterAccount->user,
            "password" => $masterAccount->password,
            "onBehalfOfUsername" => $clientAccount->user,
            "onBehalfOfPassword" => $clientAccount->password,
            "masterAccountID" => $clientAccount->masterAccountId,
            "accountID" => $clientAccount->accountId,
            "license" => $masterAccount->license
        );
    }

    function getYahooBulkReport($masterAccount, $clientAccount) {
        $this->setUser($masterAccount, $clientAccount);

        $bulkService = createClient(EWS_VERSION."/BulkService", $clientAccount->accountId);

        $retObj = execute($bulkService,
            'downloadAccount',
            array( 'accountID' => $clientAccount->accountId, 'fileType' => 'TSV' )
        );

        $reportId = $retObj->out;

        $waitTime = REPORT_WAIT_TIME;

        do {
            sleep($waitTime);
            $retObj = execute($bulkService,
                'getBulkDownloadStatus',
                array('bulkDownloadID' => $reportId)
            );
            $waitTime *= 2;
        } while($retObj->out->status == "InProgress");


        $reportUrl = $retObj->out->downloadUrl;

        $page = "";
        $inFile = fopen($reportUrl, "r");

        while($line = fgets($inFile)) {
            $page .= $line;
        }

        fclose($inFile);
        return $page;
    }

    function uploadBulkReport($masterAccount, $clientAccount, $filename) {
        $this->setUser($masterAccount, $clientAccount);

        $bulkService = createClient(EWS_VERSION."/BulkService", $clientAccount->accountId);

        $retObj = execute($bulkService,
            'getBulkUploadTokenUrl',
            array( 'fileType' => "TSV", 'feedbackFileType' => 'ERRORS_ONLY', "enableFeedbackFileCompression" => "true" )
        );

        $reportId = $retObj->out->jobId;
        $uploadUrl = $retObj->out->url;

        $uploadUrl = parse_url($uploadUrl);

        $fileuploader = new HTTPFileUploader;
        $fileuploader->postFile("ssl://".$uploadUrl["host"], $uploadUrl["port"], $uploadUrl["path"], $filename);

        $waitTime = REPORT_WAIT_TIME;

        do {
            sleep($waitTime);
            $retObj = execute($bulkService,
                'getBulkUploadStatus',
                array('bulkUploadID' => $reportId)
            );
            $waitTime *= 2;
        } while($retObj->out->uploadStatus == "InProgress" || $retObj->out->uploadStatus == "FileInQueueForProcessing");
        print_r($retObj);
    }

    function downloadCampaignBudgets($masterAccount, $clientAccount, $campaigns) {
        foreach ($campaigns as $campaign) {
            $this->downloadCampaignBudget($masterAccount, $clientAccount, $campaign);
        }
    }

    function downloadCampaignBudget($masterAccount, $clientAccount, $campaign) {
        $this->setUser($masterAccount, $clientAccount);

        $budgetingService = createClient(EWS_VERSION."/BudgetingService", $clientAccount->accountId);

        $retObj = execute($budgetingService,
            'getCampaignDailySpendLimit',
            array( 'campaignID' => $campaign->campaignId)
        );

        $campaign->currentBid = $retObj->out->limit;
        $campaign->newBid = $retObj->out->limit;
    }

    function updateCampaignBudgets($masterAccount, $clientAccount, $campaigns) {
        foreach ($campaigns as $campaign) {
            $this->updateCampaignBudget($masterAccount, $clientAccount, $campaign);
        }
    }

    function updateCampaignBudget($masterAccount, $clientAccount, $campaign) {
        $this->setUser($masterAccount, $clientAccount);

        $budgetingService = createClient(EWS_VERSION."/BudgetingService", $clientAccount->accountId);

        $retObj = execute($budgetingService,
            'updateCampaignDailySpendLimit',
            array( 'campaignID' => $campaign->campaignId, 'spendLimit' => array('limit' => $campaign->newBid) )
        );
    }

    function upload($masterAccount, $clientAccount, $campaigns, $adgroups, $keywords) {
        $filename = TEMP_FILES_DIR."upload-".date("Y-m-d-h-i-s", time()).".csv";
        $updatedKeywords = $this->createBulkUpdateReport($campaigns, $adgroups, $keywords, $filename);
        $this->uploadBulkReport($masterAccount, $clientAccount, $filename);
        $this->updateCampaignBudgets($masterAccount, $clientAccount, $campaigns);
        return $updatedKeywords;
    }

    function createBulkUpdateReport($campaigns, $adgroups, $keywords, $filename) {
    // Copy headers file
        copy(YSM_BULK_HEADERS, $filename);

        // Add campaigns
        $this->writeBulkCampaignRows($campaigns, $filename);

        // Add adgroups
        $this->writeBulkAdgroupRows($adgroups, $filename);

        // Add Keywords
        return $this->writeBulkKeywordRows($keywords, $filename);
    }

    function writeBulkCampaignRows($campaigns, $filename) {
        foreach ($campaigns as $campaign) {
            if($campaign->engine == "yahoo") {
                $this->writeBulkCampaignRow($campaign, $filename);
            }
        }
    }

    function writeBulkAdgroupRows($adgroups, $filename) {
        foreach ($adgroups as $adgroup) {
            if($adgroup->campaign->engine == "yahoo") {
                $this->writeBulkAdgroupRow($adgroup, $filename);
            }
        }
    }

    function writeBulkKeywordRows($keywords, $filename) {
        $updatedKeywords = array();
        foreach ($keywords as $keyword) {
            if($keyword->adgroup->campaign->engine == "yahoo") {
                $this->writeBulkKeywordRow($keyword, $filename);
                $updatedKeywords[] = $keyword;
            }
        }
        return $updatedKeywords;
    }

    function writeBulkCampaignRow($campaign, $filename) {
        $row = array_pad(array(), 34, "");
        $row[0] = $campaign->name;
        $row[2] = "Campaign";
        $row[3] = ($campaign->newStatus == "Active") ? "On" : "Off";
        $row[11] = "On";
        $row[12] = "Advanced";
        $row[16] = "On";
        $row[23] = "Off";
        $row[24] = $campaign->campaignId;
        $row[26] = date("m/d/Y", time());
        $this->writeBulkRow($row, $filename);
    }

    function writeBulkAdgroupRow($adgroup, $filename) {
        $row = array_pad(array(), 34, "");
        $row[0] = $adgroup->campaign->name;
        $row[1] = $adgroup->name;
        $row[2] = "Ad Group";
        $row[3] = ($adgroup->newStatus == "Active") ? "On" : "Off";
        $row[8] = $adgroup->searchMaxCpc;
        $row[11] = "On";
        $row[12] = "Advanced";
        $row[16] = "On";
        $row[13] = $adgroup->newBid;
        $row[23] = "Off";
        $row[24] = $adgroup->campaign->campaignId;
        $row[28] = $adgroup->adgroupId;
        $row[29] = "Off";
        $this->writeBulkRow($row, $filename);
    }

    function writeBulkKeywordRow($keyword, $filename) {
        $row = array_pad(array(), 34, "");
        $row[0] = $keyword->adgroup->campaign->name;
        $row[1] = $keyword->adgroup->name;
        $row[2] = "Keyword";
        $row[3] = ($keyword->newStatus == "Active") ? "On" : "Off";
        $row[5] = $keyword->text;
        $row[7] = $keyword->newUrl;
        $row[8] = $keyword->newBid;
        $row[12] = $keyword->matchType;
        $row[23] = "Off";
        $row[24] = $keyword->adgroup->campaign->campaignId;
        $row[28] = $keyword->adgroup->adgroupId;
        $row[31] = $keyword->keywordId;
        $this->writeBulkRow($row, $filename);
    }

    function writeBulkRow($row, $filename) {
    // Open file
        $fp = fopen($filename, "a");
        $stringUtils = new StringUtils;

        foreach ($row as $field) {
        // Write field
            fwrite($fp, $stringUtils->asciiToUnicode($field));

            // Write tab
            fwrite($fp, $stringUtils->asciiToUnicode("\t"));
        }
        // Write newline
        fwrite($fp, $stringUtils->asciiToUnicode("\n"));
    }
}


//=================define constants and endpoints===============//
/*
 * NOTE: remember to switch to the timezone of your master account if it's different
 *       from the server timezone
 *
 * Example: date_default_timezone_set( "America/Los_Angeles" );
 */

/*
 * Locations gotten from location service are cached to avoid
 * calling it for every request and incurring quota penalties.
 */
$ACCOUNT_LOCATION_CACHE = array();

/*
 * Load all cached location entries
 */
loadLocationCache();
//print getYahooAccountReport($start, $end, $EWS_HEADERS["accountID"], $timezone);

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
function createClient($service, $accountID, $useLocationService=true) {
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
function createHeaders() {
    global $EWS_HEADERS;

    $headers = array();

    foreach($EWS_HEADERS as $aHeaderName => $aHeaderValue) {
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

function debug($msg) {
    if(EWS_DEBUG) echo "[debug] $msg\n";
}


function checkResponse($retObj) {
    if( isset($retObj->out->operationSucceeded) && !$retObj->out->operationSucceeded ) {
        $fault = $retObj->out->errors;
        trigger_error("SOAP Fault: (faultcode: {$fault->Error->code}, faultstring: {$fault->Error->message})",E_USER_ERROR);
    }
    else if(isset($retObj->operationSucceeded) && !$retObj->operationSucceeded) {
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
function getEndPointFromLocationService($serviceName, $accountID) {
    global $ACCOUNT_LOCATION_CACHE;

    $cachedAccountLocation = array_key_exists($accountID,$ACCOUNT_LOCATION_CACHE) ? $ACCOUNT_LOCATION_CACHE[$accountID] : NULL;

    if(!$cachedAccountLocation) {
        $client     = createClient(EWS_VERSION."/LocationService",NULL,false);

        if($client) {
            $response = execute($client,"getMasterAccountLocation",NULL);
            if($response) {
                $cachedAccountLocation = $response->out;
                persistAccountLocationCache($accountID,$cachedAccountLocation);
            }
        }
    }

    if(!$cachedAccountLocation) {
        trigger_error("Service Error: Failed to get account location from server: EWS_LOCATION_SERVICE_ENDPOINT",E_USER_ERROR);
    }

    return $cachedAccountLocation."/".$serviceName;
}

/**
 * Low-level function that executes the SOAP call and performs
 * error handling.
 */
function execute($soapClient, $operation, $params) {

    try {
        if($params) {
            $result = $soapClient->__soapCall(
                $operation,
                array(
                $params
                )
            );
        }
        else {
            $result = $soapClient->__soapCall($operation,array());
        }
    }
    catch (SoapFault $fault) {
//        print pClient->__getLastRequest();

        trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})",E_USER_ERROR);
    }

    //check for faults in result
    if (isset($result) && is_soap_fault($result)) {
        trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})",E_USER_ERROR);
    }
    else if(isset($result)) {
            return $result;
        }
        else {
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
function persistAccountLocationCache($accountID, $location) {
    global $ACCOUNT_LOCATION_CACHE, $CACHE_FILE_NAME;

    //Step 1: Store in the internal data structure
    $ACCOUNT_LOCATION_CACHE[$accountID] = $location;

    //Step 2: Store in a persistent store
    $cacheFile = getLocationCacheFileName();

    if (!$handle = fopen($cacheFile, 'a')) {
        echo "Cannot open file ($cacheFile)";
        exit;
    }

    $accountLocationEntry = "\n$accountID = $location";

    // Write $somecontent to our opened file.
    if (fwrite($handle, $accountLocationEntry) === FALSE) {
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
function getLocationCacheFileName() {
    global $CACHE_FILE_NAME;

    if(!isset($CACHE_FILE_NAME)) {
        $CACHE_FILE_NAME = "ews_cache_";
        $location        = EWS_LOCATION_SERVICE_ENDPOINT;

        for($i=0;$i<strlen(EWS_LOCATION_SERVICE_ENDPOINT);$i++) {
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
function loadLocationCache() {
    global $ACCOUNT_LOCATION_CACHE;

    $cacheFile = getLocationCacheFileName();

    if(!file_exists($cacheFile) || !is_file($cacheFile)) return;

    $lines = file($cacheFile);

    $ACCOUNT_LOCATION_CACHE = array();

    foreach($lines as $line_num => $line) {
        $line = trim($line);
        if(!$line) continue;
        if(strpos($line,'#')===0) continue;

        list($accountID, $location) = explode('=',$line);

        $accountID = trim($accountID);
        $location  = trim($location);

        debug("$accountID, $location;");

        if($accountID && $location) {
            $ACCOUNT_LOCATION_CACHE[$accountID] = $location;
        }
    }

    debug(count($ACCOUNT_LOCATION_CACHE)." account location read from cache.");

    if(EWS_DEBUG) print_r($ACCOUNT_LOCATION_CACHE);
}
?>