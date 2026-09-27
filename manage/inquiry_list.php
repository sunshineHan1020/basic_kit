<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 관리
 *
 * FILE
 * /manage/inquiry_list.php
 *
 * URL
 * /manage/inquiry_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


if($_SESSION['ADMIN_ROLE']!='super' && $_SESSION['ADMIN_ROLE']!='admin' ){
    page_move("/manage/");
    exit;
}


/*
 * =========================================================
 * Escape
 * =========================================================
 */

function inquiry_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * STATUS
 * =========================================================
 */

$status_config =
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
 * PARAM
 * =========================================================
 */

$page =
    isset($_GET['page'])
        ? intval($_GET['page'])
        : 1;


if($page < 1){

    $page =
        1;

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

    $limit =
        20;

}


$keyword =
    isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';


$status =
    isset($_GET['status'])
        ? trim($_GET['status'])
        : '';


if(
    $status != '' &&
    !isset(
        $status_config[$status]
    )
){

    $status =
        '';

}


$date_start =
    isset($_GET['date_start'])
        ? trim($_GET['date_start'])
        : '';


$date_end =
    isset($_GET['date_end'])
        ? trim($_GET['date_end'])
        : '';



/*
 * =========================================================
 * WHERE
 * =========================================================
 */

$where =
    array();


$params =
    array();


$where[] =
    'i.deleted_at IS NULL';



/*
 * Keyword
 */

if(
    $keyword != ''
){

    $where[] = "

        (
            i.name LIKE ?
            OR i.company_name LIKE ?
            OR i.phone LIKE ?
            OR i.email LIKE ?
        )

    ";


    $search_keyword =
        '%'.$keyword.'%';


    $params[] =
        $search_keyword;

    $params[] =
        $search_keyword;

    $params[] =
        $search_keyword;

    $params[] =
        $search_keyword;

}



/*
 * Status
 */

if(
    $status != ''
){

    $where[] =
        'i.status = ?';


    $params[] =
        $status;

}



/*
 * Date Start
 */

if(
    $date_start != ''
){

    $where[] =
        'i.created_at >= ?';


    $params[] =
        $date_start.' 00:00:00';

}



/*
 * Date End
 */

if(
    $date_end != ''
){

    $where[] =
        'i.created_at <= ?';


    $params[] =
        $date_end.' 23:59:59';

}


$where_sql =
    implode(
        ' AND ',
        $where
    );



/*
 * =========================================================
 * STATUS COUNT
 * =========================================================
 */

$status_counts =
    array(
        'all'         => 0,
        'received'    => 0,
        'confirmed'   => 0,
        'in_progress' => 0,
        'completed'   => 0
    );


$query = "

    SELECT
        status,
        COUNT(*) AS cnt

    FROM inquiries

    WHERE
        deleted_at IS NULL

    GROUP BY
        status

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

    $status_name =
        $data['status'];


    if(
        isset(
            $status_counts[$status_name]
        )
    ){

        $status_counts[$status_name] =
            intval(
                $data['cnt']
            );

    }


    $status_counts['all'] +=
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

    FROM inquiries i

    WHERE
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
        ? intval(
            $total_data['total']
        )
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

    $total_page =
        1;

}


if(
    $page >
    $total_page
){

    $page =
        $total_page;

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

        i.id,
        i.name,
        i.company_name,
        i.phone,
        i.email,
        i.attachment_media_id,
        i.status,
        i.created_at,
        i.updated_at,

        m.storage_key AS attachment_path,
        m.original_name AS attachment_name,
        m.mime_type AS attachment_mime,
        m.byte_size AS attachment_size

    FROM inquiries i

    LEFT JOIN media m
        ON m.id = i.attachment_media_id
        AND m.deleted_at IS NULL

    WHERE
        ".$where_sql."

    ORDER BY
        i.id DESC

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
 * PAGINATION BLOCK
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

    $page_end =
        $total_page;

}



/*
 * =========================================================
 * URL
 * =========================================================
 */

