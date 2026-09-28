<?php

/*
 * =========================================================
 * WEEKENDS
 * CONTACT INQUIRY SAVE
 * =========================================================
 */

header(
    'Content-Type: application/json; charset=UTF-8'
);


include_once
    $_SERVER['DOCUMENT_ROOT'].
    '/lib/common.php';


include_once
    $_SERVER['DOCUMENT_ROOT'].
    '/plugins/mail/send_mail.php';



/*
 * =========================================================
 * JSON RESPONSE
 * =========================================================
 */

function contact_response(
    $result,
    $message
){

    $is_success =
        $result == 'success'
            ? true
            : false;


    echo json_encode(
        array(

            /*
             * 현재 WEEKENDS JS
             */
            'code' =>
                $is_success,

            'msg' =>
                $message,


            /*
             * 기존 처리 방식 호환
             */
            'result' =>
                $result,

            'message' =>
                $message

        ),
        JSON_UNESCAPED_UNICODE
    );


    exit;

}



/*
 * =========================================================
 * POST
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    contact_response(
        'error',
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * CSRF
 * =========================================================
 */

$csrf_token =
    isset($_POST['contact_csrf'])
        ? trim(
            $_POST['contact_csrf']
        )
        : '';


if(
    $csrf_token == '' ||
    empty($_SESSION['contact_csrf']) ||
    $_SESSION['contact_csrf'] !==
        $csrf_token
){

    contact_response(
        'error',
        '잘못된 요청입니다. 페이지를 새로고침 후 다시 시도해주세요.'
    );

}



/*
 * =========================================================
 * RECAPTCHA TOKEN
 * =========================================================
 */

$recaptcha_token =
    isset($_POST['recaptcha_token'])
        ? trim(
            $_POST['recaptcha_token']
        )
        : '';


if(
    $recaptcha_token === ''
){

    contact_response(
        'error',
        '보안 인증에 실패했습니다.'
    );

}



/*
 * =========================================================
 * RECAPTCHA VERIFY
 *
 * 아래 SECRET KEY만 실제 값으로 변경
 * =========================================================
 */

$recaptcha_secret_key =
    '6LcdctEtAAAAAHGivfK1W-zbSHMC7rYtrbR9aZWK';




$recaptcha_post =
    http_build_query(
        array(
            'secret' =>
                $recaptcha_secret_key,

            'response' =>
                $recaptcha_token,

            'remoteip' =>
                isset($_SERVER['REMOTE_ADDR'])
                    ? $_SERVER['REMOTE_ADDR']
                    : ''
        )
    );


$recaptcha_response =
    false;



/*
 * CURL 우선
 */

if(
    function_exists('curl_init')
){

    $ch =
        curl_init(
            'https://www.google.com/recaptcha/api/siteverify'
        );


    curl_setopt(
        $ch,
        CURLOPT_POST,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        $recaptcha_post
    );


    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_CONNECTTIMEOUT,
        5
    );


    curl_setopt(
        $ch,
        CURLOPT_TIMEOUT,
        10
    );


    $recaptcha_response =
        curl_exec(
            $ch
        );


    curl_close(
        $ch
    );



}else{


    /*
     * CURL 미지원 시 fallback
     */

    $context =
        stream_context_create(
            array(
                'http' =>
                    array(
                        'method' =>
                            'POST',

                        'header' =>
                            "Content-Type: application/x-www-form-urlencoded\r\n",

                        'content' =>
                            $recaptcha_post,

                        'timeout' =>
                            10
                    )
            )
        );


    $recaptcha_response =
        @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify',
            false,
            $context
        );

}



if(
    $recaptcha_response === false ||
    $recaptcha_response == ''
){

    contact_response(
        'error',
        '보안 인증에 실패했습니다.'
    );

}


$recaptcha_data =
    json_decode(
        $recaptcha_response,
        true
    );


