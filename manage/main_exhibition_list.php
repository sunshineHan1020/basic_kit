<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 메인 전시 관리
 *
 * FILE
 * /manage/main_exhibition_list.php
 *
 * URL
 * /manage/main_exhibition_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function main_exhibition_list_h($str)
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

$keyword =
    isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';


$is_active =
    isset($_GET['is_active'])
        ? trim($_GET['is_active'])
        : '';


$page =
    isset($_GET['page'])
        ? intval($_GET['page'])
        : 1;


if(
    $page < 1
){

    $page = 1;

}


$limit =
    20;


$offset =
    ($page - 1) * $limit;



/*
 * =========================================================
 * WHERE
 * =========================================================
 */

$where =
    array();


$params =
    array();



/*
 * KEYWORD
 *
 * 프로젝트명 / 클라이언트명
 * =========================================================
 */

if(
    $keyword != ''
){

    $where[] = "

        (
            w.title LIKE ?
            OR w.list_title LIKE ?
            OR w.slug LIKE ?

            OR EXISTS
            (
                SELECT
                    1

                FROM main_exhibition_clients mec_search

                INNER JOIN clients c_search
                    ON c_search.id = mec_search.client_id

                WHERE
                    mec_search.exhibition_id = me.id
                    AND c_search.client_name LIKE ?
            )
        )

    ";


    $keyword_like =
        '%'.$keyword.'%';


    $params[] =
        $keyword_like;


    $params[] =
        $keyword_like;


    $params[] =
        $keyword_like;


    $params[] =
        $keyword_like;

}



/*
 * STATUS
 * =========================================================
 */

if(
    $is_active === '0' ||
    $is_active === '1'
){

    $where[] = "
        me.is_active = ?
    ";


    $params[] =
        intval(
            $is_active
        );

}



/*
 * WHERE SQL
 * =========================================================
 */

$where_sql =
    '';


if(
    count($where) > 0
){

    $where_sql =
        ' WHERE '.
        implode(
            ' AND ',
            $where
        );

}



/*
 * =========================================================
 * SUMMARY
 * =========================================================
 */

$summary =
    array(
        'total'    => 0,
        'active'   => 0,
        'inactive' => 0
    );


$query = "

    SELECT

        COUNT(*) AS total,

        SUM(
            CASE
                WHEN is_active = 1
                THEN 1
                ELSE 0
            END
        ) AS active,

        SUM(
            CASE
                WHEN is_active = 0
                THEN 1
                ELSE 0
            END
        ) AS inactive

    FROM main_exhibitions

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


if($data){

    $summary['total'] =
        intval(
            $data['total']
        );


    $summary['active'] =
        intval(
            $data['active']
        );


    $summary['inactive'] =
        intval(
            $data['inactive']
        );

}



/*
 * =========================================================
 * SEARCH COUNT
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total_count

    FROM main_exhibitions me

    INNER JOIN works w
        ON w.id = me.work_id

    ".$where_sql."

";


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


$data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$total_count =
    $data
        ? intval(
            $data['total_count']
        )
        : 0;



/*
 * =========================================================
 * PAGING
 * =========================================================
 */

$total_page =
    ceil(
        $total_count / $limit
    );


if(
    $total_page < 1
){

    $total_page =
        1;

}


if(
    $page > $total_page
){

    $page =
        $total_page;


    $offset =
        ($page - 1) * $limit;

}



/*
 * =========================================================
 * LIST
 * =========================================================
 */

$exhibition_list =
    array();


$query = "

    SELECT

        me.id,
        me.work_id,
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
        w.status AS work_status,
        w.visibility AS work_visibility,

        (
            SELECT
                GROUP_CONCAT(
                    c.client_name
                    ORDER BY
                        mec.sort_order ASC,
                        c.id ASC
                    SEPARATOR ', '
                )

            FROM main_exhibition_clients mec

            INNER JOIN clients c
                ON c.id = mec.client_id

            WHERE
                mec.exhibition_id = me.id

        ) AS client_names,

        (
            SELECT
                COUNT(*)

            FROM main_exhibition_clients mec_count

            WHERE
                mec_count.exhibition_id = me.id

        ) AS client_count,

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

    FROM main_exhibitions me

    INNER JOIN works w
        ON w.id = me.work_id

    ".$where_sql."

    ORDER BY
        me.sort_order ASC,
        me.id ASC

    LIMIT ".$offset.", ".$limit."

