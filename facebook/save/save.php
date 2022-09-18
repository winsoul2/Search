<?php

header('Content-Type: application/json');
$serverName = "127.0.0.1";
$userName = "root";
$userPassword = "";
$dbName = "uptest1_db";

$conn = mysqli_connect($serverName, $userName, $userPassword, $dbName);

$sql = "INSERT INTO `up_fb` (`count`,`name`, `audience_size`, `path`, `description`, `topic`)  VALUES ";
$stackSql = [];
foreach($_POST['data'] as $record) {
    $paths = implode('|', $record["path"]);
    $stackSql[] = "('{$record["id"]}','{$record["name"]}', '{$record["audience_size"]}', '{$paths}', '{$record["description"]}', '{$record["topic"]}')";
}

$sql .= implode(',', $stackSql) . ';';

$query = mysqli_query($conn, $sql);

var_dump($query, $conn->error);






?>