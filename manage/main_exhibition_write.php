<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 메인 전시 등록 / 수정
 *
 * FILE
 * /manage/main_exhibition_write.php
 *
 * URL
 * /manage/main_exhibition_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function main_exhibition_write_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * PARAMETER
 * =========================================================
 */

$id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $id > 0
        ? true
        : false;



/*
 * =========================================================
 * DEFAULT
 * =========================================================
 */

$exhibition =
    array(
        'id'              => 0,
        'work_id'         => 0,
        'image_media_id'  => 0,
        'video_media_id'  => 0,

        'intro_text'      => '',

        'band_color'      => '#FFFFFF',
        'text_color'      => '#FFFFFF',

        'sort_order'      => 0,
        'is_active'       => 1,

        'created_at'      => '',
        'updated_at'      => '',

        'work_title'      => '',
        'work_list_title' => '',
        'work_slug'       => '',

        'image_name'      => '',
        'image_path'      => '',

        'video_name'      => '',
        'video_path'      => ''
    );



/*
 * =========================================================
 * EDIT DATA
 * =========================================================
 */

if(
    $is_edit
){

    $query = "

        SELECT

            me.id,
            me.work_id,
            me.image_media_id,
            me.video_media_id,

            me.intro_text,

            me.band_color,
            me.text_color,

            me.sort_order,
            me.is_active,

            me.created_at,
            me.updated_at,

            w.title AS work_title,
            w.list_title AS work_list_title,
            w.slug AS work_slug,

            im.original_name AS image_name,
            im.storage_key AS image_path,

            vm.original_name AS video_name,
            vm.storage_key AS video_path

        FROM main_exhibitions me

        INNER JOIN works w
            ON w.id = me.work_id

        LEFT JOIN media im
            ON im.id = me.image_media_id
            AND im.deleted_at IS NULL

        LEFT JOIN media vm
            ON vm.id = me.video_media_id
            AND vm.deleted_at IS NULL

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


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        page_move(
            '/manage/main_exhibition_list'
        );

        exit;

    }


    $exhibition =
        array_merge(
            $exhibition,
            $data
        );

}



/*
 * =========================================================
 * SELECTED CLIENTS
 * =========================================================
 */

$selected_client_ids =
    array();


if(
    $is_edit
){

    $query = "

        SELECT

            client_id

        FROM main_exhibition_clients

        WHERE
            exhibition_id = ?

        ORDER BY
            sort_order ASC,
            client_id ASC

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $id
            )
        );


    while(
        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $selected_client_ids[] =
            intval(
                $data['client_id']
            );

    }

}



/*
 * =========================================================
 * WORK LIST
 *
 * 이미 다른 메인 전시에 등록된 WORKS 제외
 * 현재 수정중인 WORKS는 유지
 * =========================================================
 */

$work_list =
    array();


$query = "

    SELECT

        w.id,
        w.title,
        w.list_title,
        w.slug,
        w.status,
        w.visibility,
        w.created_at,

        (
            SELECT

                m.storage_key

            FROM work_media wm

            INNER JOIN media m
                ON m.id = wm.media_id
                AND m.deleted_at IS NULL

            WHERE
                wm.work_id = w.id
                AND wm.role = 'thumbnail'

            ORDER BY
                wm.sort_order ASC,
                wm.id ASC

            LIMIT 1

        ) AS thumbnail_path

    FROM works w

    LEFT JOIN main_exhibitions me
        ON me.work_id = w.id

    WHERE
        w.deleted_at IS NULL

        AND
        (
            me.id IS NULL
            OR me.id = ?
        )

    ORDER BY
        w.created_at DESC,
        w.id DESC

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $id
        )
    );


while(
    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        )
){

    $work_list[] =
        $data;

}



/*
 * =========================================================
 * CLIENT LIST
 * =========================================================
 */

$client_list =
    array();


$query = "

    SELECT

        c.id,
        c.client_name,
        c.media_id,
        c.sort_order,
        c.is_active,

        m.storage_key,
        m.original_name

    FROM clients c

    INNER JOIN media m
        ON m.id = c.media_id
        AND m.deleted_at IS NULL

    ORDER BY
        c.is_active DESC,
        c.sort_order ASC,
        c.id ASC

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


