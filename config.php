<?php

$host = 'stardock.cs.virginia.edu';
$dbname = 'cs4980-zyq9qe';
$user = 'cs4980-zyq9qe';
$password = 'tychonievich';


$db = new mysqli($host, $user, $password, $dbname);

if ($db->connect_error):
    die ("Could not connect to db " . $db->connect_error);
endif;


date_default_timezone_set('America/New_York');

session_start();

/**************************************************************************************************************************
 **************************************************************************************************************************
 *
 * DEFINE ADMINS
 *
 **************************************************************************************************************************
 **************************************************************************************************************************/
$admins = array();
$admins[] = "admin";

/**************************************************************************************************************************
 **************************************************************************************************************************
 *
 * DEFINE USER
 *
 **************************************************************************************************************************
 **************************************************************************************************************************/
$_SESSION['user'] = "zyq6qe";

?>