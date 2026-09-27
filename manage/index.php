<?
/*
 * =========================================================
 * WEEKENDS CMS
 * Dashboard
 *
 * FILE
 * /manage/index.php
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function dashboard_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * WORKS SUMMARY
 * =========================================================
 */

$works_summary =
    array(
        'total'     => 0,
        'published' => 0,
        'draft'     => 0
    );


$query = "

    SELECT

        COUNT(*) AS total,

        SUM(
            CASE
                WHEN status = 'published'
                THEN 1
                ELSE 0
            END
        ) AS published,

        SUM(
            CASE
                WHEN status = 'draft'
                THEN 1
                ELSE 0
            END
        ) AS draft

    FROM works

    WHERE
        deleted_at IS NULL

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

    $works_summary['total'] =
        intval(
            $data['total']
        );


    $works_summary['published'] =
        intval(
            $data['published']
        );


    $works_summary['draft'] =
        intval(
            $data['draft']
        );

}



/*
 * =========================================================
 * NEWS SUMMARY
 *
 * 내부 board code = BLOG
 * 관리자 노출명 = NEWS
 * =========================================================
 */

$news_summary =
    array(
        'total'     => 0,
        'published' => 0,
        'draft'     => 0
    );


$query = "

    SELECT

        COUNT(*) AS total,

        SUM(
            CASE
                WHEN p.status = 'published'
                THEN 1
                ELSE 0
            END
        ) AS published,

        SUM(
            CASE
                WHEN p.status = 'draft'
                THEN 1
                ELSE 0
            END
        ) AS draft

    FROM posts p

    INNER JOIN boards b
        ON b.id = p.board_id
        AND b.code = 'BLOG'

    WHERE
        p.deleted_at IS NULL

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

    $news_summary['total'] =
        intval(
            $data['total']
        );


    $news_summary['published'] =
        intval(
            $data['published']
        );


    $news_summary['draft'] =
        intval(
            $data['draft']
        );

}



/*
 * =========================================================
 * INQUIRY SUMMARY
 * =========================================================
 */

$inquiry_summary =
    array(
        'total'       => 0,
        'received'    => 0,
        'in_progress' => 0,
        'today'       => 0
    );


$query = "

    SELECT

        COUNT(*) AS total,

        SUM(
            CASE
                WHEN status = 'received'
                THEN 1
                ELSE 0
            END
        ) AS received,

        SUM(
            CASE
                WHEN status = 'in_progress'
                THEN 1
                ELSE 0
            END
        ) AS in_progress,

        SUM(
            CASE
                WHEN DATE(created_at) = CURDATE()
                THEN 1
                ELSE 0
            END
        ) AS today

    FROM inquiries

    WHERE
        deleted_at IS NULL

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

    $inquiry_summary['total'] =
        intval(
            $data['total']
        );


    $inquiry_summary['received'] =
        intval(
            $data['received']
        );


    $inquiry_summary['in_progress'] =
        intval(
            $data['in_progress']
        );


    $inquiry_summary['today'] =
        intval(
            $data['today']
        );

}



/*
 * =========================================================
 * FILM SUMMARY
 * =========================================================
 */

$film_summary =
    array(
        'total'  => 0,
        'active' => 0
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
        ) AS active

    FROM films

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

    $film_summary['total'] =
        intval(
            $data['total']
        );


    $film_summary['active'] =
        intval(
            $data['active']
        );

}



/*
 * =========================================================
 * RECENT INQUIRIES
 * =========================================================
 */

$recent_inquiries =
    array();


$query = "

    SELECT

        id,
        name,
        company_name,
        email,
        status,
        created_at

    FROM inquiries

    WHERE
        deleted_at IS NULL

    ORDER BY
        id DESC

    LIMIT 6

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

    $recent_inquiries[] =
        $data;

}



/*
 * =========================================================
 * RECENT CONTENT
 * =========================================================
 */

$recent_contents =
    array();


