<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 메인 전시 저장
 *
 * FILE
 * /manage/proc/main_exhibition_save.php
 *
 * URL
 * /manage/proc/main_exhibition_save
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


header(
    'Content-Type: application/json; charset=UTF-8'
);



/*
 * =========================================================
 * RESPONSE
 * =========================================================
 */

function main_exhibition_save_response(
    $code,
    $msg,
    $data = array()
){

    $result =
        array(
            'code' => $code,
            'msg'  => $msg
        );


    if(
        is_array($data) &&
        count($data) > 0
    ){

        foreach(
            $data as $key => $value
        ){

            $result[$key] =
                $value;

        }

    }


    echo json_encode(
        $result
    );

    exit;

}



/*
 * =========================================================
 * INI SIZE
 * =========================================================
 */

function main_exhibition_ini_bytes($value)
{

    $value =
        trim(
            $value
        );


    if(
        $value == ''
    ){

        return 0;

    }


    $last =
        strtolower(
            substr(
                $value,
                -1
            )
        );


    $number =
        floatval(
            $value
        );


    switch($last){

        case 'g':

            $number *=
                1024;

        case 'm':

            $number *=
                1024;

        case 'k':

            $number *=
                1024;

    }


    return intval(
        $number
    );

}



/*
 * =========================================================
 * ORIGINAL FILE NAME
 * =========================================================
 */

function main_exhibition_original_name($name)
{

    $name =
        str_replace(
            '\\',
            '/',
            $name
        );


    return basename(
        $name
    );

}



/*
 * =========================================================
 * MIME
 * =========================================================
 */

function main_exhibition_get_mime($file)
{

    $mime =
        '';


    if(
        function_exists(
            'finfo_open'
        )
    ){

        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );


        if($finfo){

            $mime =
                finfo_file(
                    $finfo,
                    $file
                );


            finfo_close(
                $finfo
            );

        }

    }


    if(
        $mime == '' &&
        function_exists(
            'mime_content_type'
        )
    ){

        $mime =
            @mime_content_type(
                $file
            );

    }


    return strtolower(
        trim(
            $mime
        )
    );

}



/*
 * =========================================================
 * UPLOAD ERROR MESSAGE
 * =========================================================
 */

function main_exhibition_upload_error_message(
    $error,
    $type
){

    if(
        $error == UPLOAD_ERR_INI_SIZE
    ){

        return $type.' 파일이 서버의 업로드 허용 용량을 초과했습니다.';

    }


    if(
        $error == UPLOAD_ERR_FORM_SIZE
    ){

        return $type.' 파일이 업로드 허용 용량을 초과했습니다.';

    }


    if(
        $error == UPLOAD_ERR_PARTIAL
    ){

        return $type.' 파일 업로드가 완료되지 않았습니다. 다시 시도해주세요.';

    }


    if(
        $error == UPLOAD_ERR_NO_TMP_DIR
    ){

        return $type.' 업로드 임시 폴더를 사용할 수 없습니다.';

    }


    if(
        $error == UPLOAD_ERR_CANT_WRITE
    ){

        return $type.' 파일을 서버에 저장하지 못했습니다.';

    }


    if(
        $error == UPLOAD_ERR_EXTENSION
    ){

        return $type.' 파일 업로드가 서버 설정에 의해 중단되었습니다.';

    }


    return $type.' 파일 업로드 중 오류가 발생했습니다.';

}



/*
 * =========================================================
 * CLEANUP
 * =========================================================
 */

function main_exhibition_cleanup_files(
    $files
){

    if(
        !is_array($files)
    ){

        return;

    }


    foreach(
        $files as $file
    ){

        if(
            $file != '' &&
            file_exists(
                $file
            )
        ){

            @unlink(
                $file
            );

        }

    }

}



/*
 * =========================================================
 * METHOD
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    main_exhibition_save_response(
        false,
        '잘못된 요청입니다.'
    );

}



/*
 * =========================================================
 * POST MAX SIZE CHECK
 *
 * post_max_size 초과 시 PHP가
 * $_POST / $_FILES를 비워버리는 경우 대응
 * =========================================================
 */

