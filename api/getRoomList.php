<?php
// 로그인 시 사용하는 코드

include('./serverConnectionAndRequest.php');
    $array = array();
    $내고유번호 = $_GET['내고유번호'];

    // 내가 참여중인 채팅방에 대한 정보 select
    $sql_list = "SELECT *
                FROM chat_room
                LEFT JOIN chat_room_participation
                ON chat_room.chat_room_seq = chat_room_participation.chat_room_seq
                WHERE user_seq = '$내고유번호' AND is_able = 1
                ORDER BY chat_room.last_modified_date ASC";



    $result_list = $connect->query($sql_list);
    $참여채팅방 = array();
    if($result_list->num_rows >0){
        while($row_list = $result_list->fetch_assoc()){
            $채팅방번호 = $row_list['chat_room_seq'];

            $sql = "SELECT *
                    FROM message
                    WHERE chat_room_seq = '$채팅방번호' AND is_neutral != 1
                    ORDER BY add_date DESC
                    LIMIT 1;";

            $result = $connect->query($sql);
            $참여채팅방['안읽은메시지수'] = $result->num_rows;
            if($result->num_rows>0){
                while($row = $result->fetch_assoc()){               
        // //최근채팅메시지//////////////////////////////////////////////
                if($row['content'] == NULL){
                    $참여채팅방['최근채팅메시지'] = "사진";
                }else{
                    $참여채팅방['최근채팅메시지'] = $row['content'];
                }

        // //최근채팅시간/////////////////////////////////////////////////
                    //날짜 세팅
                    $dateTime = new DateTime($row['add_date']); // mysql에서 뽑은 타임스탬프 변환
                    $날짜문자열 = $dateTime->format('A g:i'); // 형식 맞추기
                    $날짜문자열 = str_replace('PM', '오후', $날짜문자열);
                    $날짜문자열 = str_replace('AM', '오전', $날짜문자열);
                    $참여채팅방['최근채팅시간'] = $날짜문자열;// 형식에 맞춰 반환
                    
        // // 안읽은메시지수///////////////////////////////////////////////

                    $마지막으로읽은메시지seq = get마지막으로읽은메시지seq($채팅방번호,$내고유번호);
                    $안읽은메시지수 = get안읽은메시지수($채팅방번호, $마지막으로읽은메시지seq);
                    $참여채팅방['안읽은메시지수'] = $안읽은메시지수;


                }
            }



        
            //채팅방번호
            $참여채팅방['채팅방번호'] = $row_list['chat_room_seq'];

            //채팅방이름
            if((string)$row_list['one_or_group'] === (string)0){ // 1:1일 때
                $참여채팅방['채팅방이름'] = get닉네임($row_list['chat_room_seq'], $내고유번호);
            }else{ // 그룹일 때
                $참여채팅방['채팅방이름'] = $row_list['name'];
            }

            

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



            $array[] = $참여채팅방;
        }
    }
   
    

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>
<?php

    function get마지막으로읽은메시지seq($채팅방번호, $내고유번호){
        global $connect;
        $sql = "SELECT last_read_message_seq
                FROM chat_room_participation
                WHERE chat_room_seq = '$채팅방번호' AND user_seq = '$내고유번호'";

        $result = $connect->query($sql);
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            return $row['last_read_message_seq'];
        }
    }


    function get안읽은메시지수($채팅방번호, $마지막으로읽은메시지seq){
        if(empty($마지막으로읽은메시지seq)){
            $마지막으로읽은메시지seq = 0;
        }


        global $connect;
        $sql = "SELECT COUNT(*)
                FROM message
                WHERE chat_room_seq = '$채팅방번호' AND is_neutral != 1 AND message_seq > '$마지막으로읽은메시지seq';";

        $result = $connect->query($sql);
        if($result->num_rows >0){
            $row = $result->fetch_assoc();
            return $row['COUNT(*)'];
        }
    }




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