<?php
require_once dirname(__FILE__).'/../../config.php';

function get_conn() {
    global $db_location;
    global $username;
    global $password;
    global $database;

    $conn= mysql_pconnect($db_location, $username, $password) or die ('I cannot connect to the database because : ' . mysql_error());

    mysql_select_db ($database, $conn);
    mysql_query("SET SESSION SQL_BIG_SELECTS=1") or die("didn't work");
    return $conn;
}

function close_conn($conn) {
    mysql_close($conn);
}
?>