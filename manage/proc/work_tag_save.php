<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 태그 등록 / 수정
 *
 * FILE
 * /manage/proc/work_tag_save.php
 *
 * URL
 * /manage/proc/work_tag_save
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

function work_tag_save_json(
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

    work_tag_save_json(
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

$tag_id =
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

    work_tag_save_json(
        false,
        '태그명을 입력해주세요.',
        $tag_id
    );

}


if(
    function_exists('mb_strlen') &&
    mb_strlen(
        $name,
        'UTF-8'
    ) > 100
){

    work_tag_save_json(
        false,
        '태그명은 100자 이하로 입력해주세요.',
        $tag_id
    );

}


if($slug == ''){

    work_tag_save_json(
        false,
        'Slug를 입력해주세요.',
        $tag_id
    );

}


if(
    !preg_match(
        '/^[a-z0-9\-]+$/',
        $slug
    )
){

    work_tag_save_json(
        false,
        'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
        $tag_id
    );

}


if(strlen($slug) > 180){

    work_tag_save_json(
        false,
        'Slug는 180자 이하로 입력해주세요.',
        $tag_id
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
 * 수정 태그 확인
 * =========================================================
 */

if($tag_id > 0){

    $query = "

        SELECT
            id

        FROM work_tags

        WHERE
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $tag_id
            )
        );


    $tag =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$tag){

        work_tag_save_json(
            false,
            '존재하지 않는 태그입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * Slug 중복 확인
 * =========================================================
 */

if($tag_id > 0){

    /*
     * 수정
     */

    $query = "

        SELECT
            id

        FROM work_tags

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
                $tag_id
            )
        );

}else{

    /*
     * 등록
     */

    $query = "

        SELECT
            id

        FROM work_tags

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

    work_tag_save_json(
        false,
        '이미 사용 중인 Slug입니다.',
        $tag_id
    );

}



/*
 * =========================================================
 * 수정
 * =========================================================
 */

if($tag_id > 0){

    $query = "

        UPDATE work_tags

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
            $tag_id
        )
    );


    work_tag_save_json(
        true,
        '태그가 수정되었습니다.',
        $tag_id
    );

}



/*
 * =========================================================
 * 등록
 * =========================================================
 */

$query = "

    INSERT INTO work_tags
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
 * =========================================================
 * INSERT ID
 * =========================================================
 */

$new_id =
    intval(
        $db->lastInsertId()
    );


if($new_id <= 0){

    work_tag_save_json(
        false,
        '태그를 등록하지 못했습니다.',
        0
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

work_tag_save_json(
    true,
    '태그가 등록되었습니다.',
    $new_id
);
?>