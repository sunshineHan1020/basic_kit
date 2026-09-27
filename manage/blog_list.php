<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 목록
 *
 * FILE
 * /manage/blog_list.php
 *
 * URL
 * /manage/blog_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function blog_h($str)
{

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );

}



/*
 * =========================================================
 * BLOG Board
 * 화면에서는 NEWS로 사용
 * =========================================================
 */

$BLOG_BOARD_CODE =
    'BLOG';


$query = "

    SELECT
        id,
        code,
        name,
        is_active

    FROM boards

    WHERE
        code = ?

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $BLOG_BOARD_CODE
        )
    );


$board =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$board){

    die(
        'NEWS 게시판 정보가 등록되어 있지 않습니다.'
    );

}


$board_id =
    intval(
        $board['id']
    );



/*
 * =========================================================
 * 검색값
 * =========================================================
 */

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



/*
 * =========================================================
 * 상태값 Validation
 * =========================================================
 */

if(
    $status != '' &&
    $status != 'published' &&
    $status != 'draft' &&
    $status != 'archived'
){

    $status =
        '';

}


if(
    $visibility != '' &&
    $visibility != 'public' &&
    $visibility != 'private'
){

    $visibility =
        '';

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
        name,
        slug,
        is_active

    FROM post_categories

    WHERE
        board_id = ?

    ORDER BY
        sort_order ASC,
        id ASC

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $board_id
        )
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
 * WHERE
 * =========================================================
 */

$where =
    array();


$params =
    array();


$where[] =
    "p.board_id = ?";


$params[] =
    $board_id;


$where[] =
    "p.deleted_at IS NULL";



/*
 * Keyword
 * =========================================================
 */