while(
    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        )
){

    $client_list[] =
        $data;

}



/*
 * =========================================================
 * ACTIVE COUNT
 * =========================================================
 */

$query = "

    SELECT

        COUNT(*) AS cnt

    FROM main_exhibitions

    WHERE
        is_active = 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


$data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$active_count =
    $data
        ? intval(
            $data['cnt']
        )
        : 0;



/*
 * =========================================================
 * HEADER
 * =========================================================
 */

include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/main_exhibition.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="main-exhibition-page-head">

            <div>

                <span class="main-exhibition-eyebrow">
                    MAIN
                </span>

                <h2>

                    <?
                    if($is_edit){
                    ?>

                        메인 전시 수정

                    <?
                    }else{
                    ?>

                        메인 전시 등록

                    <?
                    }
                    ?>

                </h2>

                <p>
                    메인 페이지에 노출할 프로젝트와 전시 정보를 설정합니다.
                </p>

            </div>


            <a
                href="/manage/main_exhibition_list"
                class="btn btn-white"
            >

                <i class="ri-arrow-left-line"></i>

                목록

            </a>

        </div>



        <!-- =================================================
             FORM
        ================================================== -->

        <form
            id="mainExhibitionForm"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($exhibition['id'])?>"
            >


            <input
                type="hidden"
                name="current_image_media_id"
                value="<?=intval($exhibition['image_media_id'])?>"
            >


            <input
                type="hidden"
                name="current_video_media_id"
                value="<?=intval($exhibition['video_media_id'])?>"
            >



            <div class="main-exhibition-write-layout">


                <!-- =================================================
                     MAIN
                ================================================== -->

                <div class="main-exhibition-write-main">


                    <!-- =================================================
                         PROJECT
                    ================================================== -->

                    <div class="card main-exhibition-write-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    프로젝트 선택
                                </h3>

                                <p>
                                    기존 WORKS 중 메인 페이지에 노출할 프로젝트를 선택합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label>

                                        프로젝트

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">


                                    <?
                                    if(
                                        count($work_list) > 0
                                    ){
                                    ?>

                                        <div class="main-exhibition-work-list">


                                            <?
                                            foreach(
                                                $work_list as $work
                                            ){

                                                $work_title =
                                                    trim(
                                                        $work['list_title']
                                                    ) != ''
                                                        ? $work['list_title']
                                                        : $work['title'];


                                                $checked =
                                                    intval(
                                                        $exhibition['work_id']
                                                    ) == intval(
                                                        $work['id']
                                                    );
                                            ?>

                                                <label class="main-exhibition-work-item">

                                                    <input
                                                        type="radio"
                                                        name="work_id"
                                                        value="<?=intval($work['id'])?>"
                                                        <?=$checked ? 'checked' : ''?>
                                                    >


                                                    <span class="main-exhibition-work-ui">


                                                        <span class="main-exhibition-work-thumb">

                                                            <?
                                                            if(
                                                                $work['thumbnail_path'] != ''
                                                            ){
                                                            ?>

                                                                <img
                                                                    src="<?=main_exhibition_write_h($work['thumbnail_path'])?>"
                                                                    alt="<?=main_exhibition_write_h($work_title)?>"
                                                                >

                                                            <?
                                                            }else{
                                                            ?>

                                                                <span class="main-exhibition-work-no-image">

                                                                    <i class="ri-image-line"></i>

                                                                </span>

                                                            <?
                                                            }
                                                            ?>

                                                        </span>



                                                        <span class="main-exhibition-work-info">

                                                            <strong>

                                                                <?=main_exhibition_write_h(
                                                                    $work_title
                                                                )?>

                                                            </strong>


                                                            <span>

                                                                /project/<?=main_exhibition_write_h(
                                                                    $work['slug']
                                                                )?>

                                                            </span>


                                                            <span class="main-exhibition-work-meta">


                                                                <?
                                                                if(
                                                                    $work['status'] == 'published'
                                                                ){
                                                                ?>

                                                                    <em class="published">
                                                                        게시
                                                                    </em>

                                                                <?
                                                                }else if(
                                                                    $work['status'] == 'draft'
                                                                ){
                                                                ?>

                                                                    <em class="draft">
                                                                        임시저장
                                                                    </em>

                                                                <?
                                                                }else{
                                                                ?>

                                                                    <em class="archived">
                                                                        보관
                                                                    </em>

                                                                <?
                                                                }
                                                                ?>


                                                                <?
                                                                if(
                                                                    $work['visibility'] == 'private'
                                                                ){
                                                                ?>

                                                                    <em class="private">
                                                                        비공개
                                                                    </em>

                                                                <?
                                                                }
                                                                ?>


                                                            </span>

                                                        </span>



                                                        <span class="main-exhibition-work-check">

                                                            <i class="ri-check-line"></i>

                                                        </span>


                                                    </span>

                                                </label>

                                            <?
                                            }
                                            ?>


                                        </div>

                                    <?
                                    }else{
                                    ?>

                                        <div class="main-exhibition-work-empty">

                                            <i class="ri-folder-open-line"></i>

                                            <strong>
                                                선택 가능한 프로젝트가 없습니다.
                                            </strong>

                                            <span>
                                                WORKS를 먼저 등록해주세요.
                                            </span>

                                        </div>

                                    <?
                                    }
                                    ?>


                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         MAIN IMAGE
                    ================================================== -->

                    <div class="card main-exhibition-write-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    메인 전시 이미지
                                </h3>

                                <p>
                                    메인 전시에 사용되는 전용 배경 이미지를 등록합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label for="image_file">

                                        이미지

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">


                                    <div
                                        class="main-exhibition-image-preview <?=($exhibition['image_path'] == '' ? 'empty' : '')?>"
                                        id="imagePreview"
                                    >

                                        <img
                                            src="<?=main_exhibition_write_h($exhibition['image_path'])?>"
                                            alt=""
                                            id="imagePreviewImage"
                                        >


                                        <div class="main-exhibition-image-empty">

                                            <i class="ri-image-add-line"></i>

                                            <span>
                                                메인 전시 이미지를 등록해주세요.
                                            </span>

                                        </div>

                                    </div>



                                    <div class="main-exhibition-file">

                                        <input
                                            type="file"
                                            id="image_file"
                                            name="image_file"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >

                                    </div>



                                    <?
                                    if(
                                        $exhibition['image_name'] != ''
                                    ){
                                    ?>

                                        <p class="main-exhibition-current-file">

                                            현재 파일 :

                                            <strong>

                                                <?=main_exhibition_write_h(
                                                    $exhibition['image_name']
                                                )?>

                                            </strong>

                                        </p>

                                    <?
                                    }
                                    ?>


                                    <p class="main-exhibition-help">
                                        JPG, PNG, WEBP 이미지를 사용할 수 있습니다. 수정 시 새 이미지를 선택하면 기존 이미지를 교체합니다.
                                    </p>


                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         CONTENT
                    ================================================== -->

                    <div class="card main-exhibition-write-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    전시 정보
                                </h3>

                                <p>
                                    메인 전시에 표시되는 소개문구와 클라이언트를 설정합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <!-- INTRO -->

                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label for="intro_text">
                                        소개문구
                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">

                                    <textarea
                                        id="intro_text"
                                        name="intro_text"
                                        maxlength="1000"
                                        placeholder="메인에서 보여줄 프로젝트 소개문구를 입력해주세요."
                                    ><?=main_exhibition_write_h($exhibition['intro_text'])?></textarea>


                                    <p class="main-exhibition-help">
                                        WORKS의 Summary와 별도로 메인 전시에서만 사용됩니다.
                                    </p>

                                </div>

                            </div>



                            <!-- CLIENT -->

                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label>
                                        클라이언트
                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">


                                    <?
                                    if(
                                        count($client_list) > 0
                                    ){
                                    ?>

                                        <div
                                            class="main-exhibition-client-list"
                                            id="clientList"
                                        >


                                            <?
                                            foreach(
                                                $client_list as $client
                                            ){

                                                $client_id =
                                                    intval(
                                                        $client['id']
                                                    );


                                                $checked =
                                                    in_array(
                                                        $client_id,
                                                        $selected_client_ids
                                                    );
                                            ?>

                                                <label class="main-exhibition-client-item">

                                                    <input
                                                        type="checkbox"
                                                        name="client_id[]"
                                                        value="<?=$client_id?>"
                                                        <?=$checked ? 'checked' : ''?>
                                                    >


                                                    <span class="main-exhibition-client-ui">


                                                        <span class="main-exhibition-client-logo">

                                                            <img
                                                                src="<?=main_exhibition_write_h($client['storage_key'])?>"
                                                                alt="<?=main_exhibition_write_h($client['client_name'])?>"
                                                            >

                                                        </span>



                                                        <span class="main-exhibition-client-name">

                                                            <strong>

                                                                <?=main_exhibition_write_h(
                                                                    $client['client_name']
                                                                )?>

                                                            </strong>


                                                            <?
                                                            if(
                                                                intval(
                                                                    $client['is_active']
                                                                ) != 1
                                                            ){
                                                            ?>

                                                                <em>
                                                                    비노출
                                                                </em>

                                                            <?
                                                            }
                                                            ?>

                                                        </span>



                                                        <span class="main-exhibition-client-check">

                                                            <i class="ri-check-line"></i>

                                                        </span>


                                                    </span>

                                                </label>

                                            <?
                                            }
                                            ?>


                                        </div>


                                        <p class="main-exhibition-help">
                                            여러 클라이언트를 선택할 수 있습니다. 마우스 휠로 좌우 이동할 수 있습니다.
                                        </p>

                                    <?
                                    }else{
                                    ?>

                                        <div class="main-exhibition-client-empty">

                                            <i class="ri-building-4-line"></i>

                                            <span>
                                                등록된 클라이언트가 없습니다.
                                            </span>

                                        </div>

                                    <?
                                    }
                                    ?>


                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         VIDEO
                    ================================================== -->

                    <div class="card main-exhibition-write-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    영상
                                </h3>

                                <p>
                                    프로젝트 전시에 사용할 영상을 선택적으로 등록합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label for="video_file">
                                        영상 파일
                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">


                                    <?
                                    if(
                                        intval(
                                            $exhibition['video_media_id']
                                        ) > 0
                                    ){
                                    ?>

                                        <div class="main-exhibition-current-video">

                                            <div class="main-exhibition-video-icon">

                                                <i class="ri-video-line"></i>

                                            </div>


                                            <div class="main-exhibition-current-video-info">

                                                <strong>
                                                    현재 등록 영상
                                                </strong>

                                                <span>

                                                    <?=main_exhibition_write_h(
                                                        $exhibition['video_name']
                                                    )?>

                                                </span>

                                            </div>


                                            <label class="main-exhibition-video-delete">

                                                <input
                                                    type="checkbox"
                                                    name="delete_video"
                                                    value="Y"
                                                    id="deleteVideo"
                                                >

                                                <span>
                                                    기존 영상 삭제
                                                </span>

                                            </label>

                                        </div>

                                    <?
                                    }
                                    ?>



                                    <div class="main-exhibition-file">

                                        <input
                                            type="file"
                                            id="video_file"
                                            name="video_file"
                                            accept=".mp4,video/mp4"
                                        >

                                    </div>


                                    <p class="main-exhibition-help">
                                        MP4 파일만 등록할 수 있습니다. 영상은 선택사항입니다.
                                    </p>


                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         STYLE
                    ================================================== -->

                    <div class="card main-exhibition-write-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    전시 스타일
                                </h3>

                                <p>
                                    메인 전시에 사용되는 띠와 글자 색상을 설정합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <!-- BAND -->

                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label for="band_color">
                                        띠 색상
                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">

                                    <div class="main-exhibition-color-field">

                                        <input
                                            type="color"
                                            id="band_color_picker"
                                            value="<?=main_exhibition_write_h($exhibition['band_color'])?>"
                                        >

                                        <input
                                            type="text"
                                            id="band_color"
                                            name="band_color"
                                            value="<?=main_exhibition_write_h($exhibition['band_color'])?>"
                                            maxlength="7"
                                            placeholder="#FFFFFF"
                                        >

                                    </div>


                                    <p class="main-exhibition-help">
                                        SVG 라인의 --stroke 값으로 사용됩니다.
                                    </p>

                                </div>

                            </div>



                            <!-- TEXT -->

                            <div class="main-exhibition-field">

                                <div class="main-exhibition-field-label">

                                    <label for="text_color">
                                        글자 색상
                                    </label>

                                </div>


                                <div class="main-exhibition-field-content">

                                    <div class="main-exhibition-color-field">

                                        <input
                                            type="color"
                                            id="text_color_picker"
                                            value="<?=main_exhibition_write_h($exhibition['text_color'])?>"
                                        >

                                        <input
                                            type="text"
                                            id="text_color"
                                            name="text_color"
                                            value="<?=main_exhibition_write_h($exhibition['text_color'])?>"
                                            maxlength="7"
                                            placeholder="#FFFFFF"
                                        >

                                    </div>


                                    <p class="main-exhibition-help">
                                        띠 내부 텍스트의 --fills 값으로 사용됩니다.
                                    </p>

                                </div>

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================================
                     SIDE
                ================================================== -->

                <div class="main-exhibition-write-side">


                    <div class="card main-exhibition-side-card">

                        <div class="card-header">

                            <h3>
                                노출 설정
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- ORDER -->

                            <div class="main-exhibition-side-field">

                                <label for="sort_order">
                                    정렬순서
                                </label>

                                <input
                                    type="number"
                                    id="sort_order"
                                    name="sort_order"
                                    value="<?=intval($exhibition['sort_order'])?>"
                                >

                                <p>
                                    숫자가 낮을수록 먼저 노출됩니다.
                                </p>

                            </div>



                            <!-- ACTIVE -->

                            <div class="main-exhibition-side-field">

                                <span class="main-exhibition-side-label">
                                    노출 여부
                                </span>


                                <div class="main-exhibition-active-select">


                                    <label>

                                        <input
                                            type="radio"
                                            name="is_active"
                                            value="1"
                                            <?=intval($exhibition['is_active']) == 1 ? 'checked' : ''?>
                                        >

                                        <span>
                                            노출
                                        </span>

                                    </label>



                                    <label>

                                        <input
                                            type="radio"
                                            name="is_active"
                                            value="0"
                                            <?=intval($exhibition['is_active']) != 1 ? 'checked' : ''?>
                                        >

                                        <span>
                                            비노출
                                        </span>

                                    </label>


                                </div>

                            </div>



                            <div class="main-exhibition-active-info">

                                <span>
                                    현재 메인 노출
                                </span>

                                <strong>

                                    <?=number_format($active_count)?>

                                    <em>
                                        / 4
                                    </em>

                                </strong>

                            </div>


                            <p class="main-exhibition-side-notice">

                                <i class="ri-information-line"></i>

                                메인에는 최대 4개까지 노출할 수 있습니다.

                            </p>


                        </div>

                    </div>



                    <!-- =================================================
                         INFO
                    ================================================== -->

                    <?
                    if($is_edit){
                    ?>

                        <div class="card main-exhibition-side-card">

                            <div class="card-header">

                                <h3>
                                    등록 정보
                                </h3>

                            </div>


                            <div class="card-body">

                                <div class="main-exhibition-write-info">

                                    <div>

                                        <span>
                                            등록일
                                        </span>

                                        <strong>

                                            <?=main_exhibition_write_h(
                                                substr(
                                                    $exhibition['created_at'],
                                                    0,
                                                    16
                                                )
                                            )?>

                                        </strong>

                                    </div>


                                    <div>

                                        <span>
                                            수정일
                                        </span>

                                        <strong>

                                            <?=main_exhibition_write_h(
                                                substr(
                                                    $exhibition['updated_at'],
                                                    0,
                                                    16
                                                )
                                            )?>

                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?
                    }
                    ?>



                    <!-- SAVE -->

                    <button
                        type="submit"
                        class="btn btn-primary main-exhibition-save"
                        id="mainExhibitionSave"
                    >

                        <i class="ri-save-line"></i>

                        <?
                        if($is_edit){
                        ?>

                            수정 저장

                        <?
                        }else{
                        ?>

                            전시 등록

                        <?
                        }
                        ?>

                    </button>


                </div>


            </div>

        </form>


    </div>