$query = "

    SELECT *

    FROM
    (

        SELECT

            'WORKS' AS content_type,
            w.id AS content_id,

            CASE
                WHEN TRIM(IFNULL(w.list_title, '')) != ''
                    THEN w.list_title
                ELSE w.title
            END AS title,

            w.status,
            w.updated_at,
            w.created_at

        FROM works w

        WHERE
            w.deleted_at IS NULL



        UNION ALL



        SELECT

            'NEWS' AS content_type,
            p.id AS content_id,
            p.title,
            p.status,
            p.updated_at,
            p.created_at

        FROM posts p

        INNER JOIN boards b
            ON b.id = p.board_id
            AND b.code = 'BLOG'

        WHERE
            p.deleted_at IS NULL

    ) content_data

    ORDER BY
        updated_at DESC,
        content_id DESC

    LIMIT 7

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

    $recent_contents[] =
        $data;

}



/*
 * =========================================================
 * SEO SUMMARY
 * =========================================================
 */

$seo_summary =
    array(
        'total'      => 0,
        'complete'   => 0,
        'incomplete' => 0,
        'unset'      => 0
    );


$query = "

    SELECT

        seo_state,
        COUNT(*) AS cnt

    FROM
    (

        SELECT

            CASE

                WHEN
                    TRIM(
                        IFNULL(
                            ws.seo_title,
                            ''
                        )
                    ) = ''

                    AND

                    TRIM(
                        IFNULL(
                            ws.meta_description,
                            ''
                        )
                    ) = ''

                    AND

                    ws.og_media_id IS NULL

                THEN 'unset'


                WHEN
                    TRIM(
                        IFNULL(
                            ws.seo_title,
                            ''
                        )
                    ) != ''

                    AND

                    TRIM(
                        IFNULL(
                            ws.meta_description,
                            ''
                        )
                    ) != ''

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

            CASE

                WHEN
                    TRIM(
                        IFNULL(
                            ps.seo_title,
                            ''
                        )
                    ) = ''

                    AND

                    TRIM(
                        IFNULL(
                            ps.meta_description,
                            ''
                        )
                    ) = ''

                    AND

                    ps.og_media_id IS NULL

                THEN 'unset'


                WHEN
                    TRIM(
                        IFNULL(
                            ps.seo_title,
                            ''
                        )
                    ) != ''

                    AND

                    TRIM(
                        IFNULL(
                            ps.meta_description,
                            ''
                        )
                    ) != ''

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

    $seo_state =
        $data['seo_state'];


    if(
        isset(
            $seo_summary[$seo_state]
        )
    ){

        $seo_summary[$seo_state] =
            intval(
                $data['cnt']
            );

    }


    $seo_summary['total'] +=
        intval(
            $data['cnt']
        );

}



/*
 * =========================================================
 * SEO RATE
 * =========================================================
 */

$seo_rate =
    0;


if(
    $seo_summary['total'] > 0
){

    $seo_rate =
        round(
            (
                $seo_summary['complete'] /
                $seo_summary['total']
            ) * 100
        );

}



/*
 * =========================================================
 * INQUIRY CONFIG
 * =========================================================
 */

