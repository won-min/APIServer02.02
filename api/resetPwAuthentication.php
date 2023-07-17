<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');

    $array = array();
 
    $휴대폰번호 = $requestData->휴대폰번호;
    $인증번호 = $requestData->인증번호;
    $아이디 = $requestData->아이디;
    $성공여부 = false;
    $계정존재여부 = false;
    $아이디일치여부 = false;


    $sql_authen = "SELECT authentication_code 
            FROM phone_authentication 
            WHERE phone_number = '$휴대폰번호'";


    $result_authen = $connect->query($sql_authen);

    //휴대폰번호를 가진 column이 있다면 인증번호 비교. 인증번호가 일치한다면 해당 휴대폰번호로 가입한 계정이 있는지 확인. 
    if($result_authen->num_rows >0){
        $row_authen = $result_authen->fetch_assoc();

        if ($row_authen['authentication_code'] === $인증번호) { //해당 휴대폰번호로 가입한 계정이 있는지 확인. 
            $성공여부 = true;

            $sql = "SELECT * 
            FROM user 
            WHERE phone_number = '$휴대폰번호'";

            $result = $connect->query($sql);
            if ($result->num_rows > 0) { // 계정이 있다면
                $row = $result->fetch_assoc();
                $계정존재여부 = true;
                
                if($row['id'] === $아이디){ // 해당 계정과 아이디가 일치하는지 확인
                    $아이디일치여부 = true;
                }else{ // 계정과 아이디가 일치하지 않는다면
                    $아이디일치여부 = false;
                }
            } else { // 계정이 없다면
                $계정존재여부 = false;
            }
        } else {
            $성공여부 = false;
        }

    }


    $array['성공여부'] = $성공여부;
    $array['계정존재여부'] = $계정존재여부;
    $array['아이디일치여부'] = $아이디일치여부;
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>