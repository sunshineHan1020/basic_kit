<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 선택항목 등록 / 수정
 *
 * FILE
 * /manage/proc/inquiry_option_save.php
 *
 * URL
 * /manage/proc/inquiry_option_save
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

function inquiry_option_save_json(
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
 * LAST INSERT ID
 * =========================================================
 */

function inquiry_option_last_insert_id($db)
{
    $ps =
        pdo_query(
            $db,
            "
                SELECT
                    LAST_INSERT_ID() AS id
            ",
            array()
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    return
        $data
            ? intval($data['id'])
            : 0;
}



/*
 * =========================================================
 * STRING LENGTH
 * =========================================================
 */

function inquiry_option_strlen($str)
{
    if(
        function_exists(
            'mb_strlen'
        )
    ){

        return mb_strlen(
            $str,
            'UTF-8'
        );

    }


    return strlen(
        $str
    );
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

    inquiry_option_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * POST
 * =========================================================
 */

$option_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$step_no =
    isset($_POST['step_no'])
        ? intval($_POST['step_no'])
        : 0;


$option_code =
    isset($_POST['option_code'])
        ? trim($_POST['option_code'])
        : '';


$title_en =
    isset($_POST['title_en'])
        ? trim($_POST['title_en'])
        : '';


$title_ko =
    isset($_POST['title_ko'])
        ? trim($_POST['title_ko'])
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
 * STEP
 * =========================================================
 */

if(
    $step_no < 1 ||
    $step_no > 4
){

    inquiry_option_save_json(
        false,
        '단계 정보가 올바르지 않습니다.',
        $option_id
    );

}



/*
 * =========================================================
 * TITLE EN
 * =========================================================
 */

if(
    $title_en == ''
){

    inquiry_option_save_json(
        false,
        '영문 제목을 입력해주세요.',
        $option_id
    );

}


if(
    inquiry_option_strlen(
        $title_en
    ) > 255
){

    inquiry_option_save_json(
        false,
        '영문 제목은 255자 이하로 입력해주세요.',
        $option_id
    );

}



/*
 * =========================================================
 * TITLE KO
 * =========================================================
 */

if(
    $title_ko == ''
){

    inquiry_option_save_json(
        false,
        '한글 제목을 입력해주세요.',
        $option_id
    );

}


if(
    inquiry_option_strlen(
        $title_ko
    ) > 255
){

    inquiry_option_save_json(
        false,
        '한글 제목은 255자 이하로 입력해주세요.',
        $option_id
    );

}



/*
 * =========================================================
 * OPTION CODE
 * =========================================================
 */

if(
    $option_code == ''
){

    inquiry_option_save_json(
        false,
        '코드를 입력해주세요.',
        $option_id
    );

}


$option_code =
    strtolower(
        $option_code
    );


if(
    !preg_match(
        '/^[a-z0-9_-]+$/',
        $option_code
    )
){

    inquiry_option_save_json(
        false,
        '코드는 영문 소문자, 숫자, 언더바(_), 하이픈(-)만 사용할 수 있습니다.',
        $option_id
    );

}


if(
    strlen(
        $option_code
    ) > 100
){

    inquiry_option_save_json(
        false,
        '코드는 100자 이하로 입력해주세요.',
        $option_id
    );

}



/*
 * =========================================================
 * SORT
 * =========================================================
 */

if(
    $sort_order < 0
){

    $sort_order =
        0;

}



/*
 * =========================================================
 * ACTIVE
 * =========================================================
 */

if(
    $is_active != 0 &&
    $is_active != 1
){

    $is_active =
        1;

}



/*
 * =========================================================
 * 수정 대상 확인
 * =========================================================
 */

if(
    $option_id > 0
){

    $query = "

        SELECT
            id

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


    $existing =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$existing){

        inquiry_option_save_json(
            false,
            '존재하지 않는 선택항목입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * CODE 중복 확인
 *
 * step_no + option_code UNIQUE
 * =========================================================
 */

if(
    $option_id > 0
){

    $query = "

        SELECT
            id

        FROM inquiry_options

        WHERE
            step_no = ?
            AND option_code = ?
            AND id <> ?

        LIMIT 1

    ";


    $params =
        array(
            $step_no,
            $option_code,
            $option_id
        );


}else{

    $query = "

        SELECT
            id

        FROM inquiry_options

        WHERE
            step_no = ?
            AND option_code = ?

        LIMIT 1

    ";


    $params =
        array(
            $step_no,
            $option_code
        );

}


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


$duplicate =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if($duplicate){

    inquiry_option_save_json(
        false,
        '해당 단계에 이미 사용 중인 코드입니다.',
        $option_id
    );

}



/*
 * =========================================================
 * UPDATE
 * =========================================================
 */

if(
    $option_id > 0
){

    $query = "

        UPDATE inquiry_options

        SET
            step_no = ?,
            option_code = ?,
            title_en = ?,
            title_ko = ?,
            sort_order = ?,
            is_active = ?,
            updated_at = NOW()

        WHERE
            id = ?

        LIMIT 1

    ";


    pdo_query(
        $db,
        $query,
        array(
            $step_no,
            $option_code,
            $title_en,
            $title_ko,
            $sort_order,
            $is_active,
            $option_id
        )
    );


    inquiry_option_save_json(
        true,
        '선택항목이 수정되었습니다.',
        $option_id
    );

}



/*
 * =========================================================
 * INSERT
 * =========================================================
 */

$query = "

    INSERT INTO inquiry_options
    (
        step_no,
        option_code,
        title_en,
        title_ko,
        sort_order,
        is_active,
        created_at,
        updated_at
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        NOW(),
        NOW()
    )

";


pdo_query(
    $db,
    $query,
    array(
        $step_no,
        $option_code,
        $title_en,
        $title_ko,
        $sort_order,
        $is_active
    )
);


$option_id =
    inquiry_option_last_insert_id(
        $db
    );


if(
    $option_id <= 0
){

    inquiry_option_save_json(
        false,
        '선택항목을 등록하지 못했습니다.',
        0
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

inquiry_option_save_json(
    true,
    '선택항목이 등록되었습니다.',
    $option_id
);
?>