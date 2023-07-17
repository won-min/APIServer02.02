<?php
include('./serverConnectionAndRequest.php');

    $array = array();
 
    $내seq = $_GET['내고유번호'];
    $채팅방seq = $_GET['채팅방번호'];


    ////유저가 마지막으로 읽은 메시지seq를 채팅방의 마지막 메시지seq와 같도록

    //메시지seq select하기
    $sql1 = "SELECT last_message_seq
            FROM chat_room
            WHERE chat_room_seq = '$채팅방seq'";

    $result1 = $connect->query($sql1);
    $메시지seq;
    if($result1->num_rows >0){
        $row1 = $result1->fetch_assoc();
        $메시지seq = $row1['last_message_seq'];
    }

    //메시지seq update하기
    $sql2 = "UPDATE chat_room_participation
            SET last_read_message_seq = '$메시지seq'
            WHERE user_seq = '$내seq' AND chat_room_seq = '$채팅방seq'";
    $connect->query($sql2);



    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>

<?php
function update유저마지막읽은메시지($내seq, $채팅방seq){
    global $connect;

    //메시지seq select하기
    $sql1 = "SELECT last_message_seq
            FROM chat_room
            WHERE chat_room_seq = '$채팅방seq'";

    $result1 = $connect->query($sql1);
    $메시지seq;
    if($result1->num_rows >0){
        $row1 = $result1->fetch_assoc();
        $메시지seq = $row1['last_message_seq'];
    }


    //메시지seq update하기
    $sql2 = "UPDATE chat_room_participation
            SET last_read_message_seq = '$메시지seq'
            WHERE user_seq = '$내seq' AND chat_room_seq = '$채팅방seq'";
    $connect->query($sql2);
}


function get초대날짜($내seq, $채팅방seq){
    global $connect;
    $sql = "SELECT add_date
            FROM chat_room_participation
            WHERE user_seq = '$내seq' AND chat_room_seq = '$채팅방seq'";

    $result = $connect->query($sql);
    if($result->num_rows >0){
        $row = $result->fetch_assoc();
        return $row['add_date'];
    }
}
?>