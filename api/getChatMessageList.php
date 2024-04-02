<?php
include('./serverConnectionAndRequest.php');

    $array = array();
    $tempArray = array(); // 임시 array에 item들을 담았다가 reverse한 value들을 array에 넣으려 한다.
 
    $mySeq = $_GET['내고유번호'];
    $chatRoomSeq = $_GET['채팅방번호'];
    $LastMessageNumber = $_GET['마지막메시지번호'];
    $bringedRowNum = 30;
    $invitedDay = getinvitedDay($mySeq, $chatRoomSeq);


    // $sql_able = "SELECT is_able
    //             FROM chat_room_participation
    //             WHERE chat_room_seq = '$chatRoomSeq' AND user_seq ='$mySeq';";

    // $result_able = $connect->query($sql_able);
    // $row_able = $result_able->fetch_assoc();
    // $sql;
    // if((string)$row_able['is_able'] === (string)0){
    //     //싱글채팅방이면서 비활성화상태라는 뜻. 
    //     return;
    
    // }

    // $성공여부 = true;

    $sql = null;

    //조건에 따라 sql문이 달라지도록
    //LastMessageNumber가 null이라는건 채팅방에 들어어온 순간이라는 뜻, null이 아닌 경우 스크롤 이벤트
    if($LastMessageNumber === null){ ////////////////////////////////////////////////////////이렇게 비교하는게 맞는지 확인////////////////////////////////
        //null이라면
        $sql = "SELECT *, message.add_date AS add_date
        FROM message 
        LEFT JOIN user
        ON message.user_seq = user.user_seq
        WHERE chat_room_seq = '$chatRoomSeq' AND message.add_date >= '$invitedDay'
        ORDER BY message_seq DESC 
        LIMIT $bringedRowNum"; 
        

    }else{
        //null이 아니라면
        $sql = "SELECT *, message.add_date AS add_date
        FROM message 
        LEFT JOIN user
        ON message.user_seq = user.user_seq
        WHERE chat_room_seq = '$chatRoomSeq' AND message_seq < '$LastMessageNumber' AND message.add_date > '$invitedDay'
        ORDER BY message_seq DESC
        LIMIT $bringedRowNum"; //마지막 메시지번호보다 작은 번호들 중에서 큰 놈부터 20개 가져온다. 

    }
    

    $result = $connect->query($sql);

            
    if($result->num_rows >0){
        while($row = $result->fetch_assoc()){
            $chatMessageList = array();
            $profileImagePath = $row['profile_image'];
            //프로필사진이 있다면 base64인코딩
            if(file_exists($profileImagePath)){
                
                // 파일을 가져와서 base64로 인코딩
                $chatMessageList['프로필이미지'] = base64_encode(file_get_contents($profileImagePath));
            }
            $chatMessageList['이름'] = $row['user_name'];
            $chatMessageList['내용'] = $row['content']; 

            //다중이미지라면 이미지 세팅
            if((string)$row['is_image'] == (string)1){
                $message_seq = $row['message_seq'];
                $sql_image = "SELECT image_path
                            FROM message_image
                            WHERE message_seq = '$message_seq'";
                $result_image = $connect->query($sql_image);
        
                $imageArray = array();
                if($result_image->num_rows >0){
                    while($row_image = $result_image->fetch_assoc()){
                        //경로를 가지고 파일을 불러와 byte배열을 string으로
                        if(file_exists($row_image['image_path'])){
                        
                            // 파일의 값을 불러와서 set
                            // $string = implode(array_map('chr', file_get_contents($row_image['image_path']))); // chr은 chr()인데 얘는 숫자를 ASCII에 해당하는 문자로 바꿈. implode는 스트링으로 합치는거.
                            // $string = file_get_contents($row_image['image_path']);
                            $imageArray[] = $row_image['image_path'];

                        }
                    }
                }
                $chatMessageList['이미지경로목록'] = $imageArray;
            }
            
            //날짜 세팅
            $dateTime = new DateTime($row['add_date']); // mysql에서 뽑은 타임스탬프 변환
            $chatMessageList['날짜'] = ($dateTime->getTimestamp())*1000; // 밀리세컨드로 변환

            //안읽은사람수 세팅
            $chatMessageList['안읽은사람수'] = get안읽은사람수($chatRoomSeq, $row['message_seq']);

            //메시지번호 세팅
            $chatMessageList['메시지번호'] = $row['message_seq'];


            //중립메시지인지, 내껀지, 상대껀지 구분
            if((string)$row['is_neutral'] === (string)1){
                $chatMessageList['is_neutral'] = true;
                $chatMessageList['is_my'] = false;
                $chatMessageList['is_other'] = false;
            }else{
                $chatMessageList['is_neutral'] = false;
                if((string)$row['user_seq'] === (string)$mySeq){
                    $chatMessageList['is_my'] = true;
                    $chatMessageList['is_other'] = false;

                }else{
                    $chatMessageList['is_my'] = false;
                    $chatMessageList['is_other'] = true;
                }
            }
            if((string)$row['is_video'] === (string)1){
                $chatMessageList['is_video'] = true;
                $chatMessageList['내용'] = $row['content'];
                if((string)$row['content'] === "영상통화"){
                    (string)$chatMessageList['영상통화종류'] = 1;
                }else if((string)$row['content'] === "부재중"){
                    (string)$chatMessageList['영상통화종류'] = 2;
                }else if((string)$row['content'] === "취소"){
                    (string)$chatMessageList['영상통화종류'] = 3;
                }else{
                    (string)$chatMessageList['영상통화종류'] = 4;
                }
            }else{
                $chatMessageList['is_video'] = false;
            }
            

            $tempArray[] = $chatMessageList;
        }
    }

    $array = array_reverse($tempArray);

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>

<?php

function get안읽은사람수($chatRoomSeq, $메시지seq){
    global $connect;
    $sql = "SELECT COUNT(*)
            FROM chat_room_participation
            WHERE chat_room_seq = '$chatRoomSeq' AND (last_read_message_seq < '$메시지seq' or last_read_message_seq IS NULL);";

    $result = $connect->query($sql);
    if($result->num_rows >0){
        $row = $result->fetch_assoc();
        return $row['COUNT(*)'];
    }
}




function getinvitedDay($mySeq, $chatRoomSeq){
    global $connect;
    $sql = "SELECT add_date
            FROM chat_room_participation
            WHERE user_seq = '$mySeq' AND chat_room_seq = '$chatRoomSeq'";

    $result = $connect->query($sql);
    if($result->num_rows >0){
        $row = $result->fetch_assoc();
        return $row['add_date'];
    }
}
?>