if(
    !is_array($recaptcha_data) ||
    !isset($recaptcha_data['success']) ||
    !$recaptcha_data['success']
){

    contact_response(
        'error',
        '보안 인증에 실패했습니다.'
    );

}



/*
 * v3 Action 검증
 */

if(
    isset($recaptcha_data['action']) &&
    $recaptcha_data['action'] != 'contact'
){

    contact_response(
        'error',
        '보안 인증에 실패했습니다.'
    );

}



/*
 * v3 Score
 */

if(
    isset($recaptcha_data['score']) &&
    floatval(
        $recaptcha_data['score']
    ) < 0.5
){

    contact_response(
        'error',
        '보안 인증에 실패했습니다.'
    );

}



/*
 * =========================================================
 * INPUT
 * =========================================================
 */

$name =
    isset($_POST['userName'])
        ? trim(
            $_POST['userName']
        )
        : '';


$company_name =
    isset($_POST['companyName'])
        ? trim(
            $_POST['companyName']
        )
        : '';


$phone =
    isset($_POST['userPhone'])
        ? trim(
            $_POST['userPhone']
        )
        : '';


$email =
    isset($_POST['userEmail'])
        ? trim(
            $_POST['userEmail']
        )
        : '';


$answers =
    isset($_POST['answers']) &&
    is_array($_POST['answers'])
        ? $_POST['answers']
        : array();



/*
 * =========================================================
 * BASIC VALIDATE
 * =========================================================
 */

if(
    $name == ''
){

    contact_response(
        'error',
        '이름을 입력해주세요.'
    );

}


if(
    mb_strlen(
        $name,
        'UTF-8'
    ) > 100
){

    contact_response(
        'error',
        '이름을 확인해주세요.'
    );

}



if(
    $company_name == ''
){

    contact_response(
        'error',
        '회사명을 입력해주세요.'
    );

}


if(
    mb_strlen(
        $company_name,
        'UTF-8'
    ) > 255
){

    contact_response(
        'error',
        '회사명을 확인해주세요.'
    );

}



/*
 * 전화번호
 */

$phone_number =
    preg_replace(
        '/[^0-9]/',
        '',
        $phone
    );


if(
    !preg_match(
        '/^01[016789][0-9]{7,8}$/',
        $phone_number
    )
){

    contact_response(
        'error',
        '올바른 연락처를 입력해주세요.'
    );

}



/*
 * 이메일
 */

if(
    $email == ''
){

    contact_response(
        'error',
        '이메일을 입력해주세요.'
    );

}


if(
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
){

    contact_response(
        'error',
        '올바른 이메일 주소를 입력해주세요.'
    );

}


$email =
    strtolower(
        $email
    );



/*
 * =========================================================
 * ANSWERS
 *
 * 각 STEP 반드시 1개
 * =========================================================
 */

$validated_answers =
    array();


for(
    $step_no = 1;
    $step_no <= 4;
    $step_no++
){

    if(
        !isset($answers[$step_no]) ||
        !is_array($answers[$step_no]) ||
        count($answers[$step_no]) != 1
    ){

        contact_response(
            'error',
            '문의 항목을 다시 선택해주세요.'
        );

    }


    $option_id =
        intval(
            $answers[$step_no][0]
        );


    if(
        $option_id <= 0
    ){

        contact_response(
            'error',
            '문의 항목을 다시 선택해주세요.'
        );

    }



    /*
     * 실제 DB 옵션인지 검증
     */

    $query = "

        SELECT

            id,
            step_no,
            option_code,
            title_en,
            title_ko,
            sort_order

        FROM inquiry_options

        WHERE
            id = ?
            AND step_no = ?
            AND is_active = 1

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $option_id,
                $step_no
            )
        );


    $option =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(
        !$option
    ){

        contact_response(
            'error',
            '유효하지 않은 문의 항목입니다.'
        );

    }


    $validated_answers[] =
        $option;

}



