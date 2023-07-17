<?php
// 인증번호 일치 여부를 판단하는 코드

include('./serverConnectionAndRequest.php');


    $array = array();

    $고유번호 = $requestData->고유번호;
    $연락처목록 = $requestData->연락처목록;
    $친구목록 = array();


    //상대가 계정이 있고, 친구인 상태가 아닐 때에만 INSERT

    for($i=0; $i<count($연락처목록); $i++){
        $친구고유번호;
        $sql_check = "SELECT user_seq
                        FROM user
                        WHERE phone_number = '$연락처목록[$i]'"; 
        
        
        $result_check = $connect->query($sql_check);

        //상대가 계정이 있다면
        if($result_check->num_rows >0){
            $row = $result_check->fetch_assoc();
            $친구고유번호 = $row['user_seq'];

            $sql_friend_check = "SELECT *
                        FROM friendship
                        WHERE requester_seq = '$고유번호' AND receiver_seq = '$친구고유번호'"; 

            $result_friend_check = $connect->query($sql_friend_check);

            //상대가 나와 친구추가가 되어있지 않다면
            if($result_friend_check->num_rows === 0){
                $sql_insert_friendship = "INSERT INTO friendship (requester_seq, receiver_seq)
                                      VALUES ('$고유번호', '$친구고유번호')";
                $connect->query($sql_insert_friendship);
            }

        }

    }


    $array['고유번호'] = $고유번호;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>