</main>



<script>

/*
 * =========================================================
 * ELEMENT
 * =========================================================
 */

var mainExhibitionForm =
    document.getElementById(
        'mainExhibitionForm'
    );


var mainExhibitionSave =
    document.getElementById(
        'mainExhibitionSave'
    );


var imageFile =
    document.getElementById(
        'image_file'
    );


var imagePreview =
    document.getElementById(
        'imagePreview'
    );


var imagePreviewImage =
    document.getElementById(
        'imagePreviewImage'
    );


var videoFile =
    document.getElementById(
        'video_file'
    );


var deleteVideo =
    document.getElementById(
        'deleteVideo'
    );


var clientList =
    document.getElementById(
        'clientList'
    );


var bandColor =
    document.getElementById(
        'band_color'
    );


var bandColorPicker =
    document.getElementById(
        'band_color_picker'
    );


var textColor =
    document.getElementById(
        'text_color'
    );


var textColorPicker =
    document.getElementById(
        'text_color_picker'
    );



/*
 * =========================================================
 * MAIN IMAGE PREVIEW
 * =========================================================
 */

if(imageFile){

    imageFile.addEventListener(
        'change',
        function(){

            if(
                !this.files ||
                !this.files[0]
            ){

                return;

            }


            var file =
                this.files[0];


            var reader =
                new FileReader();


            reader.onload =
                function(e){

                    imagePreviewImage.src =
                        e.target.result;


                    imagePreview.classList.remove(
                        'empty'
                    );

                };


            reader.readAsDataURL(
                file
            );

        }
    );

}



