<?php
// id중복검사하는 페이지 
// 아이디중복검사통과여부가 변수이름 

    include('./serverConnectionAndRequest.php');
    $array = array();
    $검사되는id = $_GET['id'];

    // id 중복검사 진행 
    $sql = "SELECT *
            FROM user
            WHERE id='$검사되는id';";
    $query_result = $connect->query($sql);

    // 일치하는 행이 1개 이상이면 중복 
    if($query_result->num_rows >0){
        $array['아이디중복검사통과여부'] = false;
    }else{ 
        $array['아이디중복검사통과여부'] = true;
    }

    header('Content-Type: application/json');
    echo json_encode($array);
?>