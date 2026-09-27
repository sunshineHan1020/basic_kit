<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 등록 / 수정
 *
 * FILE
 * /manage/proc/blog_save.php
 *
 * URL
 * /manage/proc/blog_save
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

function blog_save_json(
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
 * =========================================================
 */

function blog_last_insert_id($db)
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

function blog_strlen($str)
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
 * DATETIME
 *
 * datetime-local
 *
 * 2026-09-23T10:30
 *
 * ->
 *
 * 2026-09-23 10:30:00
 * =========================================================
 */

function blog_save_datetime(
    $value
){
    $value =
        trim(
            $value
        );


    if(
        $value == ''
    ){

        return null;

    }


    $value =
        str_replace(
            'T',
            ' ',
            $value
        );


    $time =
        strtotime(
            $value
        );


    if(!$time){

        throw new Exception(
            '날짜 형식을 확인해주세요.'
        );

    }


    return date(
        'Y-m-d H:i:s',
        $time
    );
}



/*
 * =========================================================
 * 이미지 업로드
 *
 * 대표 이미지 / OG 이미지
 * =========================================================
 */

function blog_upload_image(
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
     * 임시파일
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
     * 실제 크기
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
     * $_FILES size 비교
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
     * Image 확인
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
     */

    $folder =
        date(
            'Ym'
        );


    $upload_path =
        '/uploads/blog/'.
        $folder;


    $upload_dir =
        $_SERVER['DOCUMENT_ROOT'].
        $upload_path;



    /*
     * 디렉토리
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
     * 저장된 이미지 재확인
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
     * Rollback 시 삭제
     */

    $uploaded_files[] =
        $save_file;



    /*
     * 원본명
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
     * media 등록
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
        blog_last_insert_id(
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
 * 본문 IMG storage_key 추출
 *
 * TinyMCE:
 *
 * <img src="/uploads/blog/editor/202609/xxx.jpg">
 *
 * 상대 URL / 절대 URL 모두 처리
 * =========================================================
 */

function blog_extract_body_images(
    $body_html
){

    $storage_keys =
        array();


    if(
        trim($body_html) == ''
    ){

        return
            $storage_keys;

    }



    /*
     * src="..."
     * src='...'
     */

    $matches =
        array();


    preg_match_all(
        '/<img[^>]+src\s*=\s*([\'"])(.*?)\1/i',
        $body_html,
        $matches
    );


    if(
        !isset($matches[2]) ||
        !is_array($matches[2])
    ){

        return
            $storage_keys;

    }



    foreach(
        $matches[2] as
        $src
    ){

        $src =
            trim(
                html_entity_decode(
                    $src,
                    ENT_QUOTES,
                    'UTF-8'
                )
            );


        if(
            $src == ''
        ){

            continue;

        }



        /*
         * data:image 제외
         */

        if(
            strpos(
                $src,
                'data:'
            ) === 0
        ){

            continue;

        }



        /*
         * 절대 URL인 경우 path만 추출
         */

        if(
            strpos(
                $src,
                'http://'
            ) === 0 ||
            strpos(
                $src,
                'https://'
            ) === 0
        ){

            $path =
                parse_url(
                    $src,
                    PHP_URL_PATH
                );


            if(
                $path === false ||
                $path == ''
            ){

                continue;

            }


            $src =
                $path;

        }else{

            /*
             * 쿼리스트링 제거
             */

            $question_pos =
                strpos(
                    $src,
                    '?'
                );


            if(
                $question_pos !== false
            ){

                $src =
                    substr(
                        $src,
                        0,
                        $question_pos
                    );

            }


            /*
             * # 제거
             */

            $hash_pos =
                strpos(
                    $src,
                    '#'
                );


            if(
                $hash_pos !== false
            ){

                $src =
                    substr(
                        $src,
                        0,
                        $hash_pos
                    );

            }

        }



        /*
         * 우리 CMS 본문 업로드 이미지만
         */

        if(
            strpos(
                $src,
                '/uploads/blog/editor/'
            ) !== 0
        ){

            continue;

        }



        /*
         * 중복 이미지 제거
         */

        if(
            !in_array(
                $src,
                $storage_keys
            )
        ){

            $storage_keys[] =
                $src;

        }

    }


    return
        $storage_keys;
}



/*
 * =========================================================
 * 본문 post_media 동기화
 * =========================================================
 */

function blog_sync_body_media(
    $db,
    $post_id,
    $body_html
){

    /*
     * 기존 body 연결 제거
     */

    pdo_query(
        $db,
        "

            DELETE FROM post_media

            WHERE
                post_id = ?
                AND role = 'body'

        ",
        array(
            $post_id
        )
    );



    /*
     * 현재 본문 이미지
     */

    $storage_keys =
        blog_extract_body_images(
            $body_html
        );


    if(
        count(
            $storage_keys
        ) <= 0
    ){

        return;

    }



    /*
     * 본문 순서대로 연결
     */

    $sort_order =
        0;


    foreach(
        $storage_keys as
        $storage_key
    ){

        /*
         * media 조회
         */

        $query = "

            SELECT
                id

            FROM media

            WHERE
                storage_key = ?
                AND deleted_at IS NULL

            LIMIT 1

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $storage_key
                )
            );


        $media =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        /*
         * media 테이블에 없는 이미지는
         * 본문 출력은 유지하되 연결만 생략
         */

        if(!$media){

            continue;

        }


        $media_id =
            intval(
                $media['id']
            );


        if(
            $media_id <= 0
        ){

            continue;

        }



        /*
         * slot_key
         *
         * body_1
         * body_2
         * ...
         */

        $slot_key =
            'body_'.
            ($sort_order + 1);



        /*
         * post_media 등록
         */

        $query = "

            INSERT INTO post_media
            (
                post_id,
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
                'body',
                ?,
                ?,
                NULL,
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $post_id,
                $media_id,
                $slot_key,
                $sort_order
            )
        );


        $sort_order++;

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

    blog_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * BLOG Board
 * =========================================================
 */

$BLOG_BOARD_CODE =
    'BLOG';


$query = "

    SELECT
        id

    FROM boards

    WHERE
        code = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $BLOG_BOARD_CODE
        )
    );


