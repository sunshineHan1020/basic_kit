<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORK 저장
 *
 * FILE
 * /manage/proc/work_save.php
 *
 * URL
 * /manage/proc/work_save
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

function work_save_json(
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
 * Last Insert ID
 *
 * PDO 직접 호출하지 않고 pdo_query 사용
 * =========================================================
 */

function work_last_insert_id(
    $db
){

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


    if(!$data){
        return 0;
    }


    return intval(
        $data['id']
    );

}



/*
 * =========================================================
 * DATETIME
 *
 * datetime-local
 * 2026-09-23T13:30
 *
 * ->
 *
 * 2026-09-23 13:30:00
 * =========================================================
 */

function work_save_datetime(
    $value
){

    $value =
        trim(
            $value
        );


    if($value == ''){
        return null;
    }


    $value =
        str_replace(
            'T',
            ' ',
            $value
        );


    if(
        preg_match(
            '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}$/',
            $value
        )
    ){

        $value .=
            ':00';

    }


    $time =
        strtotime(
            $value
        );


    if(!$time){
        return null;
    }


    return date(
        'Y-m-d H:i:s',
        $time
    );

}



/*
 * =========================================================
 * 파일 업로드
 *
 * storage_key
 * /uploads/works/202609/xxxx.jpg
 * =========================================================
 */

function work_upload_image(
    $db,
    $file,
    $uploaded_by,
    &$uploaded_files
){

    /*
     * =====================================================
     * 파일 없음
     * =====================================================
     */

    if(
        !isset($file['error']) ||
        intval($file['error']) == UPLOAD_ERR_NO_FILE
    ){

        return 0;

    }



    /*
     * =====================================================
     * Upload Error
     * =====================================================
     */

    $upload_error =
        intval(
            $file['error']
        );


    if(
        $upload_error != UPLOAD_ERR_OK
    ){

        if(
            $upload_error == UPLOAD_ERR_INI_SIZE
        ){

            throw new Exception(
                '서버의 업로드 허용 용량을 초과했습니다.'
            );

        }else if(
            $upload_error == UPLOAD_ERR_FORM_SIZE
        ){

            throw new Exception(
                '업로드 허용 용량을 초과했습니다.'
            );

        }else if(
            $upload_error == UPLOAD_ERR_PARTIAL
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
     * =====================================================
     * 임시 파일 확인
     * =====================================================
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
     * =====================================================
     * 실제 이미지 확인
     * =====================================================
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
     * =====================================================
     * MIME
     * =====================================================
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
     * finfo 사용 불가 시 getimagesize MIME 사용
     */

    if(
        $mime_type == '' &&
        isset($image_info['mime'])
    ){

        $mime_type =
            $image_info['mime'];

    }



    /*
     * =====================================================
     * MIME / Extension
     * =====================================================
     */

    $extension =
        '';


    if(
        $mime_type == 'image/jpeg'
    ){

        $extension =
            'jpg';

    }else if(
        $mime_type == 'image/png'
    ){

        $extension =
            'png';

    }else if(
        $mime_type == 'image/gif'
    ){

        $extension =
            'gif';

    }else if(
        $mime_type == 'image/webp'
    ){

        $extension =
            'webp';

    }else{

        throw new Exception(
            'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.'
        );

    }



    /*
     * =====================================================
     * 업로드 전 파일 무결성 정보
     * =====================================================
     */

    $temp_size =
        @filesize(
            $file['tmp_name']
        );


    $temp_hash =
        @md5_file(
            $file['tmp_name']
        );


    if(
        $temp_size === false ||
        intval($temp_size) <= 0 ||
        $temp_hash === false
    ){

        throw new Exception(
            '업로드 임시 파일을 확인하지 못했습니다.'
        );

    }



    /*
     * PHP에서 받은 size와 실제 파일 크기 비교
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
     * =====================================================
     * Upload Directory
     * =====================================================
     */

    $folder =
        date(
            'Ym'
        );


    $upload_path =
        '/uploads/works/'.
        $folder;


    $upload_dir =
        $_SERVER['DOCUMENT_ROOT'].
        $upload_path;


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
     * =====================================================
     * File Name
     * =====================================================
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
     * =====================================================
     * 실제 파일 저장
     * =====================================================
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
     * =====================================================
     * 저장 후 파일 정보 갱신
     * =====================================================
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



    /*
     * =====================================================
     * 파일 무결성 검사
     *
     * move 전 / 후
     * size + MD5 모두 비교
     * =====================================================
     */

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
     * =====================================================
     * 저장된 이미지 재검사
     * =====================================================
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
     * =====================================================
     * Rollback용 신규 파일 추적
     * =====================================================
     */

    $uploaded_files[] =
        $save_file;



    /*
     * =====================================================
     * Original Name
     * =====================================================
     */

    $original_name =
        isset($file['name'])
            ? basename($file['name'])
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
     * =====================================================
     * media INSERT
     * =====================================================
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
            intval($saved_size),
            $width,
            $height,
            intval($uploaded_by)
        )
    );



    /*
     * =====================================================
     * Media ID
     * =====================================================
     */

    $media_id =
        work_last_insert_id(
            $db
        );


    if($media_id <= 0){

        throw new Exception(
            '이미지 정보를 저장하지 못했습니다.'
        );

    }


    return $media_id;

}



/*
 * =========================================================
 * Section 업로드 파일
 *
 * sections[index][media][slot]
 * =========================================================
 */

function work_get_section_file(
    $index,
    $slot
){

    if(
        !isset($_FILES['sections']) ||
        !isset($_FILES['sections']['name']) ||
        !isset(
            $_FILES['sections']['name'][$index]
        ) ||
        !isset(
            $_FILES['sections']['name'][$index]['media']
        ) ||
        !isset(
            $_FILES['sections']['name'][$index]['media'][$slot]
        )
    ){

        return null;

    }


    return array(

        'name' =>
            $_FILES['sections']['name'][$index]['media'][$slot],

        'type' =>
            isset(
                $_FILES['sections']['type'][$index]['media'][$slot]
            )
                ? $_FILES['sections']['type'][$index]['media'][$slot]
                : '',

        'tmp_name' =>
            isset(
                $_FILES['sections']['tmp_name'][$index]['media'][$slot]
            )
                ? $_FILES['sections']['tmp_name'][$index]['media'][$slot]
                : '',

        'error' =>
            isset(
                $_FILES['sections']['error'][$index]['media'][$slot]
            )
                ? $_FILES['sections']['error'][$index]['media'][$slot]
                : UPLOAD_ERR_NO_FILE,

        'size' =>
            isset(
                $_FILES['sections']['size'][$index]['media'][$slot]
            )
                ? $_FILES['sections']['size'][$index]['media'][$slot]
                : 0

    );

}



/*
 * =========================================================
 * Section Type
 * =========================================================
 */

$WORK_SECTION_MEDIA_SLOTS =
    array(

        'PROJECT_OVERVIEW' =>
            array(
                'logo'
            ),

        'IMG_FULL_01' =>
            array(
                'image'
            ),

        'IMG_FULL_02' =>
            array(
                'image_01',
                'image_02'
            ),

        'TEXT_01' =>
            array(),

        'IMG_MAX_01' =>
            array(
                'image'
            ),

        'IMG_MAX_02' =>
            array(
                'image_01',
                'image_02'
            ),

        'IMG_MAX_CUSTOM' =>
            array(
                'image'
            ),

        'MIXED_02_01' =>
            array(
                'image'
            ),

        'TEXT_03' =>
            array()

    );



/*
 * =========================================================
 * POST
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    work_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$work_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$category_id =
    isset($_POST['category_id'])
        ? intval($_POST['category_id'])
        : 0;


$list_title =
    isset($_POST['list_title'])
        ? trim($_POST['list_title'])
        : '';


$title =
    isset($_POST['title'])
        ? trim($_POST['title'])
        : '';


$slug =
    isset($_POST['slug'])
        ? strtolower(
            trim(
                $_POST['slug']
            )
        )
        : '';


$summary =
    isset($_POST['summary'])
        ? trim($_POST['summary'])
        : '';


$sort_order =
    isset($_POST['sort_order'])
        ? intval($_POST['sort_order'])
        : 0;


$status =
    isset($_POST['status'])
        ? trim($_POST['status'])
        : 'draft';


$visibility =
    isset($_POST['visibility'])
        ? trim($_POST['visibility'])
        : 'private';


$display_at =
    isset($_POST['display_at'])
        ? work_save_datetime(
            $_POST['display_at']
        )
        : null;


$publish_at =
    isset($_POST['publish_at'])
        ? work_save_datetime(
            $_POST['publish_at']
        )
        : null;


$author_id =
    isset($_SESSION['ADMIN_ID'])
        ? intval($_SESSION['ADMIN_ID'])
        : 0;


$author_display_name =
    isset($_POST['author_display_name'])
        ? trim($_POST['author_display_name'])
        : '';


if(
    $author_display_name == '' &&
    isset($_SESSION['ADMIN_NAME'])
){

    $author_display_name =
        $_SESSION['ADMIN_NAME'];

}



/*
 * =========================================================
 * Validation
 * =========================================================
 */

if($list_title == ''){

    work_save_json(
        false,
        '리스트 제목을 입력해주세요.',
        $work_id
    );

}


if($title == ''){

    work_save_json(
        false,
        '상세 제목을 입력해주세요.',
        $work_id
    );

}


if($category_id <= 0){

    work_save_json(
        false,
        '카테고리를 선택해주세요.',
        $work_id
    );

}


if($slug == ''){

    work_save_json(
        false,
        'URL Slug를 입력해주세요.',
        $work_id
    );

}


if(
    !preg_match(
        '/^[a-z0-9\-]+$/',
        $slug
    )
){

    work_save_json(
        false,
        'URL Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
        $work_id
    );

}


if(strlen($slug) > 180){

    work_save_json(
        false,
        'URL Slug는 180자 이하로 입력해주세요.',
        $work_id
    );

}


if($sort_order < 0){

    $sort_order =
        0;

}


if(
    $status != 'draft' &&
    $status != 'published' &&
    $status != 'archived'
){

    $status =
        'draft';

}


if(
    $visibility != 'public' &&
    $visibility != 'private'
){

    $visibility =
        'private';

}



/*
 * =========================================================
 * 카테고리 확인
 * =========================================================
 */

$query = "

    SELECT
        id

    FROM work_categories

    WHERE
        id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $category_id
        )
    );


