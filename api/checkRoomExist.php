<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $내고유번호 = $requestData->내고유번호;
    $친구고유번호 = $requestData->친구고유번호;
    $채팅방존재여부 = false;
    $채팅방번호 =-1;


    
    //1:1 채팅방 중에서 내가 참여중인 채팅방, 참여정보 가져오기
    $sql_me = "SELECT *, chat_room.chat_room_seq As room_seq
    FROM chat_room
    LEFT JOIN chat_room_participation
    ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
    WHERE one_or_group = 0 AND user_seq = '$내고유번호'";

    $result_me = $connect->query($sql_me);
    $내참여채팅방 = array();
    if($result_me->num_rows >0){
        while($row_me = $result_me->fetch_assoc()){
            $내참여채팅방[] = $row_me['room_seq'];
        }
    }

    //1:1 채팅방 중에서 친구가 참여중인 채팅방, 참여정보 가져오기
    $sql_friend = "SELECT *, chat_room.chat_room_seq As room_seq
    FROM chat_room
    LEFT JOIN chat_room_participation
    ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
    WHERE one_or_group = 0 AND user_seq = '$친구고유번호'";

    $result_friend = $connect->query($sql_friend);
    $친구참여채팅방 = array();
    if($result_friend->num_rows >0){
        while($row_friend = $result_friend->fetch_assoc()){
            $친구참여채팅방[] = $row_friend['room_seq'];
        }
    }

    //내가 참여중인 1:1 채팅방 목록, 친구가 참여중인 1:1채팅방 목록을 비교해서 일치하는 방이 있는지 찾기
    
    for ($i = 0; $i < count($내참여채팅방); $i++) {
        for ($j = 0; $j < count($친구참여채팅방); $j++) {
            if($내참여채팅방[$i] === $친구참여채팅방[$j]){
                $채팅방존재여부 = true;
                $채팅방번호 = $내참여채팅방[$i];
                break;
            }
        }
        if($채팅방존재여부==true){
            break;
        }
    }
   
    
    $array['채팅방존재여부'] = $채팅방존재여부; 
    $array['채팅방번호'] = $채팅방번호;
    $array['내참여채팅방'] = $내참여채팅방;
    $array['친구참여채팅방'] = $친구참여채팅방;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>