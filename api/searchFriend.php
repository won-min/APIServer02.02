<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');


    $array = array();

    $내고유번호 = $_GET['내고유번호'];
    $아이디및휴대폰번호 = $_GET['아이디및휴대폰번호'];

    $닉네임;
    $프로필사진;
    $있음여부;
    $친구여부;
    $친구고유번호;



    $sql = "SELECT user_seq, user_name, profile_image
            FROM user 
            WHERE phone_number = '$아이디및휴대폰번호' OR id = '$아이디및휴대폰번호'";
 

    $result = $connect->query($sql);

    //row가 있다면 계정을 찾은 것
    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        //해당 계정과 친구추가되어있는지 확인. friendship 테이블을 이용. 
        $sql_friend = "SELECT friendship_seq
               FROM friendship
               WHERE requester_seq = '$내고유번호' AND receiver_seq = '{$row['user_seq']}'";

        $result_friend = $connect->query($sql_friend);

        $친구행존재;
        //이미 친구추가된 계정인 경우
        if($result_friend->num_rows>0){
            $친구행존재 = true;
        }else{ //친구추가되지 않은 계정인 경우
            $친구행존재 = false;
        }

        
        //사용자 자신의 계정을 검색한 경우
        if($row['user_seq'] === $내고유번호){
            $있음여부 = false;
            $친구여부 = false; 
        }else if($친구행존재){ // 검색한 계정이 이미 친구추가된 계정이라면
            $있음여부 = false;
            $친구여부 = true; 

        }else{ // 친구추가할 수 있는 계정
            $친구고유번호 = $row['user_seq']; // 친구 고유번호
            $닉네임 = $row['user_name'];

            //프로필사진이 있다면 base64인코딩
            if(file_exists($row['profile_image'])){
                // 파일을 가져와서 base64로 인코딩
                $프로필사진 = base64_encode(file_get_contents($row['profile_image']));
            }
            $있음여부 = true;
            $친구여부 = false; 
        }

    }else{// 계정이 없을 때
        $있음여부 = false;
        $친구여부 = false; 
    }


    $array['내고유번호'] = $내고유번호;
    $array['친구고유번호'] = $친구고유번호;
    $array['닉네임'] = $닉네임;
    $array['프로필사진'] = $프로필사진;
    $array['있음여부'] = $있음여부;
    $array['친구여부'] = $친구여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>