<?php
//채팅에서 메시지에 들어가는 이미지를 반환하는 코드

include('./serverConnectionAndRequest.php');

    $array = array();
    $imagePath = $_GET['이미지경로'];
    $imageString;

    //이미지가 있다면 base64인코딩
    if(file_exists($imagePath)){
                    
        // 파일 내용 가져오기
        $imageString = file_get_contents($imagePath);
    }
  
    header('Content-Type: application/json; charset=UTF-8');
    $array["이미지문자열"] = $imageString;
    echo json_encode($array);

?>