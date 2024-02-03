<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');

    $array = array();
 
    $고유번호 = $_GET['고유번호'];
    $닉네임 = null;

    $sql = "SELECT user_name
            FROM user
            WHERE user_seq = '$고유번호'";


    

    $result = $connect->query($sql);

    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        $닉네임 = $row['user_name'];
    }
    



    $array['닉네임'] = $닉네임;
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>