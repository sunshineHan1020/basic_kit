<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 상태 / 관리자 메모 저장
 *
 * FILE
 * /manage/proc/inquiry_status.php
 *
 * URL
 * /manage/proc/inquiry_status
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

function inquiry_status_json(
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
 * METHOD
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    inquiry_status_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * POST
 * =========================================================
 */

$inquiry_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$status =
    isset($_POST['status'])
        ? trim($_POST['status'])
        : '';


$admin_memo =
    isset($_POST['admin_memo'])
        ? trim($_POST['admin_memo'])
        : '';



/*
 * =========================================================
 * ID
 * =========================================================
 */

if(
    $inquiry_id <= 0
){

    inquiry_status_json(
        false,
        '잘못된 문의 정보입니다.'
    );

}



/*
 * =========================================================
 * STATUS
 * =========================================================
 */

$status_list =
    array(
        'received',
        'confirmed',
        'in_progress',
        'completed'
    );


if(
    !in_array(
        $status,
        $status_list,
        true
    )
){

    inquiry_status_json(
        false,
        '처리 상태가 올바르지 않습니다.'
    );

}



/*
 * =========================================================
 * 문의 확인
 * =========================================================
 */

$query = "

    SELECT

        id,
        status

    FROM inquiries

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
            $inquiry_id
        )
    );


$inquiry =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$inquiry){

    inquiry_status_json(
        false,
        '존재하지 않는 문의입니다.'
    );

}



/*
 * =========================================================
 * UPDATE
 * =========================================================
 */

$query = "

    UPDATE inquiries

    SET
        status = ?,
        admin_memo = ?,
        updated_at = NOW()

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
            $status,
            $admin_memo,
            $inquiry_id
        )
    );


if(!$ps){

    inquiry_status_json(
        false,
        '문의 정보를 저장하지 못했습니다.'
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

inquiry_status_json(
    true,
    '문의 정보가 저장되었습니다.'
);
?>