/*
 * =========================================================
 * ATTACHMENT
 *
 * 최대 5MB
 *
 * IMAGE
 * JPG JPEG PNG WEBP
 *
 * ARCHIVE
 * ZIP RAR 7Z
 *
 * DOCUMENT
 * PDF PPT PPTX DOC DOCX HWP
 * =========================================================
 */

$attachment_exists =
    false;


$attachment_path =
    '';


$attachment_storage_key =
    '';


$attachment_original_name =
    '';


$attachment_mime =
    '';


$attachment_size =
    0;


$attachment_width =
    null;


$attachment_height =
    null;


$attachment_media_id =
    null;



if(
    isset($_FILES['attachment']) &&
    isset($_FILES['attachment']['error']) &&
    $_FILES['attachment']['error'] !=
        UPLOAD_ERR_NO_FILE
){

    $file =
        $_FILES['attachment'];



    /*
     * 업로드 오류
     */

    if(
        $file['error'] !=
        UPLOAD_ERR_OK
    ){

        if(
            $file['error'] ==
                UPLOAD_ERR_INI_SIZE ||
            $file['error'] ==
                UPLOAD_ERR_FORM_SIZE
        ){

            contact_response(
                'error',
                '첨부파일은 5MB 이하만 업로드할 수 있습니다.'
            );

        }


        contact_response(
            'error',
            '첨부파일 업로드 중 오류가 발생했습니다.'
        );

    }



    /*
     * 크기
     */

    $max_file_size =
        5 * 1024 * 1024;


    $attachment_size =
        intval(
            $file['size']
        );


    if(
        $attachment_size <= 0 ||
        $attachment_size >
            $max_file_size
    ){

        contact_response(
            'error',
            '첨부파일은 5MB 이하만 업로드할 수 있습니다.'
        );

    }



    /*
     * 확장자
     */

    $attachment_original_name =
        basename(
            $file['name']
        );


    $extension =
        strtolower(
            pathinfo(
                $attachment_original_name,
                PATHINFO_EXTENSION
            )
        );


    $allowed_extensions =
        array(
            'jpg',
            'jpeg',
            'png',
            'webp',

            'zip',
            'rar',
            '7z',

            'pdf',
            'ppt',
            'pptx',
            'doc',
            'docx',
            'hwp'
        );


    if(
        !in_array(
            $extension,
            $allowed_extensions
        )
    ){

        contact_response(
            'error',
            '허용되지 않는 첨부파일 형식입니다.'
        );

    }



    /*
     * 실제 MIME
     */

    $attachment_mime =
        'application/octet-stream';


    if(
        function_exists('finfo_open')
    ){

        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );


        if($finfo){

            $detected_mime =
                finfo_file(
                    $finfo,
                    $file['tmp_name']
                );


            finfo_close(
                $finfo
            );


            if(
                $detected_mime
            ){

                $attachment_mime =
                    strtolower(
                        trim(
                            $detected_mime
                        )
                    );

            }

        }

    }



    /*
     * MIME 검사
     */

    $mime_ok =
        false;


    $allowed_mime_map =
        array(

            'jpg' =>
                array(
                    'image/jpeg'
                ),

            'jpeg' =>
                array(
                    'image/jpeg'
                ),

            'png' =>
                array(
                    'image/png'
                ),

            'webp' =>
                array(
                    'image/webp'
                ),

            'zip' =>
                array(
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/octet-stream'
                ),

            'rar' =>
                array(
                    'application/vnd.rar',
                    'application/x-rar',
                    'application/x-rar-compressed',
                    'application/octet-stream'
                ),

            '7z' =>
                array(
                    'application/x-7z-compressed',
                    'application/octet-stream'
                ),

            'pdf' =>
                array(
                    'application/pdf'
                ),

            'ppt' =>
                array(
                    'application/vnd.ms-powerpoint',
                    'application/mspowerpoint',
                    'application/powerpoint',
                    'application/x-mspowerpoint',
                    'application/octet-stream'
                ),

            'pptx' =>
                array(
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip',
                    'application/octet-stream'
                ),

            'doc' =>
                array(
                    'application/msword',
                    'application/vnd.ms-office',
                    'application/octet-stream'
                ),

            'docx' =>
                array(
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                    'application/octet-stream'
                ),

            'hwp' =>
                array(
                    'application/x-hwp',
                    'application/haansofthwp',
                    'application/vnd.hancom.hwp',
                    'application/octet-stream'
                )

        );


    if(
        isset(
            $allowed_mime_map[$extension]
        ) &&
        in_array(
            $attachment_mime,
            $allowed_mime_map[$extension]
        )
    ){

        $mime_ok =
            true;

    }


    if(
        !$mime_ok
    ){

        contact_response(
            'error',
            '첨부파일 형식을 확인해주세요.'
        );

    }



    /*
     * 이미지인 경우 실제 이미지 확인
     */

    if(
        $extension == 'jpg' ||
        $extension == 'jpeg' ||
        $extension == 'png' ||
        $extension == 'webp'
    ){

        $image_info =
            @getimagesize(
                $file['tmp_name']
            );


        if(
            !$image_info
        ){

            contact_response(
                'error',
                '올바른 이미지 파일이 아닙니다.'
            );

        }


        $attachment_width =
            isset($image_info[0])
                ? intval(
                    $image_info[0]
                )
                : null;


        $attachment_height =
            isset($image_info[1])
                ? intval(
                    $image_info[1]
                )
                : null;

    }



    /*
     * 업로드 폴더
     *
     * /uploads/inquiry/YYYYMM/
     */

    $upload_relative_dir =
        '/uploads/inquiry/'.
        date('Ym').
        '/';


    $upload_absolute_dir =
        $_SERVER['DOCUMENT_ROOT'].
        $upload_relative_dir;


    if(
        !is_dir(
            $upload_absolute_dir
        )
    ){

        if(
            !@mkdir(
                $upload_absolute_dir,
                0755,
                true
            )
        ){

            contact_response(
                'error',
                '첨부파일 저장 폴더를 생성하지 못했습니다.'
            );

        }

    }



    /*
     * 랜덤 파일명
     */

    $random_bytes =
        openssl_random_pseudo_bytes(
            16
        );


    if(
        $random_bytes === false
    ){

        $save_name =
            uniqid(
                'inquiry_',
                true
            );

        $save_name =
            str_replace(
                '.',
                '',
                $save_name
            );


    }else{

        $save_name =
            bin2hex(
                $random_bytes
            );

    }


    $save_name .=
        '.'.
        $extension;


    $attachment_storage_key =
        $upload_relative_dir.
        $save_name;


    $attachment_path =
        $upload_absolute_dir.
        $save_name;



    /*
     * 실제 파일 이동
     */

    if(
        !move_uploaded_file(
            $file['tmp_name'],
            $attachment_path
        )
    ){

        contact_response(
            'error',
            '첨부파일 저장에 실패했습니다.'
        );

    }


    $attachment_exists =
        true;

}



