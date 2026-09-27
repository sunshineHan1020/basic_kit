<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 카테고리 등록 / 수정
 *
 * FILE
 * /manage/proc/blog_category_save.php
 *
 * URL
 * /manage/proc/blog_category_save
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


header(
    'Content-Type: application/json; charset=utf-8'
);



/*
 * =========================================================
 * JSON Response
 * =========================================================
 */

function blog_category_save_json(
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
 * POST 확인
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    blog_category_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * BLOG Board
 *
 * 파일/DB 코드는 BLOG
 * 화면에서는 NEWS로 사용
 * =========================================================
 */

$BLOG_BOARD_CODE =
    'BLOG';


$query = "

    SELECT
        id,
        code,
        name,
        is_active

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

    blog_category_save_json(
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
 * POST
 * =========================================================
 */

$category_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$name =
    isset($_POST['name'])
        ? trim($_POST['name'])
        : '';


$slug =
    isset($_POST['slug'])
        ? strtolower(
            trim(
                $_POST['slug']
            )
        )
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

if($name == ''){

    blog_category_save_json(
        false,
        '카테고리명을 입력해주세요.',
        $category_id
    );

}


if(
    function_exists('mb_strlen') &&
    mb_strlen(
        $name,
        'UTF-8'
    ) > 100
){

    blog_category_save_json(
        false,
        '카테고리명은 100자 이하로 입력해주세요.',
        $category_id
    );

}


if($slug == ''){

    blog_category_save_json(
        false,
        'URL Slug를 입력해주세요.',
        $category_id
    );

}


if(
    !preg_match(
        '/^[a-z0-9\-]+$/',
        $slug
    )
){

    blog_category_save_json(
        false,
        'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
        $category_id
    );

}


if(strlen($slug) > 180){

    blog_category_save_json(
        false,
        'Slug는 180자 이하로 입력해주세요.',
        $category_id
    );

}


if($sort_order < 0){

    $sort_order =
        0;

}


if(
    $is_active != 0 &&
    $is_active != 1
){

    $is_active =
        1;

}



/*
 * =========================================================
 * 수정 대상 확인
 *
 * 반드시 BLOG 게시판 카테고리인지 확인
 * =========================================================
 */

if($category_id > 0){

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

        blog_category_save_json(
            false,
            '존재하지 않는 NEWS 카테고리입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * Slug 중복 확인
 *
 * post_categories는
 * board_id + slug 기준으로 중복 확인
 * =========================================================
 */

if($category_id > 0){

    $query = "

        SELECT
            id

        FROM post_categories

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
                $category_id
            )
        );

}else{

    $query = "

        SELECT
            id

        FROM post_categories

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

    blog_category_save_json(
        false,
        '이미 사용 중인 Slug입니다.',
        $category_id
    );

}



/*
 * =========================================================
 * 수정
 * =========================================================
 */

if($category_id > 0){

    $query = "

        UPDATE post_categories

        SET
            name = ?,
            slug = ?,
            sort_order = ?,
            is_active = ?

        WHERE
            id = ?
            AND board_id = ?

        LIMIT 1

    ";


    pdo_query(
        $db,
        $query,
        array(
            $name,
            $slug,
            $sort_order,
            $is_active,
            $category_id,
            $board_id
        )
    );


    blog_category_save_json(
        true,
        'NEWS 카테고리가 수정되었습니다.',
        $category_id
    );

}



/*
 * =========================================================
 * 등록
 * =========================================================
 */

$query = "

    INSERT INTO post_categories
    (
        board_id,
        name,
        slug,
        sort_order,
        is_active
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
        $board_id,
        $name,
        $slug,
        $sort_order,
        $is_active
    )
);



/*
 * =========================================================
 * INSERT ID
 * =========================================================
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


$insert_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$new_id =
    $insert_data
        ? intval($insert_data['id'])
        : 0;


if($new_id <= 0){

    blog_category_save_json(
        false,
        'NEWS 카테고리를 등록하지 못했습니다.',
        0
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

blog_category_save_json(
    true,
    'NEWS 카테고리가 등록되었습니다.',
    $new_id
);
?>