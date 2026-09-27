<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 태그 등록 / 수정
 *
 * FILE
 * /manage/blog_tag_write.php
 *
 * URL
 * /manage/blog_tag_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



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
 * ID
 * =========================================================
 */

$tag_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $tag_id > 0
        ? true
        : false;



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$tag =
    array(
        'id'         => 0,
        'board_id'   => $board_id,
        'name'       => '',
        'slug'       => '',
        'sort_order' => 0,
        'is_active'  => 1
    );



/*
 * =========================================================
 * 수정 데이터
 * =========================================================
 */

if($is_edit){

    $query = "

        SELECT
            id,
            board_id,
            name,
            slug,
            sort_order,
            is_active

        FROM post_tags

        WHERE
            id = ?
            AND board_id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $tag_id,
                $board_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/blog_tag_list'
        );

        exit;

    }


    $tag =
        $data;

}



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function blog_tag_write_h(
    $str
){

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
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

                    <?
                    if($is_edit){
                    ?>

                        태그 수정

                    <?
                    }else{
                    ?>

                        태그 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="blog-page-action">

                <a
                    href="/manage/blog_tag_list"
                    class="btn btn-white"
                >

                    <i class="ri-list-check"></i>

                    목록

                </a>

            </div>

        </div>



        <!-- =================================================
             Form
        ================================================== -->

        <form
            id="tagForm"
            method="post"
            action="javascript:void(0);"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($tag['id'])?>"
            >


            <div class="card blog-form-card">

                <div class="card-header">

                    <h3>
                        태그 정보
                    </h3>

                </div>


                <div class="card-body">


                    <!-- =================================================
                         태그명
                    ================================================== -->

                    <div class="form-row">

                        <div class="form-group blog-form-full">

                            <label>

                                태그명

                                <em>*</em>

                            </label>


                            <input
                                type="text"
                                name="name"
                                value="<?=blog_tag_write_h($tag['name'])?>"
                                placeholder="태그명을 입력해주세요."
                                maxlength="100"
                            >

                        </div>

                    </div>



                    <!-- =================================================
                         Slug
                    ================================================== -->

                    <div class="form-row">

                        <div class="form-group blog-form-full">

                            <label>

                                Slug

                                <em>*</em>

                            </label>


                            <div class="blog-slug-input">

                                <span>
                                    #
                                </span>


                                <input
                                    type="text"
                                    name="slug"
                                    value="<?=blog_tag_write_h($tag['slug'])?>"
                                    placeholder="press"
                                    maxlength="180"
                                >

                            </div>


                            <p class="form-help">

                                영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         순서 / 사용 여부
                    ================================================== -->

                    <div class="form-row">


                        <!-- Sort -->

                        <div class="form-group">

                            <label>
                                노출 순서
                            </label>


                            <input
                                type="number"
                                name="sort_order"
                                value="<?=intval($tag['sort_order'])?>"
                                min="0"
                            >


                            <p class="form-help">

                                숫자가 낮을수록 먼저 노출됩니다.

                            </p>

                        </div>



                        <!-- Active -->

                        <div class="form-group">

                            <label>
                                사용 여부
                            </label>


                            <div class="blog-radio-group">


                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        <?=intval($tag['is_active']) == 1 ? 'checked' : ''?>
                                    >

                                    <span>
                                        사용
                                    </span>

                                </label>


                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="0"
                                        <?=intval($tag['is_active']) == 0 ? 'checked' : ''?>
                                    >

                                    <span>
                                        미사용
                                    </span>

                                </label>


                            </div>

                        </div>


                    </div>


                </div>

            </div>



            <!-- =================================================
                 Actions
            ================================================== -->

            <div class="blog-form-actions">

                <a
                    href="/manage/blog_tag_list"
                    class="btn btn-white"
                >
                    취소
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="tagSubmit"
                >

                    <?
                    if($is_edit){
                    ?>

                        수정 저장

                    <?
                    }else{
                    ?>

                        태그 등록

                    <?
                    }
                    ?>

                </button>

            </div>


        </form>


    </div>

</main>



<script>

/*
 * =========================================================
 * Form
 * =========================================================
 */

var tagForm =
    document.getElementById(
        'tagForm'
    );


var tagSubmit =
    document.getElementById(
        'tagSubmit'
    );



/*
 * =========================================================
 * Slug
 * =========================================================
 */

tagForm.slug.addEventListener(
    'input',
    function(){

        var value =
            this.value;


        /*
         * lowercase
         */

        value =
            value.toLowerCase();


        /*
         * 공백 -> 하이픈
         */

        value =
            value.replace(
                /\s+/g,
                '-'
            );


        /*
         * 허용 문자
         */

        value =
            value.replace(
                /[^a-z0-9\-]/g,
                ''
            );


        /*
         * 연속 하이픈
         */

        value =
            value.replace(
                /\-+/g,
                '-'
            );


        /*
         * 앞 하이픈 제거
         */

        value =
            value.replace(
                /^\-+/,
                ''
            );


        this.value =
            value;

    }
);



/*
 * =========================================================
 * Submit
 * =========================================================
 */

tagForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();



        /*
         * Name
         */

        var name =
            tagForm.name.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(name == ''){

            alert(
                '태그명을 입력해주세요.',
                function(){

                    tagForm.name.focus();

                }
            );

            return;

        }



        /*
         * Slug
         */

        var slug =
            tagForm.slug.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(slug == ''){

            alert(
                'Slug를 입력해주세요.',
                function(){

                    tagForm.slug.focus();

                }
            );

            return;

        }


        if(
            !/^[a-z0-9\-]+$/.test(
                slug
            )
        ){

            alert(
                'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
                function(){

                    tagForm.slug.focus();

                }
            );

            return;

        }



        /*
         * Sort
         */

        var sortOrder =
            parseInt(
                tagForm.sort_order.value,
                10
            );


        if(
            isNaN(sortOrder) ||
            sortOrder < 0
        ){

            alert(
                '노출 순서를 확인해주세요.',
                function(){

                    tagForm.sort_order.focus();

                }
            );

            return;

        }



        /*
         * Save
         */

        saveBlogTag();

    }
);