/*
 * =========================================================
 * DB SAVE
 * =========================================================
 */

try{


    $db->beginTransaction();



    /*
     * =====================================================
     * MEDIA
     * =====================================================
     */

    if(
        $attachment_exists
    ){

        $query = "

            INSERT INTO media
            (
                storage_key,
                original_name,
                mime_type,
                byte_size,
                width,
                height,
                default_alt_text,
                uploaded_by,
                created_at,
                deleted_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NULL,
                NULL,
                NOW(),
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $attachment_storage_key,
                $attachment_original_name,
                $attachment_mime,
                $attachment_size,
                $attachment_width,
                $attachment_height
            )
        );



        /*
         * Media ID
         */

        $ps =
            pdo_query(
                $db,
                "
                    SELECT
                        LAST_INSERT_ID() AS id
                ",
                array()
            );


        $media_data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $attachment_media_id =
            $media_data
                ? intval(
                    $media_data['id']
                )
                : 0;


        if(
            $attachment_media_id <= 0
        ){

            throw new Exception(
                'MEDIA_INSERT_FAILED'
            );

        }

    }



    /*
     * =====================================================
     * INQUIRY
     * =====================================================
     */

    $query = "

        INSERT INTO inquiries
        (
            name,
            company_name,
            phone,
            email,
            attachment_media_id,
            status,
            admin_memo,
            created_at,
            updated_at,
            deleted_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            'received',
            NULL,
            NOW(),
            NOW(),
            NULL
        )

    ";


    pdo_query(
        $db,
        $query,
        array(
            $name,
            $company_name,
            $phone_number,
            $email,
            $attachment_media_id
        )
    );



    /*
     * Inquiry ID
     */

    $ps =
        pdo_query(
            $db,
            "
                SELECT
                    LAST_INSERT_ID() AS id
            ",
            array()
        );


    $inquiry_data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    $inquiry_id =
        $inquiry_data
            ? intval(
                $inquiry_data['id']
            )
            : 0;


    if(
        $inquiry_id <= 0
    ){

        throw new Exception(
            'INQUIRY_INSERT_FAILED'
        );

    }



    /*
     * =====================================================
     * ANSWERS
     * =====================================================
     */

    foreach(
        $validated_answers as
        $answer
    ){

        $query = "

            INSERT INTO inquiry_answers
            (
                inquiry_id,
                step_no,
                answer_code,
                answer_label,
                sort_order
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $inquiry_id,
                intval(
                    $answer['step_no']
                ),
                $answer['option_code'],
                $answer['title_ko'],
                intval(
                    $answer['sort_order']
                )
            )
        );

    }



    /*
     * Commit
     */

    $db->commit();



}catch(Exception $e){


    if(
        $db->inTransaction()
    ){

        $db->rollBack();

    }



    /*
     * DB 실패 시 새로 업로드한 파일만 삭제
     */

    if(
        $attachment_exists &&
        $attachment_path != '' &&
        is_file(
            $attachment_path
        )
    ){

        @unlink(
            $attachment_path
        );

    }


    contact_response(
        'error',
        '문의 접수 중 오류가 발생했습니다.'
    );

}



