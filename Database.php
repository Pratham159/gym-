<?php

$servername = "sql202.infinityfree.com";
$username = "if0_41652411";
$pass = "ZZxPrathamm";
$db = "if0_41652411_usfitness";

$conn = mysqli_connect($servername, $username, $pass, $db);

if (!$conn) {
    die(mysqli_connect_error());
}



?>