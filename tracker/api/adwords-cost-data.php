<?php
require_once('soapclientfactory.php');
require_once('config.php');

$dbh = mysql_connect ($db_location, $username, $password) or die(mysql_error());
mysql_select_db ($database,$dbh);

// Report Variables:

//$arr = array("SearchOnly" => "Broad", "SearchOnly1" => "Phrase", "SearchOnly2" => "Exact", "ContentOnly" => "Broad", "ContentOnly1" => "Phrase", "ContentOnly2" => "Exact");
//$arr = array("ContentOnly" => "Broad");
//$arr = array("ContentOnly" => "Content");
// Need start and end date in this format: YYYY-MM-DD
//$now = time();
$start_time  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
//$end_time = mktime(23, 59, 59, date("m")  , date("d")-1, date("Y"));

//$start_time = $now - 86400;

$start_date = date("Y-m-d",$start_time);

//echo "Start date: $start_date<BR>";

$end_date = $start_date;



set_time_limit(0);

// Now we loop through all 6 required reports entering data as we find it.

//foreach($arr as $a => $b){

	$a = str_replace("1", "", $a);
	$a = str_replace("2", "", $a);
	$network = $a;
	$match_type = $b;

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
	 * This code sample retrieves a keyword report for the AdWords account that
	 * belongs to the customer issuing the request.
	 */

	# Provide AdWords login information.
	$email = 'beejeebers@gmail.com';
	$password = '098HHhehe232GdDD';
	$client_email = 'beejeebers@gmail.com';
	$useragent = 'Cubeophobic Inc: AdWords API PHP Sample Code';
	$developer_token = 'pWA9C0yZVLeR8nXJfQNvZQ';
	$application_token = 'ifqEeL10TR4l_vjzCoEV2A';

	# Define SOAP headers.
	$headers =
	  '<email>' . $email . '</email>'.
	  '<password>' . $password . '</password>' .
	  '<clientEmail>' . $client_email . '</clientEmail>' .
	  '<useragent>' . $useragent . '</useragent>' .
	  '<developerToken>' . $developer_token . '</developerToken>' .
	  '<applicationToken>' . $application_token . '</applicationToken>';

	# Set up service connection. To view XML request/response, change value of
	# $debug to 1. To send requests to production environment, replace
	# "sandbox.google.com" with "adwords.google.com".
	$namespace = 'https://adwords.google.com/api/adwords/v12';
	$report_service = 
	  SoapClientFactory::GetClient($namespace . '/ReportService?wsdl', 'wsdl');
	$report_service->setHeaders($headers);
	$debug = 0;

//if($network == "SearchOnly"){
	
/*
	
	# Create report job structure.
	$report_job =
	  '<selectedReportType>Keyword</selectedReportType>' .
	  '<name>Cost Data Report</name>' .
	  '<aggregationTypes>Summary</aggregationTypes>' .
	  //'<adWordsType>'.$network.'</adWordsType>' .
	  //'<keywordType>'.$match_type.'</keywordType>' .
	  '<startDay>'.$start_date.'</startDay>' .
	  '<endDay>'.$start_date.'</endDay>' .
	  '<selectedColumns>Campaign</selectedColumns>' .
	  '<selectedColumns>AdGroup</selectedColumns>' .
	  '<selectedColumns>Keyword</selectedColumns>' .
	  '<selectedColumns>KeywordId</selectedColumns>' .
	  //'<selectedColumns>KeywordStatus</selectedColumns>' .
	  //'<selectedColumns>KeywordMinCPC</selectedColumns>' .
	  //'<selectedColumns>KeywordDestUrlDisplay</selectedColumns>' .
	  '<selectedColumns>KeywordTypeDisplay</selectedColumns>' .
	  '<selectedColumns>Impressions</selectedColumns>' .
	  '<selectedColumns>Clicks</selectedColumns>' .
	  '<selectedColumns>CPC</selectedColumns>' .
	  '<selectedColumns>CTR</selectedColumns>' .
	  '<selectedColumns>Cost</selectedColumns>' .
	  '<selectedColumns>AveragePosition</selectedColumns>';
*/
	  	$report_job =
	  '<selectedReportType>Keyword</selectedReportType>' .
	  '<name>Cost Data Report</name>' .
	  '<aggregationTypes>Summary</aggregationTypes>' .
	  //'<adWordsType>'.$network.'</adWordsType>' .
	  //'<keywordType>'.$match_type.'</keywordType>' .
	  '<startDay>2008-10-01</startDay>' .
	  '<endDay>200/-10-30</endDay>' .
	  '<selectedColumns>Campaign</selectedColumns>' .
	  '<selectedColumns>AdGroup</selectedColumns>' .
	  '<selectedColumns>Keyword</selectedColumns>' .
	  '<selectedColumns>KeywordId</selectedColumns>' .
	  //'<selectedColumns>KeywordStatus</selectedColumns>' .
	  //'<selectedColumns>KeywordMinCPC</selectedColumns>' .
	  //'<selectedColumns>KeywordDestUrlDisplay</selectedColumns>' .
	  '<selectedColumns>KeywordTypeDisplay</selectedColumns>' .
	  '<selectedColumns>Impressions</selectedColumns>' .
	  '<selectedColumns>Clicks</selectedColumns>' .
	  '<selectedColumns>CPC</selectedColumns>' .
	  '<selectedColumns>CTR</selectedColumns>' .
	  '<selectedColumns>Cost</selectedColumns>' .
	  '<selectedColumns>AveragePosition</selectedColumns>';