if($keyword != ''){

    $where[] = "

        (
            p.title LIKE ?
            OR p.slug LIKE ?
            OR p.summary LIKE ?
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
 * Category
 * =========================================================
 */

if($category_id > 0){

    $where[] =
        "p.category_id = ?";


    $params[] =
        $category_id;

}



/*
 * Status
 * =========================================================
 */

if($status != ''){

    $where[] =
        "p.status = ?";


    $params[] =
        $status;

}



/*
 * Visibility
 * =========================================================
 */

if($visibility != ''){

    $where[] =
        "p.visibility = ?";


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
 * Total
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM posts p

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

    $total_page =
        1;

}


if($page > $total_page){

    $page =
        $total_page;

}


$offset =
    ($page - 1) *
    $limit;



/*
 * =========================================================
 * NEWS 목록
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        p.id,
        p.category_id,
        p.is_pinned,
        p.title,
        p.slug,
        p.summary,
        p.status,
        p.visibility,
        p.display_at,
        p.publish_at,
        p.author_display_name,
        p.view_count,
        p.created_at,
        p.updated_at,

        c.name AS category_name,

        (

            SELECT
                m.storage_key

            FROM post_media pm

            INNER JOIN media m
                ON m.id = pm.media_id

            WHERE
                pm.post_id = p.id
                AND pm.role = 'thumbnail'
                AND m.deleted_at IS NULL

            ORDER BY
                pm.sort_order ASC,
                pm.id ASC

            LIMIT 1

        ) AS thumbnail_path

    FROM posts p

    LEFT JOIN post_categories c
        ON c.board_id = p.board_id
        AND c.id = p.category_id

    WHERE
        ".$where_sql."

    ORDER BY
        p.is_pinned DESC,
        p.display_at DESC,
        p.id DESC

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
 * Pagination
 * =========================================================
 */

$page_start =
    floor(
        ($page - 1) / 10
    ) * 10 + 1;


$page_end =
    $page_start + 9;


if($page_end > $total_page){

    $page_end =
        $total_page;

}



/*
 * =========================================================
 * Query String
 * =========================================================
 */

function blog_list_url(
    $page,
    $limit,
    $keyword,
    $category_id,
    $status,
    $visibility
){

    $params =
        array(
            'page'        => $page,
            'limit'       => $limit,
            'keyword'     => $keyword,
            'category_id' => $category_id,
            'status'      => $status,
            'visibility'  => $visibility
        );


    return
        '/manage/blog_list?'.
        http_build_query(
            $params
        );

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/blog.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             Page Header
        ================================================== -->

        <div class="blog-page-head">

            <div class="blog-page-title">

                <span>
                    NEWS
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    뉴스 관리
                </h2>

            </div>


            <div class="blog-page-action">

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
                    action="/manage/blog_list"
                    id="blogSearchForm"
                >

                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="blog-search-grid">


                        <!-- Keyword -->

                        <div class="form-group">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=blog_h($keyword)?>"
                                placeholder="제목 또는 URL"
                            >

                        </div>



                        <!-- Category -->

                        <div class="form-group">

                            <label>
                                카테고리
                            </label>

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

                                        <?=blog_h(
                                            $category['name']
                                        )?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

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



                        <!-- Visibility -->

                        <div class="form-group">

                            <label>
                                노출
                            </label>

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


                    </div>


                    <div class="blog-search-action">

                        <a
                            href="/manage/blog_list"
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

        <div class="card blog-list-card">

            <div class="card-header">

                <h3>
                    NEWS 목록
                </h3>

            </div>


            <div class="card-body">


                <!-- Toolbar -->

                <div class="blog-toolbar">

                    <div class="blog-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        건

                    </div>


                    <div class="blog-toolbar-right">

                        <button
                            type="button"
                            class="btn btn-white"
                            id="blogSelectedDelete"
                        >

                            <i class="ri-delete-bin-line"></i>

                            선택삭제

                        </button>


                        <select id="blogLimit">

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



                <!-- Table -->

                <div class="blog-table-wrap">

                    <table class="blog-table blog-post-table">

                        <thead>

                            <tr>

                                <th width="42">

                                    <input
                                        type="checkbox"
                                        id="blogCheckAll"
                                    >

                                </th>

                                <th width="65">
                                    No.
                                </th>

                                <th width="100">
                                    이미지
                                </th>

                                <th>
                                    제목
                                </th>

                                <th width="140">
                                    카테고리
                                </th>

                                <th width="95">
                                    상태
                                </th>

                                <th width="95">
                                    노출
                                </th>

                                <th width="90">
                                    조회수
                                </th>

                                <th width="130">
                                    등록일
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
                        ?>

                            <tr
                                data-id="<?=$data['id']?>"
                            >


                                <!-- Checkbox -->

                                <td>

                                    <input
                                        type="checkbox"
                                        class="blog-check"
                                        value="<?=$data['id']?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Thumbnail -->

                                <td>

                                    <div class="blog-thumbnail">

                                        <?
                                        if(
                                            isset($data['thumbnail_path']) &&
                                            $data['thumbnail_path'] != ''
                                        ){
                                        ?>

                                            <img
                                                src="<?=blog_h($data['thumbnail_path'])?>"
                                                alt=""
                                            >

                                        <?
                                        }else{
                                        ?>

                                            <div class="blog-thumbnail-empty">

                                                <i class="ri-image-line"></i>

                                            </div>

                                        <?
                                        }
                                        ?>

                                    </div>

                                </td>



                                <!-- Title -->

                                <td>

                                    <div class="blog-post-title">

                                        <div class="blog-post-title-line">

                                            <?
                                            if(
                                                intval(
                                                    $data['is_pinned']
                                                ) == 1
                                            ){
                                            ?>

                                                <span class="blog-pin">

                                                    <i class="ri-pushpin-fill"></i>

                                                    고정

                                                </span>

                                            <?
                                            }
                                            ?>


                                            <a
                                                href="/manage/blog_write?id=<?=$data['id']?>"
                                            >

                                                <?
                                                if(
                                                    isset($data['title']) &&
                                                    $data['title'] != ''
                                                ){
                                                ?>

                                                    <?=blog_h(
                                                        $data['title']
                                                    )?>

                                                <?
                                                }else{
                                                ?>

                                                    <span class="blog-title-empty">
                                                        제목 없음
                                                    </span>

                                                <?
                                                }
                                                ?>

                                            </a>

                                        </div>


                                        <?
                                        if(
                                            isset($data['slug']) &&
                                            $data['slug'] != ''
                                        ){
                                        ?>

                                            <span class="blog-post-url">

                                                /news/<?=blog_h(
                                                    $data['slug']
                                                )?>

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

                                        <span class="blog-category-badge">

                                            <?=blog_h(
                                                $data['category_name']
                                            )?>

                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="blog-text-muted">
                                            -
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Status -->

                                <td>

                                    <?
                                    if(
                                        $data['status'] ==
                                        'published'
                                    ){
                                    ?>

                                        <span class="blog-status published">
                                            게시
                                        </span>

                                    <?
                                    }else if(
                                        $data['status'] ==
                                        'draft'
                                    ){
                                    ?>

                                        <span class="blog-status draft">
                                            임시저장
                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="blog-status archived">
                                            보관
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Visibility -->

                                <td>

                                    <?
                                    if(
                                        $data['visibility'] ==
                                        'public'
                                    ){
                                    ?>

                                        <span class="blog-visibility public">
                                            공개
                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="blog-visibility private">
                                            비공개
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- View Count -->

                                <td>

                                    <?=number_format(
                                        intval(
                                            $data['view_count']
                                        )
                                    )?>

                                </td>



                                <!-- Created -->

                                <td>

                                    <span class="blog-date">

                                        <?
                                        if(
                                            isset($data['display_at']) &&
                                            $data['display_at'] != ''
                                        ){
                                        ?>

                                            <?=date(
                                                'Y-m-d',
                                                strtotime(
                                                    $data['display_at']
                                                )
                                            )?>

                                        <?
                                        }else{
                                        ?>

                                            <?=date(
                                                'Y-m-d',
                                                strtotime(
                                                    $data['created_at']
                                                )
                                            )?>

                                        <?
                                        }
                                        ?>

                                    </span>

                                </td>



                                <!-- Manage -->

                                <td>

                                    <div class="blog-action">

                                        <a
                                            href="/manage/blog_write?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm blog-delete"
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
                                    colspan="10"
                                    class="blog-empty"
                                >

                                    <i class="ri-article-line"></i>

                                    등록된 NEWS가 없습니다.

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

                <div class="blog-pagination">


                    <?
                    if($page > 1){
                    ?>

                        <a
                            href="<?=blog_h(
                                blog_list_url(
                                    $page - 1,
                                    $limit,
                                    $keyword,
                                    $category_id,
                                    $status,
                                    $visibility
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
                        if($i == $page){
                        ?>

                            <span class="active">
                                <?=$i?>
                            </span>

                        <?
                        }else{
                        ?>

                            <a
                                href="<?=blog_h(
                                    blog_list_url(
                                        $i,
                                        $limit,
                                        $keyword,
                                        $category_id,
                                        $status,
                                        $visibility
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
                    if($page < $total_page){
                    ?>

                        <a
                            href="<?=blog_h(
                                blog_list_url(
                                    $page + 1,
                                    $limit,
                                    $keyword,
                                    $category_id,
                                    $status,
                                    $visibility
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
 * Limit
 * =========================================================
 */

var blogLimit =
    document.getElementById(
        'blogLimit'
    );


if(blogLimit){

    blogLimit.addEventListener(
        'change',
        function(){

            var url =
                '/manage/blog_list' +

                '?page=1' +

                '&limit=' +
                encodeURIComponent(
                    this.value
                ) +

                '&keyword=' +
                encodeURIComponent(
                    '<?=blog_h($keyword)?>'
                ) +

                '&category_id=' +
                encodeURIComponent(
                    '<?=$category_id?>'
                ) +

                '&status=' +
                encodeURIComponent(
                    '<?=blog_h($status)?>'
                ) +

                '&visibility=' +
                encodeURIComponent(
                    '<?=blog_h($visibility)?>'
                );


            location.href =
                url;

        }
    );

}



/*
 * =========================================================
 * Check All
 * =========================================================
 */

var blogCheckAll =
    document.getElementById(
        'blogCheckAll'
    );


if(blogCheckAll){

    blogCheckAll.addEventListener(
        'change',
        function(){

            var checks =
                document.querySelectorAll(
                    '.blog-check'
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
 * Check Sync
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
                'blog-check'
            )
        ){

            return;

        }


        var checks =
            document.querySelectorAll(
                '.blog-check'
            );


        var checked =
            document.querySelectorAll(
                '.blog-check:checked'
            );


        if(blogCheckAll){

            blogCheckAll.checked =
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
                    'blog-delete'
                )
            ){

                var postId =
                    target.getAttribute(
                        'data-id'
                    );


                if(!postId){

                    return;

                }


                confirm(
                    '해당 NEWS를 삭제하시겠습니까?',
                    function(){

                        deleteBlog(
                            postId
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
 * Delete
 * =========================================================
 */

function deleteBlog(
    postId
){

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/blog_delete',
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
                        : 'NEWS를 삭제하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : 'NEWS가 삭제되었습니다.',
                function(){

                    location.reload();

                }
            );

        };


    xhr.send(
        'id=' +
        encodeURIComponent(
            postId
        )
    );

}



/*
 * =========================================================
 * 선택 삭제
 * =========================================================
 */

var blogSelectedDelete =
    document.getElementById(
        'blogSelectedDelete'
    );


if(blogSelectedDelete){

    blogSelectedDelete.addEventListener(
        'click',
        function(){

            var checks =
                document.querySelectorAll(
                    '.blog-check:checked'
                );


            if(checks.length <= 0){

                alert(
                    '삭제할 NEWS를 선택해주세요.'
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
                '개의 NEWS를 삭제하시겠습니까?',
                function(){

                    deleteSelectedBlogs(
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
 * 선택 삭제 순차 처리
 * =========================================================
 */

function deleteSelectedBlogs(
    ids,
    index
){

    if(
        index >= ids.length
    ){

        alert(
            '선택한 NEWS가 삭제되었습니다.',
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
        '/manage/proc/blog_delete',
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
                        : 'NEWS를 삭제하지 못했습니다.'
                );

                return;

            }


            deleteSelectedBlogs(
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