";


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


while(
    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        )
){

    $exhibition_list[] =
        $data;

}



/*
 * =========================================================
 * QUERY STRING
 * =========================================================
 */

$query_params =
    array();


if(
    $keyword != ''
){

    $query_params[] =
        'keyword='.
        urlencode(
            $keyword
        );

}


if(
    $is_active !== ''
){

    $query_params[] =
        'is_active='.
        urlencode(
            $is_active
        );

}


$base_query =
    '';


if(
    count($query_params) > 0
){

    $base_query =
        '&'.
        implode(
            '&',
            $query_params
        );

}



/*
 * =========================================================
 * PAGING BLOCK
 * =========================================================
 */

$page_block =
    10;


$start_page =
    floor(
        ($page - 1) /
        $page_block
    ) * $page_block + 1;


$end_page =
    $start_page +
    $page_block -
    1;


if(
    $end_page > $total_page
){

    $end_page =
        $total_page;

}



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
                    메인 전시 관리
                </h2>

                <p>
                    메인 페이지에 노출되는 주요 프로젝트를 관리합니다.
                </p>

            </div>


            <a
                href="/manage/main_exhibition_write"
                class="btn btn-primary"
            >

                <i class="ri-add-line"></i>

                메인 전시 등록

            </a>

        </div>



        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="main-exhibition-summary">


            <div class="main-exhibition-summary-item">

                <span>
                    전체 등록
                </span>

                <strong>
                    <?=number_format($summary['total'])?>
                </strong>

            </div>



            <div class="main-exhibition-summary-item">

                <span>
                    메인 노출
                </span>

                <strong>

                    <?=number_format($summary['active'])?>

                    <em>
                        / 4
                    </em>

                </strong>

            </div>



            <div class="main-exhibition-summary-item">

                <span>
                    비노출
                </span>

                <strong>
                    <?=number_format($summary['inactive'])?>
                </strong>

            </div>



            <div class="main-exhibition-summary-notice">

                <i class="ri-information-line"></i>

                <span>
                    메인 페이지에는 최대 4개의 프로젝트가 노출됩니다.
                </span>

            </div>


        </div>



        <!-- =================================================
             FILTER
        ================================================== -->

        <div class="card main-exhibition-filter-card">

            <form
                method="get"
                action="/manage/main_exhibition_list"
            >

                <div class="main-exhibition-filter">


                    <select
                        name="is_active"
                        class="main-exhibition-filter-select"
                    >

                        <option value="">
                            전체 상태
                        </option>

                        <option
                            value="1"
                            <?=($is_active === '1' ? 'selected' : '')?>
                        >
                            노출
                        </option>

                        <option
                            value="0"
                            <?=($is_active === '0' ? 'selected' : '')?>
                        >
                            비노출
                        </option>

                    </select>



                    <div class="main-exhibition-search">

                        <i class="ri-search-line"></i>

                        <input
                            type="text"
                            name="keyword"
                            value="<?=main_exhibition_list_h($keyword)?>"
                            placeholder="프로젝트명, 클라이언트명 검색"
                        >

                    </div>



                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        검색
                    </button>


                    <?
                    if(
                        $keyword != '' ||
                        $is_active !== ''
                    ){
                    ?>

                        <a
                            href="/manage/main_exhibition_list"
                            class="btn btn-white"
                        >
                            초기화
                        </a>

                    <?
                    }
                    ?>


                </div>

            </form>

        </div>



        <!-- =================================================
             LIST
        ================================================== -->

        <div class="card main-exhibition-list-card">


            <div class="main-exhibition-list-head">

                <div class="main-exhibition-list-count">

                    전체

                    <strong>
                        <?=number_format($total_count)?>
                    </strong>

                    개

                </div>


                <p>
                    정렬순서가 낮은 프로젝트부터 메인에 노출됩니다.
                </p>

            </div>



            <div class="main-exhibition-table-wrap">

                <table class="main-exhibition-table">

                    <colgroup>

                        <col class="col-number">

                        <col class="col-thumb">

                        <col>

                        <col class="col-client">

                        <col class="col-video">

                        <col class="col-color">

                        <col class="col-order">

                        <col class="col-status">

                        <col class="col-manage">

                    </colgroup>


                    <thead>

                        <tr>

                            <th>
                                No.
                            </th>

                            <th>
                                프로젝트
                            </th>

                            <th>
                                소개
                            </th>

                            <th>
                                클라이언트
                            </th>

                            <th>
                                영상
                            </th>

                            <th>
                                컬러
                            </th>

                            <th>
                                정렬
                            </th>

                            <th>
                                상태
                            </th>

                            <th>
                                관리
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?
                        if(
                            count($exhibition_list) > 0
                        ){
                        ?>


                            <?
                            $list_no =
                                $total_count -
                                $offset;


                            foreach(
                                $exhibition_list as $exhibition
                            ){

                                $work_title =
                                    trim(
                                        $exhibition['work_list_title']
                                    ) != ''
                                        ? $exhibition['work_list_title']
                                        : $exhibition['work_title'];
                            ?>

                                <tr>


                                    <!-- NO -->

                                    <td class="main-exhibition-number">

                                        <?=number_format($list_no)?>

                                    </td>



                                    <!-- PROJECT -->

                                    <td>

                                        <a
                                            href="/manage/main_exhibition_write?id=<?=intval($exhibition['id'])?>"
                                            class="main-exhibition-project"
                                        >

                                            <div class="main-exhibition-thumb">

                                                <?
                                                if(
                                                    $exhibition['thumbnail_path'] != ''
                                                ){
                                                ?>

                                                    <img
                                                        src="<?=main_exhibition_list_h($exhibition['thumbnail_path'])?>"
                                                        alt="<?=main_exhibition_list_h($work_title)?>"
                                                    >

                                                <?
                                                }else{
                                                ?>

                                                    <div class="main-exhibition-no-thumb">

                                                        <i class="ri-image-line"></i>

                                                    </div>

                                                <?
                                                }
                                                ?>

                                            </div>


                                            <div class="main-exhibition-project-info">

                                                <strong>

                                                    <?=main_exhibition_list_h(
                                                        $work_title
                                                    )?>

                                                </strong>


                                                <span>

                                                    /project/<?=main_exhibition_list_h(
                                                        $exhibition['work_slug']
                                                    )?>

                                                </span>


                                                <div class="main-exhibition-project-state">


                                                    <?
                                                    if(
                                                        $exhibition['work_status'] == 'published'
                                                    ){
                                                    ?>

                                                        <span class="published">
                                                            게시
                                                        </span>

                                                    <?
                                                    }else if(
                                                        $exhibition['work_status'] == 'draft'
                                                    ){
                                                    ?>

                                                        <span class="draft">
                                                            임시저장
                                                        </span>

                                                    <?
                                                    }else{
                                                    ?>

                                                        <span class="archived">
                                                            보관
                                                        </span>

                                                    <?
                                                    }
                                                    ?>


                                                    <?
                                                    if(
                                                        $exhibition['work_visibility'] == 'private'
                                                    ){
                                                    ?>

                                                        <span class="private">
                                                            비공개
                                                        </span>

                                                    <?
                                                    }
                                                    ?>


                                                </div>

                                            </div>

                                        </a>

                                    </td>



                                    <!-- INTRO -->

                                    <td>

                                        <div class="main-exhibition-intro">

                                            <?
                                            if(
                                                trim(
                                                    $exhibition['intro_text']
                                                ) != ''
                                            ){
                                            ?>

                                                <?=main_exhibition_list_h(
                                                    $exhibition['intro_text']
                                                )?>

                                            <?
                                            }else{
                                            ?>

                                                <span class="empty">
                                                    -
                                                </span>

                                            <?
                                            }
                                            ?>

                                        </div>

                                    </td>



                                    <!-- CLIENT -->

                                    <td>

                                        <div class="main-exhibition-clients">


                                            <?
                                            if(
                                                intval(
                                                    $exhibition['client_count']
                                                ) > 0
                                            ){
                                            ?>

                                                <strong>

                                                    <?=number_format(
                                                        intval(
                                                            $exhibition['client_count']
                                                        )
                                                    )?>

                                                    개

                                                </strong>


                                                <span>

                                                    <?=main_exhibition_list_h(
                                                        $exhibition['client_names']
                                                    )?>

                                                </span>

                                            <?
                                            }else{
                                            ?>

                                                <span class="empty">
                                                    선택 없음
                                                </span>

                                            <?
                                            }
                                            ?>


                                        </div>

                                    </td>



                                    <!-- VIDEO -->

                                    <td>

                                        <?
                                        if(
                                            intval(
                                                $exhibition['video_media_id']
                                            ) > 0
                                        ){
                                        ?>

                                            <span class="main-exhibition-video registered">

                                                <i class="ri-video-line"></i>

                                                등록

                                            </span>

                                        <?
                                        }else{
                                        ?>

                                            <span class="main-exhibition-video">

                                                <i class="ri-video-off-line"></i>

                                                없음

                                            </span>

                                        <?
                                        }
                                        ?>

                                    </td>



                                    <!-- COLOR -->

                                    <td>

                                        <div class="main-exhibition-colors">

                                            <span>

                                                띠

                                                <strong>

                                                    <?=main_exhibition_list_h(
                                                        $exhibition['band_color']
                                                    )?>

                                                </strong>

                                            </span>


                                            <span>

                                                글자

                                                <strong>

                                                    <?=main_exhibition_list_h(
                                                        $exhibition['text_color']
                                                    )?>

                                                </strong>

                                            </span>

                                        </div>

                                    </td>



                                    <!-- ORDER -->

                                    <td class="main-exhibition-order">

                                        <?=number_format(
                                            intval(
                                                $exhibition['sort_order']
                                            )
                                        )?>

                                    </td>



                                    <!-- ACTIVE -->

                                    <td>

                                        <?
                                        if(
                                            intval(
                                                $exhibition['is_active']
                                            ) == 1
                                        ){
                                        ?>

                                            <span class="main-exhibition-status active">

                                                <i></i>

                                                노출

                                            </span>

                                        <?
                                        }else{
                                        ?>

                                            <span class="main-exhibition-status inactive">

                                                <i></i>

                                                비노출

                                            </span>

                                        <?
                                        }
                                        ?>

                                    </td>



                                    <!-- MANAGE -->

                                    <td>

                                        <div class="main-exhibition-manage">


                                            <a
                                                href="/manage/main_exhibition_write?id=<?=intval($exhibition['id'])?>"
                                                class="main-exhibition-manage-btn"
                                                title="수정"
                                            >

                                                <i class="ri-edit-line"></i>

                                            </a>



                                            <button
                                                type="button"
                                                class="main-exhibition-manage-btn main-exhibition-delete-btn"
                                                data-id="<?=intval($exhibition['id'])?>"
                                                data-name="<?=main_exhibition_list_h($work_title)?>"
                                                title="삭제"
                                            >

                                                <i class="ri-delete-bin-line"></i>

                                            </button>


                                        </div>

                                    </td>


                                </tr>


                            <?
                                $list_no--;
                            }
                            ?>


                        <?
                        }else{
                        ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="main-exhibition-empty"
                                >

                                    <i class="ri-layout-grid-line"></i>

                                    <strong>
                                        등록된 메인 전시가 없습니다.
                                    </strong>

                                    <span>
                                        메인에 노출할 프로젝트를 등록해주세요.
                                    </span>

                                </td>

                            </tr>

                        <?
                        }
                        ?>


                    </tbody>

                </table>

            </div>



            <!-- =================================================
                 PAGING
            ================================================== -->

            <?
            if(
                $total_page > 1
            ){
            ?>

                <div class="pagination">


                    <!-- FIRST -->

                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="/manage/main_exhibition_list?page=1<?=$base_query?>"
                            class="page-arrow"
                        >

                            <i class="ri-skip-left-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="page-arrow disabled">

                            <i class="ri-skip-left-line"></i>

                        </span>

                    <?
                    }
                    ?>



                    <!-- PREV -->

                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="/manage/main_exhibition_list?page=<?=($page - 1)?><?=$base_query?>"
                            class="page-arrow"
                        >

                            <i class="ri-arrow-left-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="page-arrow disabled">

                            <i class="ri-arrow-left-s-line"></i>

                        </span>

                    <?
                    }
                    ?>



                    <!-- NUMBER -->

                    <?
                    for(
                        $i = $start_page;
                        $i <= $end_page;
                        $i++
                    ){
                    ?>

                        <?
                        if(
                            $i == $page
                        ){
                        ?>

                            <span class="active">

                                <?=$i?>

                            </span>

                        <?
                        }else{
                        ?>

                            <a
                                href="/manage/main_exhibition_list?page=<?=$i?><?=$base_query?>"
                            >

                                <?=$i?>

                            </a>

                        <?
                        }
                        ?>

                    <?
                    }
                    ?>



                    <!-- NEXT -->

                    <?
                    if(
                        $page < $total_page
                    ){
                    ?>

                        <a
                            href="/manage/main_exhibition_list?page=<?=($page + 1)?><?=$base_query?>"
                            class="page-arrow"
                        >

                            <i class="ri-arrow-right-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="page-arrow disabled">

                            <i class="ri-arrow-right-s-line"></i>

                        </span>

                    <?
                    }
                    ?>



                    <!-- LAST -->

                    <?
                    if(
                        $page < $total_page
                    ){
                    ?>

                        <a
                            href="/manage/main_exhibition_list?page=<?=$total_page?><?=$base_query?>"
                            class="page-arrow"
                        >

                            <i class="ri-skip-right-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="page-arrow disabled">

                            <i class="ri-skip-right-line"></i>

                        </span>

                    <?
                    }
                    ?>


                </div>

            <?
            }
            ?>


        </div>


    </div>

