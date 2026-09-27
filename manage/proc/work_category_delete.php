<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 카테고리 삭제
 *
 * FILE
 * /manage/proc/work_category_delete.php
 *
 * URL
 * /manage/proc/work_category_delete
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

function work_category_delete_json(
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

    work_category_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$category_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if($category_id <= 0){

    work_category_delete_json(
        false,
        '잘못된 카테고리 정보입니다.'
    );

}



/*
 * =========================================================
 * 카테고리 확인
 * =========================================================
 */

$query = "

    SELECT
        id,
        name,
        slug

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

    work_category_delete_json(
        false,
        '존재하지 않는 카테고리입니다.'
    );

}



/*
 * =========================================================
 * 사용중인 WORK 확인
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS cnt

    FROM works

    WHERE
        category_id = ?
        AND deleted_at IS NULL

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $category_id
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
 * WORK 연결된 경우 삭제 금지
 */

if($work_count > 0){

    work_category_delete_json(
        false,
        'WORK가 등록되어 있는 카테고리는 삭제할 수 없습니다.'
    );

}



/*
 * =========================================================
 * 삭제
 * =========================================================
 */

$query = "

    DELETE FROM work_categories

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



/*
 * =========================================================
 * 삭제 결과
 * =========================================================
 */

if(
    !$ps ||
    $ps->rowCount() <= 0
){

    work_category_delete_json(
        false,
        '카테고리를 삭제하지 못했습니다.'
    );

}



/*
 * =========================================================
 * 완료
 * =========================================================
 */

work_category_delete_json(
    true,
    '카테고리가 삭제되었습니다.'
);
?>