$board =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$board){

    blog_save_json(
        false,
        'NEWS 게시판 정보가 등록되어 있지 않습니다.',
        0
    );

}


$board_id =
    intval(
        $board['id']
    );



/*
 * =========================================================
 * 관리자
 * =========================================================
 */

$admin_id =
    isset($_SESSION['ADMIN_ID'])
        ? intval($_SESSION['ADMIN_ID'])
        : 0;


$default_author_name =
    isset($_SESSION['ADMIN_NAME']) &&
    trim($_SESSION['ADMIN_NAME']) != ''
        ? trim($_SESSION['ADMIN_NAME'])
        : '관리자';



/*
 * =========================================================
 * POST
 * =========================================================
 */

$post_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


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


$category_id =
    isset($_POST['category_id'])
        ? intval($_POST['category_id'])
        : 0;


$summary =
    isset($_POST['summary'])
        ? trim($_POST['summary'])
        : '';


$body_html =
    isset($_POST['body_html'])
        ? trim($_POST['body_html'])
        : '';


$status =
    isset($_POST['status'])
        ? trim($_POST['status'])
        : 'draft';


$visibility =
    isset($_POST['visibility'])
        ? trim($_POST['visibility'])
        : 'private';


$is_pinned =
    isset($_POST['is_pinned'])
        ? intval($_POST['is_pinned'])
        : 0;


$display_at_raw =
    isset($_POST['display_at'])
        ? trim($_POST['display_at'])
        : '';


$publish_at_raw =
    isset($_POST['publish_at'])
        ? trim($_POST['publish_at'])
        : '';


$author_display_name =
    isset($_POST['author_display_name'])
        ? trim($_POST['author_display_name'])
        : '';


$thumbnail_remove =
    isset($_POST['thumbnail_remove'])
        ? intval($_POST['thumbnail_remove'])
        : 0;


$og_image_remove =
    isset($_POST['og_image_remove'])
        ? intval($_POST['og_image_remove'])
        : 0;



/*
 * =========================================================
 * TAG
 * =========================================================
 */

$tag_ids =
    array();