</main>



<script>

/*
 * =========================================================
 * DELETE
 * =========================================================
 */

var exhibitionDeleteButtons =
    document.querySelectorAll(
        '.main-exhibition-delete-btn'
    );


for(
    var i = 0;
    i < exhibitionDeleteButtons.length;
    i++
){

    exhibitionDeleteButtons[i].addEventListener(
        'click',
        function(){

            var exhibitionId =
                this.getAttribute(
                    'data-id'
                );


            var exhibitionName =
                this.getAttribute(
                    'data-name'
                );


            confirm(
                exhibitionName + ' 메인 전시를 삭제하시겠습니까?',
                function(){

                    deleteMainExhibition(
                        exhibitionId
                    );

                }
            );

        }
    );

}



/*
 * =========================================================
 * DELETE
 * =========================================================
 */

function deleteMainExhibition(exhibitionId)
{

    var xhr =
        new XMLHttpRequest();


    var formData =
        new FormData();


    formData.append(
        'id',
        exhibitionId
    );


    xhr.open(
        'POST',
        '/manage/proc/main_exhibition_delete',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){
                return;
            }


            if(
                xhr.status != 200
            ){

                alert(
                    '삭제 처리 중 오류가 발생했습니다.'
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
                    '삭제 처리 중 오류가 발생했습니다.'
                );

                return;

            }


            if(!result.code){

                alert(
                    result.msg
                        ? result.msg
                        : '삭제하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : '삭제되었습니다.',
                function(){

                    location.reload();

                }
            );

        };


    xhr.send(
        formData
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>