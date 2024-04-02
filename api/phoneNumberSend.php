<?php
// 휴대폰 인증하는 페이지 
// 성공여부

    
    include('./serverConnectionAndRequest.php');

    만료인증번호삭제();

    $array = array();
    $휴대폰번호 = $requestData->휴대폰번호;
    $성공여부 = true;
    $인증번호='';
    $인증가능시간 = 3*60; // 3분

    $sql = "SELECT COUNT(*) AS count 
            FROM phone_authentication
            WHERE phone_number = '$휴대폰번호'";

    $result = $connect->query($sql);

    // count가 1이상 이면 UPDATE, 0이라면 INSERT 
    if($result){
        $row = $result->fetch_assoc();
        $count = $row['count'];

        인증번호생성($인증번호);

        if ($count > 0) { //해당 휴대폰번호가 있다면
            update();
            인증번호발송();
        } else { //  해당 휴대폰 번호가 없다면
            insert();
            인증번호발송();
        }
    }else { // 실패 시 false response
        fail();
    }

    

    $array['성공여부'] = true;
    $array['인증가능시간'] = $인증가능시간;
    header('Content-Type: application/json');
    echo json_encode($array);
    
?>

<?php

    function 만료인증번호삭제(){
        global $connect;
        $현재시간 = time();
        $현재타임스탬프 = date('Y-m-d H:i:s', $현재시간);

        $sql = "DELETE 
                FROM phone_authentication 
                WHERE expired_time < '$현재타임스탬프'";

        $connect->query($sql);

    }

    function 인증번호발송(){
        global $휴대폰번호;
        global $인증번호;

        // sms 보내기 

        $sID = "ncp:sms:kr:307847122275:talktalk"; // 서비스 ID

        $smsURL = "https://sens.apigw.ntruss.com/sms/v2/services/".$sID."/messages";
        $smsUri = "/sms/v2/services/".$sID."/messages";

        $accKeyId = "cAEhAalYGgTstWeUe4Co";   //인증키 id
        $accSecKey = "dXjvDALeGXDPSNbrRtijkJeFYiIfpXi7VlthNBmV";  //secret key

        $sTime = floor(microtime(true) * 1000); // 현재시간

        // body부분
        $postData = array(
            'type' => 'SMS',
            'countryCode' => '82', // 한국
            'from' => '01051734547', // 발신번호 
            'content' => "인증번호입니다.",
            'messages' => array(array('content' => "[talktalk] 인증번호: [".$인증번호."]를 입력해주세요", 'to' => "$휴대폰번호"))
        );
        

        $postFields = json_encode($postData) ;

        $hashString = "POST {$smsUri}\n{$sTime}\n{$accKeyId}";

        // Body를 Access Key Id와 맵핑되는 SecretKey로 암호화한 서명
        //  HMAC 암호화 알고리즘은 HmacSHA256 사용
        $dHash = base64_encode( hash_hmac('sha256', $hashString, $accSecKey, true) );

        $header = array(
            'Content-Type: application/json; charset=utf-8',
            'x-ncp-apigw-timestamp: '.$sTime,
            "x-ncp-iam-access-key: ".$accKeyId,
            "x-ncp-apigw-signature-v2: ".$dHash
        );

        //curl은 여러 프로토콜로 request가 가능한 라이브러리.
        // curl 초기화
        $핸들 = curl_init($smsURL);

        curl_setopt_array($핸들, array(   //옵션을 배열로 한번에 설정한다
            CURLOPT_POST => TRUE, // 일반적인 HTTP POST 수행
            CURLOPT_RETURNTRANSFER => TRUE, // 아래 $response로 무언가를 문자열로 반환 
            CURLOPT_HTTPHEADER => $header, // 헤더
            CURLOPT_POSTFIELDS => $postFields // HTTP "POST" 작업에서 게시할 전체 데이터
        ));

        //설정된 옵션으로 실행
        // $response = curl_exec($핸들);///////////////////////////////////////////
        // $다듬은response = str_replace('\\', '', $response);
        // $data = json_decode($response);
        // $statusCode = $data->statusCode;
        
        // if($statusCode !== 202){
        //     fail();
        // }

        

        //curl을 닫아서 리소스 해제
        curl_close($핸들);

        // $array['성공여부'] = $성공여부;
        
        // $array1 = array();
        // // $array['휴대폰번호'] = $statusCode;
        // $array1['성공여부'] = "true";
        // header('Content-Type: application/json; charset=UTF-8');
        // echo json_encode($array1);
        // return;
    }

    //4자리 난수 생성
    function 인증번호생성(&$인증번호){
        for ($i = 0; $i < 4; $i++) {
            $난수 = rand(0, 9); 
            $인증번호 .= $난수; 
        }
    }

    function fail(){
        $array = array();
        $array['성공여부'] = false;
        header('Content-Type: application/json');
        echo json_encode($array);
        return;
    }

    function update(){
        global $connect;
        global $휴대폰번호;
        global $인증번호;
        global $인증가능시간;
        $만료시간=0;

        $만료시간 = time() + $인증가능시간;
        $만료타임스탬프 = date('Y-m-d H:i:s', $만료시간);

        $sql ="UPDATE phone_authentication
                SET authentication_code='$인증번호', expired_time='$만료타임스탬프'
                WHERE phone_number='$휴대폰번호'";

        $result = $connect->query($sql);

        // 실패 시 false response
        if(!$result){
            fail();
        }
    }

    function insert(){
        global $connect;
        global $휴대폰번호;
        global $인증번호;
        global $인증가능시간;
        global $만료시간;

        $만료시간 = time() + $인증가능시간;
        $만료타임스탬프 = date('Y-m-d H:i:s', $만료시간);

        $sql = "INSERT INTO phone_authentication(phone_number, authentication_code, expired_time) 
                VALUES('$휴대폰번호', '$인증번호', '$만료타임스탬프') ";

        $result = $connect->query($sql);

        // 실패 시 false response
        if(!$result){
            fail();
        }
    
    }
?>