/*
 * =========================================================
 * CLIENT WHEEL
 *
 * 세로 마우스 휠
 * → 가로 스크롤
 * =========================================================
 */

if(clientList){

    clientList.addEventListener(
        'wheel',
        function(e){

            if(
                clientList.scrollWidth <=
                clientList.clientWidth
            ){

                return;

            }


            if(
                Math.abs(e.deltaY) >
                Math.abs(e.deltaX)
            ){

                e.preventDefault();


                clientList.scrollLeft +=
                    e.deltaY;

            }

        },
        {
            passive: false
        }
    );

}



/*
 * =========================================================
 * COLOR
 * =========================================================
 */

function normalizeHexColor(value)
{

    value =
        value.toUpperCase();


    if(
        value.charAt(0) != '#'
    ){

        value =
            '#' + value;

    }


    return value;

}



function isHexColor(value)
{

    return /^#[0-9A-F]{6}$/i.test(
        value
    );

}



bandColorPicker.addEventListener(
    'input',
    function(){

        bandColor.value =
            this.value.toUpperCase();

    }
);



textColorPicker.addEventListener(
    'input',
    function(){

        textColor.value =
            this.value.toUpperCase();

    }
);



bandColor.addEventListener(
    'change',
    function(){

        var value =
            normalizeHexColor(
                this.value
            );


        if(
            isHexColor(
                value
            )
        ){

            this.value =
                value;


            bandColorPicker.value =
                value;

        }

    }
);



