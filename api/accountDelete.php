<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $비밀번호 = $requestData->비밀번호;
    $고유번호 = $requestData->고유번호;
    $성공여부 = false;
    $비밀번호일치여부 = false;
    

    $sql_pw = "SELECT pw
            FROM user 
            WHERE user_seq = '$고유번호'";


    $result_pw = $connect->query($sql_pw);
    $row_pw= $result_pw->fetch_assoc();

    //비밀번호가 일치하는지 검사. 
    if (password_verify($비밀번호, $row_pw['pw'])) { 
        $비밀번호일치여부 = true;

        //비밀번호가 일치한다면 계정 delete
        $sql = "DELETE FROM user 
                WHERE user_seq=$고유번호";

        if($connect->query($sql) === TRUE){
            $성공여부 = true;
        }

    } 

    
    

    $array['성공여부'] = $성공여부; 
    $array['고유번호'] = $고유번호;
    $array['비밀번호일치여부'] = $비밀번호일치여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>