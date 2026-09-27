<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 선택항목 삭제
 *
 * FILE
 * /manage/proc/inquiry_option_delete.php
 *
 * URL
 * /manage/proc/inquiry_option_delete
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

function inquiry_option_delete_json(
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

    inquiry_option_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$option_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if(
    $option_id <= 0
){

    inquiry_option_delete_json(
        false,
        '잘못된 선택항목 정보입니다.'
    );

}



/*
 * =========================================================
 * OPTION 확인
 * =========================================================
 */

$query = "

    SELECT

        id,
        step_no,
        option_code,
        title_en,
        title_ko

    FROM inquiry_options

    WHERE
        id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $option_id
        )
    );


$option =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$option){

    inquiry_option_delete_json(
        false,
        '존재하지 않는 선택항목입니다.'
    );

}



/*
 * =========================================================
 * DELETE
 * =========================================================
 */

try {


    $query = "

        DELETE FROM inquiry_options

        WHERE
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $option_id
            )
        );


    if(
        !$ps ||
        $ps->rowCount() <= 0
    ){

        inquiry_option_delete_json(
            false,
            '선택항목을 삭제하지 못했습니다.'
        );

    }



} catch(Exception $e) {


    /*
     * 추후 문의 데이터와 FK가 연결된 경우
     * 삭제 제한에도 대응
     */

    inquiry_option_delete_json(
        false,
        '사용 중인 선택항목은 삭제할 수 없습니다.'
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

inquiry_option_delete_json(
    true,
    '선택항목이 삭제되었습니다.'
);
?>