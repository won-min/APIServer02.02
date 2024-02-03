<?php

/* mysql 데이터베이스와 연결 및 json형태로 온 request데이터를 사용할 수 있게 가공하는 코드 */

/////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// // ini_set('log_errors', '1');
//////////////////////////////////////////////////////////////




    // PHP랑 mysql이랑 연결

    // $hostname = "43.200.181.222"; 
    $hostname = "localhost"; 
    $user = "ubuntu"; 
    $password = "dnjsalschl12";
    $dbname = "talktalk"; 

    $connect = new mysqli($hostname, $user, $password, $dbname);

    // Check connection
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    //HTTP의 raw한 body를 읽는 함수(json으로된거 읽을 수 있는) + json 해독 함수
    $requestBody = file_get_contents("php://input");
    $requestData;
    if (!empty($requestBody)) {
        $requestData = json_decode($requestBody);
    } 
    
?>