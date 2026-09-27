<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORK 삭제
 * /manage/proc/work_delete.php
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

function work_delete_json(
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

    work_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}


/*
 * =========================================================
 * ID
 * =========================================================
 */

$work_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if($work_id <= 0){

    work_delete_json(
        false,
        '잘못된 WORK 정보입니다.'
    );

}


/*
 * =========================================================
 * WORK 확인
 * =========================================================
 */

$query = "

    SELECT
        id,
        list_title,
        title,
        deleted_at

    FROM works

    WHERE
        id = ?

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


$work =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$work){

    work_delete_json(
        false,
        '존재하지 않는 WORK입니다.'
    );

}


/*
 * 이미 삭제된 WORK
 */

if(
    $work['deleted_at'] != '' &&
    $work['deleted_at'] != '0000-00-00 00:00:00'
){

    work_delete_json(
        false,
        '이미 삭제된 WORK입니다.'
    );

}


/*
 * =========================================================
 * 삭제
 *
 * 실제 DELETE 하지 않고
 * deleted_at 처리
 * =========================================================
 */

$query = "

    UPDATE works

    SET
        deleted_at = NOW(),
        status = 'archived',
        visibility = 'private',
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
        $work_id
    )
);


/*
 * =========================================================
 * 완료
 * =========================================================
 */

work_delete_json(
    true,
    'WORK가 삭제되었습니다.'
);
?>