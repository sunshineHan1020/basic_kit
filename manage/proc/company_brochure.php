<?
/*
 * =========================================================
 * WEEKENDS CMS
 * COMPANY BROCHURE PROC
 * =========================================================
 */

require_once
    $_SERVER['DOCUMENT_ROOT'].
    '/lib/common.php';


require_once
    $_SERVER['DOCUMENT_ROOT'].
    '/manage/inc/auth.php';



/*
 * =========================================================
 * RESPONSE
 * =========================================================
 */

function brochure_json(
    $result,
    $message
){

    header(
        'Content-Type: application/json; charset=UTF-8'
    );


    echo json_encode(
        array(
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

    brochure_json(
        'error',
        '잘못된 접근입니다.'
    );

}






/*
 * =========================================================
 * MODE
 * =========================================================
 */

$mode =
    isset($_POST['mode'])
        ? trim(
            $_POST['mode']
        )
        : '';



/*
 * =========================================================
 * DELETE
 * =========================================================
 */

if(
    $mode == 'delete'
){

    $brochure_id =
        isset($_POST['brochure_id'])
            ? intval(
                $_POST['brochure_id']
            )
            : 0;


    if(
        $brochure_id <= 0
    ){

        brochure_json(
            'error',
            '잘못된 회사소개서 정보입니다.'
        );

    }



    /*
     * 현재 데이터
     */

    $query = "

        SELECT

            cb.id,
            cb.media_id,
            m.storage_key

        FROM company_brochures cb

        INNER JOIN media m
            ON m.id = cb.media_id

        WHERE
            cb.id = ?
            AND cb.is_active = 1
            AND m.deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $brochure_id
            )
        );


    $brochure =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(
        !$brochure
    ){

        brochure_json(
            'error',
            '등록된 회사소개서를 찾을 수 없습니다.'
        );

    }



    try{


        $db->beginTransaction();



        /*
         * 회사소개서 비활성화
         */

        $query = "

            UPDATE company_brochures

            SET
                is_active = 0,
                updated_at = NOW()

            WHERE
                id = ?

        ";


        pdo_query(
            $db,
            $query,
            array(
                $brochure_id
            )
        );



        /*
         * MEDIA 삭제 처리
         */

        $query = "

            UPDATE media

            SET
                deleted_at = NOW()

            WHERE
                id = ?

        ";


        pdo_query(
            $db,
            $query,
            array(
                intval(
                    $brochure['media_id']
                )
            )
        );


        $db->commit();



    }catch(Exception $e){


        if(
            $db->inTransaction()
        ){

            $db->rollBack();

        }


        brochure_json(
            'error',
            '회사소개서를 삭제하지 못했습니다.'
        );

    }



    /*
     * 실제 파일 삭제
     */

    $file_path =
        $_SERVER['DOCUMENT_ROOT'].
        $brochure['storage_key'];


    if(
        is_file(
            $file_path
        )
    ){

        @unlink(
            $file_path
        );

    }



    brochure_json(
        'success',
        '회사소개서가 삭제되었습니다.'
    );

}



/*
 * =========================================================
 * UPLOAD
 * =========================================================
 */

if(
    $mode != 'upload'
){

    brochure_json(
        'error',
        '잘못된 요청입니다.'
    );

}



/*
 * 파일 확인
 */

if(
    !isset($_FILES['brochure_file']) ||
    !isset($_FILES['brochure_file']['error']) ||
    $_FILES['brochure_file']['error'] ==
        UPLOAD_ERR_NO_FILE
){

    brochure_json(
        'error',
        '등록할 PDF 파일을 선택해주세요.'
    );

}


$file =
    $_FILES['brochure_file'];



if(
    $file['error'] !=
        UPLOAD_ERR_OK
){

    brochure_json(
        'error',
        '파일 업로드 중 오류가 발생했습니다.'
    );

}



/*
 * =========================================================
 * FILE VALIDATE
 * =========================================================
 */

$max_size =
    50 * 1024 * 1024;


$file_size =
    intval(
        $file['size']
    );


if(
    $file_size <= 0 ||
    $file_size >
        $max_size
){

    brochure_json(
        'error',
        '회사소개서는 50MB 이하만 등록할 수 있습니다.'
    );

}



