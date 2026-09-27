<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 사이트 기본 설정 저장
 *
 * FILE
 * /manage/proc/setting_save.php
 *
 * URL
 * /manage/proc/setting_save
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

function setting_save_json(
    $code,
    $msg
){
    echo json_encode(
        array(
            'code' => $code,
            'msg'  => $msg
        )
    );

    exit;
}



/*
 * =========================================================
 * LENGTH
 * =========================================================
 */

function setting_save_strlen($str)
{
    if(
        function_exists('mb_strlen')
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
 * IMAGE MIME
 * =========================================================
 */

function setting_image_mime(
    $tmp_name,
    $extension
){
    $mime =
        '';


    /*
     * FileInfo
     */

    if(
        function_exists('finfo_open')
    ){

        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );


        if($finfo){

            $mime =
                finfo_file(
                    $finfo,
                    $tmp_name
                );


            finfo_close(
                $finfo
            );

        }

    }



    /*
     * MIME alias
     */

    if(
        $mime == 'image/pjpeg'
    ){
        $mime = 'image/jpeg';
    }


    if(
        $mime == 'image/x-png'
    ){
        $mime = 'image/png';
    }



    /*
     * PHP 5.4 환경에서 WEBP MIME 인식이
     * 안 되는 경우가 있어 RIFF 헤더 확인
     */

    if(
        $extension == 'webp' &&
        $mime != 'image/webp'
    ){

        $fp =
            @fopen(
                $tmp_name,
                'rb'
            );


        if($fp){

            $header =
                fread(
                    $fp,
                    12
                );


            fclose(
                $fp
            );


            if(
                strlen($header) >= 12 &&
                substr($header, 0, 4) == 'RIFF' &&
                substr($header, 8, 4) == 'WEBP'
            ){

                $mime =
                    'image/webp';

            }

        }

    }


    return
        $mime;
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

    setting_save_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * PERMISSION
 *
 * SUPER / ADMIN
 * =========================================================
 */

if(
    !isset($_SESSION['ADMIN_ROLE']) ||
    (
        $_SESSION['ADMIN_ROLE'] != 'super' &&
        $_SESSION['ADMIN_ROLE'] != 'admin'
    )
){

    setting_save_json(
        false,
        '설정 관리 권한이 없습니다.'
    );

}



/*
 * =========================================================
 * POST
 * =========================================================
 */

$site_name =
    isset($_POST['site_name'])
        ? trim($_POST['site_name'])
        : '';


$seo_title =
    isset($_POST['seo_title'])
        ? trim($_POST['seo_title'])
        : '';


$meta_description =
    isset($_POST['meta_description'])
        ? trim($_POST['meta_description'])
        : '';


$meta_keywords =
    isset($_POST['meta_keywords'])
        ? trim($_POST['meta_keywords'])
        : '';


$og_title =
    isset($_POST['og_title'])
        ? trim($_POST['og_title'])
        : '';


$og_description =
    isset($_POST['og_description'])
        ? trim($_POST['og_description'])
        : '';


$canonical_url =
    isset($_POST['canonical_url'])
        ? trim($_POST['canonical_url'])
        : '';


$delete_og_image =
    isset($_POST['delete_og_image']) &&
    $_POST['delete_og_image'] == 'Y'
        ? 'Y'
        : 'N';



/*
 * =========================================================
 * VALIDATE
 * =========================================================
 */

if(
    $site_name == ''
){

    setting_save_json(
        false,
        '사이트명을 입력해주세요.'
    );

}


if(
    setting_save_strlen(
        $site_name
    ) > 255
){

    setting_save_json(
        false,
        '사이트명은 255자 이내로 입력해주세요.'
    );

}



if(
    $seo_title == ''
){

    setting_save_json(
        false,
        '기본 SEO Title을 입력해주세요.'
    );

}


if(
    setting_save_strlen(
        $seo_title
    ) > 255
){

    setting_save_json(
        false,
        'SEO Title은 255자 이내로 입력해주세요.'
    );

}



if(
    setting_save_strlen(
        $meta_description
    ) > 1000
){

    setting_save_json(
        false,
        'Meta Description은 1000자 이내로 입력해주세요.'
    );

}



if(
    setting_save_strlen(
        $og_title
    ) > 255
){

    setting_save_json(
        false,
        'OG Title은 255자 이내로 입력해주세요.'
    );

}



if(
    setting_save_strlen(
        $og_description
    ) > 1000
){

    setting_save_json(
        false,
        'OG Description은 1000자 이내로 입력해주세요.'
    );

}



if(
    $canonical_url != ''
){

    if(
        strlen(
            $canonical_url
        ) > 2048
    ){

        setting_save_json(
            false,
            '기본 URL이 너무 깁니다.'
        );

    }


    if(
        !filter_var(
            $canonical_url,
            FILTER_VALIDATE_URL
        )
    ){

        setting_save_json(
            false,
            '기본 URL 형식이 올바르지 않습니다.'
        );

    }


    $scheme =
        parse_url(
            $canonical_url,
            PHP_URL_SCHEME
        );


    if(
        $scheme != 'http' &&
        $scheme != 'https'
    ){

        setting_save_json(
            false,
            '기본 URL은 http 또는 https 주소만 사용할 수 있습니다.'
        );

    }

}



/*
 * =========================================================
 * CURRENT SETTING
 * =========================================================
 */

$query = "

    SELECT

        id,
        og_media_id

    FROM site_settings

    WHERE
        id = 1

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


$current_setting =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$current_og_media_id =
    $current_setting
        ? intval(
            $current_setting['og_media_id']
        )
        : 0;



/*
 * =========================================================
 * OG IMAGE
 * =========================================================
 */

$has_new_image =
    false;


$new_file_path =
    '';


$new_storage_key =
    '';


$new_original_name =
    '';


$new_mime_type =
    '';


$new_byte_size =
    0;


$new_width =
    null;


$new_height =
    null;



if(
    isset($_FILES['og_image']) &&
    isset($_FILES['og_image']['error']) &&
    $_FILES['og_image']['error'] != UPLOAD_ERR_NO_FILE
){

    /*
     * Upload Error
     */

    if(
        $_FILES['og_image']['error'] != UPLOAD_ERR_OK
    ){

        setting_save_json(
            false,
            'OG 이미지 업로드 중 오류가 발생했습니다.'
        );

    }


    $tmp_name =
        $_FILES['og_image']['tmp_name'];


    $original_name =
        $_FILES['og_image']['name'];


    $file_size =
        intval(
            $_FILES['og_image']['size']
        );


    /*
     * Size 5MB
     */

    if(
        $file_size <= 0
    ){

        setting_save_json(
            false,
            'OG 이미지 파일이 올바르지 않습니다.'
        );

    }


    if(
        $file_size >
        5 * 1024 * 1024
    ){

        setting_save_json(
            false,
            'OG 이미지는 5MB 이하만 업로드할 수 있습니다.'
        );

    }



    /*
     * Extension
     */

    $extension =
        strtolower(
            pathinfo(
                $original_name,
                PATHINFO_EXTENSION
            )
        );


    if(
        $extension == 'jpeg'
    ){
        $extension = 'jpg';
    }


    $allowed_extensions =
        array(
            'jpg',
            'png',
            'gif',
            'webp'
        );


    if(
        !in_array(
            $extension,
            $allowed_extensions,
            true
        )
    ){

        setting_save_json(
            false,
            'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.'
        );

    }



    /*
     * MIME
     */

    $mime_type =
        setting_image_mime(
            $tmp_name,
            $extension
        );


    $allowed_mimes =
        array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        );


    if(
        !in_array(
            $mime_type,
            $allowed_mimes,
            true
        )
    ){

        setting_save_json(
            false,
            '올바른 이미지 파일이 아닙니다.'
        );

    }



    /*
     * MIME / EXTENSION MATCH
     */

    $mime_extension =
        array(
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp'
        );


    if(
        !isset(
            $mime_extension[$extension]
        ) ||
        $mime_extension[$extension] !=
        $mime_type
    ){

        setting_save_json(
            false,
            '이미지 확장자와 파일 형식이 일치하지 않습니다.'
        );

    }



    /*
     * Image Size
     */

    $image_info =
        @getimagesize(
            $tmp_name
        );


    if(
        is_array(
            $image_info
        )
    ){

        $new_width =
            isset($image_info[0])
                ? intval($image_info[0])
                : null;


        $new_height =
            isset($image_info[1])
                ? intval($image_info[1])
                : null;

    }else{

        /*
         * PHP 5.4에서는 WEBP를 getimagesize가
         * 인식하지 못할 수 있으므로 WEBP만 허용
         */

        if(
            $extension != 'webp'
        ){

            setting_save_json(
                false,
                '올바른 이미지 파일이 아닙니다.'
            );

        }

    }



    /*
     * Upload Directory
     */

    $upload_sub_dir =
        '/uploads/setting/'.
        date('Ym').
        '/';


    $upload_dir =
        $_SERVER['DOCUMENT_ROOT'].
        $upload_sub_dir;


    if(
        !is_dir(
            $upload_dir
        )
    ){

        if(
            !@mkdir(
                $upload_dir,
                0755,
                true
            )
        ){

            setting_save_json(
                false,
                '이미지 저장 폴더를 생성하지 못했습니다.'
            );

        }

    }



    /*
     * Filename
     */

    $save_name =
        'og_'.
        date('YmdHis').
        '_'.
        substr(
            sha1(
                uniqid(
                    mt_rand(),
                    true
                )
            ),
            0,
            16
        ).
        '.'.
        $extension;


    $new_file_path =
        $upload_dir.
        $save_name;


    $new_storage_key =
        $upload_sub_dir.
        $save_name;



    /*
     * Move
     */

    if(
        !move_uploaded_file(
            $tmp_name,
            $new_file_path
        )
    ){

        setting_save_json(
            false,
            'OG 이미지 저장에 실패했습니다.'
        );

    }


    $has_new_image =
        true;


    $new_original_name =
        $original_name;


    $new_mime_type =
        $mime_type;


    $new_byte_size =
        $file_size;

}



/*
 * =========================================================
 * SAVE
 * =========================================================
 */

try {


    $db->beginTransaction();



    /*
     * =====================================================
     * NEW MEDIA
     * =====================================================
     */

    $new_og_media_id =
        0;


    if(
        $has_new_image
    ){

        $uploaded_by =
            isset($_SESSION['ADMIN_ID'])
                ? intval($_SESSION['ADMIN_ID'])
                : null;


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


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $new_storage_key,
                    $new_original_name,
                    $new_mime_type,
                    $new_byte_size,
                    $new_width,
                    $new_height,
                    $site_name.' OG Image',
                    $uploaded_by
                )
            );


        if(!$ps){

            throw new Exception(
                'OG 이미지 정보를 저장하지 못했습니다.'
            );

        }



        /*
         * LAST INSERT ID
         */

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


        $media_data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $new_og_media_id =
            $media_data
                ? intval(
                    $media_data['id']
                )
                : 0;


        if(
            $new_og_media_id <= 0
        ){

            throw new Exception(
                'OG 이미지 정보를 저장하지 못했습니다.'
            );

        }

    }



    /*
     * =====================================================
     * FINAL OG MEDIA
     * =====================================================
     */

    $final_og_media_id =
        $current_og_media_id;


    /*
     * 새 이미지 우선
     */

    if(
        $has_new_image
    ){

        $final_og_media_id =
            $new_og_media_id;

    }
    else if(
        $delete_og_image == 'Y'
    ){

        $final_og_media_id =
            null;

    }



    /*
     * =====================================================
     * SETTING
     * =====================================================
     */

    if($current_setting){


        /*
         * UPDATE
         */

        $query = "

            UPDATE site_settings

            SET
                site_name = ?,
                seo_title = ?,
                meta_description = ?,
                meta_keywords = ?,
                og_title = ?,
                og_description = ?,
                og_media_id = ?,
                canonical_url = ?,
                updated_at = NOW()

            WHERE
                id = 1

            LIMIT 1

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $site_name,
                    $seo_title,
                    $meta_description,
                    $meta_keywords,
                    $og_title,
                    $og_description,
                    $final_og_media_id,
                    $canonical_url
                )
            );


        if(!$ps){

            throw new Exception(
                '사이트 설정을 저장하지 못했습니다.'
            );

        }


    }else{


        /*
         * INSERT
         */

        $query = "

            INSERT INTO site_settings
            (
                id,
                site_name,
                seo_title,
                meta_description,
                meta_keywords,
                og_title,
                og_description,
                og_media_id,
                canonical_url,
                updated_at
            )
            VALUES
            (
                1,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW()
            )

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $site_name,
                    $seo_title,
                    $meta_description,
                    $meta_keywords,
                    $og_title,
                    $og_description,
                    $final_og_media_id,
                    $canonical_url
                )
            );


        if(!$ps){

            throw new Exception(
                '사이트 설정을 저장하지 못했습니다.'
            );

        }

    }



    /*
     * =====================================================
     * COMMIT
     * =====================================================
     */

    $db->commit();



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
     * 새로 올린 실제 파일 삭제
     */

    if(
        $has_new_image &&
        $new_file_path != '' &&
        file_exists(
            $new_file_path
        )
    ){

        @unlink(
            $new_file_path
        );

    }



    setting_save_json(
        false,
        $e->getMessage()
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

setting_save_json(
    true,
    '사이트 설정이 저장되었습니다.'
);
?>