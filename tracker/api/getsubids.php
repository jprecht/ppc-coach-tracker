<?php
// Insert the NuSOAP code
require_once('lib/nusoap.php');

$network = "Wotogepa";
$url='http://wotogepa.directtrack.com/api/soap_affiliate.php?wsdl';
$co='wotogepa';
$add_code='CD3';
$password='haisi11';

$client = new nusoap_client($url, true);

/*
$login = array(
'client' => $co, 
'add_code' => $add_code, 
'password' => $password, 
'program_id'=>'', 
'ignore_campaign_images'=>'', 
'category'=>$category
);
*/
/*
$login = array(
'add_code' => "$add_code",
'password' => "$password",
'client' => "$co",
'primary' => "subid1"
);
*/

$result = $client->call('getSubIDStats', array('client' => $co, 'add_code' => $add_code, 'password' => $password, 'primary'=>'subid1'));

//$result = $client->call('campaignInfo', array('client' => $co, 'add_code' => $add_code, 'password' => $password, 'program_id'=>'', 'ignore_campaign_images'=>'', 'category'=>$category));

//$result = $client->call('campaignInfo', array('values' => $login));


if ($client->fault) {
    echo '<h2>Fault</h2><pre>';
    print_r($result);
    echo '</pre>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<h2>Error</h2><pre>' . $err . '</pre>';
    } else {
        // Display the result

	
		echo htmlspecialchars($result);
		echo "<BR><BR>";
		echo $client->response;
	
	

		$pieces = explode('<?xml', $client->repsonse);

		$xml_end = $pieces[1];

		$xml_string = '<?xml'.$xml_end;

		echo $xml_string;

		/*
		$convert = htmlspecialchars($client->response, ENT_QUOTES);
		//$convert = htmlspecialchars($result, ENT_QUOTES);
		$xml_string = html_entity_decode($convert);
		//echo '<pre>' . html_entity_decode($convert) . '</pre>';

		

		$xml_string = str_replace('&lt', '<', $xml_string);
		$xml_string = str_replace('&gt', '>', $xml_string);

		$xml = new SimpleXMLElement($xml_string);
		*/
		


		/*
        echo '<h2>Result</h2><pre>';
        print_r($result);
		echo '</pre>';
		*/
    }
}

/*
// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
$convert = htmlspecialchars($client->response, ENT_QUOTES);
echo '<pre>' . html_entity_decode($convert) . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
*/
?>

