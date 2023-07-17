<?php
include('./serverConnectionAndRequest.php');

    $array = array();
 
    $내seq = $_GET['내고유번호'];
    $채팅방seq = $_GET['채팅방번호'];

    $sql = "SELECT *
            FROM chat_room_participation LEFT JOIN user
            ON chat_room_participation.user_seq = user.user_seq
            WHERE chat_room_seq = '$채팅방seq'";


    

    $result = $connect->query($sql);

    
    if($result->num_rows >0){
        while($row = $result->fetch_assoc()){
            $참여자목록 = array();
            $참여자목록['participation_seq'] = $row['chat_room_participation_seq'];
            $참여자목록['user_seq'] = $row['user_seq'];
            $프로필사진경로 = $row['profile_image'];
            $참여자목록['name'] = $row['user_name'];

            //프로필사진이 있다면 base64인코딩
            if(file_exists($프로필사진경로)){
                
                // 파일을 가져와서 base64로 인코딩
                $참여자목록['profile_image'] = base64_encode(file_get_contents($프로필사진경로));
            }

            //친구인지 확인
            $sql_friend = "SELECT *
                FROM friendship
                WHERE requester_seq = '$내seq' AND receiver_seq = '{$row['user_seq']}'";
            
            $result_friend = $connect->query($sql_friend);
            if($result_friend->num_rows > 0){
                $참여자목록['is_friend'] = true;
            }else{
                $참여자목록['is_friend'] = false;
            }


            $array[] = $참여자목록;
        }
        
    }

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>