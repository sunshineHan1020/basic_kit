<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 태그 삭제
 *
 * FILE
 * /manage/proc/work_tag_delete.php
 *
 * URL
 * /manage/proc/work_tag_delete
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

function work_tag_delete_json(
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

    work_tag_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$tag_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if($tag_id <= 0){

    work_tag_delete_json(
        false,
        '잘못된 태그 정보입니다.'
    );

}



/*
 * =========================================================
 * 태그 확인
 * =========================================================
 */

$query = "

    SELECT
        id,
        name,
        slug

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

    work_tag_delete_json(
        false,
        '존재하지 않는 태그입니다.'
    );

}



/*
 * =========================================================
 * WORK 사용 여부 확인
 *
 * work_tag_map에 연결이 하나라도 있으면 삭제 금지
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS cnt

    FROM work_tag_map

    WHERE
        tag_id = ?

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $tag_id
        )
    );


$count_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$work_count =
    $count_data
        ? intval($count_data['cnt'])
        : 0;



/*
 * =========================================================
 * 사용 중인 태그
 * =========================================================
 */

if($work_count > 0){

    work_tag_delete_json(
        false,
        'WORK에 사용 중인 태그는 삭제할 수 없습니다.'
    );

}



/*
 * =========================================================
 * 삭제
 * =========================================================
 */

$query = "

    DELETE FROM work_tags

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



/*
 * =========================================================
 * 결과
 * =========================================================
 */

if(
    !$ps ||
    $ps->rowCount() <= 0
){

    work_tag_delete_json(
        false,
        '태그를 삭제하지 못했습니다.'
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

work_tag_delete_json(
    true,
    '태그가 삭제되었습니다.'
);
?>