/*
}else{
		$report_job =
	  '<selectedReportType>Adgroup</selectedReportType>' .
	  '<name>'.$network.' '.$match_type.' Report</name>' .
	  '<aggregationTypes>Summary</aggregationTypes>' .
	  '<adWordsType>'.$network.'</adWordsType>' .
//	  '<keywordType>'.$match_type.'</keywordType>' .
	  '<startDay>'.$start_date.'</startDay>' .
	  '<endDay>'.$start_date.'</endDay>' .
	  '<selectedColumns>Campaign</selectedColumns>' .
	  '<selectedColumns>AdGroup</selectedColumns>' .
//	  '<selectedColumns>Keyword</selectedColumns>' .
//	  '<selectedColumns>KeywordId</selectedColumns>' .
//	  '<selectedColumns>KeywordStatus</selectedColumns>' .
//	  '<selectedColumns>KeywordMinCPC</selectedColumns>' .
//	  '<selectedColumns>KeywordDestUrlDisplay</selectedColumns>' .
//	  '<selectedColumns>KeywordTypeDisplay</selectedColumns>' .
	  '<selectedColumns>Impressions</selectedColumns>' .
	  '<selectedColumns>Clicks</selectedColumns>' .
	  '<selectedColumns>CPC</selectedColumns>' .
	  '<selectedColumns>CTR</selectedColumns>' .
	  '<selectedColumns>Cost</selectedColumns>' .
	  '<selectedColumns>AveragePosition</selectedColumns>';
}
*/
	$request_xml =
	  '<validateReportJob>' .
	  '<job xmlns:impl="https://adwords.google.com/api/adwords/v12" ' .
	  'xsi:type="impl:DefinedReportJob">' .
	  $report_job .
	  '</job>' .
	  '</validateReportJob>';

	# Validate report.
	$report_service->call('validateReportJob', $request_xml);
	if ($debug) show_xml($report_service);
	if ($report_service->fault) show_fault($report_service);

	# Schedule report.
	$request_xml =
	  '<scheduleReportJob>' .
	  '<job xmlns:impl="https://adwords.google.com/api/adwords/v12" ' .
	  'xsi:type="impl:DefinedReportJob">' .
	  $report_job .
	  '</job>' .
	  '</scheduleReportJob>';
	$job_id = $report_service->call('scheduleReportJob', $request_xml);
	$job_id = $job_id['scheduleReportJobReturn'];
	if ($debug) show_xml($report_service);
	if ($report_service->fault) show_fault($service);

	# Wait for report to finish.
	$request_xml =
	  '<getReportJobStatus>' .
	  '<reportJobId>' .
	  $job_id .
	  '</reportJobId>' .
	  '</getReportJobStatus>';
	$status = $report_service->call('getReportJobStatus', $request_xml);
	$status = $status['getReportJobStatusReturn'];
	if ($debug) show_xml($report_service);
	if ($report_service->fault) show_fault($service);
	while ($status != 'Completed' and $status != 'Failed') {
	  echo 'Report job status is "' . $status . '".' . "<BR>";
	  sleep(30);
	  $status = $report_service->call('getReportJobStatus', $request_xml);
	  $status = $status['getReportJobStatusReturn'];
	  if ($debug) show_xml($report_service);
	  if ($report_service->fault) show_fault($service);
	}

	if ($status == 'Failed') {
	  echo 'Report job generation failed.' . "<BR>";
	  return;
	}

	/*
	# Download report.
	$request_xml =
	  '<getGzipReportDownloadUrl>' .
	  '<reportJobId>' .
	  $job_id .
	  '</reportJobId>' .
	  '</getGzipReportDownloadUrl>';
	$report_url = $report_service->call('getGzipReportDownloadUrl', $request_xml);
	$report_url = $report_url['getGzipReportDownloadUrlReturn'];
	if ($debug) show_xml($report_service);
	if ($report_service->fault) show_fault($service);
	echo 'Report is available at "' . $report_url . '".' . "<BR>";
	*/

	#Show Report.
	$request_xml =
	  '<getReportDownloadUrl>' .
	  '<reportJobId>' .
	  $job_id .
	  '</reportJobId>' .
	  '</getReportDownloadUrl>';
	$report_url = $report_service->call('getReportDownloadUrl', $request_xml);
	$report_url = $report_url['getReportDownloadUrlReturn'];
	if ($debug) show_xml($report_service);
	if ($report_service->fault) show_fault($service);
	echo 'Report is available at "' . $report_url . '".' . "<BR>";

	// have to tell the script what date range to look up.

	$search = urlencode($_REQUEST['search']);

	// url is the url provided by the api script

	$url = $report_url;

	//$url = "https://adwords.google.com/api/adwords/ReportDownload?t=AAAAACEXaccAAAEcuhu7mgAAAAACCo0sCDI15Q4C1LynKpUKNoWun_uqqxks";

	$ch = curl_init(); // Initialize a CURL session.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return Page contents.
	curl_setopt($ch, CURLOPT_URL, $url); // Pass URL as parameter.
	$res = curl_exec($ch); // grab URL and pass it to the variable.
	curl_close($ch); // close curl resource, and free up system resources.

	$xml = simplexml_load_string($res);

	$iv = array();

	$i = 0;

	foreach ($xml->table->rows->row as $Rows ){

		foreach($Rows->attributes() as $a => $b) {
			
			$iv[$a][$i] = $b;

		}

		$i++;
	}

	$divider = 1000000;

	$now = time();
	
	$mysql_datetime = gmdate("Y-m-d H:i:s", $start_time);
	
	for($j=0;$j<$i;$j++){
		$campaign = $iv[campaign][$j];
		$adgroup = $iv[adgroup][$j];
		$keyword = $iv[kwSite][$j];
		//$kw_status = $iv[siteKwStatus][$j];
		//$kw_mincpca = $iv[keywordMinCpc][$j];
		//$kw_mincpc = $kw_mincpca / $divider;
		//$kw_desturl = $iv[kwDestUrl][$j];
		$kw_match_type = strtolower($match_type);
		$imps = $iv[imps][$j];
		$clicks = $iv[clicks][$j];
		$cpca = $iv[cpc][$j];
		$cpc = $cpca / $divider;
		$costa = $iv[cost][$j];
		$cost = $costa / $divider;
		$pos = $iv[pos][$j];

		$sql = "INSERT INTO `cost` (`int_date`, `pretty_date`, `network`, `campaign`,`adgroup` ,`keyword` ,`keyword_status` ,`keyword_mincpc` ,`keyword_desturl` ,`match_type` ,`impressions` ,`clicks`, `cpc`, `total_cost` ,`position`) VALUES ('$start_time' , $mysql_datetime, '$network', '$campaign', '$adgroup', '$keyword', '$kw_status', '$kw_mincpc', '$kw_desturl', '$kw_match_type', '$imps', '$clicks', '$cpc', '$cost', '$pos')";
		echo $sql."<BR>";
		$result = mysql_query($sql,$dbh);
	}

//echo "TYPE: $network MATCH: $match_type DONE...<BR>";

//} // end foreach loop

// functions

	function show_xml($service) {
	  echo $service->request;
	  echo $service->response;
	  echo "<BR>";
	}

	function show_fault($service) {
	  echo "<BR>";
	  echo 'Fault: ' . $service->fault . "<BR>";
	  echo 'Code: ' . $service->faultcode . "<BR>";
	  echo 'String: ' . $service->faultstring . "<BR>";
	  echo 'Detail: ' . $service->faultdetail . "<BR>";
	  //exit(0);
	}
?>
