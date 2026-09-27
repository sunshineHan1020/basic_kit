<?
/*
 * =========================================================
 * WEEKENDS CMS
 * FILM 등록 / 수정
 *
 * FILE
 * /manage/film_write.php
 *
 * URL
 * /manage/film_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function film_write_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$film_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $film_id > 0
        ? true
        : false;



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$film =
    array(
        'id'            => 0,
        'media_id'      => 0,
        'title'         => '',
        'link_url'      => '',
        'sort_order'    => 0,
        'is_active'     => 1,
        'created_at'    => '',
        'updated_at'    => '',
        'image_path'    => '',
        'original_name' => ''
    );



/*
 * =========================================================
 * 수정 데이터
 * =========================================================
 */

if($is_edit){

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
            m.original_name

        FROM films f

        INNER JOIN media m
            ON m.id = f.media_id
            AND m.deleted_at IS NULL

        WHERE
            f.id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $film_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/film_list'
        );

        exit;

    }


    $film =
        $data;

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

                    <?
                    if($is_edit){
                    ?>

                        FILM 수정

                    <?
                    }else{
                    ?>

                        FILM 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="film-page-action">

                <a
                    href="/manage/film_list"
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
            id="filmForm"
            method="post"
            enctype="multipart/form-data"
            action="javascript:void(0);"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($film['id'])?>"
            >


            <div class="card film-form-card">

                <div class="card-header">

                    <h3>
                        FILM 정보
                    </h3>

                </div>


                <div class="card-body">


                    <!-- =================================================
                         Image
                    ================================================== -->

                    <div class="film-form-row">

                        <label>

                            이미지

                            <em>*</em>

                        </label>


                        <div class="film-form-control">


                            <div
                                class="film-upload-area"
                                id="filmUploadArea"
                            >

                                <input
                                    type="file"
                                    name="image"
                                    id="filmImageInput"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                >


                                <button
                                    type="button"
                                    class="film-upload-button"
                                    id="filmImageButton"
                                >

                                    <i class="ri-image-add-line"></i>

                                    <span>

                                        <?
                                        if($is_edit){
                                        ?>

                                            이미지 교체

                                        <?
                                        }else{
                                        ?>

                                            이미지 업로드

                                        <?
                                        }
                                        ?>

                                    </span>

                                </button>

                            </div>


                            <div class="film-upload-guide">

                                <p>
                                    JPG, PNG, GIF, WEBP
                                </p>

                                <p>
                                    최대 5MB
                                </p>

                            </div>



                            <!-- Preview -->

                            <div
                                class="film-image-preview <?=($film['image_path'] != '' ? 'active' : '')?>"
                                id="filmImagePreview"
                            >

                                <img
                                    src="<?=film_write_h($film['image_path'])?>"
                                    alt=""
                                    id="filmImagePreviewImage"
                                    <?=($film['image_path'] == '' ? 'style="display:none;"' : '')?>
                                >


                                <?
                                if(
                                    $film['original_name'] != ''
                                ){
                                ?>

                                    <div
                                        class="film-image-name"
                                        id="filmImageName"
                                    >

                                        <?=film_write_h(
                                            $film['original_name']
                                        )?>

                                    </div>

                                <?
                                }else{
                                ?>

                                    <div
                                        class="film-image-name"
                                        id="filmImageName"
                                    ></div>

                                <?
                                }
                                ?>

                            </div>


                            <?
                            if($is_edit){
                            ?>

                                <p class="film-form-help">

                                    새 이미지를 선택하지 않으면 기존 이미지가 유지됩니다.

                                </p>

                            <?
                            }
                            ?>


                        </div>

                    </div>



                    <!-- =================================================
                         Title
                    ================================================== -->

                    <div class="film-form-row">

                        <label>

                            제목

                            <em>*</em>

                        </label>


                        <div class="film-form-control">

                            <input
                                type="text"
                                name="title"
                                value="<?=film_write_h($film['title'])?>"
                                maxlength="255"
                                placeholder="FILM 제목을 입력해주세요."
                            >

                        </div>

                    </div>



                    <!-- =================================================
                         Link
                    ================================================== -->

                    <div class="film-form-row">

                        <label>
                            링크
                        </label>


                        <div class="film-form-control">

                            <input
                                type="text"
                                name="link_url"
                                value="<?=film_write_h($film['link_url'])?>"
                                maxlength="2048"
                                placeholder="https://..."
                            >


                            <p class="film-form-help">

                                FILM 클릭 시 이동할 외부 링크를 입력해주세요.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         Sort
                    ================================================== -->

                    <div class="film-form-row">

                        <label>
                            노출 순서
                        </label>


                        <div class="film-form-control film-form-small">

                            <input
                                type="number"
                                name="sort_order"
                                value="<?=intval($film['sort_order'])?>"
                                min="0"
                                step="1"
                            >


                            <p class="film-form-help">

                                숫자가 작을수록 먼저 노출됩니다.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         Active
                    ================================================== -->

                    <div class="film-form-row">

                        <label>
                            노출 여부
                        </label>


                        <div class="film-form-control">

                            <div class="film-radio-group">

                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        <?=intval($film['is_active']) == 1 ? 'checked' : ''?>
                                    >

                                    <span>
                                        노출
                                    </span>

                                </label>


                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="0"
                                        <?=intval($film['is_active']) == 0 ? 'checked' : ''?>
                                    >

                                    <span>
                                        비노출
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

            <div class="film-form-actions">

                <a
                    href="/manage/film_list"
                    class="btn btn-white"
                >
                    취소
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="filmSubmit"
                >

                    <?
                    if($is_edit){
                    ?>

                        수정 저장

                    <?
                    }else{
                    ?>

                        등록하기

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
 * Elements
 * =========================================================
 */

var filmForm =
    document.getElementById(
        'filmForm'
    );


var filmSubmit =
    document.getElementById(
        'filmSubmit'
    );


var filmImageInput =
    document.getElementById(
        'filmImageInput'
    );


var filmImageButton =
    document.getElementById(
        'filmImageButton'
    );


var filmImagePreview =
    document.getElementById(
        'filmImagePreview'
    );


var filmImagePreviewImage =
    document.getElementById(
        'filmImagePreviewImage'
    );


var filmImageName =
    document.getElementById(
        'filmImageName'
    );


var isEdit =
    <?=($is_edit ? 'true' : 'false')?>;



/*
 * =========================================================
 * Image Select
 * =========================================================
 */

filmImageButton.addEventListener(
    'click',
    function(){

        filmImageInput.click();

    }
);



filmImageInput.addEventListener(
    'change',
    function(){

        if(
            !this.files ||
            !this.files[0]
        ){

            return;

        }


        var file =
            this.files[0];



        /*
         * Size
         */

        if(
            file.size >
            5 * 1024 * 1024
        ){

            alert(
                '이미지는 최대 5MB까지 업로드할 수 있습니다.'
            );


            this.value =
                '';


            return;

        }



        /*
         * Type
         */

        var allowedTypes =
            [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp'
            ];


        var allowed =
            false;


        for(
            var i = 0;
            i < allowedTypes.length;
            i++
        ){

            if(
                file.type ==
                allowedTypes[i]
            ){

                allowed =
                    true;

                break;

            }

        }


        if(!allowed){

            alert(
                'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.'
            );


            this.value =
                '';


            return;

        }



        /*
         * Preview
         */

        var reader =
            new FileReader();


        reader.onload =
            function(e){

                filmImagePreviewImage.src =
                    e.target.result;


                filmImagePreviewImage.style.display =
                    'block';


                filmImageName.innerHTML =
                    escapeHtml(
                        file.name
                    );


                filmImagePreview.classList.add(
                    'active'
                );

            };


        reader.readAsDataURL(
            file
        );

    }
);



/*
 * =========================================================
 * Submit
 * =========================================================
 */

filmForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();



        /*
         * 신규 이미지 필수
         */

        if(
            !isEdit &&
            (
                !filmImageInput.files ||
                !filmImageInput.files[0]
            )
        ){

            alert(
                'FILM 이미지를 등록해주세요.'
            );

            return;

        }



        /*
         * Title
         */

        var title =
            filmForm.title.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(
            title == ''
        ){

            alert(
                '제목을 입력해주세요.',
                function(){

                    filmForm.title.focus();

                }
            );

            return;

        }



        /*
         * Link
         */

        var linkUrl =
            filmForm.link_url.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(
            linkUrl != '' &&
            !/^https?:\/\//i.test(
                linkUrl
            )
        ){

            alert(
                '링크는 http:// 또는 https:// 로 시작해주세요.',
                function(){

                    filmForm.link_url.focus();

                }
            );

            return;

        }



        /*
         * Sort
         */

        var sortOrder =
            parseInt(
                filmForm.sort_order.value,
                10
            );


        if(
            isNaN(sortOrder) ||
            sortOrder < 0
        ){

            alert(
                '노출 순서를 확인해주세요.',
                function(){

                    filmForm.sort_order.focus();

                }
            );

            return;

        }



        /*
         * Active
         */

        var activeRadios =
            filmForm.elements[
                'is_active'
            ];


        var isActive =
            '1';


        for(
            var i = 0;
            i < activeRadios.length;
            i++
        ){

            if(
                activeRadios[i].checked
            ){

                isActive =
                    activeRadios[i].value;

                break;

            }

        }



        /*
         * Save
         */

        saveFilm();

    }
);



/*
 * =========================================================
 * Save
 * =========================================================
 */

function saveFilm()
{

    if(
        filmSubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    filmSubmit.setAttribute(
        'data-loading',
        'Y'
    );


    filmSubmit.disabled =
        true;



    var formData =
        new FormData(
            filmForm
        );


    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/film_save',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){

                return;

            }


            filmSubmit.removeAttribute(
                'data-loading'
            );


            filmSubmit.disabled =
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
                        : 'FILM을 저장하지 못했습니다.'
                );

                return;

            }



            alert(
                result.msg
                    ? result.msg
                    : 'FILM이 저장되었습니다.',
                function(){

                    location.href =
                        '/manage/film_list';

                }
            );

        };


    xhr.send(
        formData
    );

}



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function escapeHtml(
    value
){

    return String(
        value
    )
    .replace(
        /&/g,
        '&amp;'
    )
    .replace(
        /</g,
        '&lt;'
    )
    .replace(
        />/g,
        '&gt;'
    )
    .replace(
        /"/g,
        '&quot;'
    )
    .replace(
        /'/g,
        '&#039;'
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>