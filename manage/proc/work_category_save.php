<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 카테고리 등록 / 수정
 *
 * FILE
 * /manage/proc/work_category_save.php
 *
 * URL
 * /manage/proc/work_category_save
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

function work_category_save_json(
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

    work_category_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * 값
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
        ? trim($_POST['slug'])
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

    work_category_save_json(
        false,
        '카테고리명을 입력해주세요.',
        $category_id
    );

}


if(
    strlen($name) > 100
){

    work_category_save_json(
        false,
        '카테고리명은 100자 이하로 입력해주세요.',
        $category_id
    );

}


if($slug == ''){

    work_category_save_json(
        false,
        'Slug를 입력해주세요.',
        $category_id
    );

}


/*
 * 소문자 처리
 */

$slug =
    strtolower(
        $slug
    );


/*
 * Slug 형식
 */

if(
    !preg_match(
        '/^[a-z0-9\-]+$/',
        $slug
    )
){

    work_category_save_json(
        false,
        'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
        $category_id
    );

}


if(
    strlen($slug) > 180
){

    work_category_save_json(
        false,
        'Slug는 180자 이하로 입력해주세요.',
        $category_id
    );

}


if($sort_order < 0){

    $sort_order = 0;

}


if(
    $is_active != 0 &&
    $is_active != 1
){

    $is_active = 1;

}



/*
 * =========================================================
 * 수정일 경우 기존 카테고리 확인
 * =========================================================
 */

if($category_id > 0){

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

        work_category_save_json(
            false,
            '존재하지 않는 카테고리입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * Slug 중복 확인
 * =========================================================
 */

if($category_id > 0){

    /*
     * 수정
     */

    $query = "

        SELECT
            id

        FROM work_categories

        WHERE
            slug = ?
            AND id != ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $slug,
                $category_id
            )
        );

}else{

    /*
     * 등록
     */

    $query = "

        SELECT
            id

        FROM work_categories

        WHERE
            slug = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $slug
            )
        );

}


$duplicate =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if($duplicate){

    work_category_save_json(
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

        UPDATE work_categories

        SET
            name = ?,
            slug = ?,
            sort_order = ?,
            is_active = ?

        WHERE
            id = ?

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
            $category_id
        )
    );


    work_category_save_json(
        true,
        '카테고리가 수정되었습니다.',
        $category_id
    );

}



/*
 * =========================================================
 * 등록
 * =========================================================
 */

$query = "

    INSERT INTO work_categories
    (
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
        ?
    )

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $name,
            $slug,
            $sort_order,
            $is_active
        )
    );


/*
 * INSERT ID
 */

$new_id =
    intval(
        $db->lastInsertId()
    );


if($new_id <= 0){

    work_category_save_json(
        false,
        '카테고리를 등록하지 못했습니다.',
        0
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

work_category_save_json(
    true,
    '카테고리가 등록되었습니다.',
    $new_id
);
?>