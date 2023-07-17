<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $내고유번호 = $requestData->내고유번호;
    $친구고유번호 = $requestData->친구고유번호;
    $성공여부 = false;

    $sql = "DELETE FROM friendship
            WHERE requester_seq = '$내고유번호' AND receiver_seq = '$친구고유번호'";

    if($connect->query($sql) === TRUE){
        $성공여부 = true;
    }
    
    

    $array['성공여부'] = $성공여부; 

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>