if(
    isset($_POST['tag_ids']) &&
    is_array($_POST['tag_ids'])
){

    foreach(
        $_POST['tag_ids'] as
        $tag_id
    ){

        $tag_id =
            intval(
                $tag_id
            );


        if(
            $tag_id > 0 &&
            !in_array(
                $tag_id,
                $tag_ids
            )
        ){

            $tag_ids[] =
                $tag_id;

        }

    }

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
        ? 1
        : 0;


$nofollow =
    isset($_POST['nofollow'])
        ? 1
        : 0;



/*
 * =========================================================
 * VALIDATION
 * =========================================================
 */

if(
    $title == ''
){

    blog_save_json(
        false,
        '제목을 입력해주세요.',
        $post_id
    );

}


if(
    blog_strlen(
        $title
    ) > 500
){

    blog_save_json(
        false,
        '제목은 500자 이하로 입력해주세요.',
        $post_id
    );

}



/*
 * Slug
 */

if(
    $slug == ''
){

    blog_save_json(
        false,
        'URL Slug를 입력해주세요.',
        $post_id
    );

}


if(
    !preg_match(
        '/^[a-z0-9\-]+$/',
        $slug
    )
){

    blog_save_json(
        false,
        'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
        $post_id
    );

}


if(
    strlen(
        $slug
    ) > 180
){

    blog_save_json(
        false,
        'Slug는 180자 이하로 입력해주세요.',
        $post_id
    );

}



/*
 * Category
 */

if(
    $category_id <= 0
){

    blog_save_json(
        false,
        '카테고리를 선택해주세요.',
        $post_id
    );

}



/*
 * Summary
 */

if(
    blog_strlen(
        $summary
    ) > 200
){

    blog_save_json(
        false,
        '요약 설명은 200자 이하로 입력해주세요.',
        $post_id
    );

}



/*
 * Body
 */

if(
    $body_html == ''
){

    blog_save_json(
        false,
        '본문을 입력해주세요.',
        $post_id
    );

}



/*
 * Status
 */

if(
    $status != 'draft' &&
    $status != 'published' &&
    $status != 'archived'
){

    blog_save_json(
        false,
        '게시 상태를 확인해주세요.',
        $post_id
    );

}



/*
 * Visibility
 */

if(
    $visibility != 'public' &&
    $visibility != 'private'
){

    blog_save_json(
        false,
        '공개 여부를 확인해주세요.',
        $post_id
    );

}



/*
 * Pin
 */

if(
    $is_pinned != 0 &&
    $is_pinned != 1
){

    $is_pinned =
        0;

}



/*
 * Author
 */

if(
    $author_display_name == ''
){

    $author_display_name =
        $default_author_name;

}


if(
    blog_strlen(
        $author_display_name
    ) > 100
){

    blog_save_json(
        false,
        '작성자명은 100자 이하로 입력해주세요.',
        $post_id
    );

}



/*
 * SEO
 */

if(
    blog_strlen(
        $seo_title
    ) > 255
){

    blog_save_json(
        false,
        'SEO Title은 255자 이하로 입력해주세요.',
        $post_id
    );

}


if(
    strlen(
        $canonical_url
    ) > 2048
){

    blog_save_json(
        false,
        'Canonical URL이 너무 깁니다.',
        $post_id
    );

}


if(
    blog_strlen(
        $og_title
    ) > 255
){

    blog_save_json(
        false,
        'OG Title은 255자 이하로 입력해주세요.',
        $post_id
    );

}



/*
 * =========================================================
 * DATE
 * =========================================================
 */

try {

    $display_at =
        blog_save_datetime(
            $display_at_raw
        );


    $publish_at =
        blog_save_datetime(
            $publish_at_raw
        );


} catch(Exception $e) {

    blog_save_json(
        false,
        $e->getMessage(),
        $post_id
    );

}



/*
 * 작성일이 없으면 현재 시간
 */

if(
    $display_at === null
){

    $display_at =
        date(
            'Y-m-d H:i:s'
        );

}



/*
 * =========================================================
 * 수정 대상 확인
 * =========================================================
 */

$existing_post =
    false;


if(
    $post_id > 0
){

    $query = "

        SELECT
            id,
            board_id,
            author_id

        FROM posts

        WHERE
            id = ?
            AND board_id = ?
            AND deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $post_id,
                $board_id
            )
        );


    $existing_post =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$existing_post){

        blog_save_json(
            false,
            '존재하지 않는 NEWS입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * Category 확인
 * =========================================================
 */

$query = "

    SELECT
        id

    FROM post_categories

    WHERE
        id = ?
        AND board_id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $category_id,
            $board_id
        )
    );


