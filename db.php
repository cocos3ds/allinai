<?php
    $servername = "localhost";
    $username = "root"; // 替换为你的数据库用户名
    $password = "root"; // 替换为你的数据库密码
    $dbname = "exam_db";
    $port = 3307;

    // 创建连接
    $conn = new mysqli($servername, $username, $password, $dbname,$port);


    function writeLog($str){
        file_put_contents("log.txt",date("Y-m-d H:i:s").'---'.$str."\n",FILE_APPEND);
    }