textColor.addEventListener(
    'change',
    function(){

        var value =
            normalizeHexColor(
                this.value
            );


        if(
            isHexColor(
                value
            )
        ){

            this.value =
                value;


            textColorPicker.value =
                value;

        }

    }
);



/*
 * =========================================================
 * VIDEO
 * =========================================================
 */

if(videoFile){

    videoFile.addEventListener(
        'change',
        function(){

            if(
                this.files &&
                this.files[0] &&
                deleteVideo
            ){

                deleteVideo.checked =
                    false;

            }

        }
    );

}



/*
 * =========================================================
 * SUBMIT
 * =========================================================
 */

mainExhibitionForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();



        /*
         * WORK
         */

        var selectedWork =
            document.querySelector(
                'input[name="work_id"]:checked'
            );


        if(!selectedWork){

            alert(
                '메인에 노출할 프로젝트를 선택해주세요.'
            );

            return;

        }



        /*
         * IMAGE
         *
         * 신규 등록은 이미지 필수
         */

        var currentImageMediaId =
            parseInt(
                mainExhibitionForm
                    .querySelector(
                        'input[name="current_image_media_id"]'
                    )
                    .value,
                10
            );


        if(
            (
                !imageFile.files ||
                !imageFile.files[0]
            ) &&
            (
                !currentImageMediaId ||
                currentImageMediaId <= 0
            )
        ){

            alert(
                '메인 전시 이미지를 등록해주세요.'
            );

            return;

        }



        /*
         * IMAGE EXTENSION
         */

        if(
            imageFile.files &&
            imageFile.files[0]
        ){

            var imageExtension =
                imageFile.files[0]
                    .name
                    .split('.')
                    .pop()
                    .toLowerCase();


            if(
                imageExtension != 'jpg' &&
                imageExtension != 'jpeg' &&
                imageExtension != 'png' &&
                imageExtension != 'webp'
            ){

                alert(
                    '메인 전시 이미지는 JPG, PNG, WEBP 파일만 등록할 수 있습니다.'
                );

                return;

            }

        }



        /*
         * VIDEO
         */

        if(
            videoFile &&
            videoFile.files &&
            videoFile.files[0]
        ){

            var videoExtension =
                videoFile.files[0]
                    .name
                    .split('.')
                    .pop()
                    .toLowerCase();


            if(
                videoExtension != 'mp4'
            ){

                alert(
                    '영상은 MP4 파일만 등록할 수 있습니다.'
                );

                return;

            }

        }



        /*
         * COLORS
         */

        var bandColorValue =
            normalizeHexColor(
                bandColor.value
            );


        if(
            !isHexColor(
                bandColorValue
            )
        ){

            alert(
                '띠 색상은 #FFFFFF 형식으로 입력해주세요.',
                function(){

                    bandColor.focus();

                }
            );

            return;

        }


        bandColor.value =
            bandColorValue;



        var textColorValue =
            normalizeHexColor(
                textColor.value
            );


        if(
            !isHexColor(
                textColorValue
            )
        ){

            alert(
                '글자 색상은 #FFFFFF 형식으로 입력해주세요.',
                function(){

                    textColor.focus();

                }
            );

            return;

        }


        textColor.value =
            textColorValue;



        /*
         * DUPLICATE REQUEST
         */

        if(
            mainExhibitionSave.getAttribute(
                'data-loading'
            ) == 'Y'
        ){

            return;

        }


        mainExhibitionSave.setAttribute(
            'data-loading',
            'Y'
        );


        mainExhibitionSave.disabled =
            true;



        /*
         * AJAX
         */

        var formData =
            new FormData(
                mainExhibitionForm
            );


        var xhr =
            new XMLHttpRequest();


        xhr.open(
            'POST',
            '/manage/proc/main_exhibition_save',
            true
        );


        xhr.onreadystatechange =
            function(){

                if(
                    xhr.readyState != 4
                ){

                    return;

                }


                mainExhibitionSave.removeAttribute(
                    'data-loading'
                );


                mainExhibitionSave.disabled =
                    false;



                if(
                    xhr.status != 200
                ){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                var result;


                try{

                    result =
                        JSON.parse(
                            xhr.responseText
                        );

                }catch(error){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }



                if(!result.code){

                    alert(
                        result.msg
                            ? result.msg
                            : '저장하지 못했습니다.'
                    );

                    return;

                }



                alert(
                    result.msg
                        ? result.msg
                        : '저장되었습니다.',
                    function(){

                        location.href =
                            '/manage/main_exhibition_list';

                    }
                );

            };


        xhr.send(
            formData
        );

    }
);

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>