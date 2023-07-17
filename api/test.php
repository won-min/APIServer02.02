<?php
// testAc에서 유저 닉네임 request할 때 쓰는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    
    $고유번호 = $requestData->고유번호;


    $sql = "SELECT user_name 
            FROM user 
            WHERE user_seq = '$고유번호'";

    //쿼리가 잘 수행되었다면 로그인페이지로 이동

    $result = $connect->query($sql);
    $row = $result->fetch_assoc();

    $array['닉네임'] = $row['user_name'];

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>