$category =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$category){

    blog_save_json(
        false,
        '존재하지 않는 NEWS 카테고리입니다.',
        $post_id
    );

}



/*
 * =========================================================
 * Slug 중복
 *
 * soft delete 데이터도 UNIQUE KEY에는 걸리므로
 * deleted_at 조건을 넣지 않는다.
 * =========================================================
 */

if(
    $post_id > 0
){

    $query = "

        SELECT
            id

        FROM posts

        WHERE
            board_id = ?
            AND slug = ?
            AND id != ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $board_id,
                $slug,
                $post_id
            )
        );

}else{

    $query = "

        SELECT
            id

        FROM posts

        WHERE
            board_id = ?
            AND slug = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $board_id,
                $slug
            )
        );

}


$duplicate =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if($duplicate){

    blog_save_json(
        false,
        '이미 사용 중인 URL Slug입니다.',
        $post_id
    );

}



/*
 * =========================================================
 * Tag 확인
 * =========================================================
 */

foreach(
    $tag_ids as
    $tag_id
){

    $query = "

        SELECT
            id

        FROM post_tags

        WHERE
            id = ?
            AND board_id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $tag_id,
                $board_id
            )
        );


    $tag =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$tag){

        blog_save_json(
            false,
            '잘못된 NEWS 태그가 포함되어 있습니다.',
            $post_id
        );

    }

}



