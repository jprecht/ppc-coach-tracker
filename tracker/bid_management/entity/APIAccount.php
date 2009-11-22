<?php
class YSMMasterAccount {
	var $id=0;
	var $user = "";
	var $password = "";
	var $license = "";
	var $accounts = array();
}

class YSMAccount {
	var $id=0;
	var $user = "";
	var $password = "";
	var $accountId = "";
	var $masterAccountId = "";
}

class AdwordsMasterAccount {
	var $id=0;
	var $user = "";
	var $password = "";
	var $applicationToken = "";
	var $developerToken = "";
	var $accounts = array();
}

?>