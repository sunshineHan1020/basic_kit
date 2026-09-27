<?
/*
 * =========================================================
 * WEEKENDS CMS
 * BLOG 카테고리 관리
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * BLOG Board
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
        'BLOG 게시판 정보가 등록되어 있지 않습니다.'
    );

}


$board_id =
    intval(
        $board['id']
    );



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
 * Escape
 * =========================================================
 */

function blog_category_h($str)
{

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );

}



/*
 * =========================================================
 * 전체 개수
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM post_categories

    WHERE
        board_id = ?

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $board_id
        )
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
 * 카테고리 목록
 *
 * post_count는 삭제 가능 여부와 맞추기 위해
 * soft delete 여부와 관계없이 모든 참조를 계산
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        c.id,
        c.board_id,
        c.name,
        c.slug,
        c.sort_order,
        c.is_active,

        COUNT(p.id) AS post_count

    FROM post_categories c

    LEFT JOIN posts p
        ON p.board_id = c.board_id
        AND p.category_id = c.id

    WHERE
        c.board_id = ?

    GROUP BY
        c.id,
        c.board_id,
        c.name,
        c.slug,
        c.sort_order,
        c.is_active

    ORDER BY
        c.sort_order ASC,
        c.id ASC

    LIMIT ".$offset.", ".$limit."

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
                    카테고리 관리
                </h2>

            </div>


            <div class="blog-page-action">

                <a
                    href="/manage/blog_category_write"
                    class="btn btn-primary"
                >

                    <i class="ri-add-line"></i>

                    카테고리 등록

                </a>

            </div>

        </div>



        <!-- =================================================
             List
        ================================================== -->

        <div class="card">

            <div class="card-header">

                <h3>
                    NEWS 카테고리
                </h3>

            </div>


            <div class="card-body">


                <!-- =================================================
                     Toolbar
                ================================================== -->

                <div class="blog-toolbar">

                    <div class="blog-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        개

                    </div>


                    <div class="blog-toolbar-right">

                        <button
                            type="button"
                            class="btn btn-white"
                            id="categorySelectedDelete"
                        >

                            <i class="ri-delete-bin-line"></i>

                            선택삭제

                        </button>


                        <select id="categoryLimit">

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

                <div class="blog-table-wrap">

                    <table class="blog-table blog-category-table">

                        <thead>

                            <tr>

                                <th width="42">

                                    <input
                                        type="checkbox"
                                        id="categoryCheckAll"
                                    >

                                </th>

                                <th width="70">
                                    No.
                                </th>

                                <th>
                                    카테고리명
                                </th>

                                <th width="260">
                                    Slug
                                </th>

                                <th width="110">
                                    POST
                                </th>

                                <th width="90">
                                    순서
                                </th>

                                <th width="110">
                                    사용여부
                                </th>

                                <th width="170">
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


                                <!-- Check -->

                                <td>

                                    <input
                                        type="checkbox"
                                        class="category-check"
                                        value="<?=$data['id']?>"
                                        data-count="<?=intval($data['post_count'])?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Name -->

                                <td>

                                    <div class="blog-category-name">

                                        <strong>

                                            <?=blog_category_h(
                                                $data['name']
                                            )?>

                                        </strong>

                                    </div>

                                </td>



                                <!-- Slug -->

                                <td>

                                    <span class="blog-category-slug">

                                        /blog/category/<?=blog_category_h(
                                            $data['slug']
                                        )?>

                                    </span>

                                </td>



                                <!-- Post Count -->

                                <td>

                                    <?
                                    if(
                                        intval(
                                            $data['post_count']
                                        ) > 0
                                    ){
                                    ?>

                                        <a
                                            href="/manage/blog_list?category_id=<?=$data['id']?>"
                                            class="blog-category-count"
                                        >

                                            <?=number_format(
                                                $data['post_count']
                                            )?>

                                            건

                                        </a>

                                    <?
                                    }else{
                                    ?>

                                        <span class="blog-category-count zero">

                                            0건

                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Sort -->

                                <td>

                                    <?=intval(
                                        $data['sort_order']
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

                                        <span class="blog-status published">

                                            사용

                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="blog-status archived">

                                            미사용

                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Manage -->

                                <td>

                                    <div class="blog-action">

                                        <a
                                            href="/manage/blog_category_write?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm blog-category-delete"
                                            data-id="<?=$data['id']?>"
                                            data-count="<?=intval($data['post_count'])?>"
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
                                    class="blog-empty"
                                >

                                    <i class="ri-folder-open-line"></i>

                                    등록된 카테고리가 없습니다.

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
                            href="/manage/blog_category_list?page=<?=($page - 1)?>&limit=<?=$limit?>"
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
                                href="/manage/blog_category_list?page=<?=$i?>&limit=<?=$limit?>"
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
                            href="/manage/blog_category_list?page=<?=($page + 1)?>&limit=<?=$limit?>"
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

var categoryLimit =
    document.getElementById(
        'categoryLimit'
    );


if(categoryLimit){

    categoryLimit.addEventListener(
        'change',
        function(){

            location.href =
                '/manage/blog_category_list' +
                '?page=1' +
                '&limit=' +
                encodeURIComponent(
                    this.value
                );

        }
    );

}



/*
 * =========================================================
 * Check All
 * =========================================================
 */

