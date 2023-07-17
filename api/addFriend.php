<?php
include('./serverConnectionAndRequest.php');


    $array = array();

    $내고유번호 = $requestData->내고유번호;
    $친구고유번호 = $requestData->친구고유번호;
    $성공여부; 




    $sql = "INSERT INTO friendship(requester_seq, receiver_seq)
            VALUES('$내고유번호','$친구고유번호') ";
 


    if($connect->query($sql)){

        $성공여부 = true;
    }else{
        $성공여부 = false;
    }



    $array['성공여부'] = $성공여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>