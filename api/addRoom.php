<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $내고유번호 = $_GET['내고유번호'];
    $채팅방번호 = $_GET['채팅방번호'];

    
    //내가 참여중인 채팅방에 대한 정보 select
    $sql_list = "SELECT *
                FROM chat_room
                LEFT JOIN chat_room_participation
                ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
                WHERE user_seq = '$내고유번호' AND chat_room.chat_room_seq = '$채팅방번호'";

    $result_list = $connect->query($sql_list);
    $참여채팅방 = array();
    if($result_list->num_rows >0){
        while($row_list = $result_list->fetch_assoc()){
            //채팅방번호
            $참여채팅방['채팅방번호'] = $row_list['chat_room_seq'];

            //채팅방이름
            if((string)$row_list['one_or_group'] === (string)0){ // 1:1일 때
                $참여채팅방['채팅방이름'] = get닉네임($row_list['chat_room_seq'], $내고유번호);
            }else{ // 그룹일 때
                $참여채팅방['채팅방이름'] = $row_list['name'];
            }

            // //최근채팅메시지//////////////////////////////////////////////
            

            // //알림여부
            if((string)$row_list['is_alarmed'] === (string)0){
                $참여채팅방['알림여부'] = true;
            }else{
                $참여채팅방['알림여부'] = false;
            }

            //참여인원수
            if((string)$row_list['one_or_group'] === (string)1){ // 그룹일 때
                $참여채팅방['참여인원수'] = get참여인원수($row_list['chat_room_seq']);
            }

            // //최근채팅시간/////////////////////////////////////////////////

            //이미지
            $이미지목록 = array();
            if((string)$row_list['one_or_group'] === (string)0){ // 1:1일 때
                set이미지목록($row_list['chat_room_seq'], $내고유번호, $이미지목록, 1);
            }else{ //그룹일 때;
                set이미지목록($row_list['chat_room_seq'], $내고유번호, $이미지목록, 2);
            }
            $참여채팅방['이미지목록'] = $이미지목록;

            //그룹여부
            if((string)$row_list['one_or_group'] === (string)0){
                $참여채팅방['그룹여부'] = false;
            }else{
                $참여채팅방['그룹여부'] = true;
            }

            // // 안읽은메시지수///////////////////////////////////////////////


        }
    }
   
    

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($참여채팅방);

?>
<?php
    function set이미지목록($방seq, $내seq, &$이미지목록,$몇개){
        //내가 아닌 참여자들의 이미지를 가져와서 개수만큼 set
        global $connect;
        $sql = "SELECT user.user_seq AS user_seq, profile_image
                FROM user
                LEFT JOIN chat_room_participation
                ON user.user_seq = chat_room_participation.user_seq
                WHERE chat_room_seq = '$방seq'";



        $result = $connect->query($sql);
        if($result->num_rows >0){
            while($row = $result->fetch_assoc()){
                if($row['user_seq'] !== $내seq){
                    $프로필사진경로 = $row['profile_image'];
                    //프로필사진이 있다면 base64인코딩
                    if(file_exists($프로필사진경로)){
                        // 파일을 가져와서 base64로 인코딩
                        $이미지목록[] = base64_encode(file_get_contents($프로필사진경로));
                    }else{
                        $이미지목록[] = null;
                    }
                    $몇개 = $몇개-1;
                    //그룹이면 2번 이미지 넣고, 1:1이면 1번 이미지 넣고 탈출
                    if((string)$몇개 === (string)0){
                        return;
                    }
                }
            }

        }

    }


    function get참여인원수($채팅방seq){
        global $connect;
        $sql = "SELECT COUNT(*) AS count
                FROM chat_room
                LEFT JOIN chat_room_participation
                ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
                WHERE chat_room.chat_room_seq = '$채팅방seq'";

        $result = $connect->query($sql);
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            return $row['count'];
        }
    }

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