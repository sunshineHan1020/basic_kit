<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 선택항목 등록 / 수정
 *
 * FILE
 * /manage/inquiry_option_write.php
 *
 * URL
 * /manage/inquiry_option_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Escape
 * =========================================================
 */

function inquiry_option_write_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * STEP CONFIG
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
 * ID
 * =========================================================
 */

$option_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $option_id > 0
        ? true
        : false;



/*
 * =========================================================
 * STEP
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



/*
 * =========================================================
 * DEFAULT
 * =========================================================
 */

$option =
    array(
        'id'          => 0,
        'step_no'     => $step,
        'option_code' => '',
        'title_en'    => '',
        'title_ko'    => '',
        'sort_order'  => 0,
        'is_active'   => 1,
        'created_at'  => '',
        'updated_at'  => ''
    );



/*
 * =========================================================
 * EDIT DATA
 * =========================================================
 */

if($is_edit){

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
            id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $option_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/inquiry_option_list'
        );

        exit;

    }


    $option =
        $data;


    $step =
        intval(
            $option['step_no']
        );


    if(
        !isset(
            $step_config[$step]
        )
    ){

        $step =
            1;

    }

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
             PAGE HEADER
        ================================================== -->

        <div class="inquiry-page-head">

            <div class="inquiry-page-title">

                <span>
                    문의 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>

                    <?
                    if($is_edit){
                    ?>

                        문의 선택항목 수정

                    <?
                    }else{
                    ?>

                        문의 선택항목 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="inquiry-page-action">

                <a
                    href="/manage/inquiry_option_list?step=<?=$step?>"
                    class="btn btn-white"
                >

                    <i class="ri-list-check-3"></i>

                    목록

                </a>

            </div>

        </div>



        <!-- =================================================
             FORM
        ================================================== -->

        <form
            id="inquiryOptionForm"
            method="post"
            action="javascript:void(0);"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($option['id'])?>"
            >


            <div class="card inquiry-form-card">

                <div class="card-header">

                    <h3>
                        선택항목 정보
                    </h3>

                </div>


                <div class="card-body">


                    <!-- =================================================
                         STEP
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>

                            단계

                            <em>*</em>

                        </label>


                        <div class="inquiry-form-control">

                            <select
                                name="step_no"
                                id="stepNo"
                            >

                                <?
                                foreach(
                                    $step_config as
                                    $step_no => $step_data
                                ){
                                ?>

                                    <option
                                        value="<?=$step_no?>"
                                        <?=intval($option['step_no']) == $step_no ? 'selected' : ''?>
                                    >

                                        <?=$step_data['number']?>

                                        <?=$step_data['day']?>

                                        -

                                        <?=$step_data['title']?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

                        </div>

                    </div>



                    <!-- =================================================
                         TITLE EN
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>

                            영문 제목

                            <em>*</em>

                        </label>


                        <div class="inquiry-form-control">

                            <input
                                type="text"
                                name="title_en"
                                id="titleEn"
                                value="<?=inquiry_option_write_h($option['title_en'])?>"
                                maxlength="255"
                                placeholder="New Product|Launch"
                            >


                            <p class="inquiry-form-help">

                                줄바꿈이 필요한 위치에는

                                <strong>|</strong>

                                를 입력해주세요.

                                예: New Product|Launch

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         TITLE KO
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>

                            한글 제목

                            <em>*</em>

                        </label>


                        <div class="inquiry-form-control">

                            <input
                                type="text"
                                name="title_ko"
                                id="titleKo"
                                value="<?=inquiry_option_write_h($option['title_ko'])?>"
                                maxlength="255"
                                placeholder="신제품 런칭"
                            >

                        </div>

                    </div>



                    <!-- =================================================
                         CODE
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>

                            코드

                            <em>*</em>

                        </label>


                        <div class="inquiry-form-control">

                            <input
                                type="text"
                                name="option_code"
                                id="optionCode"
                                value="<?=inquiry_option_write_h($option['option_code'])?>"
                                maxlength="100"
                                placeholder="new_product"
                                autocomplete="off"
                            >


                            <p class="inquiry-form-help">

                                영문 소문자, 숫자, 언더바(_), 하이픈(-)만 사용합니다.

                                프론트 및 문의 데이터 저장 시 기준값으로 사용됩니다.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         PREVIEW
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>
                            미리보기
                        </label>


                        <div class="inquiry-form-control">

                            <div class="inquiry-option-preview">

                                <div class="inquiry-preview-step">

                                    <span id="previewStepNumber">

                                        <?=$step_config[$step]['number']?>

                                    </span>

                                </div>


                                <div class="inquiry-preview-text">

                                    <strong id="previewTitleEn"></strong>

                                    <span id="previewTitleKo"></span>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         SORT
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>
                            노출 순서
                        </label>


                        <div class="inquiry-form-control inquiry-form-small">

                            <input
                                type="number"
                                name="sort_order"
                                value="<?=intval($option['sort_order'])?>"
                                min="0"
                                step="1"
                            >


                            <p class="inquiry-form-help">

                                숫자가 작을수록 먼저 노출됩니다.

                            </p>

                        </div>

                    </div>



                    <!-- =================================================
                         ACTIVE
                    ================================================== -->

                    <div class="inquiry-form-row">

                        <label>
                            노출 여부
                        </label>


                        <div class="inquiry-form-control">

                            <div class="inquiry-radio-group">

                                <label>

                                    <input
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        <?=intval($option['is_active']) == 1 ? 'checked' : ''?>
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
                                        <?=intval($option['is_active']) == 0 ? 'checked' : ''?>
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
                 ACTION
            ================================================== -->

            <div class="inquiry-form-actions">

                <a
                    href="/manage/inquiry_option_list?step=<?=$step?>"
                    class="btn btn-white"
                >
                    취소
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="inquiryOptionSubmit"
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
 * ELEMENT
 * =========================================================
 */

var inquiryOptionForm =
    document.getElementById(
        'inquiryOptionForm'
    );


var inquiryOptionSubmit =
    document.getElementById(
        'inquiryOptionSubmit'
    );


var stepNo =
    document.getElementById(
        'stepNo'
    );


var titleEn =
    document.getElementById(
        'titleEn'
    );


var titleKo =
    document.getElementById(
        'titleKo'
    );


var optionCode =
    document.getElementById(
        'optionCode'
    );


var previewStepNumber =
    document.getElementById(
        'previewStepNumber'
    );


var previewTitleEn =
    document.getElementById(
        'previewTitleEn'
    );


var previewTitleKo =
    document.getElementById(
        'previewTitleKo'
    );



/*
 * =========================================================
 * STEP NUMBER
 * =========================================================
 */

var stepNumbers =
    {
        '1': '01',
        '2': '02',
        '3': '03',
        '4': '04'
    };



/*
 * =========================================================
 * CODE NORMALIZE
 * =========================================================
 */

optionCode.addEventListener(
    'input',
    function(){

        this.value =
            this.value
                .toLowerCase()
                .replace(
                    /[^a-z0-9_-]/g,
                    ''
                );

    }
);



/*
 * =========================================================
 * PREVIEW
 * =========================================================
 */

function renderInquiryPreview()
{

    var currentStep =
        stepNo.value;


    previewStepNumber.textContent =
        stepNumbers[currentStep]
            ? stepNumbers[currentStep]
            : '01';



    /*
     * 영문 제목
     *
     * | 기준 줄바꿈
     */

    var en =
        titleEn.value.replace(
            /^\s+|\s+$/g,
            ''
        );


    previewTitleEn.innerHTML =
        '';


    if(
        en == ''
    ){

        previewTitleEn.textContent =
            'English Title';

    }else{

        var lines =
            en.split(
                '|'
            );


        for(
            var i = 0;
            i < lines.length;
            i++
        ){

            if(
                i > 0
            ){

                previewTitleEn.appendChild(
                    document.createElement(
                        'br'
                    )
                );

            }


            previewTitleEn.appendChild(
                document.createTextNode(
                    lines[i].replace(
                        /^\s+|\s+$/g,
                        ''
                    )
                )
            );

        }

    }



    /*
     * 한글 제목
     */

    var ko =
        titleKo.value.replace(
            /^\s+|\s+$/g,
            ''
        );


    previewTitleKo.textContent =
        ko != ''
            ? ko
            : '한글 제목';

}



stepNo.addEventListener(
    'change',
    renderInquiryPreview
);


titleEn.addEventListener(
    'input',
    renderInquiryPreview
);


titleKo.addEventListener(
    'input',
    renderInquiryPreview
);


renderInquiryPreview();



/*
 * =========================================================
 * SUBMIT
 * =========================================================
 */

inquiryOptionForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();



        /*
         * STEP
         */

        var stepValue =
            parseInt(
                stepNo.value,
                10
            );


        if(
            stepValue < 1 ||
            stepValue > 4
        ){

            alert(
                '단계를 선택해주세요.'
            );

            return;

        }



        /*
         * TITLE EN
         */

        var en =
            titleEn.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(
            en == ''
        ){

            alert(
                '영문 제목을 입력해주세요.',
                function(){

                    titleEn.focus();

                }
            );

            return;

        }



        /*
         * TITLE KO
         */

        var ko =
            titleKo.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(
            ko == ''
        ){

            alert(
                '한글 제목을 입력해주세요.',
                function(){

                    titleKo.focus();

                }
            );

            return;

        }



        /*
         * CODE
         */

        var code =
            optionCode.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(
            code == ''
        ){

            alert(
                '코드를 입력해주세요.',
                function(){

                    optionCode.focus();

                }
            );

            return;

        }


        if(
            !/^[a-z0-9_-]+$/.test(
                code
            )
        ){

            alert(
                '코드는 영문 소문자, 숫자, 언더바(_), 하이픈(-)만 사용할 수 있습니다.',
                function(){

                    optionCode.focus();

                }
            );

            return;

        }



        /*
         * SORT
         */

        var sortOrder =
            parseInt(
                inquiryOptionForm.sort_order.value,
                10
            );


        if(
            isNaN(sortOrder) ||
            sortOrder < 0
        ){

            alert(
                '노출 순서를 확인해주세요.',
                function(){

                    inquiryOptionForm.sort_order.focus();

                }
            );

            return;

        }



        saveInquiryOption();

    }
);



/*
 * =========================================================
 * SAVE
 * =========================================================
 */

function saveInquiryOption()
{

    if(
        inquiryOptionSubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    inquiryOptionSubmit.setAttribute(
        'data-loading',
        'Y'
    );


    inquiryOptionSubmit.disabled =
        true;



    var formData =
        new FormData(
            inquiryOptionForm
        );


    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/inquiry_option_save',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){

                return;

            }


            inquiryOptionSubmit.removeAttribute(
                'data-loading'
            );


            inquiryOptionSubmit.disabled =
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
                        : '선택항목을 저장하지 못했습니다.'
                );

                return;

            }



            alert(
                result.msg
                    ? result.msg
                    : '선택항목이 저장되었습니다.',
                function(){

                    location.href =
                        '/manage/inquiry_option_list?step=' +
                        encodeURIComponent(
                            stepNo.value
                        );

                }
            );

        };


    xhr.send(
        formData
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>