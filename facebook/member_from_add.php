<?php

$condb = mysqli_connect("localhost","root","","workshop_crud_member")
or die("Error : " . mysqli_error($condb));

mysqli_query($condb, "SET NAME 'utf8' ");
date_default_timezone_set('Asia/Bangkok');

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="">
<head>
<body>

<h1>เพิ่มข้อมูล </h1>>
        <form action="" method="post">
            usernaem
            <input type="text" name="username" placeholder="username" required>
            <br>
            Password
            <input type="password" name="password" placeholder="password"
                   required>

        </form>
    </body>
</html>

