<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');

    $array = array();
 
    $고유번호 = $_GET['고유번호'];
    $닉네임 = null;
    $상태메시지 = null;
    $프로필사진 = null; 
    $배경사진 = null;
    $휴대폰번호 = null;
    $성공여부 = false;

    $sql = "SELECT user_name, state_message, profile_image, background_image, phone_number
            FROM user
            WHERE user_seq = '$고유번호'";


    

    $result = $connect->query($sql);

    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        $닉네임 = $row['user_name'];
        $상태메시지 = $row['state_message'];
        $프로필사진경로 = $row['profile_image'];
        $배경사진경로 = $row['background_image'];
        $휴대폰번호 = $row['phone_number'];

        //프로필사진이 있다면 base64인코딩
        if(file_exists($프로필사진경로)){
            
            // 파일을 가져와서 base64로 인코딩
            $프로필사진 = base64_encode(file_get_contents($프로필사진경로));
        }

        //배경사진이 있다면 base64인코딩
        if(file_exists($배경사진경로)){
            // 파일을 가져와서서 base64로 인코딩
            $배경사진 = base64_encode(file_get_contents($배경사진경로));
        }
        
        $성공여부 = true; 
    }
    


    $array['성공여부'] = $성공여부;
    $array['닉네임'] = $닉네임;
    $array['상태메시지'] = $상태메시지;
    $array['프로필사진'] = $프로필사진;
    $array['배경사진'] = $배경사진;
    $array['휴대폰번호'] = $휴대폰번호;
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>