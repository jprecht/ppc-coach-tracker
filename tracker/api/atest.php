<?php
require_once('soapclientfactory.php');

require_once('lib/nusoap.php');

$network = "Wotogepa";
$url='http://wotogepa.directtrack.com/api/soap_affiliate.php?wsdl';
$co='wotogepa';
$add_code='CD3';
$password='haisi11';

$client = new nusoap_client($url,true);
//$client = new nusoap_client($url, true);
//$client = new soapclient($url);

$param = array (
'client' => $co,
'add_code' => $add_code, 
'password' => $password, 
'primary'=>'subid1'
);

$res = $client -> call('getSubIDStats', $param);

//$info = $client -> response;

$info = htmlspecialchars_decode($client -> response, ENT_QUOTES);

$p = explode('<?xml version="1.0" encoding="utf-8"?>', $info);

$info = '<?xml version="1.0" encoding="utf-8"?>'.$p[1];

$q = explode('</return>', $info);

$info = $q[0];


/*
echo "<PRE>";
print_r($p);
echo "</PRE>";
*/

/*
$res = '<'.$res;
$res = $res.'>';
str_replace('   ', '><', $res);
str_replace("\r\n", '>', $res);
str_replace("\/", '<', $res);

echo $res;

//$result = $client -> response;

echo "<PRE>";
print_r($res);
echo "</PRE>";

*/

//$info = str_replace('lt;', '<', $info);
//$info = str_replace('gt;', '>', $info);
//$info = str_replace('quot;', '"', $info);
//$info = htmlspecialchars_decode($res, ENT_QUOTES);
//$info = htmlspecialchars($res, ENT_QUOTES);
			//$info = html_entity_decode($info);

/*
echo "<PRE>";
var_dump($info);
echo "</PRE>";
*/

//$info = html_entity_decode($info);

try {
    $xml = new SimpleXMLElement($info);
 } catch (Exception $e) {
    // handle the error
    echo $e->getMessage();
 }

$xml = simplexml_load_string($info);

//var_dump($xml);

foreach ($xml->subid_stats as $opt) {
	$check_for_subid = $opt->primary_group;
	$lead = $opt->leads;
		if($lead > 0){
			//update sql table
			echo "You have a lead here<BR><BR>";
		}else{
			echo "No Leads to report<BR>";
		}

}

//$result = html_entity_decode($result, ENT_QUOTES);



echo "<pre>";
print_r($result);
echo "</pre>";


//$res = html_entity_decode($client->response, ENT_QUOTES);

////$rw = htmlspecialchars($res, ENT_QUOTES)

//$xml = simplexml_load_string($result);

//$xml = new SimpleXMLElement($result);


/*
$p = explode("xml version", $res);

$res = '<?xml version'.$p[2];

echo "<pre>";
print_r($res);
echo "</pre>";


*/

//$xml = simplexml_load_string($res);





//echo '<pre>' . html_entity_decode($result, ENT_QUOTES) . '</pre>';
//echo '<pre>' . htmlspecialchars($result, ENT_QUOTES) . '</pre>';

//echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
//echo '<pre>' . html_entity_decode($client->response, ENT_QUOTES) . '</pre>';



/*
header('Content-Type: Content-Type: text/xml'.'\r\n'); 
echo "<pre>";
print_r($result);
echo "</pre>";
*/
?>