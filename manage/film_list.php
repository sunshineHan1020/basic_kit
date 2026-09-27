<?
/*
 * =========================================================
 * WEEKENDS CMS
 * FILM 관리
 *
 * FILE
 * /manage/film_list.php
 *
 * URL
 * /manage/film_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function film_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



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
 * 검색조건
 * =========================================================
 */

$where =
    array();


$params =
    array();



/*
 * Keyword
 */

if(
    $keyword != ''
){

    $where[] = "

        (
            f.title LIKE ?
            OR f.link_url LIKE ?
        )

    ";


    $search_keyword =
        '%'.$keyword.'%';


    $params[] =
        $search_keyword;


    $params[] =
        $search_keyword;

}



/*
 * Active
 */

if(
    $is_active != ''
){

    $where[] =
        "f.is_active = ?";


    $params[] =
        intval(
            $is_active
        );

}



/*
 * WHERE
 */

$where_sql =
    '';


if(
    count($where) > 0
){

    $where_sql =
        'WHERE '.
        implode(
            ' AND ',
            $where
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

    FROM films f

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

}


$offset =
    ($page - 1) *
    $limit;



/*
 * =========================================================
 * FILM 목록
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        f.id,
        f.media_id,
        f.title,
        f.link_url,
        f.sort_order,
        f.is_active,
        f.created_at,
        f.updated_at,

        m.storage_key AS image_path,
        m.original_name AS image_name

    FROM films f

    INNER JOIN media m
        ON m.id = f.media_id
        AND m.deleted_at IS NULL

    ".$where_sql."

    ORDER BY
        f.sort_order ASC,
        f.id DESC

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

function film_list_url(
    $page,
    $limit,
    $keyword,
    $is_active
){

    return

        '/manage/film_list?'.

        http_build_query(
            array(
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
    href="/manage/css/film.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             Page Header
        ================================================== -->

        <div class="film-page-head">

            <div class="film-page-title">

                <span>
                    FILM
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    FILM 관리
                </h2>

            </div>


            <div class="film-page-action">

                <a
                    href="/manage/film_write"
                    class="btn btn-primary"
                >

                    <i class="ri-add-line"></i>

                    FILM 등록

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
                    action="/manage/film_list"
                    id="filmSearchForm"
                >

                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="film-search-grid">


                        <div class="form-group">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=film_h($keyword)?>"
                                placeholder="제목 또는 링크"
                            >

                        </div>


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


                    <div class="film-search-action">

                        <a
                            href="/manage/film_list"
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

        <div class="card film-list-card">

            <div class="card-header">

                <h3>
                    FILM 목록
                </h3>

            </div>


            <div class="card-body">


                <!-- Toolbar -->

                <div class="film-toolbar">

                    <div class="film-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        개

                    </div>


                    <div class="film-toolbar-right">

                        <button
                            type="button"
                            class="btn btn-white"
                            id="filmSelectedDelete"
                        >

                            <i class="ri-delete-bin-line"></i>

                            선택삭제

                        </button>


                        <select id="filmLimit">

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

                <div class="film-table-wrap">

                    <table class="film-table">

                        <thead>

                            <tr>

                                <th width="42">

                                    <input
                                        type="checkbox"
                                        id="filmCheckAll"
                                    >

                                </th>

                                <th width="65">
                                    No.
                                </th>

                                <th width="150">
                                    이미지
                                </th>

                                <th>
                                    제목
                                </th>

                                <th width="300">
                                    링크
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
                                        class="film-check"
                                        value="<?=$data['id']?>"
                                    >

                                </td>



                                <!-- No -->

                                <td>

                                    <?=number_format($no)?>

                                </td>



                                <!-- Image -->

                                <td>

                                    <div class="film-thumb">

                                        <img
                                            src="<?=film_h($data['image_path'])?>"
                                            alt="<?=film_h($data['title'])?>"
                                        >

                                    </div>

                                </td>



                                <!-- Title -->

                                <td>

                                    <div class="film-title">

                                        <a
                                            href="/manage/film_write?id=<?=$data['id']?>"
                                        >

                                            <?=film_h(
                                                $data['title']
                                            )?>

                                        </a>

                                    </div>

                                </td>



                                <!-- Link -->

                                <td>

                                    <?
                                    if(
                                        isset($data['link_url']) &&
                                        $data['link_url'] != ''
                                    ){
                                    ?>

                                        <a
                                            href="<?=film_h($data['link_url'])?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="film-link"
                                        >

                                            <span>

                                                <?=film_h(
                                                    $data['link_url']
                                                )?>

                                            </span>


                                            <i class="ri-external-link-line"></i>

                                        </a>

                                    <?
                                    }else{
                                    ?>

                                        <span class="film-muted">
                                            -
                                        </span>

                                    <?
                                    }
                                    ?>

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

                                        <span class="film-status active">
                                            노출
                                        </span>

                                    <?
                                    }else{
                                    ?>

                                        <span class="film-status inactive">
                                            비노출
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <!-- Action -->

                                <td>

                                    <div class="film-action">

                                        <a
                                            href="/manage/film_write?id=<?=$data['id']?>"
                                            class="btn btn-white btn-sm"
                                        >
                                            수정
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm film-delete"
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
                                    class="film-empty"
                                >

                                    <i class="ri-film-line"></i>

                                    등록된 FILM이 없습니다.

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

                <div class="film-pagination">


                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="<?=film_h(
                                film_list_url(
                                    $page - 1,
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
                                href="<?=film_h(
                                    film_list_url(
                                        $i,
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
                        $page < $total_page
                    ){
                    ?>

                        <a
                            href="<?=film_h(
                                film_list_url(
                                    $page + 1,
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
 * Limit
 * =========================================================
 */

var filmLimit =
    document.getElementById(
        'filmLimit'
    );


if(filmLimit){

    filmLimit.addEventListener(
        'change',
        function(){

            var url =
                '/manage/film_list' +

                '?page=1' +

                '&limit=' +
                encodeURIComponent(
                    this.value
                ) +

                '&keyword=' +
                encodeURIComponent(
                    '<?=film_h($keyword)?>'
                ) +

                '&is_active=' +
                encodeURIComponent(
                    '<?=film_h($is_active)?>'
                );


            location.href =
                url;

        }
    );

}



/*
 * =========================================================
 * 전체 선택
 * =========================================================
 */

var filmCheckAll =
    document.getElementById(
        'filmCheckAll'
    );


if(filmCheckAll){

    filmCheckAll.addEventListener(
        'change',
        function(){

            var checks =
                document.querySelectorAll(
                    '.film-check'
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
 * 체크박스 Sync
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
                'film-check'
            )
        ){

            return;

        }


        var checks =
            document.querySelectorAll(
                '.film-check'
            );


        var checked =
            document.querySelectorAll(
                '.film-check:checked'
            );


        if(filmCheckAll){

            filmCheckAll.checked =
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
                    'film-delete'
                )
            ){

                var filmId =
                    target.getAttribute(
                        'data-id'
                    );


                if(!filmId){

                    return;

                }


                confirm(
                    '해당 FILM을 삭제하시겠습니까?',
                    function(){

                        deleteFilm(
                            filmId,
                            function(){

                                alert(
                                    'FILM이 삭제되었습니다.',
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

var filmSelectedDelete =
    document.getElementById(
        'filmSelectedDelete'
    );


if(filmSelectedDelete){

    filmSelectedDelete.addEventListener(
        'click',
        function(){

            var checks =
                document.querySelectorAll(
                    '.film-check:checked'
                );


            if(
                checks.length <= 0
            ){

                alert(
                    '삭제할 FILM을 선택해주세요.'
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
                '개의 FILM을 삭제하시겠습니까?',
                function(){

                    deleteSelectedFilms(
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
 * 삭제 AJAX
 * =========================================================
 */

function deleteFilm(
    filmId,
    callback
){

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/film_delete',
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
                        : 'FILM을 삭제하지 못했습니다.'
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
            filmId
        )
    );

}



/*
 * =========================================================
 * 선택 삭제 순차 처리
 * =========================================================
 */

function deleteSelectedFilms(
    ids,
    index
){

    if(
        index >= ids.length
    ){

        alert(
            '선택한 FILM이 삭제되었습니다.',
            function(){

                location.reload();

            }
        );

        return;

    }


    deleteFilm(
        ids[index],
        function(){

            deleteSelectedFilms(
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