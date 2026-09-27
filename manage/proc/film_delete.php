<?
/*
 * =========================================================
 * WEEKENDS CMS
 * FILM 삭제
 *
 * FILE
 * /manage/proc/film_delete.php
 *
 * URL
 * /manage/proc/film_delete
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

function film_delete_json(
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

    film_delete_json(
        false,
        '잘못된 접근입니다.'
    );

}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$film_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


if(
    $film_id <= 0
){

    film_delete_json(
        false,
        '잘못된 FILM 정보입니다.'
    );

}



/*
 * =========================================================
 * FILM 확인
 * =========================================================
 */

$query = "

    SELECT
        id,
        media_id,
        title

    FROM films

    WHERE
        id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $film_id
        )
    );


$film =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$film){

    film_delete_json(
        false,
        '존재하지 않는 FILM입니다.'
    );

}



/*
 * =========================================================
 * DELETE
 *
 * media / 실제 이미지 파일은 삭제하지 않는다.
 * =========================================================
 */

$query = "

    DELETE FROM films

    WHERE
        id = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $film_id
        )
    );


if(
    !$ps ||
    $ps->rowCount() <= 0
){

    film_delete_json(
        false,
        'FILM을 삭제하지 못했습니다.'
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

film_delete_json(
    true,
    'FILM이 삭제되었습니다.'
);
?>