$category =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$category){

    work_save_json(
        false,
        '존재하지 않는 카테고리입니다.',
        $work_id
    );

}



/*
 * =========================================================
 * 수정 WORK 확인
 * =========================================================
 */

if($work_id > 0){

    $query = "

        SELECT
            id

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


    $work_check =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$work_check){

        work_save_json(
            false,
            '존재하지 않는 WORK입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * Slug 중복
 *
 * deleted_at과 무관하게 UNIQUE이므로
 * 삭제된 WORK까지 포함해서 체크
 * =========================================================
 */

if($work_id > 0){

    $query = "

        SELECT
            id

        FROM works

        WHERE
            slug = ?
            AND id != ?

        LIMIT 1

    ";


    $params =
        array(
            $slug,
            $work_id
        );

}else{

    $query = "

        SELECT
            id

        FROM works

        WHERE
            slug = ?

        LIMIT 1

    ";


    $params =
        array(
            $slug
        );

}


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


$slug_check =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if($slug_check){

    work_save_json(
        false,
        '이미 사용 중인 URL Slug입니다.',
        $work_id
    );

}



/*
 * =========================================================
 * SEO
 * =========================================================
 */

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


$canonical_url =
    isset($_POST['canonical_url'])
        ? trim($_POST['canonical_url'])
        : '';


$og_title =
    isset($_POST['og_title'])
        ? trim($_POST['og_title'])
        : '';


$og_description =
    isset($_POST['og_description'])
        ? trim($_POST['og_description'])
        : '';


$allow_index =
    isset($_POST['allow_index'])
        ? intval($_POST['allow_index'])
        : 0;


$nofollow =
    isset($_POST['nofollow'])
        ? intval($_POST['nofollow'])
        : 0;


$allow_index =
    $allow_index == 1
        ? 1
        : 0;


$nofollow =
    $nofollow == 1
        ? 1
        : 0;



/*
 * =========================================================
 * 기존 OG
 * =========================================================
 */

$current_og_media_id =
    0;


if($work_id > 0){

    $query = "

        SELECT
            og_media_id

        FROM work_seo

        WHERE
            work_id = ?

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


    $seo_current =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($seo_current){

        $current_og_media_id =
            intval(
                $seo_current['og_media_id']
            );

    }

}



/*
 * =========================================================
 * 데이터
 * =========================================================
 */

$tag_ids =
    isset($_POST['tag_ids']) &&
    is_array($_POST['tag_ids'])
        ? $_POST['tag_ids']
        : array();


$sections =
    isset($_POST['sections']) &&
    is_array($_POST['sections'])
        ? $_POST['sections']
        : array();


$deleted_section_ids =
    isset($_POST['deleted_section_ids']) &&
    is_array($_POST['deleted_section_ids'])
        ? $_POST['deleted_section_ids']
        : array();



/*
 * =========================================================
 * 업로드 파일 추적
 *
 * DB rollback 시 신규 파일 삭제
 * =========================================================
 */

$uploaded_files =
    array();



/*
 * =========================================================
 * TRANSACTION
 * =========================================================
 */

try {


      $db->beginTransaction();


    /*
     * =====================================================
     * WORK
     * =====================================================
     */

    if($work_id > 0){

        $query = "

            UPDATE works

            SET
                category_id = ?,
                list_title = ?,
                sort_order = ?,
                title = ?,
                slug = ?,
                summary = ?,
                status = ?,
                visibility = ?,
                display_at = ?,
                publish_at = ?,
                author_id = ?,
                author_display_name = ?,
                updated_at = NOW()

            WHERE
                id = ?
                AND deleted_at IS NULL

            LIMIT 1

        ";


        pdo_query(
            $db,
            $query,
            array(
                $category_id,
                $list_title,
                $sort_order,
                $title,
                $slug,
                $summary,
                $status,
                $visibility,
                $display_at,
                $publish_at,
                $author_id,
                $author_display_name,
                $work_id
            )
        );


        $is_new =
            false;

    }else{

        $query = "

            INSERT INTO works
            (
                category_id,
                list_title,
                sort_order,
                title,
                slug,
                summary,
                status,
                visibility,
                display_at,
                publish_at,
                author_id,
                author_display_name,
                view_count,
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
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                0,
                NOW(),
                NOW(),
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $category_id,
                $list_title,
                $sort_order,
                $title,
                $slug,
                $summary,
                $status,
                $visibility,
                $display_at,
                $publish_at,
                $author_id,
                $author_display_name
            )
        );


        $work_id =
            work_last_insert_id(
                $db
            );


        if($work_id <= 0){

            throw new Exception(
                'WORK 등록에 실패했습니다.'
            );

        }


        $is_new =
            true;

    }



    /*
     * =====================================================
     * THUMBNAIL
     * =====================================================
     */

    $thumbnail_remove =
        isset($_POST['thumbnail_remove'])
            ? intval($_POST['thumbnail_remove'])
            : 0;


    $thumbnail_file =
        isset($_FILES['thumbnail'])
            ? $_FILES['thumbnail']
            : null;


    $new_thumbnail_media_id =
        0;


    if(
        $thumbnail_file &&
        isset($thumbnail_file['error']) &&
        intval($thumbnail_file['error']) != UPLOAD_ERR_NO_FILE
    ){

        $new_thumbnail_media_id =
            work_upload_image(
                $db,
                $thumbnail_file,
                $author_id,
                $uploaded_files
            );


        /*
         * 기존 연결 삭제
         */

        pdo_query(
            $db,
            "
                DELETE FROM work_media

                WHERE
                    work_id = ?
                    AND role = 'thumbnail'
            ",
            array(
                $work_id
            )
        );


        /*
         * 신규 연결
         */

        pdo_query(
            $db,
            "
                INSERT INTO work_media
                (
                    work_id,
                    media_id,
                    role,
                    slot_key,
                    sort_order,
                    alt_text,
                    caption
                )
                VALUES
                (
                    ?,
                    ?,
                    'thumbnail',
                    'thumbnail',
                    0,
                    NULL,
                    NULL
                )
            ",
            array(
                $work_id,
                $new_thumbnail_media_id
            )
        );

    }else if($thumbnail_remove == 1){

        pdo_query(
            $db,
            "
                DELETE FROM work_media

                WHERE
                    work_id = ?
                    AND role = 'thumbnail'
            ",
            array(
                $work_id
            )
        );

    }



    /*
     * =====================================================
     * TAG
     * =====================================================
     */

    pdo_query(
        $db,
        "
            DELETE FROM work_tag_map

            WHERE
                work_id = ?
        ",
        array(
            $work_id
        )
    );


    $tag_sort =
        0;


    for(
        $i = 0;
        $i < count($tag_ids);
        $i++
    ){

        $tag_id =
            intval(
                $tag_ids[$i]
            );


        if($tag_id <= 0){
            continue;
        }


        /*
         * 태그 존재 확인
         */

        $ps =
            pdo_query(
                $db,
                "
                    SELECT
                        id

                    FROM work_tags

                    WHERE
                        id = ?

                    LIMIT 1
                ",
                array(
                    $tag_id
                )
            );


        $tag =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        if(!$tag){
            continue;
        }


        pdo_query(
            $db,
            "
                INSERT INTO work_tag_map
                (
                    work_id,
                    tag_id,
                    sort_order
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
            ",
            array(
                $work_id,
                $tag_id,
                $tag_sort
            )
        );


        $tag_sort++;

    }



    /*
     * =====================================================
     * OG IMAGE
     * =====================================================
     */

    $og_remove =
        isset($_POST['og_remove'])
            ? intval($_POST['og_remove'])
            : 0;


    $og_media_id =
        $current_og_media_id;


    $og_file =
        isset($_FILES['og_image'])
            ? $_FILES['og_image']
            : null;


    if(
        $og_file &&
        isset($og_file['error']) &&
        intval($og_file['error']) != UPLOAD_ERR_NO_FILE
    ){

        $og_media_id =
            work_upload_image(
                $db,
                $og_file,
                $author_id,
                $uploaded_files
            );

    }else if($og_remove == 1){

        $og_media_id =
            0;

    }



    /*
     * =====================================================
     * SEO UPSERT
     * =====================================================
     */

    $query = "

        INSERT INTO work_seo
        (
            work_id,
            seo_title,
            meta_description,
            meta_keywords,
            canonical_url,
            og_title,
            og_description,
            og_media_id,
            allow_index,
            nofollow,
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
            ?,
            ?,
            NOW()
        )

        ON DUPLICATE KEY UPDATE

            seo_title =
                VALUES(seo_title),

            meta_description =
                VALUES(meta_description),

            meta_keywords =
                VALUES(meta_keywords),

            canonical_url =
                VALUES(canonical_url),

            og_title =
                VALUES(og_title),

            og_description =
                VALUES(og_description),

            og_media_id =
                VALUES(og_media_id),

            allow_index =
                VALUES(allow_index),

            nofollow =
                VALUES(nofollow),

            updated_at =
                NOW()

    ";


    pdo_query(
        $db,
        $query,
        array(
            $work_id,
            $seo_title,
            $meta_description,
            $meta_keywords,
            $canonical_url,
            $og_title,
            $og_description,
            $og_media_id > 0
                ? $og_media_id
                : null,
            $allow_index,
            $nofollow
        )
    );



    /*
     * =====================================================
     * 삭제된 SECTION
     * =====================================================
     */

    for(
        $i = 0;
        $i < count($deleted_section_ids);
        $i++
    ){

        $delete_section_id =
            intval(
                $deleted_section_ids[$i]
            );


        if($delete_section_id <= 0){
            continue;
        }


        pdo_query(
            $db,
            "
                DELETE FROM work_sections

                WHERE
                    id = ?
                    AND work_id = ?

                LIMIT 1
            ",
            array(
                $delete_section_id,
                $work_id
            )
        );

    }



    /*
     * =====================================================
     * 기존 Section ID
     * =====================================================
     */

    $existing_sections =
        array();


    $ps =
        pdo_query(
            $db,
            "
                SELECT
                    id

                FROM work_sections

                WHERE
                    work_id = ?
            ",
            array(
                $work_id
            )
        );


    while(
        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $existing_sections[
            intval($data['id'])
        ] =
            true;

    }



    /*
     * =====================================================
     * SECTIONS
     * =====================================================
     */

    foreach(
        $sections as
        $section_index =>
        $section_data
    ){

        /*
         * Section Type
         */

        $section_type =
            isset($section_data['section_type'])
                ? trim($section_data['section_type'])
                : '';


        if(
            !isset(
                $WORK_SECTION_MEDIA_SLOTS[
                    $section_type
                ]
            )
        ){

            throw new Exception(
                '잘못된 상세 섹션 타입이 포함되어 있습니다.'
            );

        }



        /*
         * ID
         */

        $section_id =
            isset($section_data['id'])
                ? intval($section_data['id'])
                : 0;



        /*
         * 순서
         */

        $section_sort_order =
            isset($section_data['sort_order'])
                ? intval($section_data['sort_order'])
                : 0;


        if($section_sort_order < 0){

            $section_sort_order =
                0;

        }



        /*
         * 사용 여부
         */

        $section_is_enabled =
            isset($section_data['is_enabled']) &&
            intval($section_data['is_enabled']) == 1
                ? 1
                : 0;



        /*
         * Title
         */

        $section_title =
            isset($section_data['title'])
                ? trim($section_data['title'])
                : '';



        /*
         * Body
         */

        $section_body_html =
            isset($section_data['body_html'])
                ? $section_data['body_html']
                : '';



        /*
         * -----------------------------------------------
         * 기존 Section
         * -----------------------------------------------
         */

        if($section_id > 0){

            if(
                !isset(
                    $existing_sections[
                        $section_id
                    ]
                )
            ){

                throw new Exception(
                    '잘못된 상세 섹션 정보입니다.'
                );

            }


            /*
             * eyebrow / subtitle / admin_memo는
             * 현재 works_write 입력폼에 없으므로
             * 기존값 유지
             */

            pdo_query(
                $db,
                "
                    UPDATE work_sections

                    SET
                        section_type = ?,
                        sort_order = ?,
                        is_enabled = ?,
                        title = ?,
                        body_html = ?,
                        updated_at = NOW()

                    WHERE
                        id = ?
                        AND work_id = ?

                    LIMIT 1
                ",
                array(
                    $section_type,
                    $section_sort_order,
                    $section_is_enabled,
                    $section_title,
                    $section_body_html,
                    $section_id,
                    $work_id
                )
            );

        }else{

            /*
             * -------------------------------------------
             * 신규 Section
             * -------------------------------------------
             */

            pdo_query(
                $db,
                "
                    INSERT INTO work_sections
                    (
                        work_id,
                        section_type,
                        sort_order,
                        is_enabled,
                        eyebrow,
                        title,
                        subtitle,
                        body_html,
                        admin_memo,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        NULL,
                        ?,
                        NULL,
                        ?,
                        NULL,
                        NOW(),
                        NOW()
                    )
                ",
                array(
                    $work_id,
                    $section_type,
                    $section_sort_order,
                    $section_is_enabled,
                    $section_title,
                    $section_body_html
                )
            );


            $section_id =
                work_last_insert_id(
                    $db
                );


            if($section_id <= 0){

                throw new Exception(
                    '상세 섹션을 등록하지 못했습니다.'
                );

            }

        }



        /*
         * =================================================
         * SECTION ITEMS
         *
         * 현재 UI:
         * summary_items
         * detail_items
         *
         * 통째로 재구성
         * =================================================
         */

        pdo_query(
            $db,
            "
                DELETE FROM work_section_items

                WHERE
                    section_id = ?
            ",
            array(
                $section_id
            )
        );


        $items =
            isset($section_data['items']) &&
            is_array($section_data['items'])
                ? $section_data['items']
                : array();


        foreach(
            $items as
            $group_key =>
            $group_items
        ){

            /*
             * 현재 허용되는 반복 그룹
             */

            if(
                $group_key != 'summary_items' &&
                $group_key != 'detail_items'
            ){

                continue;

            }


            if(
                !is_array(
                    $group_items
                )
            ){

                continue;

            }


            $item_sort =
                0;


            foreach(
                $group_items as
                $item_data
            ){

                $label =
                    isset($item_data['label'])
                        ? trim($item_data['label'])
                        : '';


                $value_text =
                    isset($item_data['value_text'])
                        ? trim($item_data['value_text'])
                        : '';


                /*
                 * 완전히 빈 행은 저장하지 않음
                 */

                if(
                    $label == '' &&
                    $value_text == ''
                ){

                    continue;

                }


                pdo_query(
                    $db,
                    "
                        INSERT INTO work_section_items
                        (
                            section_id,
                            group_key,
                            item_key,
                            label,
                            value_text,
                            value_html,
                            sort_order
                        )
                        VALUES
                        (
                            ?,
                            ?,
                            NULL,
                            ?,
                            ?,
                            NULL,
                            ?
                        )
                    ",
                    array(
                        $section_id,
                        $group_key,
                        $label,
                        $value_text,
                        $item_sort
                    )
                );


                $item_sort++;

            }

        }



        /*
         * =================================================
         * SECTION MEDIA
         * =================================================
         */

        $media_slots =
            $WORK_SECTION_MEDIA_SLOTS[
                $section_type
            ];


        for(
            $slot_index = 0;
            $slot_index < count($media_slots);
            $slot_index++
        ){

            $slot_key =
                $media_slots[
                    $slot_index
                ];


            /*
             * 삭제 여부
             */

            $remove_media =
                isset(
                    $section_data['remove_media']
                ) &&
                isset(
                    $section_data['remove_media'][$slot_key]
                )
                    ? intval(
                        $section_data['remove_media'][$slot_key]
                    )
                    : 0;



            /*
             * 신규 파일
             */

            $section_file =
                work_get_section_file(
                    $section_index,
                    $slot_key
                );


            $has_new_file =
                $section_file &&
                isset($section_file['error']) &&
                intval($section_file['error']) != UPLOAD_ERR_NO_FILE;



            /*
             * 신규 업로드가 있으면 신규 파일 우선
             */

            if($has_new_file){

                $media_id =
                    work_upload_image(
                        $db,
                        $section_file,
                        $author_id,
                        $uploaded_files
                    );


                /*
                 * 기존 슬롯 연결 삭제
                 */

                pdo_query(
                    $db,
                    "
                        DELETE FROM work_section_media

                        WHERE
                            section_id = ?
                            AND slot_key = ?
                    ",
                    array(
                        $section_id,
                        $slot_key
                    )
                );


                /*
                 * 신규 연결
                 */

                pdo_query(
                    $db,
                    "
                        INSERT INTO work_section_media
                        (
                            section_id,
                            media_id,
                            slot_key,
                            sort_order,
                            alt_text,
                            caption
                        )
                        VALUES
                        (
                            ?,
                            ?,
                            ?,
                            ?,
                            NULL,
                            NULL
                        )
                    ",
                    array(
                        $section_id,
                        $media_id,
                        $slot_key,
                        $slot_index
                    )
                );

            }else if($remove_media == 1){

                /*
                 * 파일 삭제 체크
                 */

                pdo_query(
                    $db,
                    "
                        DELETE FROM work_section_media

                        WHERE
                            section_id = ?
                            AND slot_key = ?
                    ",
                    array(
                        $section_id,
                        $slot_key
                    )
                );

            }

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
     * RESULT
     * =====================================================
     */

    if($is_new){

        work_save_json(
            true,
            'WORK가 등록되었습니다.',
            $work_id
        );

    }else{

        work_save_json(
            true,
            'WORK가 수정되었습니다.',
            $work_id
        );

    }


}catch(Exception $e){


    /*
     * =====================================================
     * ROLLBACK
     * =====================================================
     */

    try {

       $db->rollBack();

    }catch(Exception $rollback_e){

    }



    /*
     * DB rollback 되어도
     * 이미 저장된 신규 파일은 남기 때문에 제거
     */

    for(
        $i = 0;
        $i < count($uploaded_files);
        $i++
    ){

        if(
            is_file(
                $uploaded_files[$i]
            )
        ){

            @unlink(
                $uploaded_files[$i]
            );

        }

    }


    work_save_json(
        false,
        $e->getMessage(),
        $work_id
    );

}
?>