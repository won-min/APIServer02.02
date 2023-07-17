<?php

include('./serverConnectionAndRequest.php');

    $array = array();
 
    $내고유번호 = $_GET['내고유번호'];


    $sql = "SELECT user_name, state_message, profile_image, background_image, receiver_seq, phone_number
            FROM friendship
            LEFT JOIN user
            ON friendship.receiver_seq = user.user_seq
            WHERE friendship.requester_seq = '$내고유번호'";


    

    $result = $connect->query($sql);

    if($result->num_rows >0){
        while($row = $result->fetch_assoc()){
            $ProfileInfo = array();

            $ProfileInfo['고유번호'] = $row['receiver_seq']; // 친구고유번호
            $ProfileInfo['닉네임'] = $row['user_name'];
            $ProfileInfo['상태메시지'] = $row['state_message'];
            $ProfileInfo['휴대폰번호'] = $row['phone_number'];

            $프로필사진경로 = $row['profile_image'];
            //프로필사진이 있다면 base64인코딩
            if(file_exists($프로필사진경로)){
                // 파일을 가져와서 base64로 인코딩
                $ProfileInfo['프로필사진'] = base64_encode(file_get_contents($프로필사진경로));
            }

            $배경사진경로 = $row['background_image'];
            //배경사진이 있다면 base64인코딩
            if(file_exists($배경사진경로)){
                // 파일을 가져와서 base64로 인코딩
                $ProfileInfo['배경사진'] = base64_encode(file_get_contents($배경사진경로));
            }

            $array[] = $ProfileInfo;
        }
    }
    
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>