<?
/*
 * =========================================================
 * WEEKENDS CMS
 * SEO 관리
 *
 * FILE
 * /manage/seo_list.php
 *
 * URL
 * /manage/seo_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function seo_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * PARAM
 * =========================================================
 */

$page =
    isset($_GET['page'])
        ? intval($_GET['page'])
        : 1;


if($page < 1){
    $page = 1;
}


$limit =
    isset($_GET['limit'])
        ? intval($_GET['limit'])
        : 20;


if(
    $limit != 20 &&
    $limit != 50 &&
    $limit != 100
){
    $limit = 20;
}


$type =
    isset($_GET['type'])
        ? strtoupper(
            trim($_GET['type'])
        )
        : '';


if(
    $type != '' &&
    $type != 'BLOG' &&
    $type != 'WORKS'
){
    $type = '';
}


$seo_status =
    isset($_GET['seo_status'])
        ? trim($_GET['seo_status'])
        : '';


if(
    $seo_status != '' &&
    $seo_status != 'complete' &&
    $seo_status != 'incomplete' &&
    $seo_status != 'unset'
){
    $seo_status = '';
}


$keyword =
    isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';



/*
 * =========================================================
 * BASE QUERY
 * =========================================================
 */

$base_query = "

    SELECT

        'WORKS' AS content_type,

        w.id AS content_id,

        CASE
            WHEN TRIM(IFNULL(w.list_title, '')) != ''
                THEN w.list_title
            ELSE w.title
        END AS content_title,

        w.slug AS content_slug,
        w.status AS content_status,
        w.visibility AS content_visibility,

        ws.seo_title,
        ws.meta_description,
        ws.meta_keywords,
        ws.canonical_url,
        ws.og_title,
        ws.og_description,
        ws.og_media_id,

        IFNULL(ws.allow_index, 1) AS allow_index,
        IFNULL(ws.nofollow, 0) AS nofollow,

        ws.updated_at AS seo_updated_at,
        w.updated_at AS content_updated_at,

        CASE

            WHEN
                TRIM(IFNULL(ws.seo_title, '')) = ''
                AND
                TRIM(IFNULL(ws.meta_description, '')) = ''
                AND
                ws.og_media_id IS NULL
            THEN 'unset'

            WHEN
                TRIM(IFNULL(ws.seo_title, '')) != ''
                AND
                TRIM(IFNULL(ws.meta_description, '')) != ''
                AND
                ws.og_media_id IS NOT NULL
            THEN 'complete'

            ELSE 'incomplete'

        END AS seo_state

    FROM works w

    LEFT JOIN work_seo ws
        ON ws.work_id = w.id

    WHERE
        w.deleted_at IS NULL



    UNION ALL



    SELECT

        'BLOG' AS content_type,

        p.id AS content_id,
        p.title AS content_title,
        p.slug AS content_slug,
        p.status AS content_status,
        p.visibility AS content_visibility,

        ps.seo_title,
        ps.meta_description,
        ps.meta_keywords,
        ps.canonical_url,
        ps.og_title,
        ps.og_description,
        ps.og_media_id,

        IFNULL(ps.allow_index, 1) AS allow_index,
        IFNULL(ps.nofollow, 0) AS nofollow,

        ps.updated_at AS seo_updated_at,
        p.updated_at AS content_updated_at,

        CASE

            WHEN
                TRIM(IFNULL(ps.seo_title, '')) = ''
                AND
                TRIM(IFNULL(ps.meta_description, '')) = ''
                AND
                ps.og_media_id IS NULL
            THEN 'unset'

            WHEN
                TRIM(IFNULL(ps.seo_title, '')) != ''
                AND
                TRIM(IFNULL(ps.meta_description, '')) != ''
                AND
                ps.og_media_id IS NOT NULL
            THEN 'complete'

            ELSE 'incomplete'

        END AS seo_state

    FROM posts p

    INNER JOIN boards b
        ON b.id = p.board_id
        AND b.code = 'BLOG'

    LEFT JOIN post_seo ps
        ON ps.post_id = p.id

    WHERE
        p.deleted_at IS NULL

";



/*
 * =========================================================
 * WHERE
 * =========================================================
 */

$where =
    array();


$params =
    array();


if(
    $type != ''
){

    $where[] =
        'seo_data.content_type = ?';


    $params[] =
        $type;

}


if(
    $seo_status != ''
){

    $where[] =
        'seo_data.seo_state = ?';


    $params[] =
        $seo_status;

}