$inquiry_status_config =
    array(

        'received' => array(
            'label' => '접수',
            'class' => 'received'
        ),

        'confirmed' => array(
            'label' => '확인',
            'class' => 'confirmed'
        ),

        'in_progress' => array(
            'label' => '진행중',
            'class' => 'progress'
        ),

        'completed' => array(
            'label' => '완료',
            'class' => 'completed'
        )

    );



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
    href="/manage/css/dashboard.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="dashboard-head">

            <div>

                <span class="dashboard-eyebrow">
                    WEEKENDS CMS
                </span>

                <h2>
                    Dashboard
                </h2>

                <p>
                    콘텐츠와 문의 현황을 한눈에 확인하세요.
                </p>

            </div>


            <div class="dashboard-head-actions">

                <a
                    href="/manage/works_write"
                    class="btn btn-white"
                >

                    <i class="ri-add-line"></i>

                    WORKS 등록

                </a>


                <a
                    href="/manage/blog_write"
                    class="btn btn-primary"
                >

                    <i class="ri-add-line"></i>

                    NEWS 등록

                </a>

            </div>

        </div>



        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="dashboard-summary">


            <!-- WORKS -->

            <a
                href="/manage/works_list"
                class="dashboard-summary-card"
            >

                <div class="dashboard-summary-top">

                    <span class="dashboard-summary-icon">

                        <i class="ri-box-3-line"></i>

                    </span>


                    <span class="dashboard-summary-arrow">

                        <i class="ri-arrow-right-up-line"></i>

                    </span>

                </div>


                <div class="dashboard-summary-body">

                    <span>
                        WORKS
                    </span>

                    <strong>
                        <?=number_format($works_summary['total'])?>
                    </strong>

                </div>


                <div class="dashboard-summary-meta">

                    <span>

                        게시

                        <strong>
                            <?=number_format($works_summary['published'])?>
                        </strong>

                    </span>


                    <span>

                        임시저장

                        <strong>
                            <?=number_format($works_summary['draft'])?>
                        </strong>

                    </span>

                </div>

            </a>



            <!-- NEWS -->

            <a
                href="/manage/blog_list"
                class="dashboard-summary-card"
            >

                <div class="dashboard-summary-top">

                    <span class="dashboard-summary-icon">

                        <i class="ri-article-line"></i>

                    </span>


                    <span class="dashboard-summary-arrow">

                        <i class="ri-arrow-right-up-line"></i>

                    </span>

                </div>


                <div class="dashboard-summary-body">

                    <span>
                        NEWS
                    </span>

                    <strong>
                        <?=number_format($news_summary['total'])?>
                    </strong>

                </div>


                <div class="dashboard-summary-meta">

                    <span>

                        게시

                        <strong>
                            <?=number_format($news_summary['published'])?>
                        </strong>

                    </span>


                    <span>

                        임시저장

                        <strong>
                            <?=number_format($news_summary['draft'])?>
                        </strong>

                    </span>

                </div>

            </a>



            <!-- INQUIRY -->

            <a
                href="/manage/inquiry_list"
                class="dashboard-summary-card dashboard-summary-card-important"
            >

                <div class="dashboard-summary-top">

                    <span class="dashboard-summary-icon">

                        <i class="ri-mail-line"></i>

                    </span>


                    <span class="dashboard-summary-arrow">

                        <i class="ri-arrow-right-up-line"></i>

                    </span>

                </div>


                <div class="dashboard-summary-body">

                    <span>
                        INQUIRY
                    </span>

                    <strong>
                        <?=number_format($inquiry_summary['received'])?>
                    </strong>

                </div>


                <div class="dashboard-summary-meta">

                    <span>

                        오늘

                        <strong>
                            <?=number_format($inquiry_summary['today'])?>
                        </strong>

                    </span>


                    <span>

                        진행중

                        <strong>
                            <?=number_format($inquiry_summary['in_progress'])?>
                        </strong>

                    </span>

                </div>

            </a>



            <!-- FILM -->

            <a
                href="/manage/film_list"
                class="dashboard-summary-card"
            >

                <div class="dashboard-summary-top">

                    <span class="dashboard-summary-icon">

                        <i class="ri-movie-line"></i>

                    </span>


                    <span class="dashboard-summary-arrow">

                        <i class="ri-arrow-right-up-line"></i>

                    </span>

                </div>


                <div class="dashboard-summary-body">

                    <span>
                        FILM
                    </span>

                    <strong>
                        <?=number_format($film_summary['total'])?>
                    </strong>

                </div>


                <div class="dashboard-summary-meta">

                    <span>

                        노출중

                        <strong>
                            <?=number_format($film_summary['active'])?>
                        </strong>

                    </span>

                </div>

            </a>


        </div>



        <!-- =================================================
             CONTENT GRID
        ================================================== -->

        <div class="dashboard-grid">


            <!-- =================================================
                 LEFT
            ================================================== -->

            <div class="dashboard-main">


                <!-- =================================================
                     INQUIRY
                ================================================== -->

                <div class="card dashboard-card">

                    <div class="card-header dashboard-card-head">

                        <div>

                            <h3>
                                최근 문의
                            </h3>

                            <p>
                                최근 접수된 프로젝트 문의입니다.
                            </p>

                        </div>


                        <a href="/manage/inquiry_list">

                            전체보기

                            <i class="ri-arrow-right-line"></i>

                        </a>

                    </div>


                    <div class="card-body dashboard-card-body">


                        <?
                        if(
                            count($recent_inquiries) > 0
                        ){
                        ?>

                            <div class="dashboard-inquiry-list">


                                <?
                                foreach(
                                    $recent_inquiries as $inquiry
                                ){

                                    $status_data =
                                        isset(
                                            $inquiry_status_config[
                                                $inquiry['status']
                                            ]
                                        )
                                            ? $inquiry_status_config[
                                                $inquiry['status']
                                            ]
                                            : array(
                                                'label' => $inquiry['status'],
                                                'class' => ''
                                            );
                                ?>

                                    <a
                                        href="/manage/inquiry_view?id=<?=intval($inquiry['id'])?>"
                                        class="dashboard-inquiry-item"
                                    >

                                        <div class="dashboard-inquiry-content">

                                            <div class="dashboard-inquiry-title">

                                                <strong>

                                                    <?=dashboard_h(
                                                        $inquiry['name']
                                                    )?>

                                                </strong>


                                                <span>

                                                    <?=dashboard_h(
                                                        $inquiry['company_name']
                                                    )?>

                                                </span>

                                            </div>


                                            <div class="dashboard-inquiry-meta">

                                                <span>

                                                    <?=dashboard_h(
                                                        $inquiry['email']
                                                    )?>

                                                </span>


                                                <span>

                                                    <?=dashboard_h(
                                                        substr(
                                                            $inquiry['created_at'],
                                                            0,
                                                            16
                                                        )
                                                    )?>

                                                </span>

                                            </div>

                                        </div>


                                        <div class="dashboard-inquiry-side">

                                            <span class="dashboard-inquiry-status <?=$status_data['class']?>">

                                                <?=dashboard_h(
                                                    $status_data['label']
                                                )?>

                                            </span>


                                            <i class="ri-arrow-right-s-line"></i>

                                        </div>

                                    </a>

                                <?
                                }
                                ?>


                            </div>

                        <?
                        }else{
                        ?>

                            <div class="dashboard-empty">

                                <i class="ri-mail-open-line"></i>

                                <span>
                                    접수된 문의가 없습니다.
                                </span>

                            </div>

                        <?
                        }
                        ?>


                    </div>

                </div>



                <!-- =================================================
                     RECENT CONTENT
                ================================================== -->

                <div class="card dashboard-card">

                    <div class="card-header dashboard-card-head">

                        <div>

                            <h3>
                                최근 콘텐츠
                            </h3>

                            <p>
                                최근 등록 또는 수정된 콘텐츠입니다.
                            </p>

                        </div>

                    </div>


                    <div class="card-body dashboard-card-body">


                        <?
                        if(
                            count($recent_contents) > 0
                        ){
                        ?>

                            <div class="dashboard-content-list">


                                <?
                                foreach(
                                    $recent_contents as $content
                                ){

                                    if(
                                        $content['content_type'] == 'WORKS'
                                    ){

                                        $content_url =
                                            '/manage/works_write?id='.
                                            intval(
                                                $content['content_id']
                                            );

                                    }else{

                                        $content_url =
                                            '/manage/blog_write?id='.
                                            intval(
                                                $content['content_id']
                                            );

                                    }



                                    $status_label =
                                        $content['status'];


                                    if(
                                        $content['status'] == 'published'
                                    ){

                                        $status_label =
                                            '게시';

                                    }
                                    else if(
                                        $content['status'] == 'draft'
                                    ){

                                        $status_label =
                                            '임시저장';

                                    }
                                    else if(
                                        $content['status'] == 'archived'
                                    ){

                                        $status_label =
                                            '보관';

                                    }
                                ?>

                                    <a
                                        href="<?=$content_url?>"
                                        class="dashboard-content-item"
                                    >

                                        <span class="dashboard-content-type <?=strtolower($content['content_type'])?>">

                                            <?=dashboard_h(
                                                $content['content_type']
                                            )?>

                                        </span>


                                        <div class="dashboard-content-title">

                                            <strong>

                                                <?
                                                if(
                                                    trim(
                                                        $content['title']
                                                    ) != ''
                                                ){
                                                ?>

                                                    <?=dashboard_h(
                                                        $content['title']
                                                    )?>

                                                <?
                                                }else{
                                                ?>

                                                    제목 없음

                                                <?
                                                }
                                                ?>

                                            </strong>


                                            <span>

                                                <?=dashboard_h(
                                                    substr(
                                                        $content['updated_at'],
                                                        0,
                                                        16
                                                    )
                                                )?>

                                            </span>

                                        </div>


                                        <span class="dashboard-content-status <?=$content['status']?>">

                                            <?=dashboard_h(
                                                $status_label
                                            )?>

                                        </span>


                                        <i class="ri-arrow-right-s-line"></i>

                                    </a>

                                <?
                                }
                                ?>


                            </div>

                        <?
                        }else{
                        ?>

                            <div class="dashboard-empty">

                                <i class="ri-file-list-3-line"></i>

                                <span>
                                    등록된 콘텐츠가 없습니다.
                                </span>

                            </div>

                        <?
                        }
                        ?>


                    </div>

                </div>


            </div>



            <!-- =================================================
                 SIDE
            ================================================== -->

            <div class="dashboard-side">


                <!-- =================================================
                     SEO
                ================================================== -->

                <div class="card dashboard-card">

                    <div class="card-header dashboard-card-head">

                        <div>

                            <h3>
                                SEO 상태
                            </h3>

                            <p>
                                WORKS · NEWS
                            </p>

                        </div>


                        <a href="/manage/seo_list">

                            점검

                            <i class="ri-arrow-right-line"></i>

                        </a>

                    </div>


                    <div class="card-body">


                        <div class="dashboard-seo-score">

                            <div class="dashboard-seo-score-number">

                                <strong>
                                    <?=$seo_rate?>
                                </strong>

                                <span>
                                    %
                                </span>

                            </div>


                            <p>
                                SEO 설정 완료율
                            </p>

                        </div>



                        <div class="dashboard-seo-progress">

                            <span>

                                <i
                                    style="width:<?=$seo_rate?>%"
                                ></i>

                            </span>

                        </div>



                        <div class="dashboard-seo-list">


                            <div>

                                <span>
                                    정상
                                </span>

                                <strong class="complete">

                                    <?=number_format(
                                        $seo_summary['complete']
                                    )?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    보완 필요
                                </span>

                                <strong class="incomplete">

                                    <?=number_format(
                                        $seo_summary['incomplete']
                                    )?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    미설정
                                </span>

                                <strong class="unset">

                                    <?=number_format(
                                        $seo_summary['unset']
                                    )?>

                                </strong>

                            </div>


                        </div>


                    </div>

                </div>



                <!-- =================================================
                     QUICK
                ================================================== -->

                <div class="card dashboard-card">

                    <div class="card-header dashboard-card-head">

                        <div>

                            <h3>
                                빠른 메뉴
                            </h3>

                        </div>

                    </div>


                    <div class="card-body">


                        <div class="dashboard-quick">


                            <a href="/manage/works_write">

                                <i class="ri-box-3-line"></i>

                                <span>
                                    WORKS 등록
                                </span>

                            </a>


                            <a href="/manage/blog_write">

                                <i class="ri-article-line"></i>

                                <span>
                                    NEWS 등록
                                </span>

                            </a>


                            <a href="/manage/film_write">

                                <i class="ri-movie-line"></i>

                                <span>
                                    FILM 등록
                                </span>

                            </a>


                            <a href="/manage/inquiry_list">

                                <i class="ri-mail-line"></i>

                                <span>
                                    문의 확인
                                </span>

                            </a>


                            <a href="/manage/seo_list">

                                <i class="ri-search-eye-line"></i>

                                <span>
                                    SEO 점검
                                </span>

                            </a>


                            <?
                            if(
                                isset($_SESSION['ADMIN_ROLE']) &&
                                (
                                    $_SESSION['ADMIN_ROLE'] == 'super' ||
                                    $_SESSION['ADMIN_ROLE'] == 'admin'
                                )
                            ){
                            ?>

                                <a href="/manage/setting">

                                    <i class="ri-settings-3-line"></i>

                                    <span>
                                        기본 설정
                                    </span>

                                </a>

                            <?
                            }
                            ?>


                        </div>


                    </div>

                </div>


            </div>


        </div>


    </div>

</main>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>