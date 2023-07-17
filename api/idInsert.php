<?php
// 회원가입 시 유저 정보를 DB에 insert하는 코드
// 성공여부
// error_log(urlencode("아이디"), 0);

include('./serverConnectionAndRequest.php');
    $array = array();
    
    $아이디 = $requestData->아이디;
    $비밀번호 = $requestData->비밀번호;
    $닉네임 = $requestData->닉네임;
    $휴대폰번호 = $requestData->휴대폰번호;
    $성공여부 = false;


    // 비밀번호 암호화. 알고리즘 및 cost는 기본값사용. 
    $해쉬된_비밀번호 = password_hash($비밀번호, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (id, pw, user_name, phone_number) 
            VALUES('$아이디', '$해쉬된_비밀번호', '$닉네임', '$휴대폰번호')"; 

    //쿼리가 잘 수행되었다면 로그인페이지로 이동

    if($connect->query($sql) === TRUE){
        $성공여부 = true;
    } 
    $array['성공여부'] = $성공여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>