if(
    $keyword != ''
){

    $where[] = "

        (
            seo_data.content_title LIKE ?
            OR seo_data.content_slug LIKE ?
            OR seo_data.seo_title LIKE ?
            OR seo_data.meta_description LIKE ?
        )

    ";


    $search_keyword =
        '%'.$keyword.'%';


    $params[] = $search_keyword;
    $params[] = $search_keyword;
    $params[] = $search_keyword;
    $params[] = $search_keyword;

}


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
        'all'        => 0,
        'complete'   => 0,
        'incomplete' => 0,
        'unset'      => 0
    );


$query = "

    SELECT

        seo_state,
        COUNT(*) AS cnt

    FROM (

        ".$base_query."

    ) seo_data

    GROUP BY
        seo_state

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

    $state =
        $data['seo_state'];


    if(
        isset(
            $summary[$state]
        )
    ){

        $summary[$state] =
            intval(
                $data['cnt']
            );

    }


    $summary['all'] +=
        intval(
            $data['cnt']
        );

}



/*
 * =========================================================
 * TOTAL
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM (

        ".$base_query."

    ) seo_data

    ".$where_sql."

";


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


$total_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$total =
    $total_data
        ? intval($total_data['total'])
        : 0;



/*
 * =========================================================
 * PAGING
 * =========================================================
 */

$total_page =
    ceil(
        $total /
        $limit
    );


if(
    $total_page < 1
){
    $total_page = 1;
}


if(
    $page >
    $total_page
){
    $page = $total_page;
}


$offset =
    ($page - 1) *
    $limit;



/*
 * =========================================================
 * LIST
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT
        *

    FROM (

        ".$base_query."

    ) seo_data

    ".$where_sql."

    ORDER BY

        CASE seo_data.seo_state

            WHEN 'unset'
                THEN 1

            WHEN 'incomplete'
                THEN 2

            WHEN 'complete'
                THEN 3

            ELSE 4

        END ASC,

        seo_data.content_updated_at DESC,
        seo_data.content_id DESC

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

    $list[] =
        $data;

}



/*
 * =========================================================
 * PAGING
 * =========================================================
 */

$page_start =
    floor(
        ($page - 1) / 10
    ) * 10 + 1;


$page_end =
    $page_start + 9;


if(
    $page_end >
    $total_page
){
    $page_end = $total_page;
}



/*
 * =========================================================
 * URL
 * =========================================================
 */