/*
 * =========================================================
 * CSRF TOKEN 폐기
 *
 * DB 저장 성공 후 폐기
 * =========================================================
 */

unset(
    $_SESSION['contact_csrf']
);



/*
 * =========================================================
 * MAIL CONTENT
 * =========================================================
 */

function contact_mail_h(
    $str
){

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );

}



$safe_name =
    contact_mail_h(
        $name
    );


$safe_company =
    contact_mail_h(
        $company_name
    );


$safe_phone =
    contact_mail_h(
        $phone_number
    );


$safe_email =
    contact_mail_h(
        $email
    );



/*
 * 선택 답변
 */

$mail_answer_rows =
    '';


$step_names =
    array(
        1 =>
            '프로젝트 유형',

        2 =>
            '미팅 장소',

        3 =>
            '프로젝트 일정',

        4 =>
            '프로젝트 형태'
    );


foreach(
    $validated_answers as
    $answer
){

    $step_no =
        intval(
            $answer['step_no']
        );


    $step_name =
        isset($step_names[$step_no])
            ? $step_names[$step_no]
            : 'STEP '.$step_no;


    $answer_label =
        $answer['title_ko'];


    if(
        isset($answer['title_en']) &&
        trim($answer['title_en']) != ''
    ){

        $answer_label .=
            ' ('.
            $answer['title_en'].
            ')';

    }


    $mail_answer_rows .= '

        <tr>

            <th
                align="left"
                style="
                    width:150px;
                    padding:12px;
                    background:#f6f6f6;
                    border:1px solid #dddddd;
                "
            >
                '.contact_mail_h($step_name).'
            </th>

            <td
                style="
                    padding:12px;
                    border:1px solid #dddddd;
                "
            >
                '.contact_mail_h($answer_label).'
            </td>

        </tr>

    ';

}



