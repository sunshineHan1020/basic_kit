<?php
require $_SERVER['DOCUMENT_ROOT'].'/plugins/mail/phpmailer/PHPMailerAutoload.php';   //Create a new PHPMailer instance

function send_mail_daum($s_name,$r_mail,$add_mail,$title,$contents,$file1,$file2 =''){

    //받는사람주소 배열
    $r_mail_arr = [];
    if( strpos($r_mail, ";") !== false ) {
        $r_mail_arr = explode(";", $r_mail);
    }
    else {
        $r_mail_arr[] = $r_mail;
    }
    $r_mail_arr = array_filter($r_mail_arr);

    //메일발송
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.naver.com';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->SMTPAuth = true;

    $mail->Username = "decentlaw0527@naver.com";
   // $mail->Password = "gksqha202512!@";
	$mail->Password = "QNLNDFCJF777";
    $mail->setFrom("decentlaw0527@naver.com", $s_name);
    $mail->isHTML(true);
    foreach($r_mail_arr as $k => $v) {
        if( empty($v) ) {
            continue;
        }
        $mail->addAddress($v);
    }
    if( !empty($add_mail) ) {
        $mail->addAddress($add_mail);
    }

    $mail->Subject = $title;
    $mail->msgHTML($contents);
    if(!empty($file1) && !empty($file2)){
        $mail->AddAttachment($file1,$file2); //첨부파일
    }

    if(!empty($file1) && empty($file2)) {
        $mail->AddAttachment($_SERVER['DOCUMENT_ROOT'].$file1); //첨부파일
    }

    $aResult = [];
    if( !$mail->send() ) {
        //error;
        $aResult['status'] = 'ERR';
        $aResult['msg'] = $mail->ErrorInfo;

    }
    else {
        //success
        $aResult['status'] = 'OK';
        $aResult['msg'] = '';
    }

    $mail->clearAddresses();
    $mail->clearAttachments();
    return $aResult;
}

?>
