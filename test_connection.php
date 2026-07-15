<?php

$servername = "localhost";
$username = "story_user";
$password = "Testing3";
$dbname = "story_list";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("failed connection? " . $conn->connect_error);
}
echo "Success hi :3";