/*
 * =========================================================
 * 신규 업로드 실제 파일
 *
 * transaction rollback 발생 시 삭제
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
     * POSTS
     * =====================================================
     */

    if(
        $post_id > 0
    ){

        /*
         * 수정
         *
         * 기존 author_id는 유지
         */

        $query = "

            UPDATE posts

            SET
                category_id = ?,
                is_pinned = ?,
                title = ?,
                slug = ?,
                summary = ?,
                body_html = ?,
                status = ?,
                visibility = ?,
                display_at = ?,
                publish_at = ?,
                author_display_name = ?,
                updated_at = NOW()

            WHERE
                id = ?
                AND board_id = ?
                AND deleted_at IS NULL

            LIMIT 1

        ";


        pdo_query(
            $db,
            $query,
            array(
                $category_id,
                $is_pinned,
                $title,
                $slug,
                $summary,
                $body_html,
                $status,
                $visibility,
                $display_at,
                $publish_at,
                $author_display_name,
                $post_id,
                $board_id
            )
        );


    }else{


        /*
         * 신규 등록
         */

        $query = "

            INSERT INTO posts
            (
                board_id,
                category_id,
                is_pinned,
                title,
                slug,
                summary,
                body_html,
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
                $board_id,
                $category_id,
                $is_pinned,
                $title,
                $slug,
                $summary,
                $body_html,
                $status,
                $visibility,
                $display_at,
                $publish_at,
                $admin_id > 0
                    ? $admin_id
                    : null,
                $author_display_name
            )
        );


        $post_id =
            blog_last_insert_id(
                $db
            );


        if(
            $post_id <= 0
        ){

            throw new Exception(
                'NEWS 정보를 등록하지 못했습니다.'
            );

        }

    }



    /*
     * =====================================================
     * TAG MAP
     * =====================================================
     */

    pdo_query(
        $db,
        "

            DELETE FROM post_tag_map

            WHERE
                board_id = ?
                AND post_id = ?

        ",
        array(
            $board_id,
            $post_id
        )
    );



    /*
     * 태그 재등록
     */

    $tag_sort_order =
        0;


    foreach(
        $tag_ids as
        $tag_id
    ){

        $query = "

            INSERT INTO post_tag_map
            (
                board_id,
                post_id,
                tag_id,
                sort_order
            )
            VALUES
            (
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
                $board_id,
                $post_id,
                $tag_id,
                $tag_sort_order
            )
        );


        $tag_sort_order++;

    }



    /*
     * =====================================================
     * THUMBNAIL
     * =====================================================
     */

    $thumbnail_media_id =
        0;


    if(
        isset($_FILES['thumbnail'])
    ){

        $thumbnail_media_id =
            blog_upload_image(
                $db,
                $_FILES['thumbnail'],
                $admin_id,
                $uploaded_files
            );

    }



    /*
     * 새 대표 이미지
     */

    if(
        $thumbnail_media_id > 0
    ){

        /*
         * 기존 연결 제거
         */

        pdo_query(
            $db,
            "

                DELETE FROM post_media

                WHERE
                    post_id = ?
                    AND role = 'thumbnail'

            ",
            array(
                $post_id
            )
        );


        /*
         * 새 연결
         */

        $query = "

            INSERT INTO post_media
            (
                post_id,
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
                ?,
                NULL
            )

        ";


        pdo_query(
            $db,
            $query,
            array(
                $post_id,
                $thumbnail_media_id,
                $title
            )
        );


    }else if(
        $thumbnail_remove == 1
    ){

        /*
         * 대표 이미지 제거
         */

        pdo_query(
            $db,
            "

                DELETE FROM post_media

                WHERE
                    post_id = ?
                    AND role = 'thumbnail'

            ",
            array(
                $post_id
            )
        );

    }



    /*
     * =====================================================
     * BODY IMAGE
     *
     * body_html의 TinyMCE 이미지와
     * post_media(role=body)를 동기화
     * =====================================================
     */

    blog_sync_body_media(
        $db,
        $post_id,
        $body_html
    );



    /*
     * =====================================================
     * 기존 SEO
     * =====================================================
     */

    $query = "

        SELECT
            post_id,
            og_media_id

        FROM post_seo

        WHERE
            post_id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $post_id
            )
        );


    $current_seo =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    $current_og_media_id =
        0;


    if($current_seo){

        $current_og_media_id =
            intval(
                $current_seo['og_media_id']
            );

    }



    /*
     * =====================================================
     * OG IMAGE
     * =====================================================
     */

    $new_og_media_id =
        0;


    if(
        isset($_FILES['og_image'])
    ){

        $new_og_media_id =
            blog_upload_image(
                $db,
                $_FILES['og_image'],
                $admin_id,
                $uploaded_files
            );

    }



    /*
     * 새 OG 이미지
     */

    if(
        $new_og_media_id > 0
    ){

        $og_media_id =
            $new_og_media_id;


    /*
     * OG 제거
     */

    }else if(
        $og_image_remove == 1
    ){

        $og_media_id =
            null;


    /*
     * 기존 유지
     */

    }else{

        $og_media_id =
            $current_og_media_id > 0
                ? $current_og_media_id
                : null;

    }



    /*
     * =====================================================
     * SEO
     * =====================================================
     */

    if($current_seo){

        /*
         * 수정
         */

        $query = "

            UPDATE post_seo

            SET
                seo_title = ?,
                meta_description = ?,
                meta_keywords = ?,
                canonical_url = ?,
                og_title = ?,
                og_description = ?,
                og_media_id = ?,
                allow_index = ?,
                nofollow = ?,
                updated_at = NOW()

            WHERE
                post_id = ?

            LIMIT 1

        ";


        pdo_query(
            $db,
            $query,
            array(
                $seo_title,
                $meta_description,
                $meta_keywords,
                $canonical_url,
                $og_title,
                $og_description,
                $og_media_id,
                $allow_index,
                $nofollow,
                $post_id
            )
        );


    }else{

        /*
         * 신규
         */

        $query = "

            INSERT INTO post_seo
            (
                post_id,
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

        ";


        pdo_query(
            $db,
            $query,
            array(
                $post_id,
                $seo_title,
                $meta_description,
                $meta_keywords,
                $canonical_url,
                $og_title,
                $og_description,
                $og_media_id,
                $allow_index,
                $nofollow
            )
        );

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

        blog_save_json(
            true,
            'NEWS가 수정되었습니다.',
            $post_id
        );

    }


    blog_save_json(
        true,
        'NEWS가 등록되었습니다.',
        $post_id
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
     * 이번 SAVE 요청에서 신규 생성한 실제 파일 제거
     *
     * TinyMCE editor_image_upload로 미리 올라간 본문 파일은
     * 여기서 생성한 파일이 아니므로 건드리지 않음.
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
     * 개발 단계에서는 에러 확인
     */

    blog_save_json(
        false,
        $e->getMessage(),
        $post_id
    );

}
?>