function seo_list_url(
    $page,
    $limit,
    $type,
    $seo_status,
    $keyword
){

    return

        '/manage/seo_list?'.

        http_build_query(
            array(
                'page'       => $page,
                'limit'      => $limit,
                'type'       => $type,
                'seo_status' => $seo_status,
                'keyword'    => $keyword
            )
        );

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/seo.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="seo-page-head">

            <div class="seo-page-title">

                <span>
                    사이트 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    SEO 관리
                </h2>

            </div>

        </div>



        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="seo-summary-grid">


            <a
                href="/manage/seo_list"
                class="seo-summary-item <?=($seo_status == '' ? 'active' : '')?>"
            >

                <span>
                    전체
                </span>

                <strong>
                    <?=number_format($summary['all'])?>
                </strong>

            </a>



            <a
                href="/manage/seo_list?seo_status=complete"
                class="seo-summary-item complete <?=($seo_status == 'complete' ? 'active' : '')?>"
            >

                <span>
                    정상
                </span>

                <strong>
                    <?=number_format($summary['complete'])?>
                </strong>

            </a>



            <a
                href="/manage/seo_list?seo_status=incomplete"
                class="seo-summary-item incomplete <?=($seo_status == 'incomplete' ? 'active' : '')?>"
            >

                <span>
                    보완 필요
                </span>

                <strong>
                    <?=number_format($summary['incomplete'])?>
                </strong>

            </a>



            <a
                href="/manage/seo_list?seo_status=unset"
                class="seo-summary-item unset <?=($seo_status == 'unset' ? 'active' : '')?>"
            >

                <span>
                    미설정
                </span>

                <strong>
                    <?=number_format($summary['unset'])?>
                </strong>

            </a>


        </div>



        <!-- =================================================
             SEARCH
        ================================================== -->

        <div class="card">

            <div class="card-header">

                <h3>
                    검색조건
                </h3>

            </div>


            <div class="card-body">

                <form
                    method="get"
                    action="/manage/seo_list"
                >

                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="seo-search-grid">


                        <div class="form-group">

                            <label>
                                구분
                            </label>

                            <select name="type">

                                <option value="">
                                    전체
                                </option>

                                <option
                                    value="BLOG"
                                    <?=$type == 'BLOG' ? 'selected' : ''?>
                                >
                                    BLOG
                                </option>

                                <option
                                    value="WORKS"
                                    <?=$type == 'WORKS' ? 'selected' : ''?>
                                >
                                    WORKS
                                </option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                SEO 상태
                            </label>

                            <select name="seo_status">

                                <option value="">
                                    전체
                                </option>

                                <option
                                    value="complete"
                                    <?=$seo_status == 'complete' ? 'selected' : ''?>
                                >
                                    정상
                                </option>

                                <option
                                    value="incomplete"
                                    <?=$seo_status == 'incomplete' ? 'selected' : ''?>
                                >
                                    보완 필요
                                </option>

                                <option
                                    value="unset"
                                    <?=$seo_status == 'unset' ? 'selected' : ''?>
                                >
                                    미설정
                                </option>

                            </select>

                        </div>



                        <div class="form-group seo-search-keyword">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=seo_h($keyword)?>"
                                placeholder="콘텐츠명, URL, SEO Title, Description"
                            >

                        </div>


                    </div>


                    <div class="seo-search-action">

                        <a
                            href="/manage/seo_list"
                            class="btn btn-white"
                        >
                            초기화
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="ri-search-line"></i>

                            검색

                        </button>

                    </div>

                </form>

            </div>

        </div>



        <!-- =================================================
             LIST
        ================================================== -->

        <div class="card seo-list-card">

            <div class="card-header">

                <h3>
                    SEO 점검 목록
                </h3>

            </div>


            <div class="card-body">


                <div class="seo-toolbar">

                    <div class="seo-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        건

                    </div>


                    <select id="seoLimit">

                        <option
                            value="20"
                            <?=$limit == 20 ? 'selected' : ''?>
                        >
                            20개씩 보기
                        </option>

                        <option
                            value="50"
                            <?=$limit == 50 ? 'selected' : ''?>
                        >
                            50개씩 보기
                        </option>

                        <option
                            value="100"
                            <?=$limit == 100 ? 'selected' : ''?>
                        >
                            100개씩 보기
                        </option>

                    </select>

                </div>



                <div class="seo-table-wrap">

                    <table class="seo-table">

                        <thead>

                            <tr>

                                <th width="70">
                                    구분
                                </th>

                                <th>
                                    콘텐츠
                                </th>

                                <th width="150">
                                    SEO TITLE
                                </th>

                                <th width="150">
                                    DESCRIPTION
                                </th>

                                <th width="90">
                                    OG
                                </th>

                                <th width="90">
                                    INDEX
                                </th>

                                <th width="105">
                                    SEO 상태
                                </th>

                                <th width="100">
                                    관리
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?
                        if(
                            count($list) > 0
                        ){

                            foreach(
                                $list as $data
                            ){

                                $seo_title_ok =
                                    trim(
                                        $data['seo_title']
                                    ) != '';


                                $description_ok =
                                    trim(
                                        $data['meta_description']
                                    ) != '';


                                $og_ok =
                                    intval(
                                        $data['og_media_id']
                                    ) > 0;


                                $edit_url =
                                    '';


                                if(
                                    $data['content_type'] == 'BLOG'
                                ){

                                    $edit_url =
                                        '/manage/blog_write?id='.
                                        intval(
                                            $data['content_id']
                                        );

                                }else{

                                    $edit_url =
                                        '/manage/works_write?id='.
                                        intval(
                                            $data['content_id']
                                        );

                                }


                                $state_label =
                                    '';


                                if(
                                    $data['seo_state'] == 'complete'
                                ){

                                    $state_label =
                                        '정상';

                                }else if(
                                    $data['seo_state'] == 'incomplete'
                                ){

                                    $state_label =
                                        '보완필요';

                                }else{

                                    $state_label =
                                        '미설정';

                                }
                        ?>

                            <tr>


                                <!-- TYPE -->

                                <td>

                                    <span class="seo-type <?=$data['content_type'] == 'BLOG' ? 'blog' : 'works'?>">

                                        <?=seo_h(
                                            $data['content_type']
                                        )?>

                                    </span>

                                </td>



                                <!-- CONTENT -->

                                <td>

                                    <div class="seo-content">

                                        <a
                                            href="<?=$edit_url?>"
                                            class="seo-content-title"
                                        >

                                            <?
                                            if(
                                                trim(
                                                    $data['content_title']
                                                ) != ''
                                            ){
                                            ?>

                                                <?=seo_h(
                                                    $data['content_title']
                                                )?>

                                            <?
                                            }else{
                                            ?>

                                                <span class="seo-empty-text">
                                                    제목 없음
                                                </span>

                                            <?
                                            }
                                            ?>

                                        </a>


                                        <div class="seo-content-meta">

                                            <?
                                            if(
                                                trim(
                                                    $data['content_slug']
                                                ) != ''
                                            ){
                                            ?>

                                                <span>

                                                    /<?=seo_h(
                                                        $data['content_slug']
                                                    )?>

                                                </span>

                                            <?
                                            }
                                            ?>


                                            <span>

                                                <?=seo_h(
                                                    strtoupper(
                                                        $data['content_status']
                                                    )
                                                )?>

                                            </span>

                                        </div>

                                    </div>

                                </td>



                                <!-- SEO TITLE -->

                                <td>

                                    <span class="seo-check <?=$seo_title_ok ? 'ok' : 'missing'?>">

                                        <i class="<?=$seo_title_ok ? 'ri-check-line' : 'ri-close-line'?>"></i>

                                        <?=$seo_title_ok ? '입력됨' : '미입력'?>

                                    </span>

                                </td>



                                <!-- DESCRIPTION -->

                                <td>

                                    <span class="seo-check <?=$description_ok ? 'ok' : 'missing'?>">

                                        <i class="<?=$description_ok ? 'ri-check-line' : 'ri-close-line'?>"></i>

                                        <?=$description_ok ? '입력됨' : '미입력'?>

                                    </span>

                                </td>



                                <!-- OG -->

                                <td>

                                    <span class="seo-check <?=$og_ok ? 'ok' : 'missing'?>">

                                        <i class="<?=$og_ok ? 'ri-check-line' : 'ri-close-line'?>"></i>

                                        <?=$og_ok ? '있음' : '없음'?>

                                    </span>

                                </td>



                                <!-- INDEX -->

                                <td>

                                    <?
                                    if(
                                        intval(
                                            $data['allow_index']
                                        ) == 1
                                    ){
                                    ?>

                                        <span class="seo-index index">
                                            INDEX
                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="seo-index noindex">
                                            NOINDEX
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- STATE -->

                                <td>

                                    <span class="seo-state <?=seo_h($data['seo_state'])?>">

                                        <?=seo_h(
                                            $state_label
                                        )?>

                                    </span>

                                </td>



                                <!-- MANAGE -->

                                <td>

                                    <a
                                        href="<?=$edit_url?>"
                                        class="btn btn-white btn-sm seo-edit-button"
                                    >

                                        이동

                                        <i class="ri-arrow-right-line"></i>

                                    </a>

                                </td>


                            </tr>

                        <?
                            }

                        }else{
                        ?>

                            <tr>

                                <td
                                    colspan="8"
                                    class="seo-list-empty"
                                >

                                    <i class="ri-search-eye-line"></i>

                                    조건에 해당하는 SEO 항목이 없습니다.

                                </td>

                            </tr>

                        <?
                        }
                        ?>

                        </tbody>

                    </table>

                </div>



                <!-- =================================================
                     PAGINATION
                ================================================== -->

                <div class="seo-pagination">


                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="<?=seo_h(
                                seo_list_url(
                                    $page - 1,
                                    $limit,
                                    $type,
                                    $seo_status,
                                    $keyword
                                )
                            )?>"
                        >

                            <i class="ri-arrow-left-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="disabled">

                            <i class="ri-arrow-left-s-line"></i>

                        </span>

                    <?
                    }
                    ?>



                    <?
                    for(
                        $i = $page_start;
                        $i <= $page_end;
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
                                href="<?=seo_h(
                                    seo_list_url(
                                        $i,
                                        $limit,
                                        $type,
                                        $seo_status,
                                        $keyword
                                    )
                                )?>"
                            >
                                <?=$i?>
                            </a>

                        <?
                        }
                        ?>

                    <?
                    }
                    ?>



                    <?
                    if(
                        $page <
                        $total_page
                    ){
                    ?>

                        <a
                            href="<?=seo_h(
                                seo_list_url(
                                    $page + 1,
                                    $limit,
                                    $type,
                                    $seo_status,
                                    $keyword
                                )
                            )?>"
                        >

                            <i class="ri-arrow-right-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="disabled">

                            <i class="ri-arrow-right-s-line"></i>

                        </span>

                    <?
                    }
                    ?>


                </div>


            </div>

        </div>


    </div>

</main>



<script>

/*
 * =========================================================
 * LIMIT
 * =========================================================
 */

var seoLimit =
    document.getElementById(
        'seoLimit'
    );


if(seoLimit){

    seoLimit.addEventListener(
        'change',
        function(){

            var url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                'limit',
                this.value
            );


            url.searchParams.set(
                'page',
                '1'
            );


            window.location.href =
                url.toString();

        }
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>