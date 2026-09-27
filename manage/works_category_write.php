<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 카테고리 등록 / 수정
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * ID
 * =========================================================
 */

$category_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $category_id > 0
        ? true
        : false;



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$category =
    array(
        'id'         => 0,
        'name'       => '',
        'slug'       => '',
        'sort_order' => 0,
        'is_active'  => 1
    );



/*
 * =========================================================
 * 수정
 * =========================================================
 */

if($is_edit){

    $query = "

        SELECT
            id,
            name,
            slug,
            sort_order,
            is_active

        FROM work_categories

        WHERE
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $category_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/works_category_list'
        );

        exit;

    }


    $category =
        $data;

}



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function works_category_write_h($str)
{

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

                    <?
                    if($is_edit){
                    ?>

                        카테고리 수정

                    <?
                    }else{
                    ?>

                        카테고리 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="works-page-action">

                <a
                    href="/manage/works_category_list"
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
            id="categoryForm"
            method="post"
            action="javascript:void(0);"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($category['id'])?>"
            >


            <div class="card works-form-card">

                <div class="card-header">

                    <h3>
                        카테고리 정보
                    </h3>

                </div>


                <div class="card-body">


                    <!-- =================================================
                         카테고리명
                    ================================================== -->

                    <div class="form-row">

                        <div class="form-group works-form-full">

                            <label>

                                카테고리명

                                <em>*</em>

                            </label>

                            <input
                                type="text"
                                name="name"
                                value="<?=works_category_write_h($category['name'])?>"
                                placeholder="카테고리명을 입력해주세요."
                                maxlength="100"
                            >

                        </div>

                    </div>



                    <!-- =================================================
                         Slug
                    ================================================== -->

                    <div class="form-row">

                        <div class="form-group works-form-full">

                            <label>

                                Slug

                                <em>*</em>

                            </label>


                            <div class="works-slug-input">

                                <span>
                                    /
                                </span>

                                <input
                                    type="text"
                                    name="slug"
                                    value="<?=works_category_write_h($category['slug'])?>"
                                    placeholder="ex. branding"
                                    maxlength="180"
                                >

                            </div>


                            <p class="form-help">

                                영문 소문자, 숫자, 하이픈(-) 사용을 권장합니다.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         순서
                    ================================================== -->

                    <div class="form-row">

                        <div class="form-group">

                            <label>
                                노출 순서
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                value="<?=intval($category['sort_order'])?>"
                                min="0"
                            >


                            <p class="form-help">

                                숫자가 낮을수록 먼저 노출됩니다.

                            </p>

                        </div>



                        <!-- =================================================
                             사용 여부
                        ================================================== -->

                        <div class="form-group">

                            <label>
                                사용 여부
                            </label>


                            <div class="works-radio-group">


                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        <?=intval($category['is_active']) == 1 ? 'checked' : ''?>
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
                                        <?=intval($category['is_active']) == 0 ? 'checked' : ''?>
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
                 Buttons
            ================================================== -->

            <div class="works-form-actions">

                <a
                    href="/manage/works_category_list"
                    class="btn btn-white"
                >
                    취소
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="categorySubmit"
                >

                    <?
                    if($is_edit){
                    ?>

                        수정 저장

                    <?
                    }else{
                    ?>

                        카테고리 등록

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

var categoryForm =
    document.getElementById(
        'categoryForm'
    );


var categorySubmit =
    document.getElementById(
        'categorySubmit'
    );



/*
 * =========================================================
 * Slug 입력 정리
 * =========================================================
 */

categoryForm.slug.addEventListener(
    'input',
    function(){

        var value =
            this.value;


        /*
         * 소문자
         */

        value =
            value.toLowerCase();


        /*
         * 공백 -> -
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
         * 앞 하이픈
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

categoryForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();


        /*
         * 카테고리명
         */

        var name =
            categoryForm.name.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(name == ''){

            alert(
                '카테고리명을 입력해주세요.',
                function(){

                    categoryForm.name.focus();

                }
            );

            return;

        }



        /*
         * Slug
         */

        var slug =
            categoryForm.slug.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(slug == ''){

            alert(
                'Slug를 입력해주세요.',
                function(){

                    categoryForm.slug.focus();

                }
            );

            return;

        }



        /*
         * Slug 형식
         */

        if(
            !/^[a-z0-9\-]+$/.test(
                slug
            )
        ){

            alert(
                'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
                function(){

                    categoryForm.slug.focus();

                }
            );

            return;

        }



        /*
         * 순서
         */

        var sortOrder =
            parseInt(
                categoryForm.sort_order.value,
                10
            );


        if(
            isNaN(sortOrder) ||
            sortOrder < 0
        ){

            alert(
                '노출 순서를 확인해주세요.',
                function(){

                    categoryForm.sort_order.focus();

                }
            );

            return;

        }



        /*
         * 저장
         */

        saveWorkCategory();

    }
);



/*
 * =========================================================
 * Save
 * =========================================================
 */

function saveWorkCategory()
{

    /*
     * 중복 클릭 방지
     */

    if(
        categorySubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    categorySubmit.setAttribute(
        'data-loading',
        'Y'
    );


    categorySubmit.disabled =
        true;


    var params =
        'id=' +
        encodeURIComponent(
            categoryForm.id.value
        ) +

        '&name=' +
        encodeURIComponent(
            categoryForm.name.value
        ) +

        '&slug=' +
        encodeURIComponent(
            categoryForm.slug.value
        ) +

        '&sort_order=' +
        encodeURIComponent(
            categoryForm.sort_order.value
        ) +

        '&is_active=' +
        encodeURIComponent(
            categoryForm.is_active.value
        );



    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/work_category_save',
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


            categorySubmit.removeAttribute(
                'data-loading'
            );


            categorySubmit.disabled =
                false;


            if(
                xhr.status != 200
            ){

                alert(
                    '저장 처리 중 오류가 발생했습니다.'
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
                    '저장 처리 중 오류가 발생했습니다.'
                );

                return;

            }


            if(!result.code){

                alert(
                    result.msg
                        ? result.msg
                        : '저장하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : '저장되었습니다.',
                function(){

                    location.href =
                        '/manage/works_category_list';

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