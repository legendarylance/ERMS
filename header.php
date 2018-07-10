<?php

// TESTING SESSION THING
session_start();
// TESTING IF SESSION HAS BEEN STARTED
if (!isset($_SESSION['session_user'])){
    header("location: login.php");
}
else{
    $user = $_SESSION["session_user"];
}
$servername = "localhost";
$username = "ayazhan";
$password = "";
$dbname = "ERMS";
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ERMS</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>

<body>
