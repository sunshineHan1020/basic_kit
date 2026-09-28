<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 메인 전시 삭제
 *
 * FILE
 * /manage/proc/main_exhibition_delete.php
 *
 * URL
 * /manage/proc/main_exhibition_delete
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


header(
    'Content-Type: application/json; charset=UTF-8'
);



/*
 * =========================================================
 * RESPONSE
 * =========================================================
 */

function main_exhibition_delete_response(
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

    main_exhibition_delete_response(
        false,
        '잘못된 요청입니다.'
    );

}



/*
 * =========================================================
 * PARAMETER
 * =========================================================
 */

$id =
    isset($_POST['id'])
        ? intval(
            $_POST['id']
        )
        : 0;


if(
    $id <= 0
){

    main_exhibition_delete_response(
        false,
        '삭제할 메인 전시 정보가 없습니다.'
    );

}



/*
 * =========================================================
 * DATA CHECK
 * =========================================================
 */

$query = "

    SELECT

        me.id,
        me.work_id,
        me.image_media_id,
        me.video_media_id,
        w.title,
        w.list_title

    FROM main_exhibitions me

    INNER JOIN works w
        ON w.id = me.work_id

    WHERE
        me.id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $id
        )
    );


$exhibition =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(
    !$exhibition
){

    main_exhibition_delete_response(
        false,
        '이미 삭제되었거나 존재하지 않는 메인 전시입니다.'
    );

}



/*
 * =========================================================
 * DELETE
 *
 * main_exhibition_clients
 * -> FK ON DELETE CASCADE
 *
 * media / image file / video file
 * -> 삭제하지 않음
 * =========================================================
 */

try{

    $query = "

        DELETE FROM main_exhibitions

        WHERE
            id = ?

        LIMIT 1

    ";


    pdo_query(
        $db,
        $query,
        array(
            $id
        )
    );


    main_exhibition_delete_response(
        true,
        '메인 전시가 삭제되었습니다.'
    );


}catch(Exception $e){

    main_exhibition_delete_response(
        false,
        '삭제 처리 중 오류가 발생했습니다.'
    );

}
?>