$content_length =
    isset($_SERVER['CONTENT_LENGTH'])
        ? intval(
            $_SERVER['CONTENT_LENGTH']
        )
        : 0;


$post_max_size =
    main_exhibition_ini_bytes(
        ini_get(
            'post_max_size'
        )
    );


if(
    $post_max_size > 0 &&
    $content_length > $post_max_size
){

    main_exhibition_save_response(
        false,
        '업로드 용량이 서버의 post_max_size 설정을 초과했습니다.'
    );

}



/*
 * =========================================================
 * PARAMETER
 * =========================================================
 */

$id =
    isset($_POST['id'])
        ? intval(
            $_POST['id']
        )
        : 0;


$work_id =
    isset($_POST['work_id'])
        ? intval(
            $_POST['work_id']
        )
        : 0;


$intro_text =
    isset($_POST['intro_text'])
        ? trim(
            $_POST['intro_text']
        )
        : '';


$band_color =
    isset($_POST['band_color'])
        ? strtoupper(
            trim(
                $_POST['band_color']
            )
        )
        : '';


$text_color =
    isset($_POST['text_color'])
        ? strtoupper(
            trim(
                $_POST['text_color']
            )
        )
        : '';


$sort_order =
    isset($_POST['sort_order'])
        ? intval(
            $_POST['sort_order']
        )
        : 0;


$is_active =
    isset($_POST['is_active']) &&
    $_POST['is_active'] == '1'
        ? 1
        : 0;


$delete_video =
    isset($_POST['delete_video']) &&
    $_POST['delete_video'] == 'Y'
        ? true
        : false;



/*
 * =========================================================
 * VALIDATION
 * =========================================================
 */

if(
    $work_id <= 0
){

    main_exhibition_save_response(
        false,
        '메인에 노출할 프로젝트를 선택해주세요.'
    );

}


if(
    !preg_match(
        '/^#[0-9A-F]{6}$/',
        $band_color
    )
){

    main_exhibition_save_response(
        false,
        '띠 색상 값이 올바르지 않습니다.'
    );

}


if(
    !preg_match(
        '/^#[0-9A-F]{6}$/',
        $text_color
    )
){

    main_exhibition_save_response(
        false,
        '글자 색상 값이 올바르지 않습니다.'
    );

}



/*
 * =========================================================
 * CURRENT DATA
 * =========================================================
 */

$current_exhibition =
    null;


$current_image_media_id =
    null;


$current_video_media_id =
    null;


if(
    $id > 0
){

    $query = "

        SELECT

            id,
            work_id,
            image_media_id,
            video_media_id,
            is_active

        FROM main_exhibitions

        WHERE
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $id
            )
        );


    $current_exhibition =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(
        !$current_exhibition
    ){

        main_exhibition_save_response(
            false,
            '수정할 메인 전시 정보를 찾을 수 없습니다.'
        );

    }


    if(
        isset(
            $current_exhibition['image_media_id']
        ) &&
        $current_exhibition['image_media_id'] != ''
    ){

        $current_image_media_id =
            intval(
                $current_exhibition['image_media_id']
            );


        if(
            $current_image_media_id <= 0
        ){

            $current_image_media_id =
                null;

        }

    }


    if(
        isset(
            $current_exhibition['video_media_id']
        ) &&
        $current_exhibition['video_media_id'] != ''
    ){

        $current_video_media_id =
            intval(
                $current_exhibition['video_media_id']
            );


        if(
            $current_video_media_id <= 0
        ){

            $current_video_media_id =
                null;

        }

    }

}



/*
 * =========================================================
 * WORK CHECK
 * =========================================================
 */

$query = "

    SELECT

        id,
        title,
        list_title,
        slug,
        status,
        visibility

    FROM works

    WHERE
        id = ?
        AND deleted_at IS NULL

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $work_id
        )
    );


$work =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(
    !$work
){

    main_exhibition_save_response(
        false,
        '선택한 프로젝트를 찾을 수 없습니다.'
    );

}



/*
 * =========================================================
 * DUPLICATE WORK CHECK
 * =========================================================
 */

