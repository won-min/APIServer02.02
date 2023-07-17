<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $아이디 = $requestData->아이디;
    $비밀번호 = $requestData->비밀번호;
    $성공여부 = false;
    $고유번호 = 0;

    $sql = "SELECT pw, user_seq
            FROM user 
            WHERE id = '$아이디'";


    $result = $connect->query($sql);

    //아이디를 가진 column이 있다면 비밀번호 비교. 비밀번호가 일치한다면 성공. 
    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        
        if (password_verify($비밀번호, $row['pw'])) { //해당 휴대폰번호로 가입한 계정이 있는지 확인. 
            $성공여부 = true;
            $고유번호 = $row['user_seq'];
        } 
    }





    $array['성공여부'] = $성공여부; 
    $array['고유번호'] = $고유번호;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>