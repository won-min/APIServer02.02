<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $내고유번호 = $_GET['내고유번호'];
    $채팅방번호 = $_GET['채팅방번호'];

    
    //내가 참여중인 채팅방에 대한 정보 select
    $sql = "SELECT *
                FROM chat_room
                LEFT JOIN chat_room_participation
                ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
                WHERE user_seq = '$내고유번호' AND chat_room.chat_room_seq = '$채팅방번호'";

    $result = $connect->query($sql);
    if($result->num_rows >0){
        $row = $result->fetch_assoc();

        //채팅방이름
        if((string)$row['one_or_group'] === (string)0){ // 1:1일 때
            $array['채팅방이름'] = get닉네임($row['chat_room_seq'], $내고유번호);
        }else{ // 그룹일 때
            $array['채팅방이름'] = $row['name'];
        }
        

        //알림여부
        if((string)$row['is_alarmed'] === (string)0){
            $array['알림여부'] = true;
        }else{
            $array['알림여부'] = false;
        }


        //그룹여부
        if((string)$row['one_or_group'] === (string)0){
            $array['그룹여부'] = false;
        }else{
            $array['그룹여부'] = true;
        }

        
    }
   
    

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>
<?php


    function get닉네임($방seq, $내seq){
        global $connect;
        $sql = "SELECT user_name, user.user_seq AS user_seq
                FROM user
                LEFT JOIN chat_room_participation
                ON user.user_seq = chat_room_participation.user_seq
                WHERE chat_room_seq = '$방seq'";



        $result = $connect->query($sql);
        if($result->num_rows >0){
            while($row = $result->fetch_assoc()){
                if((string)$row['user_seq'] !== (string)$내seq){
                    return $row['user_name'];
                }
            }
        }
    }
?>