$query = "

    SELECT

        id

    FROM main_exhibitions

    WHERE
        work_id = ?
        AND id != ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $work_id,
            $id
        )
    );


$duplicate =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(
    $duplicate
){

    main_exhibition_save_response(
        false,
        '이미 메인 전시에 등록된 프로젝트입니다.'
    );

}



/*
 * =========================================================
 * ACTIVE LIMIT
 * 메인 최대 4개
 * =========================================================
 */

if(
    $is_active == 1
){

    $query = "

        SELECT

            COUNT(*) AS cnt

        FROM main_exhibitions

        WHERE
            is_active = 1
            AND id != ?

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    $active_count =
        $data
            ? intval(
                $data['cnt']
            )
            : 0;


    if(
        $active_count >= 4
    ){

        main_exhibition_save_response(
            false,
            '메인 전시는 최대 4개까지 노출할 수 있습니다.'
        );

    }

}



/*
 * =========================================================
 * CLIENT IDS
 * =========================================================
 */

$client_ids =
    array();


if(
    isset($_POST['client_id']) &&
    is_array(
        $_POST['client_id']
    )
){

    foreach(
        $_POST['client_id'] as $client_id
    ){

        $client_id =
            intval(
                $client_id
            );


        if(
            $client_id <= 0
        ){

            continue;

        }


        if(
            !in_array(
                $client_id,
                $client_ids
            )
        ){

            $client_ids[] =
                $client_id;

        }

    }

}



/*
 * =========================================================
 * CLIENT VALIDATION
 * =========================================================
 */

if(
    count($client_ids) > 0
){

    foreach(
        $client_ids as $client_id
    ){

        $query = "

            SELECT

                id

            FROM clients

            WHERE
                id = ?

            LIMIT 1

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $client_id
                )
            );


        $client =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        if(
            !$client
        ){

            main_exhibition_save_response(
                false,
                '선택한 클라이언트 정보가 올바르지 않습니다.'
            );

        }

    }

}



/*
 * =========================================================
 * IMAGE UPLOAD VALIDATION
 * =========================================================
 */

$image_upload =
    null;


$has_image_upload =
    isset($_FILES['image_file']) &&
    isset($_FILES['image_file']['error']) &&
    intval(
        $_FILES['image_file']['error']
    ) != UPLOAD_ERR_NO_FILE;


if(
    $has_image_upload
){

    $image_error =
        intval(
            $_FILES['image_file']['error']
        );


    if(
        $image_error != UPLOAD_ERR_OK
    ){

        main_exhibition_save_response(
            false,
            main_exhibition_upload_error_message(
                $image_error,
                '이미지'
            )
        );

    }


    $image_tmp =
        $_FILES['image_file']['tmp_name'];


    if(
        !is_uploaded_file(
            $image_tmp
        )
    ){

        main_exhibition_save_response(
            false,
            '이미지 업로드 파일이 올바르지 않습니다.'
        );

    }


    $image_original_name =
        main_exhibition_original_name(
            $_FILES['image_file']['name']
        );


    $image_extension =
        strtolower(
            pathinfo(
                $image_original_name,
                PATHINFO_EXTENSION
            )
        );


    $image_extensions =
        array(
            'jpg',
            'jpeg',
            'png',
            'webp'
        );


    if(
        !in_array(
            $image_extension,
            $image_extensions
        )
    ){

        main_exhibition_save_response(
            false,
            '메인 전시 이미지는 JPG, PNG, WEBP 파일만 등록할 수 있습니다.'
        );

    }


    $image_mime =
        main_exhibition_get_mime(
            $image_tmp
        );


    $image_mimes =
        array(
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/webp'
        );


    if(
        $image_mime != '' &&
        !in_array(
            $image_mime,
            $image_mimes
        )
    ){

        main_exhibition_save_response(
            false,
            '올바른 이미지 파일이 아닙니다.'
        );

    }


    $image_width =
        null;


    $image_height =
        null;


    $image_info =
        @getimagesize(
            $image_tmp
        );


    if(
        $image_info &&
        isset($image_info[0]) &&
        isset($image_info[1])
    ){

        $image_width =
            intval(
                $image_info[0]
            );


        $image_height =
            intval(
                $image_info[1]
            );

    }


    $image_upload =
        array(
            'tmp_name'      => $image_tmp,
            'original_name' => $image_original_name,
            'extension'     => $image_extension,
            'mime_type'     => $image_mime != ''
                ? $image_mime
                : 'image/'.$image_extension,
            'byte_size'     => intval(
                $_FILES['image_file']['size']
            ),
            'width'         => $image_width,
            'height'        => $image_height
        );

}



