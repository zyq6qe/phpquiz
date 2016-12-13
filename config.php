<?php

$host = 'stardock.cs.virginia.edu';
$dbname = 'cs4980-zyq9qe';
$user = 'cs4980-zyq9qe';
$password = 'tychonievich';


$db = new mysqli($host, $user, $password, $dbname);

if ($db->connect_error):
    die ("Could not connect to db " . $db->connect_error);
endif;

session_start();
$_SESSION['user'] = "test";
//$_SESSION['quiz'] = 1;
