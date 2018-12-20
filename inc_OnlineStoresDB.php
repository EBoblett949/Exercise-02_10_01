<?php
    $errorMsgs = array();
    $hostname = "localhost";
    $username = "adminer";
    $passwd = "south-proud-55";
    $DBName = "onlinestores1";
    $DBConnect = @new mysqli($hostname, $username, $passwd, $DBName);
    if ($DBConnect->connect_error) {
        $errorMsgs[] = "Unable to connect to the database server." . " Error code " . $DBConnect->connect_errno . ": " . $DBConnect->connect_error;
    }
?>