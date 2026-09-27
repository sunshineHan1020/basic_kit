<?
/*
 * =========================================================
 * WEEKENDS CMS
 * TinyMCE 본문 이미지 업로드
 *
 * FILE
 * /manage/proc/editor_image_upload.php
 *
 * URL
 * /manage/proc/editor_image_upload
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


header(
    'Content-Type: application/json; charset=utf-8'
);



/*
 * =========================================================
 * JSON
 * =========================================================
 */

function editor_image_json(
    $code,
    $message,
    $location,
    $media_id
){

    $result =
        array(
            'code' => $code
        );


    if(
        $message != ''
    ){

        $result['message'] =
            $message;

    }


    if(
        $location != ''
    ){

        /*
         * TinyMCE가 요구하는 필드
         */

        $result['location'] =
            $location;

    }


    if(
        intval($media_id) > 0
    ){

        $result['media_id'] =
            intval(
                $media_id
            );

    }


    echo json_encode(
        $result
    );

    exit;
}



/*
 * =========================================================
 * Last Insert ID
 * =========================================================
 */

function editor_image_last_id($db)
{

    $ps =
        pdo_query(
            $db,
            "

                SELECT
                    LAST_INSERT_ID() AS id

            ",
            array()
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    return
        $data
            ? intval($data['id'])
            : 0;

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

    http_response_code(
        405
    );


    editor_image_json(
        false,
        '잘못된 접근입니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * 관리자
 * =========================================================
 */

$admin_id =
    isset($_SESSION['ADMIN_ID'])
        ? intval($_SESSION['ADMIN_ID'])
        : 0;


if(
    $admin_id <= 0
){

    http_response_code(
        401
    );


    editor_image_json(
        false,
        '로그인이 필요합니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * FILE
 *
 * TinyMCE 기본 field = file
 * =========================================================
 */

if(
    !isset($_FILES['file'])
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '업로드할 이미지가 없습니다.',
        '',
        0
    );

}


$file =
    $_FILES['file'];



/*
 * =========================================================
 * Upload Error
 * =========================================================
 */

if(
    !isset($file['error'])
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '업로드 정보를 확인할 수 없습니다.',
        '',
        0
    );

}


$upload_error =
    intval(
        $file['error']
    );


if(
    $upload_error !=
    UPLOAD_ERR_OK
){

    if(
        $upload_error ==
        UPLOAD_ERR_INI_SIZE
    ){

        $message =
            '서버의 업로드 허용 용량을 초과했습니다.';

    }else if(
        $upload_error ==
        UPLOAD_ERR_FORM_SIZE
    ){

        $message =
            '업로드 허용 용량을 초과했습니다.';

    }else if(
        $upload_error ==
        UPLOAD_ERR_PARTIAL
    ){

        $message =
            '이미지가 일부만 업로드되었습니다.';

    }else if(
        $upload_error ==
        UPLOAD_ERR_NO_FILE
    ){

        $message =
            '업로드할 이미지가 없습니다.';

    }else{

        $message =
            '이미지 업로드 중 오류가 발생했습니다.';

    }


    http_response_code(
        400
    );


    editor_image_json(
        false,
        $message,
        '',
        0
    );

}



/*
 * =========================================================
 * Temp File
 * =========================================================
 */

if(
    !isset($file['tmp_name']) ||
    $file['tmp_name'] == '' ||
    !is_uploaded_file(
        $file['tmp_name']
    )
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '정상적인 업로드 파일이 아닙니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * Size
 * =========================================================
 */

$temp_size =
    @filesize(
        $file['tmp_name']
    );


if(
    $temp_size === false ||
    intval($temp_size) <= 0
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '이미지 크기를 확인할 수 없습니다.',
        '',
        0
    );

}



/*
 * 최대 5MB
 * =========================================================
 */

if(
    intval($temp_size) >
    5 * 1024 * 1024
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '이미지는 최대 5MB까지 업로드할 수 있습니다.',
        '',
        0
    );

}



/*
 * $_FILES Size 확인
 * =========================================================
 */

if(
    isset($file['size']) &&
    intval($file['size']) > 0 &&
    intval($file['size']) != intval($temp_size)
){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '업로드된 파일의 크기가 일치하지 않습니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * Image 확인
 * =========================================================
 */

$image_info =
    @getimagesize(
        $file['tmp_name']
    );


if(!$image_info){

    http_response_code(
        400
    );


    editor_image_json(
        false,
        '정상적인 이미지 파일이 아닙니다.',
        '',
        0
    );

}


$width =
    isset($image_info[0])
        ? intval($image_info[0])
        : null;


$height =
    isset($image_info[1])
        ? intval($image_info[1])
        : null;



/*
 * =========================================================
 * MIME
 * =========================================================
 */

$mime_type =
    '';


if(
    function_exists(
        'finfo_open'
    )
){

    $finfo =
        @finfo_open(
            FILEINFO_MIME_TYPE
        );


    if($finfo){

        $mime_type =
            @finfo_file(
                $finfo,
                $file['tmp_name']
            );


        finfo_close(
            $finfo
        );

    }

}



/*
 * finfo 없는 서버 fallback
 * =========================================================
 */

if(
    $mime_type == '' &&
    isset($image_info['mime'])
){

    $mime_type =
        $image_info['mime'];

}



/*
 * =========================================================
 * Extension
 * =========================================================
 */

$extension =
    '';


if(
    $mime_type ==
    'image/jpeg'
){

    $extension =
        'jpg';

}else if(
    $mime_type ==
    'image/png'
){

    $extension =
        'png';

}else if(
    $mime_type ==
    'image/gif'
){

    $extension =
        'gif';

}else if(
    $mime_type ==
    'image/webp'
){

    $extension =
        'webp';

}else{

    http_response_code(
        400
    );


    editor_image_json(
        false,
        'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * 저장 전 HASH
 * =========================================================
 */

$temp_hash =
    @md5_file(
        $file['tmp_name']
    );


if(
    $temp_hash === false
){

    http_response_code(
        500
    );


    editor_image_json(
        false,
        '업로드 파일을 확인하지 못했습니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * Directory
 *
 * /uploads/blog/editor/202609/
 * =========================================================
 */

$folder =
    date(
        'Ym'
    );


$upload_path =
    '/uploads/blog/editor/'.
    $folder;


$upload_dir =
    $_SERVER['DOCUMENT_ROOT'].
    $upload_path;



/*
 * =========================================================
 * Directory 생성
 * =========================================================
 */

if(
    !is_dir(
        $upload_dir
    )
){

    if(
        !mkdir(
            $upload_dir,
            0755,
            true
        )
    ){

        http_response_code(
            500
        );


        editor_image_json(
            false,
            '이미지 저장 디렉토리를 생성하지 못했습니다.',
            '',
            0
        );

    }

}



/*
 * =========================================================
 * File Name
 * =========================================================
 */

do {

    $file_name =

        date(
            'YmdHis'
        ).

        '_'.

        $admin_id.

        '_'.

        mt_rand(
            100000,
            999999
        ).

        '.'.

        $extension;


    $save_file =
        $upload_dir.
        '/'.
        $file_name;


} while(
    is_file(
        $save_file
    )
);


$storage_key =
    $upload_path.
    '/'.
    $file_name;



/*
 * =========================================================
 * Move
 * =========================================================
 */

if(
    !move_uploaded_file(
        $file['tmp_name'],
        $save_file
    )
){

    http_response_code(
        500
    );


    editor_image_json(
        false,
        '이미지 파일을 저장하지 못했습니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * 저장 후 검증
 * =========================================================
 */

clearstatcache(
    true,
    $save_file
);


$saved_size =
    @filesize(
        $save_file
    );


$saved_hash =
    @md5_file(
        $save_file
    );


if(
    $saved_size === false ||
    $saved_hash === false ||
    intval($saved_size) != intval($temp_size) ||
    $saved_hash != $temp_hash
){

    @unlink(
        $save_file
    );


    http_response_code(
        500
    );


    editor_image_json(
        false,
        '이미지 저장 과정에서 파일이 손상되었습니다.',
        '',
        0
    );

}



/*
 * 이미지 재확인
 * =========================================================
 */

$saved_image_info =
    @getimagesize(
        $save_file
    );


if(!$saved_image_info){

    @unlink(
        $save_file
    );


    http_response_code(
        500
    );


    editor_image_json(
        false,
        '저장된 이미지 파일을 확인할 수 없습니다.',
        '',
        0
    );

}



/*
 * =========================================================
 * Original Name
 * =========================================================
 */

$original_name =
    isset($file['name'])
        ? basename(
            $file['name']
        )
        : $file_name;


if(
    strlen(
        $original_name
    ) > 255
){

    $original_name =
        substr(
            $original_name,
            0,
            255
        );

}



/*
 * =========================================================
 * MEDIA DB
 * =========================================================
 */

try {

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
            ?,
            NOW(),
            NULL
        )

    ";


    pdo_query(
        $db,
        $query,
        array(
            $storage_key,
            $original_name,
            $mime_type,
            intval(
                $saved_size
            ),
            $width,
            $height,
            $admin_id
        )
    );


    $media_id =
        editor_image_last_id(
            $db
        );


    if(
        $media_id <= 0
    ){

        throw new Exception(
            '미디어 정보를 저장하지 못했습니다.'
        );

    }


} catch(Exception $e) {


    /*
     * DB 저장 실패 시
     * 실제 파일도 제거
     */

    if(
        is_file(
            $save_file
        )
    ){

        @unlink(
            $save_file
        );

    }


    http_response_code(
        500
    );


    editor_image_json(
        false,
        $e->getMessage(),
        '',
        0
    );

}



/*
 * =========================================================
 * SUCCESS
 *
 * TinyMCE는 location을 사용함
 * =========================================================
 */

editor_image_json(
    true,
    '',
    $storage_key,
    $media_id
);
?>