/*
 * 첨부파일
 */

$mail_attachment =
    '없음';


if(
    $attachment_exists
){

    $scheme =
        (
            isset($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] != '' &&
            $_SERVER['HTTPS'] != 'off'
        )
            ? 'https://'
            : 'http://';


    $host =
        isset($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST']
            : '';


    $file_url =
        $scheme.
        $host.
        $attachment_storage_key;


    $mail_attachment =

        '<a href="'.
        contact_mail_h(
            $file_url
        ).
        '" target="_blank">'.
        contact_mail_h(
            $attachment_original_name
        ).
        '</a>';

}



/*
 * =========================================================
 * MAIL
 * =========================================================
 */

$mail_subject =
    '[WEEKENDS 문의] '.
    $company_name.
    ' / '.
    $name;


$mail_body = '

<!DOCTYPE html>

<html lang="ko">

<head>

<meta charset="UTF-8">

<title>WEEKENDS 문의</title>

</head>


<body
    style="
        margin:0;
        padding:30px;
        background:#f5f5f5;
        font-family:Arial, \'Noto Sans KR\', sans-serif;
        color:#222222;
    "
>


<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
>

<tr>

<td align="center">


<table
    width="700"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width:100%;
        max-width:700px;
        background:#ffffff;
    "
>


<tr>

<td
    style="
        padding:30px;
    "
>


<h2
    style="
        margin:0 0 25px;
        font-size:24px;
    "
>
    WEEKENDS 프로젝트 문의
</h2>


<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        border-collapse:collapse;
    "
>


<tr>

<th
    align="left"
    style="
        width:150px;
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    접수번호
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    #'.intval($inquiry_id).'
</td>

</tr>


<tr>

<th
    align="left"
    style="
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    이름
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    '.$safe_name.'
</td>

</tr>


<tr>

<th
    align="left"
    style="
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    회사명
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    '.$safe_company.'
</td>

</tr>


<tr>

<th
    align="left"
    style="
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    연락처
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    '.$safe_phone.'
</td>

</tr>


<tr>

<th
    align="left"
    style="
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    이메일
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    '.$safe_email.'
</td>

</tr>


'.$mail_answer_rows.'


<tr>

<th
    align="left"
    style="
        padding:12px;
        background:#f6f6f6;
        border:1px solid #dddddd;
    "
>
    첨부파일
</th>

<td
    style="
        padding:12px;
        border:1px solid #dddddd;
    "
>
    '.$mail_attachment.'
</td>

</tr>


</table>


</td>

</tr>


</table>


</td>

</tr>

</table>


</body>

</html>

';



/*
 * =========================================================
 * ADMIN EMAIL
 * =========================================================
 */

$admin_email =
    'www@hanbom.com';

$admin_email2 =
    'bobaexxi@weekends.co.kr';

$admin_email3 =
    'master@weekends.co.kr ';

$admin_email4 =
    'bbar@weekends.co.kr';




/*
 * =========================================================
 * SEND MAIL
 * =========================================================
 */

//$mail_result = send_mail_daum('WEEKENDS',$admin_email,'',$mail_subject,$mail_body,'','');

$mail_result2 = send_mail_daum('WEEKENDS',$admin_email2,'',$mail_subject,$mail_body,'','');
$mail_result3 = send_mail_daum('WEEKENDS',$admin_email3,'',$mail_subject,$mail_body,'','');
$mail_result4 = send_mail_daum('WEEKENDS',$admin_email4,'',$mail_subject,$mail_body,'','');



/*
 * =========================================================
 * RESULT
 *
 * DB 접수는 이미 완료되었으므로
 * 관리자 알림 메일 실패 때문에
 * 사용자가 중복 접수하지 않도록 성공 처리
 * =========================================================
 */

contact_response(
    'success',
    '문의가 정상적으로 접수되었습니다.'
);