function inquiry_list_url(
    $page,
    $limit,
    $keyword,
    $status,
    $date_start,
    $date_end
){

    return

        '/manage/inquiry_list?'.

        http_build_query(
            array(
                'page'       => $page,
                'limit'      => $limit,
                'keyword'    => $keyword,
                'status'     => $status,
                'date_start' => $date_start,
                'date_end'   => $date_end
            )
        );

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/inquiry.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="inquiry-page-head">

            <div class="inquiry-page-title">

                <span>
                    문의 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    문의 내역
                </h2>

            </div>

        </div>



        <!-- =================================================
             STATUS SUMMARY
        ================================================== -->

        <div class="inquiry-summary-grid">


            <a
                href="/manage/inquiry_list"
                class="inquiry-summary-item <?=($status == '' ? 'active' : '')?>"
            >

                <span>
                    전체 문의
                </span>

                <strong>
                    <?=number_format($status_counts['all'])?>
                </strong>

            </a>



            <a
                href="/manage/inquiry_list?status=received"
                class="inquiry-summary-item <?=($status == 'received' ? 'active' : '')?>"
            >

                <span>
                    접수
                </span>

                <strong>
                    <?=number_format($status_counts['received'])?>
                </strong>

            </a>



            <a
                href="/manage/inquiry_list?status=confirmed"
                class="inquiry-summary-item <?=($status == 'confirmed' ? 'active' : '')?>"
            >

                <span>
                    확인
                </span>

                <strong>
                    <?=number_format($status_counts['confirmed'])?>
                </strong>

            </a>



            <a
                href="/manage/inquiry_list?status=in_progress"
                class="inquiry-summary-item <?=($status == 'in_progress' ? 'active' : '')?>"
            >

                <span>
                    진행중
                </span>

                <strong>
                    <?=number_format($status_counts['in_progress'])?>
                </strong>

            </a>



            <a
                href="/manage/inquiry_list?status=completed"
                class="inquiry-summary-item <?=($status == 'completed' ? 'active' : '')?>"
            >

                <span>
                    완료
                </span>

                <strong>
                    <?=number_format($status_counts['completed'])?>
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
                    action="/manage/inquiry_list"
                    id="inquirySearchForm"
                >

                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="inquiry-manage-search-grid">


                        <!-- Keyword -->

                        <div class="form-group">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=inquiry_h($keyword)?>"
                                placeholder="이름, 회사명, 연락처, 이메일"
                            >

                        </div>



                        <!-- Status -->

                        <div class="form-group">

                            <label>
                                상태
                            </label>

                            <select name="status">

                                <option value="">
                                    전체
                                </option>

                                <?
                                foreach(
                                    $status_config as
                                    $status_key => $status_data
                                ){
                                ?>

                                    <option
                                        value="<?=$status_key?>"
                                        <?=$status == $status_key ? 'selected' : ''?>
                                    >

                                        <?=inquiry_h(
                                            $status_data['label']
                                        )?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

                        </div>



                        <!-- Start -->

                        <div class="form-group">

                            <label>
                                시작일
                            </label>

                            <input
                                type="date"
                                name="date_start"
                                value="<?=inquiry_h($date_start)?>"
                            >

                        </div>



                        <!-- End -->

                        <div class="form-group">

                            <label>
                                종료일
                            </label>

                            <input
                                type="date"
                                name="date_end"
                                value="<?=inquiry_h($date_end)?>"
                            >

                        </div>


                    </div>


                    <div class="inquiry-search-action">

                        <a
                            href="/manage/inquiry_list"
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

        <div class="card inquiry-list-card">

            <div class="card-header">

                <h3>
                    문의 목록
                </h3>

            </div>


            <div class="card-body">


                <!-- Toolbar -->

                <div class="inquiry-toolbar">

                    <div class="inquiry-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        건

                    </div>


                    <div class="inquiry-toolbar-right">

                        <select id="inquiryLimit">

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

                </div>



                <!-- =================================================
                     TABLE
                ================================================== -->

                <div class="inquiry-table-wrap">

                    <table class="inquiry-table inquiry-manage-table">

                        <thead>

                            <tr>

                                <th width="65">
                                    No.
                                </th>

                                <th width="145">
                                    접수일
                                </th>

                                <th width="110">
                                    이름
                                </th>

                                <th>
                                    회사명
                                </th>

                                <th width="145">
                                    연락처
                                </th>

                                <th width="220">
                                    이메일
                                </th>

                                <th width="90">
                                    첨부
                                </th>

                                <th width="95">
                                    상태
                                </th>

                                <th width="90">
                                    관리
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?
                        if(
                            count($list) > 0
                        ){

                            $no =
                                $total -
                                $offset;


                            foreach(
                                $list as
                                $data
                            ){

                                $data_status =
                                    isset(
                                        $status_config[
                                            $data['status']
                                        ]
                                    )
                                        ? $status_config[
                                            $data['status']
                                        ]
                                        : array(
                                            'label' => $data['status'],
                                            'class' => ''
                                        );
                        ?>

                            <tr>


                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Date -->

                                <td>

                                    <div class="inquiry-date">

                                        <?=inquiry_h(
                                            substr(
                                                $data['created_at'],
                                                0,
                                                10
                                            )
                                        )?>

                                        <small>

                                            <?=inquiry_h(
                                                substr(
                                                    $data['created_at'],
                                                    11,
                                                    5
                                                )
                                            )?>

                                        </small>

                                    </div>

                                </td>



                                <!-- Name -->

                                <td>

                                    <a
                                        href="/manage/inquiry_view?id=<?=$data['id']?>"
                                        class="inquiry-name"
                                    >

                                        <?=inquiry_h(
                                            $data['name']
                                        )?>

                                    </a>

                                </td>



                                <!-- Company -->

                                <td>

                                    <div class="inquiry-company">

                                        <?=inquiry_h(
                                            $data['company_name']
                                        )?>

                                    </div>

                                </td>



                                <!-- Phone -->

                                <td>

                                    <a
                                        href="tel:<?=inquiry_h($data['phone'])?>"
                                        class="inquiry-contact-link"
                                    >

                                        <?=inquiry_h(
                                            $data['phone']
                                        )?>

                                    </a>

                                </td>



                                <!-- Email -->

                                <td>

                                    <a
                                        href="mailto:<?=inquiry_h($data['email'])?>"
                                        class="inquiry-contact-link inquiry-email"
                                    >

                                        <?=inquiry_h(
                                            $data['email']
                                        )?>

                                    </a>

                                </td>



                                <!-- Attachment -->

                                <td>

                                    <?
                                    if(
                                        intval(
                                            $data['attachment_media_id']
                                        ) > 0 &&
                                        $data['attachment_path'] != ''
                                    ){
                                    ?>

                                        <a
                                            href="<?=inquiry_h($data['attachment_path'])?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inquiry-file"
                                            title="<?=inquiry_h($data['attachment_name'])?>"
                                        >

                                            <i class="ri-attachment-2"></i>

                                            <span>
                                                있음
                                            </span>

                                        </a>

                                    <?
                                    }else{
                                    ?>

                                        <span class="inquiry-muted">
                                            -
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Status -->

                                <td>

                                    <span class="inquiry-manage-status <?=$data_status['class']?>">

                                        <?=inquiry_h(
                                            $data_status['label']
                                        )?>

                                    </span>

                                </td>



                                <!-- Manage -->

                                <td>

                                    <a
                                        href="/manage/inquiry_view?id=<?=$data['id']?>"
                                        class="btn btn-white btn-sm inquiry-view-button"
                                    >
                                        보기
                                    </a>

                                </td>


                            </tr>

                        <?
                                $no--;

                            }

                        }else{
                        ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="inquiry-empty"
                                >

                                    <i class="ri-question-answer-line"></i>

                                    접수된 문의가 없습니다.

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

                <div class="inquiry-pagination">


                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="<?=inquiry_h(
                                inquiry_list_url(
                                    $page - 1,
                                    $limit,
                                    $keyword,
                                    $status,
                                    $date_start,
                                    $date_end
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
                                href="<?=inquiry_h(
                                    inquiry_list_url(
                                        $i,
                                        $limit,
                                        $keyword,
                                        $status,
                                        $date_start,
                                        $date_end
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
                            href="<?=inquiry_h(
                                inquiry_list_url(
                                    $page + 1,
                                    $limit,
                                    $keyword,
                                    $status,
                                    $date_start,
                                    $date_end
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

var inquiryLimit =
    document.getElementById(
        'inquiryLimit'
    );


if(inquiryLimit){

    inquiryLimit.addEventListener(
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