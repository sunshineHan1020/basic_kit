<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 카테고리 삭제
 *
 * FILE
 * /manage/proc/blog_category_delete.php
 *
 * URL
 * /manage/proc/blog_category_delete
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

function blog_category_delete_json(
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
 * POST 확인
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    blog_category_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * BLOG Board
 *
 * 파일 / DB code = BLOG
 * 화면에서는 NEWS
 * =========================================================
 */

$BLOG_BOARD_CODE =
    'BLOG';


$query = "

    SELECT
        id,
        code,
        name

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

    blog_category_delete_json(
        false,
        'NEWS 게시판 정보가 등록되어 있지 않습니다.'
    );

}


$board_id =
    intval(
        $board['id']
    );



/*
 * =========================================================
 * Category ID
 * =========================================================
 */

$category_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if($category_id <= 0){

    blog_category_delete_json(
        false,
        '잘못된 카테고리 정보입니다.'
    );

}



/*
 * =========================================================
 * 카테고리 확인
 *
 * 반드시 BLOG 게시판 소속인지 확인
 * =========================================================
 */

$query = "

    SELECT
        id,
        board_id,
        name,
        slug

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

    blog_category_delete_json(
        false,
        '존재하지 않는 NEWS 카테고리입니다.'
    );

}



/*
 * =========================================================
 * 게시글 사용 여부
 *
 * 중요:
 * deleted_at 조건을 넣지 않는다.
 *
 * soft delete 된 게시글이라도
 * category_id FK가 남아 있으면
 * 카테고리 DELETE가 불가능함.
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS cnt

    FROM posts

    WHERE
        board_id = ?
        AND category_id = ?

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $board_id,
            $category_id
        )
    );


$count_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$post_count =
    $count_data
        ? intval($count_data['cnt'])
        : 0;



/*
 * =========================================================
 * 사용 중
 * =========================================================
 */

if($post_count > 0){

    blog_category_delete_json(
        false,
        'NEWS 게시글에 사용 중인 카테고리는 삭제할 수 없습니다.'
    );

}



/*
 * =========================================================
 * 삭제
 * =========================================================
 */

$query = "

    DELETE FROM post_categories

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



/*
 * =========================================================
 * 결과 확인
 * =========================================================
 */

if(
    !$ps ||
    $ps->rowCount() <= 0
){

    blog_category_delete_json(
        false,
        '카테고리를 삭제하지 못했습니다.'
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

blog_category_delete_json(
    true,
    'NEWS 카테고리가 삭제되었습니다.'
);
?>