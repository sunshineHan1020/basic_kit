<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 카테고리 관리
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



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function works_category_h($str)
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

    FROM work_categories

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
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
 * 카테고리 목록
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        c.id,
        c.name,
        c.slug,
        c.sort_order,
        c.is_active,

        COUNT(w.id) AS work_count

    FROM work_categories c

    LEFT JOIN works w
        ON w.category_id = c.id
        AND w.deleted_at IS NULL

    GROUP BY
        c.id,
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
        array()
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
                    카테고리 관리
                </h2>

            </div>


            <div class="works-page-action">

                <a
                    href="/manage/works_category_write"
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
                    WORK 카테고리
                </h3>

            </div>


            <div class="card-body">


                <!-- =================================================
                     Toolbar
                ================================================== -->

                <div class="works-toolbar">

                    <div class="works-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        개

                    </div>


                    <div class="works-toolbar-right">

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

                <div class="works-table-wrap">

                    <table class="works-table works-category-table">

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
                                    WORK
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


                                <!-- 체크 -->

                                <td>

                                    <input
                                        type="checkbox"
                                        class="category-check"
                                        value="<?=$data['id']?>"
                                        data-count="<?=intval($data['work_count'])?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Category -->

                                <td>

                                    <div class="work-category-name">

                                        <strong>

                                            <?=works_category_h(
                                                $data['name']
                                            )?>

                                        </strong>

                                    </div>

                                </td>



                                <!-- Slug -->

                                <td>

                                    <span class="work-category-slug">

                                        /<?=works_category_h(
                                            $data['slug']
                                        )?>

                                    </span>

                                </td>



                                <!-- Work Count -->

                                <td>

                                    <?
                                    if(
                                        intval(
                                            $data['work_count']
                                        ) > 0
                                    ){
                                    ?>

                                        <a
                                            href="/manage/works_list?category_id=<?=$data['id']?>"
                                            class="work-category-count"
                                        >

                                            <?=number_format(
                                                $data['work_count']
                                            )?>

                                            건

                                        </a>

                                    <?
                                    }else{
                                    ?>

                                        <span class="work-category-count zero">

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

                                        <span class="work-status published">

                                            사용

                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="work-status archived">

                                            미사용

                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Manage -->

                                <td>

                                    <div class="works-action">

                                        <a
                                            href="/manage/works_category_write?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm work-category-delete"
                                            data-id="<?=$data['id']?>"
                                            data-count="<?=intval($data['work_count'])?>"
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
                                    class="works-empty"
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

                <div class="works-pagination">


                    <?
                    if($page > 1){
                    ?>

                        <a
                            href="/manage/works_category_list?page=<?=($page - 1)?>&limit=<?=$limit?>"
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
                                href="/manage/works_category_list?page=<?=$i?>&limit=<?=$limit?>"
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
                            href="/manage/works_category_list?page=<?=($page + 1)?>&limit=<?=$limit?>"
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

var categoryLimit =
    document.getElementById(
        'categoryLimit'
    );


if(categoryLimit){

    categoryLimit.addEventListener(
        'change',
        function(){

            window.location.href =
                '/manage/works_category_list' +
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
 * 전체 선택
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


        if(
            !target.classList ||
            !target.classList.contains(
                'work-category-delete'
            )
        ){
            return;
        }


        var categoryId =
            target.getAttribute(
                'data-id'
            );


        var workCount =
            parseInt(
                target.getAttribute(
                    'data-count'
                ),
                10
            );


        if(!categoryId){
            return;
        }


        if(workCount > 0){

            alert(
                'WORK가 등록되어 있는 카테고리는 삭제할 수 없습니다.'
            );

            return;

        }


        confirm(
            '해당 카테고리를 삭제하시겠습니까?',
            function(){

                deleteWorkCategory(
                    categoryId
                );

            }
        );

    }
);



/*
 * =========================================================
 * 개별 삭제 처리
 * =========================================================
 */

function deleteWorkCategory(
    categoryId
){

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/work_category_delete',
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


            var usingCategoryCount =
                0;


            for(
                var i = 0;
                i < checks.length;
                i++
            ){

                var workCount =
                    parseInt(
                        checks[i].getAttribute(
                            'data-count'
                        ),
                        10
                    );


                if(workCount > 0){

                    usingCategoryCount++;

                }


                ids.push(
                    checks[i].value
                );

            }


            if(usingCategoryCount > 0){

                alert(
                    'WORK가 등록되어 있는 카테고리가 포함되어 있습니다.<br>사용 중인 카테고리는 삭제할 수 없습니다.'
                );

                return;
            }


            confirm(
                '선택한 ' +
                ids.length +
                '개의 카테고리를 삭제하시겠습니까?',
                function(){

                    deleteSelectedCategories(
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

function deleteSelectedCategories(
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
        '/manage/proc/work_category_delete',
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


            deleteSelectedCategories(
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