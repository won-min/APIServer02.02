<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');


    $array = array();
 
    $휴대폰번호 = $requestData->휴대폰번호;
    $인증번호 = $requestData->인증번호;
    $성공여부 = false;
    $휴대폰번호중복여부 = false;

//phone_number
//authentication_code
//expired_time

    $sql = "SELECT authentication_code 
            FROM phone_authentication 
            WHERE phone_number = '$휴대폰번호'";


    $result = $connect->query($sql);

    //휴대폰번호를 가진 column이 있다면 인증번호 비교. 인증번호가 일치한다면 해당 휴대폰번호로 가입한 계정이 있는지 확인. 
    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        if ($row['authentication_code'] === $인증번호) { //해당 휴대폰번호로 가입한 계정이 있는지 확인. 
            $성공여부 = true;

            $sql = "SELECT * 
            FROM user 
            WHERE phone_number = '$휴대폰번호'";

            $result = $connect->query($sql);
            if ($result->num_rows > 0) { // 중복된다면
                $휴대폰번호중복여부 = true;
            } else { // 중복되지 않는다면
                $휴대폰번호중복여부 = false;
            }

        } else {
            $성공여부 = false;
        }

    }



    $array['성공여부'] = $성공여부;
    $array['휴대폰번호중복여부'] = $휴대폰번호중복여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>