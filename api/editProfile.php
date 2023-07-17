<?php
// 회원가입 시 유저 정보를 DB에 insert하는 코드
// 성공여부
// error_log(urlencode("아이디"), 0);

include('./serverConnectionAndRequest.php');
    $array = array();
    
    $고유번호 = $_POST['seq'];
    $닉네임 = $_POST['name'];
    $상태메시지 = $_POST['statemessage'];
    $성공여부 = false;
    $프로필사진변경여부 = false;// 변경되었다면 기존 경로에 있던 파일은 삭제
    $배경사진변경여부 = false; // 변경되었다면 기존 경로에 있던 파일은 삭제

    //원래 저장되어있던 프로필, 배경파일의 경로
    $sql_pre = "SELECT profile_image, background_image
            FROM user
            WHERE user_seq='$고유번호'";
    $result_pre = $connect->query($sql_pre);
    $row = $result_pre->fetch_assoc();


    //프로필사진 경로 만들기 및 이동
    $프로필저장경로 = $row['profile_image'];
    if(!empty($_FILES['profile_image'])){
        $프로필임시저장경로 = $_FILES['profile_image']['tmp_name'];
        $프로필파일이름 = time().rand(0, 20).$_FILES['profile_image']['name'];
        $프로필저장할폴더 = './userImage/';
        // $프로필저장경로 = $프로필저장할폴더.$프로필파일이름;
        $프로필저장경로 = realpath($프로필저장할폴더) . '/' . $프로필파일이름;
        move_uploaded_file($프로필임시저장경로, $프로필저장경로);
        $프로필사진변경여부 = true;
    }
    

    //배경사진 경로 만들기 및 이동
    $배경저장경로 = $row['background_image'];
    if(!empty($_FILES['backgrouned_image'])){
        $배경임시저장경로 = $_FILES['background_image']['tmp_name'];
        $배경파일이름 = time().rand(0, 20).$_FILES['background_image']['name'];
        $배경저장할폴더 = './userImage/';
        // $배경저장경로 = $배경저장할폴더.$배경파일이름;
        $배경저장경로 = realpath($배경저장할폴더) . '/' . $배경파일이름;
        move_uploaded_file($배경임시저장경로, $배경저장경로);
        $배경사진변경여부 = true;
    }
    


    

    //request받은 데이터들을 DB에 update
    $sql = "UPDATE user
    SET profile_image='$프로필저장경로', background_image='$배경저장경로', state_message='$상태메시지', user_name='$닉네임'
    WHERE user_seq = '$고유번호'";

    if($connect->query($sql) === TRUE){
        $성공여부 = true;

    }

    if($프로필사진변경여부){
        //원래 저장되어있던 프로필사진파일 제거
        unlink($row['profile_image']);
    }

    if($배경사진변경여부){
        //원래 저장되어있던 배경사진파일을 제거

        unlink($row['background_image']);
    }
    

    $array['성공여부'] = $성공여부;

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($array);

?>