/*
 * =========================================================
 * Save
 * =========================================================
 */

function saveBlogTag()
{

    /*
     * 중복 클릭 방지
     */

    if(
        tagSubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    tagSubmit.setAttribute(
        'data-loading',
        'Y'
    );


    tagSubmit.disabled =
        true;



    /*
     * Params
     */

    var params =
        'id=' +
        encodeURIComponent(
            tagForm.id.value
        ) +

        '&name=' +
        encodeURIComponent(
            tagForm.name.value
        ) +

        '&slug=' +
        encodeURIComponent(
            tagForm.slug.value
        ) +

        '&sort_order=' +
        encodeURIComponent(
            tagForm.sort_order.value
        ) +

        '&is_active=' +
        encodeURIComponent(
            tagForm.is_active.value
        );



    /*
     * AJAX
     */

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/blog_tag_save',
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


            tagSubmit.removeAttribute(
                'data-loading'
            );


            tagSubmit.disabled =
                false;



            /*
             * HTTP Error
             */

            if(
                xhr.status != 200
            ){

                alert(
                    '저장 처리 중 오류가 발생했습니다.'
                );

                return;

            }



            /*
             * JSON
             */

            var result;


            try{

                result =
                    JSON.parse(
                        xhr.responseText
                    );

            }catch(e){

                alert(
                    '저장 처리 중 오류가 발생했습니다.'
                );

                return;

            }



            /*
             * Error
             */

            if(!result.code){

                alert(
                    result.msg
                        ? result.msg
                        : '태그를 저장하지 못했습니다.'
                );

                return;

            }



            /*
             * Success
             */

            alert(
                result.msg
                    ? result.msg
                    : '태그가 저장되었습니다.',
                function(){

                    location.href =
                        '/manage/blog_tag_list';

                }
            );

        };


    xhr.send(
        params
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>