<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 태그 삭제
 *
 * FILE
 * /manage/proc/blog_tag_delete.php
 *
 * URL
 * /manage/proc/blog_tag_delete
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

function blog_tag_delete_json(
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

    blog_tag_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * BLOG Board
 * 화면에서는 NEWS로 사용
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

    blog_tag_delete_json(
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
 * Tag ID
 * =========================================================
 */

$tag_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if($tag_id <= 0){

    blog_tag_delete_json(
        false,
        '잘못된 태그 정보입니다.'
    );

}



/*
 * =========================================================
 * 태그 확인
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

    blog_tag_delete_json(
        false,
        '존재하지 않는 NEWS 태그입니다.'
    );

}



/*
 * =========================================================
 * 게시글 사용 여부
 *
 * post_tag_map에 연결 정보가 남아 있으면
 * FK 때문에 태그 삭제 불가
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS cnt

    FROM post_tag_map

    WHERE
        board_id = ?
        AND tag_id = ?

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $board_id,
            $tag_id
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

    blog_tag_delete_json(
        false,
        'NEWS 게시글에 사용 중인 태그는 삭제할 수 없습니다.'
    );

}



/*
 * =========================================================
 * 삭제
 * =========================================================
 */

$query = "

    DELETE FROM post_tags

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



/*
 * =========================================================
 * 결과 확인
 * =========================================================
 */

if(
    !$ps ||
    $ps->rowCount() <= 0
){

    blog_tag_delete_json(
        false,
        '태그를 삭제하지 못했습니다.'
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

blog_tag_delete_json(
    true,
    'NEWS 태그가 삭제되었습니다.'
);
?>