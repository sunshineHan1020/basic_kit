<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 선택항목 관리
 *
 * FILE
 * /manage/inquiry_option_list.php
 *
 * URL
 * /manage/inquiry_option_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function inquiry_option_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * STEP
 * =========================================================
 */

$step_config =
    array(

        1 => array(
            'number' => '01',
            'day'    => 'Monday',
            'title'  => '프로젝트 유형'
        ),

        2 => array(
            'number' => '02',
            'day'    => 'Tuesday',
            'title'  => '미팅 장소'
        ),

        3 => array(
            'number' => '03',
            'day'    => 'Wednesday',
            'title'  => '프로젝트 일정'
        ),

        4 => array(
            'number' => '04',
            'day'    => 'Thursday',
            'title'  => '프로젝트 형태'
        )

    );



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$step =
    isset($_GET['step'])
        ? intval($_GET['step'])
        : 1;


if(
    !isset(
        $step_config[$step]
    )
){

    $step =
        1;

}


$page =
    isset($_GET['page'])
        ? intval($_GET['page'])
        : 1;


if(
    $page < 1
){

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


$is_active =
    isset($_GET['is_active'])
        ? trim($_GET['is_active'])
        : '';


if(
    $is_active != '' &&
    $is_active != '1' &&
    $is_active != '0'
){

    $is_active =
        '';

}



/*
 * =========================================================
 * STEP별 전체 개수
 * =========================================================
 */

$step_counts =
    array(
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0
    );


$query = "

    SELECT
        step_no,
        COUNT(*) AS cnt

    FROM inquiry_options

    WHERE
        step_no BETWEEN 1 AND 4

    GROUP BY
        step_no

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

    $step_no =
        intval(
            $data['step_no']
        );


    if(
        isset(
            $step_counts[$step_no]
        )
    ){

        $step_counts[$step_no] =
            intval(
                $data['cnt']
            );

    }

}



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
 * STEP
 */

$where[] =
    'step_no = ?';


$params[] =
    $step;



/*
 * 검색
 */