/*
 * =========================================================
 * IMAGE REQUIRED
 * =========================================================
 */

if(
    !$image_upload &&
    empty(
        $current_image_media_id
    )
){

    main_exhibition_save_response(
        false,
        '메인 전시 이미지를 등록해주세요.'
    );

}



/*
 * =========================================================
 * VIDEO UPLOAD VALIDATION
 *
 * MAX 100MB
 * =========================================================
 */

$video_upload =
    null;


$video_max_size =
    100 * 1024 * 1024;


$has_video_upload =
    isset($_FILES['video_file']) &&
    isset($_FILES['video_file']['error']) &&
    intval(
        $_FILES['video_file']['error']
    ) != UPLOAD_ERR_NO_FILE;


if(
    $has_video_upload
){

    $video_error =
        intval(
            $_FILES['video_file']['error']
        );


    if(
        $video_error != UPLOAD_ERR_OK
    ){

        main_exhibition_save_response(
            false,
            main_exhibition_upload_error_message(
                $video_error,
                '영상'
            )
        );

    }


    $video_size =
        intval(
            $_FILES['video_file']['size']
        );


    if(
        $video_size <= 0
    ){

        main_exhibition_save_response(
            false,
            '영상 파일의 크기를 확인할 수 없습니다.'
        );

    }


    if(
        $video_size > $video_max_size
    ){

        main_exhibition_save_response(
            false,
            '영상은 100MB 이하의 파일만 등록할 수 있습니다.'
        );

    }


    $video_tmp =
        $_FILES['video_file']['tmp_name'];


    if(
        !is_uploaded_file(
            $video_tmp
        )
    ){

        main_exhibition_save_response(
            false,
            '영상 업로드 파일이 올바르지 않습니다.'
        );

    }


    $video_original_name =
        main_exhibition_original_name(
            $_FILES['video_file']['name']
        );


    $video_extension =
        strtolower(
            pathinfo(
                $video_original_name,
                PATHINFO_EXTENSION
            )
        );


    if(
        $video_extension != 'mp4'
    ){

        main_exhibition_save_response(
            false,
            '영상은 MP4 파일만 등록할 수 있습니다.'
        );

    }


    $video_mime =
        main_exhibition_get_mime(
            $video_tmp
        );


    $video_mimes =
        array(
            'video/mp4',
            'application/mp4',
            'video/x-m4v'
        );


    if(
        $video_mime != '' &&
        !in_array(
            $video_mime,
            $video_mimes
        )
    ){

        main_exhibition_save_response(
            false,
            '올바른 MP4 영상 파일이 아닙니다.'
        );

    }


    $video_upload =
        array(
            'tmp_name'      => $video_tmp,
            'original_name' => $video_original_name,
            'extension'     => 'mp4',
            'mime_type'     => $video_mime != ''
                ? $video_mime
                : 'video/mp4',
            'byte_size'     => $video_size
        );

}



/*
 * =========================================================
 * UPLOAD DIRECTORY
 * =========================================================
 */

$upload_month =
    date(
        'Ym'
    );


$upload_relative_dir =
    '/uploads/main_exhibition/'.
    $upload_month.
    '/';


$upload_dir =
    $_SERVER['DOCUMENT_ROOT'].
    $upload_relative_dir;


if(
    (
        $image_upload ||
        $video_upload
    ) &&
    !is_dir(
        $upload_dir
    )
){

    if(
        !@mkdir(
            $upload_dir,
            0775,
            true
        )
    ){

        main_exhibition_save_response(
            false,
            '업로드 폴더를 생성하지 못했습니다.'
        );

    }

}


