<?php
include('./serverConnectionAndRequest.php');


    $array = array();

    $내seq = $_GET['내고유번호'];
    $채팅방seq = $_GET['채팅방번호'];
    $입장여부 = $_GET['입장여부'];
    $성공여부; 

    if($입장여부 === 'true'){
        $is_enter =1;
    }else{
        $is_enter =0;
    }



    $sql = "UPDATE chat_room_participation
            SET is_enter = '$is_enter'
            WHERE chat_room_seq = '$채팅방seq' AND user_seq = '$내seq'";
 


    if($connect->query($sql)){

        $성공여부 = true;
    }else{
        $성공여부 = false;
    }



    $array['성공여부'] = $성공여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>