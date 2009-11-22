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
 * This code sample retrieves a keyword report for the AdWords account that
 * belongs to the customer issuing the request.
 */

require_once('soapclientfactory.php');

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

# Create report job structure.
$report_job =
  '<selectedReportType>Keyword</selectedReportType>' .
  '<name>Sample Keyword Report</name>' .
  '<aggregationTypes>Summary</aggregationTypes>' .
  '<adWordsType>SearchOnly</adWordsType>' .
  '<keywordType>Exact</keywordType>' .
  '<startDay>2008-09-23</startDay>' .
  '<endDay>2008-09-24</endDay>' .
  '<selectedColumns>Campaign</selectedColumns>' .
  '<selectedColumns>AdGroup</selectedColumns>' .
  '<selectedColumns>Keyword</selectedColumns>' .
  '<selectedColumns>KeywordId</selectedColumns>' .
  '<selectedColumns>KeywordStatus</selectedColumns>' .
  '<selectedColumns>KeywordMinCPC</selectedColumns>' .
  '<selectedColumns>KeywordDestUrlDisplay</selectedColumns>' .
  '<selectedColumns>Impressions</selectedColumns>' .
  '<selectedColumns>Clicks</selectedColumns>' .
  '<selectedColumns>CPC</selectedColumns>' .
  '<selectedColumns>CTR</selectedColumns>' .
  '<selectedColumns>Cost</selectedColumns>' .
  '<selectedColumns>AveragePosition</selectedColumns>';
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
  echo 'Report job status is "' . $status . '".' . "\n";
  sleep(30);
  $status = $report_service->call('getReportJobStatus', $request_xml);
  $status = $status['getReportJobStatusReturn'];
  if ($debug) show_xml($report_service);
  if ($report_service->fault) show_fault($service);
}

if ($status == 'Failed') {
  echo 'Report job generation failed.' . "\n";
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
echo 'Report is available at "' . $report_url . '".' . "\n";
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
echo 'Report is available at "' . $report_url . '".' . "\n";
function show_xml($service) {
  echo $service->request;
  echo $service->response;
  echo "\n";
}

function show_fault($service) {
  echo "\n";
  echo 'Fault: ' . $service->fault . "\n";
  echo 'Code: ' . $service->faultcode . "\n";
  echo 'String: ' . $service->faultstring . "\n";
  echo 'Detail: ' . $service->faultdetail . "\n";
  exit(0);
}
?>