if(
    (
        $image_upload ||
        $video_upload
    ) &&
    !is_writable(
        $upload_dir
    )
){

    main_exhibition_save_response(
        false,
        '업로드 폴더에 파일을 저장할 수 없습니다.'
    );

}



/*
 * =========================================================
 * MOVE UPLOAD FILES
 * =========================================================
 */

$new_files =
    array();



/*
 * IMAGE
 */

if(
    $image_upload
){

    $image_extension =
        $image_upload['extension'];


    if(
        $image_extension == 'jpeg'
    ){

        $image_extension =
            'jpg';

    }


    $image_file_name =
        date(
            'YmdHis'
        ).
        '_'.
        md5(
            uniqid(
                mt_rand(),
                true
            )
        ).
        '.'.
        $image_extension;


    $image_absolute_path =
        $upload_dir.
        $image_file_name;


    $image_storage_key =
        $upload_relative_dir.
        $image_file_name;


    if(
        !move_uploaded_file(
            $image_upload['tmp_name'],
            $image_absolute_path
        )
    ){

        main_exhibition_save_response(
            false,
            '메인 전시 이미지를 저장하지 못했습니다.'
        );

    }


    $new_files[] =
        $image_absolute_path;


    $image_upload['storage_key'] =
        $image_storage_key;

}



/*
 * VIDEO
 */

if(
    $video_upload
){

    $video_file_name =
        date(
            'YmdHis'
        ).
        '_'.
        md5(
            uniqid(
                mt_rand(),
                true
            )
        ).
        '.mp4';


    $video_absolute_path =
        $upload_dir.
        $video_file_name;


    $video_storage_key =
        $upload_relative_dir.
        $video_file_name;


    if(
        !move_uploaded_file(
            $video_upload['tmp_name'],
            $video_absolute_path
        )
    ){

        main_exhibition_cleanup_files(
            $new_files
        );


        main_exhibition_save_response(
            false,
            '영상 파일을 저장하지 못했습니다.'
        );

    }


    $new_files[] =
        $video_absolute_path;


    $video_upload['storage_key'] =
        $video_storage_key;

}



/*
 * =========================================================
 * ADMIN ID
 * =========================================================
 */

$uploaded_by =
    isset($_SESSION['ADMIN_ID'])
        ? intval(
            $_SESSION['ADMIN_ID']
        )
        : null;


if(
    empty($uploaded_by)
){

    $uploaded_by =
        null;

}



/*
 * =========================================================
 * TRANSACTION
 * =========================================================
 */

