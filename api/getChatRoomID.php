<?php
// 친구seq가지고 1:1채팅방의 seq를 반환하는 부분

include('./serverConnectionAndRequest.php');
    $array = array();
    $내고유번호 = $_GET['내고유번호'];
    $친구고유번호 = $_GET['친구고유번호'];

    
    //싱글채팅방에서  싱글채팅방 대한 정보 select
    $sql = "SELECT *
            FROM chat_room
            LEFT JOIN chat_room_participation
            ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
            WHERE user_seq = '$내고유번호' OR user_seq = '$친구고유번호' AND one_or_group = 0";

    $result = $connect->query($sql);

    $채팅방번호목록 = array();
    if($result->num_rows >0){
        while($row = $result->fetch_assoc()){
            $채팅방번호목록[] = $row['chat_room_seq'];
        }
    }
    //친구고유번호가 있는 채팅방seq검색
    //같은 번호가 2번 나오면 그게 정답.

    // 각 요소의 등장 횟수를 계산.
    $count_values = array_count_values($채팅방번호목록);

    // 중복된 값을 찾습니다.
    $keys = array_keys($count_values);
    $채팅방번호;
    for ($i = 0; $i < count($keys); $i++) {
        if ($count_values[$keys[$i]] > 1) {
            // client에게 보낼 배열에 추가.
            $채팅방번호 = $keys[$i];
        }
    }
   
    

    header('Content-Type: application/json; charset=UTF-8');
    echo $채팅방번호;
?>