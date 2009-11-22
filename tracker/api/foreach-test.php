<?php
$arr = array("SearchOnly" => "Broad", "SearchOnly1" => "Phrase", "SearchOnly2" => "Exact", "ContentOnly" => "Broad", "ContentOnly1" => "Phrase", "ContentOnly2" => "Exact");

echo "<PRE>";
print_r($arr);
echo "</PRE>";

foreach($arr as $a => $b){

	$a = str_replace("1", "", $a);
	$a = str_replace("2", "", $a);

	echo "$a to $b<BR>";
	

}
?>