if(
    $keyword != ''
){

    $where[] = "

        (
            title_en LIKE ?
            OR title_ko LIKE ?
            OR option_code LIKE ?
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

}



/*
 * 노출
 */

if(
    $is_active != ''
){

    $where[] =
        'is_active = ?';


    $params[] =
        intval(
            $is_active
        );

}


$where_sql =
    implode(
        ' AND ',
        $where
    );



/*
 * =========================================================
 * Total
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM inquiry_options

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
 * Pagination
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
 * List
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        id,
        step_no,
        option_code,
        title_en,
        title_ko,
        sort_order,
        is_active,
        created_at,
        updated_at

    FROM inquiry_options

    WHERE
        ".$where_sql."

    ORDER BY
        sort_order ASC,
        id ASC

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
 * Pagination Block
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

function inquiry_option_list_url(
    $page,
    $step,
    $limit,
    $keyword,
    $is_active
){

    return

        '/manage/inquiry_option_list?'.

        http_build_query(
            array(
                'step'      => $step,
                'page'      => $page,
                'limit'     => $limit,
                'keyword'   => $keyword,
                'is_active' => $is_active
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
             Page Head
        ================================================== -->

        <div class="inquiry-page-head">

            <div class="inquiry-page-title">

                <span>
                    문의 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    문의 선택항목 관리
                </h2>

            </div>


            <div class="inquiry-page-action">

                <a
                    href="/manage/inquiry_option_write?step=<?=$step?>"
                    class="btn btn-primary"
                >

                    <i class="ri-add-line"></i>

                    항목 등록

                </a>

            </div>

        </div>



        <!-- =================================================
             STEP TAB
        ================================================== -->

        <div class="inquiry-step-tabs">

            <?
            foreach(
                $step_config as
                $step_no => $step_data
            ){
            ?>

                <a
                    href="/manage/inquiry_option_list?step=<?=$step_no?>"
                    class="inquiry-step-tab <?=$step == $step_no ? 'active' : ''?>"
                >

                    <span class="step-number">

                        <?=$step_data['number']?>

                    </span>


                    <div class="step-text">

                        <strong>

                            <?=inquiry_option_h(
                                $step_data['day']
                            )?>

                        </strong>


                        <small>

                            <?=inquiry_option_h(
                                $step_data['title']
                            )?>

                        </small>

                    </div>


                    <em>

                        <?=number_format(
                            $step_counts[$step_no]
                        )?>

                    </em>

                </a>

            <?
            }
            ?>

        </div>



        <!-- =================================================
             Search
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
                    action="/manage/inquiry_option_list"
                >

                    <input
                        type="hidden"
                        name="step"
                        value="<?=$step?>"
                    >


                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="inquiry-search-grid">


                        <!-- Keyword -->

                        <div class="form-group">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=inquiry_option_h($keyword)?>"
                                placeholder="영문 제목, 한글 제목 또는 코드"
                            >

                        </div>



                        <!-- Active -->

                        <div class="form-group">

                            <label>
                                노출 여부
                            </label>

                            <select name="is_active">

                                <option value="">
                                    전체
                                </option>

                                <option
                                    value="1"
                                    <?=$is_active === '1' ? 'selected' : ''?>
                                >
                                    노출
                                </option>

                                <option
                                    value="0"
                                    <?=$is_active === '0' ? 'selected' : ''?>
                                >
                                    비노출
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="inquiry-search-action">

                        <a
                            href="/manage/inquiry_option_list?step=<?=$step?>"
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
             List
        ================================================== -->

        <div class="card inquiry-list-card">

            <div class="card-header">

                <div class="inquiry-card-title">

                    <span class="inquiry-card-step">

                        <?=$step_config[$step]['number']?>

                    </span>


                    <div>

                        <h3>

                            <?=inquiry_option_h(
                                $step_config[$step]['day']
                            )?>

                        </h3>


                        <p>

                            <?=inquiry_option_h(
                                $step_config[$step]['title']
                            )?>

                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body">


                <!-- Toolbar -->

                <div class="inquiry-toolbar">

                    <div class="inquiry-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        개

                    </div>


                    <div class="inquiry-toolbar-right">

                        <button
                            type="button"
                            class="btn btn-white"
                            id="inquirySelectedDelete"
                        >

                            <i class="ri-delete-bin-line"></i>

                            선택삭제

                        </button>


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
                     Table
                ================================================== -->

                <div class="inquiry-table-wrap">

                    <table class="inquiry-table">

                        <thead>

                            <tr>

                                <th width="45">

                                    <input
                                        type="checkbox"
                                        id="inquiryCheckAll"
                                    >

                                </th>

                                <th width="70">
                                    No.
                                </th>

                                <th>
                                    영문 제목
                                </th>

                                <th>
                                    한글 제목
                                </th>

                                <th width="190">
                                    코드
                                </th>

                                <th width="90">
                                    순서
                                </th>

                                <th width="100">
                                    노출
                                </th>

                                <th width="150">
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
                        ?>

                            <tr>


                                <!-- Check -->

                                <td>

                                    <input
                                        type="checkbox"
                                        class="inquiry-check"
                                        value="<?=intval($data['id'])?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- EN -->

                                <td>

                                    <div class="inquiry-option-title en">

                                        <a
                                            href="/manage/inquiry_option_write?id=<?=$data['id']?>"
                                        >

                                            <?=inquiry_option_h(
                                                str_replace(
                                                    '|',
                                                    ' / ',
                                                    $data['title_en']
                                                )
                                            )?>

                                        </a>

                                    </div>

                                </td>



                                <!-- KO -->

                                <td>

                                    <div class="inquiry-option-title">

                                        <?=inquiry_option_h(
                                            $data['title_ko']
                                        )?>

                                    </div>

                                </td>



                                <!-- Code -->

                                <td>

                                    <code class="inquiry-option-code">

                                        <?=inquiry_option_h(
                                            $data['option_code']
                                        )?>

                                    </code>

                                </td>



                                <!-- Sort -->

                                <td>

                                    <?=number_format(
                                        intval(
                                            $data['sort_order']
                                        )
                                    )?>

                                </td>



                                <!-- Active -->

                                <td>

                                    <?
                                    if(
                                        intval(
                                            $data['is_active']
                                        ) == 1
                                    ){
                                    ?>

                                        <span class="inquiry-status active">
                                            노출
                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="inquiry-status inactive">
                                            비노출
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Action -->

                                <td>

                                    <div class="inquiry-action">

                                        <a
                                            href="/manage/inquiry_option_write?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm inquiry-delete"
                                            data-id="<?=$data['id']?>"
                                        >
                                            삭제
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        <?
                                $no--;

                            }

                        }else{
                        ?>

                            <tr>

                                <td
                                    colspan="8"
                                    class="inquiry-empty"
                                >

                                    <i class="ri-checkbox-multiple-line"></i>

                                    등록된 선택항목이 없습니다.

                                </td>

                            </tr>

                        <?
                        }
                        ?>

                        </tbody>

                    </table>

                </div>



                <!-- =================================================
                     Pagination
                ================================================== -->

                <div class="inquiry-pagination">


                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="<?=inquiry_option_h(
                                inquiry_option_list_url(
                                    $page - 1,
                                    $step,
                                    $limit,
                                    $keyword,
                                    $is_active
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
                                href="<?=inquiry_option_h(
                                    inquiry_option_list_url(
                                        $i,
                                        $step,
                                        $limit,
                                        $keyword,
                                        $is_active
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
                            href="<?=inquiry_option_h(
                                inquiry_option_list_url(
                                    $page + 1,
                                    $step,
                                    $limit,
                                    $keyword,
                                    $is_active
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



/*
 * =========================================================
 * CHECK ALL
 * =========================================================
 */

var inquiryCheckAll =
    document.getElementById(
        'inquiryCheckAll'
    );


if(inquiryCheckAll){

    inquiryCheckAll.addEventListener(
        'change',
        function(){

            var checks =
                document.querySelectorAll(
                    '.inquiry-check'
                );


            for(
                var i = 0;
                i < checks.length;
                i++
            ){

                checks[i].checked =
                    this.checked;

            }

        }
    );

}



/*
 * =========================================================
 * CHECK SYNC
 * =========================================================
 */

document.addEventListener(
    'change',
    function(e){

        var target =
            e.target;


        if(
            !target.classList ||
            !target.classList.contains(
                'inquiry-check'
            )
        ){

            return;

        }


        var checks =
            document.querySelectorAll(
                '.inquiry-check'
            );


        var checked =
            document.querySelectorAll(
                '.inquiry-check:checked'
            );


        if(inquiryCheckAll){

            inquiryCheckAll.checked =
                checks.length > 0 &&
                checks.length ==
                checked.length;

        }

    }
);



/*
 * =========================================================
 * 개별 삭제
 * =========================================================
 */

document.addEventListener(
    'click',
    function(e){

        var target =
            e.target;


        while(
            target &&
            target !== document
        ){

            if(
                target.classList &&
                target.classList.contains(
                    'inquiry-delete'
                )
            ){

                var optionId =
                    target.getAttribute(
                        'data-id'
                    );


                if(!optionId){

                    return;

                }


                confirm(
                    '해당 선택항목을 삭제하시겠습니까?',
                    function(){

                        deleteInquiryOption(
                            optionId,
                            function(){

                                alert(
                                    '선택항목이 삭제되었습니다.',
                                    function(){

                                        location.reload();

                                    }
                                );

                            }
                        );

                    }
                );


                return;

            }


            target =
                target.parentNode;

        }

    }
);



/*
 * =========================================================
 * 선택 삭제
 * =========================================================
 */

var inquirySelectedDelete =
    document.getElementById(
        'inquirySelectedDelete'
    );


if(inquirySelectedDelete){

    inquirySelectedDelete.addEventListener(
        'click',
        function(){

            var checks =
                document.querySelectorAll(
                    '.inquiry-check:checked'
                );


            if(
                checks.length <= 0
            ){

                alert(
                    '삭제할 선택항목을 선택해주세요.'
                );

                return;

            }


            var ids =
                [];


            for(
                var i = 0;
                i < checks.length;
                i++
            ){

                ids.push(
                    checks[i].value
                );

            }


            confirm(
                '선택한 ' +
                ids.length +
                '개의 항목을 삭제하시겠습니까?',
                function(){

                    deleteSelectedInquiryOptions(
                        ids,
                        0
                    );

                }
            );

        }
    );

}



/*
 * =========================================================
 * DELETE AJAX
 * =========================================================
 */

function deleteInquiryOption(
    optionId,
    callback
){

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/inquiry_option_delete',
        true
    );


    xhr.setRequestHeader(
        'Content-Type',
        'application/x-www-form-urlencoded; charset=UTF-8'
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

            }catch(e){

                alert(
                    '삭제 처리 중 오류가 발생했습니다.'
                );

                return;

            }


            if(!result.code){

                alert(
                    result.msg
                        ? result.msg
                        : '선택항목을 삭제하지 못했습니다.'
                );

                return;

            }


            if(callback){

                callback(
                    result
                );

            }

        };


    xhr.send(
        'id=' +
        encodeURIComponent(
            optionId
        )
    );

}



/*
 * =========================================================
 * 선택 삭제 순차 처리
 * =========================================================
 */

function deleteSelectedInquiryOptions(
    ids,
    index
){

    if(
        index >=
        ids.length
    ){

        alert(
            '선택한 항목이 삭제되었습니다.',
            function(){

                location.reload();

            }
        );

        return;

    }


    deleteInquiryOption(
        ids[index],
        function(){

            deleteSelectedInquiryOptions(
                ids,
                index + 1
            );

        }
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>