var categoryCheckAll =
    document.getElementById(
        'categoryCheckAll'
    );


if(categoryCheckAll){

    categoryCheckAll.addEventListener(
        'change',
        function(){

            var checks =
                document.querySelectorAll(
                    '.category-check'
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
                'category-check'
            )
        ){

            return;

        }


        var checks =
            document.querySelectorAll(
                '.category-check'
            );


        var checked =
            document.querySelectorAll(
                '.category-check:checked'
            );


        if(categoryCheckAll){

            categoryCheckAll.checked =
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
                    'blog-category-delete'
                )
            ){

                var categoryId =
                    target.getAttribute(
                        'data-id'
                    );


                var postCount =
                    parseInt(
                        target.getAttribute(
                            'data-count'
                        ),
                        10
                    );


                if(!categoryId){
                    return;
                }


                if(postCount > 0){

                    alert(
                        '게시글에 사용 중인 카테고리는 삭제할 수 없습니다.'
                    );

                    return;

                }


                confirm(
                    '해당 카테고리를 삭제하시겠습니까?',
                    function(){

                        deleteBlogCategory(
                            categoryId
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

function deleteBlogCategory(
    categoryId
){

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/blog_category_delete',
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
                        : '카테고리를 삭제하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : '카테고리가 삭제되었습니다.',
                function(){

                    location.reload();

                }
            );

        };


    xhr.send(
        'id=' +
        encodeURIComponent(
            categoryId
        )
    );

}



/*
 * =========================================================
 * 선택 삭제
 * =========================================================
 */

var categorySelectedDelete =
    document.getElementById(
        'categorySelectedDelete'
    );


if(categorySelectedDelete){

    categorySelectedDelete.addEventListener(
        'click',
        function(){

            var checks =
                document.querySelectorAll(
                    '.category-check:checked'
                );


            if(checks.length <= 0){

                alert(
                    '삭제할 카테고리를 선택해주세요.'
                );

                return;

            }


            var ids =
                [];


            var usingCount =
                0;


            for(
                var i = 0;
                i < checks.length;
                i++
            ){

                var postCount =
                    parseInt(
                        checks[i].getAttribute(
                            'data-count'
                        ),
                        10
                    );


                if(postCount > 0){

                    usingCount++;

                }


                ids.push(
                    checks[i].value
                );

            }


            if(usingCount > 0){

                alert(
                    '게시글에 사용 중인 카테고리가 포함되어 있습니다.<br>사용 중인 카테고리는 삭제할 수 없습니다.'
                );

                return;

            }


            confirm(
                '선택한 ' +
                ids.length +
                '개의 카테고리를 삭제하시겠습니까?',
                function(){

                    deleteSelectedBlogCategories(
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

function deleteSelectedBlogCategories(
    ids,
    index
){

    if(
        index >= ids.length
    ){

        alert(
            '선택한 카테고리가 삭제되었습니다.',
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
        '/manage/proc/blog_category_delete',
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
                        : '카테고리를 삭제하지 못했습니다.'
                );

                return;

            }


            deleteSelectedBlogCategories(
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