$original_name =
    basename(
        $file['name']
    );


$extension =
    strtolower(
        pathinfo(
            $original_name,
            PATHINFO_EXTENSION
        )
    );


if(
    $extension != 'pdf'
){

    brochure_json(
        'error',
        'PDF 파일만 등록할 수 있습니다.'
    );

}



/*
 * MIME
 */

$mime_type =
    '';


if(
    function_exists('finfo_open')
){

    $finfo =
        finfo_open(
            FILEINFO_MIME_TYPE
        );


    if($finfo){

        $mime_type =
            finfo_file(
                $finfo,
                $file['tmp_name']
            );


        finfo_close(
            $finfo
        );

    }

}


if(
    $mime_type != 'application/pdf'
){

    brochure_json(
        'error',
        '올바른 PDF 파일이 아닙니다.'
    );

}



/*
 * PDF SIGNATURE
 */

$fp =
    @fopen(
        $file['tmp_name'],
        'rb'
    );


$pdf_signature =
    '';


if($fp){

    $pdf_signature =
        fread(
            $fp,
            5
        );


    fclose(
        $fp
    );

}


if(
    $pdf_signature != '%PDF-'
){

    brochure_json(
        'error',
        '올바른 PDF 파일이 아닙니다.'
    );

}



/*
 * =========================================================
 * UPLOAD PATH
 * =========================================================
 */

$relative_dir =
    '/uploads/company_brochure/'.
    date('Ym').
    '/';


$absolute_dir =
    $_SERVER['DOCUMENT_ROOT'].
    $relative_dir;


if(
    !is_dir(
        $absolute_dir
    )
){

    if(
        !@mkdir(
            $absolute_dir,
            0755,
            true
        )
    ){

        brochure_json(
            'error',
            '업로드 폴더를 생성하지 못했습니다.'
        );

    }

}



/*
 * 랜덤 파일명
 */

$random =
    openssl_random_pseudo_bytes(
        16
    );


if(
    $random !== false
){

    $save_name =
        bin2hex(
            $random
        );


}else{

    $save_name =
        str_replace(
            '.',
            '',
            uniqid(
                'brochure_',
                true
            )
        );

}


$save_name .=
    '.pdf';


$storage_key =
    $relative_dir.
    $save_name;


$save_path =
    $absolute_dir.
    $save_name;



if(
    !move_uploaded_file(
        $file['tmp_name'],
        $save_path
    )
){

    brochure_json(
        'error',
        '회사소개서 저장에 실패했습니다.'
    );

}



/*
 * =========================================================
 * DB SAVE
 * =========================================================
 */

try{


    $db->beginTransaction();



    /*
     * MEDIA
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
            NULL,
            NULL,
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
            $file_size,
            isset($_SESSION['ADMIN_ID'])
                ? intval(
                    $_SESSION['ADMIN_ID']
                )
                : null
        )
    );



    /*
     * MEDIA ID
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


    $media_id =
        $media_data
            ? intval(
                $media_data['id']
            )
            : 0;


    if(
        $media_id <= 0
    ){

        throw new Exception(
            'MEDIA_INSERT_ERROR'
        );

    }



    /*
     * 기존 회사소개서 비활성화
     */

    $query = "

        UPDATE company_brochures

        SET
            is_active = 0,
            updated_at = NOW()

        WHERE
            is_active = 1

    ";


    pdo_query(
        $db,
        $query,
        array()
    );



    /*
     * 신규 회사소개서
     */

    $query = "

        INSERT INTO company_brochures
        (
            media_id,
            title,
            sort_order,
            is_active,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,
            ?,
            0,
            1,
            NOW(),
            NOW()
        )

    ";


    pdo_query(
        $db,
        $query,
        array(
            $media_id,
            'WEEKENDS 회사소개서'
        )
    );


    $db->commit();



}catch(Exception $e){


    if(
        $db->inTransaction()
    ){

        $db->rollBack();

    }


    if(
        is_file(
            $save_path
        )
    ){

        @unlink(
            $save_path
        );

    }


    brochure_json(
        'error',
        '회사소개서 등록에 실패했습니다.'
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

brochure_json(
    'success',
    '회사소개서가 등록되었습니다.'
);