try{

    $db->beginTransaction();



    /*
     * =====================================================
     * FINAL MEDIA ID
     * =====================================================
     */

    $final_image_media_id =
        $current_image_media_id;


    $final_video_media_id =
        $current_video_media_id;



    /*
     * =====================================================
     * IMAGE MEDIA
     * =====================================================
     */

    if(
        $image_upload
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
                ?,
                ?,
                NOW(),
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $image_upload['storage_key'],
                $image_upload['original_name'],
                $image_upload['mime_type'],
                $image_upload['byte_size'],
                $image_upload['width'],
                $image_upload['height'],
                '',
                $uploaded_by
            )
        );


        $query = "

            SELECT

                LAST_INSERT_ID() AS id

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array()
            );


        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $final_image_media_id =
            $data
                ? intval(
                    $data['id']
                )
                : 0;


        if(
            $final_image_media_id <= 0
        ){

            throw new Exception(
                '이미지 정보를 저장하지 못했습니다.'
            );

        }

    }



    /*
     * =====================================================
     * VIDEO MEDIA
     * =====================================================
     */

    if(
        $video_upload
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
                NULL,
                NULL,
                ?,
                ?,
                NOW(),
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $video_upload['storage_key'],
                $video_upload['original_name'],
                $video_upload['mime_type'],
                $video_upload['byte_size'],
                '',
                $uploaded_by
            )
        );


        $query = "

            SELECT

                LAST_INSERT_ID() AS id

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array()
            );


        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $final_video_media_id =
            $data
                ? intval(
                    $data['id']
                )
                : 0;


        if(
            $final_video_media_id <= 0
        ){

            throw new Exception(
                '영상 정보를 저장하지 못했습니다.'
            );

        }

    }else if(
        $delete_video
    ){

        /*
         * 기존 media/file은 유지
         * 연결만 제거
         */

        $final_video_media_id =
            null;

    }



    /*
     * =====================================================
     * IMAGE REQUIRED FINAL CHECK
     * =====================================================
     */

    if(
        empty(
            $final_image_media_id
        )
    ){

        throw new Exception(
            '메인 전시 이미지가 등록되지 않았습니다.'
        );

    }



    /*
     * =====================================================
     * MAIN EXHIBITION
     * =====================================================
     */

    if(
        $id > 0
    ){

        $query = "

            UPDATE main_exhibitions

            SET
                work_id = ?,
                image_media_id = ?,
                video_media_id = ?,
                intro_text = ?,
                band_color = ?,
                text_color = ?,
                sort_order = ?,
                is_active = ?,
                updated_at = NOW()

            WHERE
                id = ?

        ";


        pdo_query(
            $db,
            $query,
            array(
                $work_id,
                $final_image_media_id,
                $final_video_media_id,
                $intro_text,
                $band_color,
                $text_color,
                $sort_order,
                $is_active,
                $id
            )
        );


        $exhibition_id =
            $id;

    }else{

        $query = "

            INSERT INTO main_exhibitions
            (
                work_id,
                image_media_id,
                video_media_id,
                intro_text,
                band_color,
                text_color,
                sort_order,
                is_active,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW(),
                NOW()
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $work_id,
                $final_image_media_id,
                $final_video_media_id,
                $intro_text,
                $band_color,
                $text_color,
                $sort_order,
                $is_active
            )
        );


        $query = "

            SELECT

                LAST_INSERT_ID() AS id

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array()
            );


        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $exhibition_id =
            $data
                ? intval(
                    $data['id']
                )
                : 0;


        if(
            $exhibition_id <= 0
        ){

            throw new Exception(
                '메인 전시 정보를 저장하지 못했습니다.'
            );

        }

    }



    /*
     * =====================================================
     * CLIENT MAPPING RESET
     * =====================================================
     */

    $query = "

        DELETE FROM main_exhibition_clients

        WHERE
            exhibition_id = ?

    ";


    pdo_query(
        $db,
        $query,
        array(
            $exhibition_id
        )
    );



    /*
     * =====================================================
     * CLIENT MAPPING INSERT
     *
     * 체크된 순서대로 sort_order 저장
     * =====================================================
     */

    if(
        count($client_ids) > 0
    ){

        $client_sort_order =
            0;


        foreach(
            $client_ids as $client_id
        ){

            $query = "

                INSERT INTO main_exhibition_clients
                (
                    exhibition_id,
                    client_id,
                    sort_order
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )

            ";


            pdo_query(
                $db,
                $query,
                array(
                    $exhibition_id,
                    $client_id,
                    $client_sort_order
                )
            );


            $client_sort_order++;

        }

    }



    /*
     * =====================================================
     * COMMIT
     * =====================================================
     */

    $db->commit();



    /*
     * =====================================================
     * RESPONSE
     * =====================================================
     */

    main_exhibition_save_response(
        true,
        $id > 0
            ? '메인 전시가 수정되었습니다.'
            : '메인 전시가 등록되었습니다.',
        array(
            'id' => $exhibition_id
        )
    );


}catch(Exception $e){


    if(
        $db->inTransaction()
    ){

        $db->rollBack();

    }


    /*
     * 이번 요청에서 새로 업로드한 파일만 제거
     *
     * 기존 media / 기존 파일은 삭제하지 않음
     */

    main_exhibition_cleanup_files(
        $new_files
    );


    main_exhibition_save_response(
        false,
        $e->getMessage() != ''
            ? $e->getMessage()
            : '저장 처리 중 오류가 발생했습니다.'
    );

}
?>