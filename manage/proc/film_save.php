<?
/*
 * =========================================================
 * WEEKENDS CMS
 * FILM 등록 / 수정
 *
 * FILE
 * /manage/proc/film_save.php
 *
 * URL
 * /manage/proc/film_save
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

function film_save_json(
    $code,
    $msg,
    $id
){
    echo json_encode(
        array(
            'code' => $code,
            'msg'  => $msg,
            'id'   => intval($id)
        )
    );

    exit;
}



/*
 * =========================================================
 * LAST INSERT ID
 * =========================================================
 */

function film_last_insert_id($db)
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
 * 문자열 길이
 * =========================================================
 */

function film_strlen($str)
{
    if(
        function_exists(
            'mb_strlen'
        )
    ){

        return mb_strlen(
            $str,
            'UTF-8'
        );

    }


    return strlen(
        $str
    );
}



/*
 * =========================================================
 * IMAGE UPLOAD
 * =========================================================
 */

function film_upload_image(
    $db,
    $file,
    $uploaded_by,
    &$uploaded_files
){

    /*
     * 파일 없음
     */

    if(
        !isset($file['error']) ||
        intval($file['error']) ==
        UPLOAD_ERR_NO_FILE
    ){

        return 0;

    }



    /*
     * 업로드 오류
     */

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

            throw new Exception(
                '서버의 업로드 허용 용량을 초과했습니다.'
            );

        }else if(
            $upload_error ==
            UPLOAD_ERR_FORM_SIZE
        ){

            throw new Exception(
                '업로드 허용 용량을 초과했습니다.'
            );

        }else if(
            $upload_error ==
            UPLOAD_ERR_PARTIAL
        ){

            throw new Exception(
                '파일이 일부만 업로드되었습니다.'
            );

        }else{

            throw new Exception(
                '이미지 업로드 중 오류가 발생했습니다. ('.$upload_error.')'
            );

        }

    }



    /*
     * 임시 파일
     */

    if(
        !isset($file['tmp_name']) ||
        $file['tmp_name'] == '' ||
        !is_uploaded_file(
            $file['tmp_name']
        )
    ){

        throw new Exception(
            '업로드 파일을 확인할 수 없습니다.'
        );

    }



    /*
     * 실제 파일 크기
     */

    $temp_size =
        @filesize(
            $file['tmp_name']
        );


    if(
        $temp_size === false ||
        intval($temp_size) <= 0
    ){

        throw new Exception(
            '업로드 파일의 크기를 확인할 수 없습니다.'
        );

    }



    /*
     * 최대 5MB
     */

    if(
        intval($temp_size) >
        5 * 1024 * 1024
    ){

        throw new Exception(
            '이미지는 최대 5MB까지 업로드할 수 있습니다.'
        );

    }



    /*
     * $_FILES size 확인
     */

    if(
        isset($file['size']) &&
        intval($file['size']) > 0 &&
        intval($file['size']) != intval($temp_size)
    ){

        throw new Exception(
            '업로드된 파일의 크기가 일치하지 않습니다.'
        );

    }



    /*
     * 이미지 확인
     */

    $image_info =
        @getimagesize(
            $file['tmp_name']
        );


    if(!$image_info){

        throw new Exception(
            '정상적인 이미지 파일이 아닙니다.'
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
     * MIME
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
     * fallback
     */

    if(
        $mime_type == '' &&
        isset($image_info['mime'])
    ){

        $mime_type =
            $image_info['mime'];

    }



    /*
     * Extension
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

        throw new Exception(
            'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.'
        );

    }



    /*
     * 저장 전 HASH
     */

    $temp_hash =
        @md5_file(
            $file['tmp_name']
        );


    if(
        $temp_hash === false
    ){

        throw new Exception(
            '업로드 임시 파일을 확인하지 못했습니다.'
        );

    }



    /*
     * 저장 경로
     *
     * /uploads/film/202609/
     */

    $folder =
        date(
            'Ym'
        );


    $upload_path =
        '/uploads/film/'.
        $folder;


    $upload_dir =
        $_SERVER['DOCUMENT_ROOT'].
        $upload_path;



    /*
     * 디렉토리 생성
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

            throw new Exception(
                '이미지 저장 디렉토리를 생성하지 못했습니다.'
            );

        }

    }



    /*
     * 파일명
     */

    do {

        $file_name =

            date(
                'YmdHis'
            ).

            '_'.

            intval(
                $uploaded_by
            ).

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
     * 저장
     */

    if(
        !move_uploaded_file(
            $file['tmp_name'],
            $save_file
        )
    ){

        throw new Exception(
            '이미지 파일을 저장하지 못했습니다.'
        );

    }



    /*
     * 캐시 제거
     */

    clearstatcache(
        true,
        $save_file
    );



    /*
     * 저장 후 검증
     */

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


        throw new Exception(
            '이미지 저장 과정에서 파일이 손상되었습니다.'
        );

    }



    /*
     * 저장 이미지 재검증
     */

    $saved_image_info =
        @getimagesize(
            $save_file
        );


    if(!$saved_image_info){

        @unlink(
            $save_file
        );


        throw new Exception(
            '저장된 이미지 파일이 손상되었습니다.'
        );

    }



    /*
     * Rollback 시 삭제 대상
     */

    $uploaded_files[] =
        $save_file;



    /*
     * Original Name
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
     * media INSERT
     */

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
            intval(
                $uploaded_by
            )
        )
    );


    $media_id =
        film_last_insert_id(
            $db
        );


    if(
        $media_id <= 0
    ){

        throw new Exception(
            '이미지 정보를 저장하지 못했습니다.'
        );

    }


    return
        $media_id;
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

    film_save_json(
        false,
        '잘못된 접근입니다.',
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



/*
 * =========================================================
 * POST
 * =========================================================
 */

$film_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$title =
    isset($_POST['title'])
        ? trim($_POST['title'])
        : '';


$link_url =
    isset($_POST['link_url'])
        ? trim($_POST['link_url'])
        : '';


$sort_order =
    isset($_POST['sort_order'])
        ? intval($_POST['sort_order'])
        : 0;


$is_active =
    isset($_POST['is_active'])
        ? intval($_POST['is_active'])
        : 1;



/*
 * =========================================================
 * Validation
 * =========================================================
 */

if(
    $title == ''
){

    film_save_json(
        false,
        '제목을 입력해주세요.',
        $film_id
    );

}


if(
    film_strlen(
        $title
    ) > 255
){

    film_save_json(
        false,
        '제목은 255자 이하로 입력해주세요.',
        $film_id
    );

}



/*
 * Link
 */

if(
    strlen(
        $link_url
    ) > 2048
){

    film_save_json(
        false,
        '링크 주소가 너무 깁니다.',
        $film_id
    );

}


if(
    $link_url != '' &&
    !preg_match(
        '/^https?:\/\//i',
        $link_url
    )
){

    film_save_json(
        false,
        '링크는 http:// 또는 https:// 로 시작해주세요.',
        $film_id
    );

}



/*
 * Sort
 */

if(
    $sort_order < 0
){

    $sort_order =
        0;

}



/*
 * Active
 */

if(
    $is_active != 0 &&
    $is_active != 1
){

    $is_active =
        1;

}



/*
 * =========================================================
 * 수정 대상
 * =========================================================
 */

$existing_film =
    false;


if(
    $film_id > 0
){

    $query = "

        SELECT
            id,
            media_id,
            title,
            link_url,
            sort_order,
            is_active

        FROM films

        WHERE
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $film_id
            )
        );


    $existing_film =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$existing_film){

        film_save_json(
            false,
            '존재하지 않는 FILM입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * 신규는 이미지 필수
 * =========================================================
 */

if(
    $film_id <= 0
){

    if(
        !isset($_FILES['image']) ||
        !isset($_FILES['image']['error']) ||
        intval($_FILES['image']['error']) ==
        UPLOAD_ERR_NO_FILE
    ){

        film_save_json(
            false,
            'FILM 이미지를 등록해주세요.',
            0
        );

    }

}



/*
 * =========================================================
 * Rollback 시 삭제할 신규 파일
 * =========================================================
 */

$uploaded_files =
    array();



/*
 * =========================================================
 * SAVE
 * =========================================================
 */

try {


    /*
     * =====================================================
     * TRANSACTION
     * =====================================================
     */

    $db->beginTransaction();



    /*
     * =====================================================
     * IMAGE
     * =====================================================
     */

    $new_media_id =
        0;


    if(
        isset($_FILES['image'])
    ){

        $new_media_id =
            film_upload_image(
                $db,
                $_FILES['image'],
                $admin_id,
                $uploaded_files
            );

    }



    /*
     * =====================================================
     * UPDATE
     * =====================================================
     */

    if(
        $film_id > 0
    ){

        /*
         * 새 이미지가 있으면 교체
         */

        if(
            $new_media_id > 0
        ){

            $query = "

                UPDATE films

                SET
                    media_id = ?,
                    title = ?,
                    link_url = ?,
                    sort_order = ?,
                    is_active = ?,
                    updated_at = NOW()

                WHERE
                    id = ?

                LIMIT 1

            ";


            pdo_query(
                $db,
                $query,
                array(
                    $new_media_id,
                    $title,
                    $link_url,
                    $sort_order,
                    $is_active,
                    $film_id
                )
            );


        /*
         * 이미지 유지
         */

        }else{

            $query = "

                UPDATE films

                SET
                    title = ?,
                    link_url = ?,
                    sort_order = ?,
                    is_active = ?,
                    updated_at = NOW()

                WHERE
                    id = ?

                LIMIT 1

            ";


            pdo_query(
                $db,
                $query,
                array(
                    $title,
                    $link_url,
                    $sort_order,
                    $is_active,
                    $film_id
                )
            );

        }



    /*
     * =====================================================
     * INSERT
     * =====================================================
     */

    }else{


        if(
            $new_media_id <= 0
        ){

            throw new Exception(
                'FILM 이미지를 등록해주세요.'
            );

        }


        $query = "

            INSERT INTO films
            (
                media_id,
                title,
                link_url,
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
                NOW(),
                NOW()
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $new_media_id,
                $title,
                $link_url,
                $sort_order,
                $is_active
            )
        );


        $film_id =
            film_last_insert_id(
                $db
            );


        if(
            $film_id <= 0
        ){

            throw new Exception(
                'FILM 정보를 등록하지 못했습니다.'
            );

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
     * SUCCESS
     * =====================================================
     */

    if(
        isset($_POST['id']) &&
        intval($_POST['id']) > 0
    ){

        film_save_json(
            true,
            'FILM이 수정되었습니다.',
            $film_id
        );

    }


    film_save_json(
        true,
        'FILM이 등록되었습니다.',
        $film_id
    );


} catch(Exception $e) {


    /*
     * =====================================================
     * ROLLBACK
     * =====================================================
     */

    if(
        $db->inTransaction()
    ){

        $db->rollBack();

    }



    /*
     * =====================================================
     * 이번 요청에서 새로 생성된 파일 제거
     * =====================================================
     */

    foreach(
        $uploaded_files as
        $uploaded_file
    ){

        if(
            $uploaded_file != '' &&
            is_file(
                $uploaded_file
            )
        ){

            @unlink(
                $uploaded_file
            );

        }

    }



    /*
     * =====================================================
     * ERROR
     * =====================================================
     */

    film_save_json(
        false,
        $e->getMessage(),
        $film_id
    );

}
?>