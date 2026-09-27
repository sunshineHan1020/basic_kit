<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 목록
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


/*
 * =========================================================
 * 기본값
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


$keyword =
    isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';


$category_id =
    isset($_GET['category_id'])
        ? intval($_GET['category_id'])
        : 0;


$status =
    isset($_GET['status'])
        ? trim($_GET['status'])
        : '';


$visibility =
    isset($_GET['visibility'])
        ? trim($_GET['visibility'])
        : '';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function works_h($str)
{

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );

}



/*
 * =========================================================
 * 카테고리
 * =========================================================
 */

$categories =
    array();


$query = "

    SELECT
        id,
        name

    FROM work_categories

    ORDER BY
        sort_order ASC,
        id ASC

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

    $categories[] =
        $data;

}



/*
 * =========================================================
 * 검색조건
 * =========================================================
 */

$where =
    array();


$params =
    array();


$where[] =
    "w.deleted_at IS NULL";


/*
 * 검색어
 */

if($keyword != ''){

    $where[] = "

        (
            w.list_title LIKE ?
            OR
            w.title LIKE ?
            OR
            w.slug LIKE ?
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
 * 카테고리
 */

if($category_id > 0){

    $where[] =
        "w.category_id = ?";


    $params[] =
        $category_id;

}


/*
 * 상태
 */

if(
    $status == 'draft' ||
    $status == 'published' ||
    $status == 'archived'
){

    $where[] =
        "w.status = ?";


    $params[] =
        $status;

}


/*
 * 노출
 */

if(
    $visibility == 'public' ||
    $visibility == 'private'
){

    $where[] =
        "w.visibility = ?";


    $params[] =
        $visibility;

}



$where_sql =
    implode(
        ' AND ',
        $where
    );



/*
 * =========================================================
 * 전체 개수
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM works w

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
        ? intval($total_data['total'])
        : 0;



/*
 * =========================================================
 * Paging
 * =========================================================
 */

$total_page =
    ceil(
        $total / $limit
    );


if($total_page < 1){
    $total_page = 1;
}


if($page > $total_page){
    $page = $total_page;
}


$offset =
    ($page - 1) * $limit;



/*
 * =========================================================
 * WORKS 목록
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        w.id,
        w.category_id,
        w.list_title,
        w.title,
        w.slug,
        w.sort_order,
        w.status,
        w.visibility,
        w.view_count,
        w.author_display_name,
        w.created_at,
        w.updated_at,

        c.name AS category_name,

        (

            SELECT
                m.storage_key

            FROM work_media wm

            INNER JOIN media m
                ON m.id = wm.media_id

            WHERE
                wm.work_id = w.id
                AND wm.role = 'thumbnail'
                AND m.deleted_at IS NULL

            ORDER BY
                wm.sort_order ASC,
                wm.id ASC

            LIMIT 1

        ) AS thumbnail_path

    FROM works w

    LEFT JOIN work_categories c
        ON c.id = w.category_id

    WHERE
        ".$where_sql."

    ORDER BY
        w.sort_order ASC,
        w.id DESC

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
 * Pagination QueryString
 * =========================================================
 */

$query_string =
    '&keyword='.
    urlencode($keyword).

    '&category_id='.
    $category_id.

    '&status='.
    urlencode($status).

    '&visibility='.
    urlencode($visibility).

    '&limit='.
    $limit;



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



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/works.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             Page Header
        ================================================== -->

        <div class="works-page-head">

            <div class="works-page-title">

                <span>
                    WORKS
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    Weekends Works
                </h2>

            </div>


            <div class="works-page-action">

                <a
                    href="/manage/works_write"
                    class="btn btn-primary"
                >

                    <i class="ri-add-line"></i>

                    WORK 등록

                </a>

            </div>

        </div>



        <!-- =================================================
             Search
        ================================================== -->

        <!-- =================================================
     Search
================================================== -->

<div class="card works-search-card">

    <div class="card-body">

        <form
            method="get"
            action="/manage/works_list"
            id="worksSearchForm"
        >

            <div class="works-search-row">


                <div class="works-search-field keyword">

                    <span class="works-search-label">
                        검색어
                    </span>

                    <input
                        type="text"
                        name="keyword"
                        value="<?=works_h($keyword)?>"
                        placeholder="프로젝트명 또는 URL"
                    >

                </div>


                <div class="works-search-field">

                    <span class="works-search-label">
                        카테고리
                    </span>

                    <select name="category_id">

                        <option value="0">
                            전체
                        </option>

                        <?
                        foreach(
                            $categories as
                            $category
                        ){
                        ?>

                            <option
                                value="<?=$category['id']?>"
                                <?=$category_id == $category['id'] ? 'selected' : ''?>
                            >
                                <?=works_h($category['name'])?>
                            </option>

                        <?
                        }
                        ?>

                    </select>

                </div>


                <div class="works-search-field">

                    <span class="works-search-label">
                        상태
                    </span>

                    <select name="status">

                        <option value="">
                            전체
                        </option>

                        <option
                            value="published"
                            <?=$status == 'published' ? 'selected' : ''?>
                        >
                            게시
                        </option>

                        <option
                            value="draft"
                            <?=$status == 'draft' ? 'selected' : ''?>
                        >
                            임시저장
                        </option>

                        <option
                            value="archived"
                            <?=$status == 'archived' ? 'selected' : ''?>
                        >
                            보관
                        </option>

                    </select>

                </div>


                <div class="works-search-field">

                    <span class="works-search-label">
                        노출
                    </span>

                    <select name="visibility">

                        <option value="">
                            전체
                        </option>

                        <option
                            value="public"
                            <?=$visibility == 'public' ? 'selected' : ''?>
                        >
                            공개
                        </option>

                        <option
                            value="private"
                            <?=$visibility == 'private' ? 'selected' : ''?>
                        >
                            비공개
                        </option>

                    </select>

                </div>


                <div class="works-search-buttons">

                    <a
                        href="/manage/works_list"
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


            </div>

        </form>

    </div>

</div>



        <!-- =================================================
             List
        ================================================== -->

        <div class="card">

            <div class="card-header">

                <h3>
                    WORK 목록
                </h3>

            </div>


            <div class="card-body">


                <div class="works-toolbar">

    <div class="works-total">

        총

        <strong>
            <?=number_format($total)?>
        </strong>

        건

    </div>


    <div class="works-toolbar-right">

        <button
            type="button"
            class="btn btn-white"
            id="workSelectedDelete"
        >
            <i class="ri-delete-bin-line"></i>

            선택삭제
        </button>


        <select
            id="worksLimit"
        >

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

                <div class="works-table-wrap">

                    <table class="works-table">

                        <thead>

                            <tr>

                                <th width="42">

                                    <input
                                        type="checkbox"
                                        id="workCheckAll"
                                    >

                                </th>

                                <th width="65">
                                    No.
                                </th>

                                <th width="120">
                                    썸네일
                                </th>

                                <th>
                                    프로젝트
                                </th>

                                <th width="120">
                                    카테고리
                                </th>

                                <th width="80">
                                    순서
                                </th>

                                <th width="90">
                                    상태
                                </th>

                                <th width="90">
                                    노출
                                </th>

                                <th width="80">
                                    조회
                                </th>

                                <th width="115">
                                    수정일
                                </th>

                                <th width="150">
                                    관리
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?
                        if(count($list) > 0){

                            $no =
                                $total -
                                $offset;


                            foreach(
                                $list as
                                $data
                            ){

                                /*
                                 * 표시 제목
                                 */

                                $display_title =
                                    $data['list_title'] != ''
                                        ? $data['list_title']
                                        : $data['title'];


                                if($display_title == ''){

                                    $display_title =
                                        '(제목 없음)';

                                }


                                /*
                                 * 썸네일
                                 */

                                $thumbnail =
                                    isset($data['thumbnail_path'])
                                        ? trim($data['thumbnail_path'])
                                        : '';


                                if(
                                    $thumbnail != '' &&
                                    substr(
                                        $thumbnail,
                                        0,
                                        1
                                    ) != '/'
                                ){

                                    $thumbnail =
                                        '/'.$thumbnail;

                                }


                                /*
                                 * 상태명
                                 */

                                $status_text =
                                    '임시저장';


                                if(
                                    $data['status'] ==
                                    'published'
                                ){

                                    $status_text =
                                        '게시';

                                }


                                if(
                                    $data['status'] ==
                                    'archived'
                                ){

                                    $status_text =
                                        '보관';

                                }

                        ?>

                            <tr
                                data-id="<?=$data['id']?>"
                            >


                                <!-- 체크 -->

                                <td>

                                    <input
                                        type="checkbox"
                                        class="work-check"
                                        value="<?=$data['id']?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Thumbnail -->

                                <td>

                                    <div class="work-thumb">

                                    <?
                                    if($thumbnail != ''){
                                    ?>

                                        <img
                                            src="<?=works_h($thumbnail)?>"
                                            alt=""
                                        >

                                    <?
                                    }else{
                                    ?>

                                        <div class="work-thumb-empty">

                                            <i class="ri-image-line"></i>

                                        </div>

                                    <?
                                    }
                                    ?>

                                    </div>

                                </td>



                                <!-- Project -->

                                <td>

                                    <div class="work-title">

                                        <strong>

                                            <?=works_h($display_title)?>

                                        </strong>


                                        <?
                                        if(
                                            isset($data['slug']) &&
                                            $data['slug'] != ''
                                        ){
                                        ?>

                                            <span class="slug">

                                                /<?=works_h($data['slug'])?>

                                            </span>

                                        <?
                                        }
                                        ?>

                                    </div>

                                </td>



                                <!-- Category -->

                                <td>

                                    <?
                                    if(
                                        isset($data['category_name']) &&
                                        $data['category_name'] != ''
                                    ){
                                    ?>

                                        <?=works_h($data['category_name'])?>

                                    <?
                                    }else{
                                    ?>

                                        -

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Sort -->

                                <td>

                                    <input
                                        type="text"
                                        class="work-sort"
                                        data-id="<?=$data['id']?>"
                                        value="<?=intval($data['sort_order'])?>"
                                    >

                                </td>



                                <!-- Status -->

                                <td>

                                    <span
                                        class="work-status <?=works_h($data['status'])?>"
                                    >

                                        <?=$status_text?>

                                    </span>

                                </td>



                                <!-- Visibility -->

                                <td>

                                    <span
                                        class="work-visibility <?=works_h($data['visibility'])?>"
                                    >

                                    <?
                                    if(
                                        $data['visibility'] ==
                                        'public'
                                    ){
                                    ?>

                                        공개

                                    <?
                                    }else{
                                    ?>

                                        비공개

                                    <?
                                    }
                                    ?>

                                    </span>

                                </td>



                                <!-- View -->

                                <td>

                                    <?=number_format($data['view_count'])?>

                                </td>



                                <!-- Updated -->

                                <td>

                                    <?
                                    if($data['updated_at'] != ''){
                                    ?>

                                        <?=date(
                                            'Y.m.d',
                                            strtotime(
                                                $data['updated_at']
                                            )
                                        )?>

                                    <?
                                    }else{
                                    ?>

                                        -

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Manage -->

                                <td>

                                    <div class="works-action">

                                        <a
                                            href="./works_write.php?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm work-delete"
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
                                    colspan="11"
                                    class="works-empty"
                                >

                                    <i class="ri-folder-open-line"></i>

                                    등록된 WORK가 없습니다.

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

                <div class="works-pagination">


                    <!-- 이전 -->

                    <?
                    if($page > 1){
                    ?>

                        <a
                            href="?page=<?=($page - 1)?><?=$query_string?>"
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



                    <!-- 페이지 번호 -->

                    <?
                    for(
                        $i = $page_start;
                        $i <= $page_end;
                        $i++
                    ){
                    ?>

                        <?
                        if($i == $page){
                        ?>

                            <span class="active">

                                <?=$i?>

                            </span>

                        <?
                        }else{
                        ?>

                            <a
                                href="?page=<?=$i?><?=$query_string?>"
                            >

                                <?=$i?>

                            </a>

                        <?
                        }
                        ?>

                    <?
                    }
                    ?>



                    <!-- 다음 -->

                    <?
                    if($page < $total_page){
                    ?>

                        <a
                            href="?page=<?=($page + 1)?><?=$query_string?>"
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
 * 페이지당 개수
 * =========================================================
 */

var worksLimit =
    document.getElementById(
        'worksLimit'
    );


if(worksLimit){

    worksLimit.addEventListener(
        'change',
        function(){

            var url =
                window.location.pathname;


            var query =
                '?page=1' +
                '&limit=' +
                encodeURIComponent(
                    this.value
                ) +
                '&keyword=' +
                encodeURIComponent(
                    '<?=addslashes($keyword)?>'
                ) +
                '&category_id=' +
                encodeURIComponent(
                    '<?=$category_id?>'
                ) +
                '&status=' +
                encodeURIComponent(
                    '<?=addslashes($status)?>'
                ) +
                '&visibility=' +
                encodeURIComponent(
                    '<?=addslashes($visibility)?>'
                );


            window.location.href =
                url + query;

        }
    );

}



/*
 * =========================================================
 * 전체 선택
 * =========================================================
 */

var workCheckAll =
    document.getElementById(
        'workCheckAll'
    );


if(workCheckAll){

    workCheckAll.addEventListener(
        'change',
        function(){

            var checks =
                document.querySelectorAll(
                    '.work-check'
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
 * 개별 체크
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
                'work-check'
            )
        ){

            return;

        }


        var checks =
            document.querySelectorAll(
                '.work-check'
            );


        var checked =
            document.querySelectorAll(
                '.work-check:checked'
            );


        if(workCheckAll){

            workCheckAll.checked =
                checks.length > 0 &&
                checks.length ==
                checked.length;

        }

    }
);



/*
 * =========================================================
 * 행 개별 삭제
 * =========================================================
 */

document.addEventListener(
    'click',
    function(e){

        var target =
            e.target;


        if(
            !target.classList ||
            !target.classList.contains(
                'work-delete'
            )
        ){

            return;

        }


        var workId =
            target.getAttribute(
                'data-id'
            );


        if(!workId){
            return;
        }


        confirm(
            '해당 WORK를 삭제하시겠습니까?',
            function(){

                deleteWork(
                    workId
                );

            }
        );

    }
);



/*
 * =========================================================
 * 개별 삭제
 * =========================================================
 */

function deleteWork(workId)
{

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/work_delete',
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
                        : '삭제하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : 'WORK가 삭제되었습니다.',
                function(){

                    location.reload();

                }
            );

        };


    xhr.send(
        'id=' +
        encodeURIComponent(
            workId
        )
    );

}



/*
 * =========================================================
 * 선택삭제
 * =========================================================
 */

var workSelectedDelete =
    document.getElementById(
        'workSelectedDelete'
    );


if(workSelectedDelete){

    workSelectedDelete.addEventListener(
        'click',
        function(){

            var checks =
                document.querySelectorAll(
                    '.work-check:checked'
                );


            if(checks.length <= 0){

                alert(
                    '삭제할 WORK를 선택해주세요.'
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
                '개의 WORK를 삭제하시겠습니까?',
                function(){

                    deleteSelectedWorks(
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
 * 선택삭제 순차 처리
 * =========================================================
 */

function deleteSelectedWorks(
    ids,
    index
){

    if(
        index >= ids.length
    ){

        alert(
            '선택한 WORK가 삭제되었습니다.',
            function(){

                location.reload();

            }
        );

        return;

    }


    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/work_delete',
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
                        : '삭제하지 못했습니다.'
                );

                return;

            }


            deleteSelectedWorks(
                ids,
                index + 1
            );

        };


    xhr.send(
        'id=' +
        encodeURIComponent(
            ids[index]
        )
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>