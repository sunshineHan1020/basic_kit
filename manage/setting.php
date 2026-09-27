<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 사이트 기본 설정
 *
 * FILE
 * /manage/setting.php
 *
 * URL
 * /manage/setting
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * SUPER ADMIN ONLY
 * =========================================================
 */


if($_SESSION['ADMIN_ROLE']!='super' && $_SESSION['ADMIN_ROLE']!='admin' ){
    page_move("/manage/");
    exit;
}






/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function setting_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * DEFAULT
 * =========================================================
 */

$setting =
    array(
        'id'               => 1,
        'site_name'        => '',
        'seo_title'        => '',
        'meta_description' => '',
        'meta_keywords'    => '',
        'og_title'         => '',
        'og_description'   => '',
        'og_media_id'      => 0,
        'canonical_url'    => '',
        'updated_at'       => '',
        'og_image_path'    => '',
        'og_image_name'    => ''
    );



/*
 * =========================================================
 * SETTING
 * =========================================================
 */

$query = "

    SELECT

        s.id,
        s.site_name,
        s.seo_title,
        s.meta_description,
        s.meta_keywords,
        s.og_title,
        s.og_description,
        s.og_media_id,
        s.canonical_url,
        s.updated_at,

        m.storage_key AS og_image_path,
        m.original_name AS og_image_name

    FROM site_settings s

    LEFT JOIN media m
        ON m.id = s.og_media_id
        AND m.deleted_at IS NULL

    WHERE
        s.id = 1

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


$data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if($data){

    $setting =
        array_merge(
            $setting,
            $data
        );

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/setting.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="setting-page-head">

            <div class="setting-page-title">

                <span>
                    시스템 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    기본 설정
                </h2>

            </div>

        </div>



        <form
            id="settingForm"
            enctype="multipart/form-data"
        >


            <div class="setting-layout">


                <!-- =================================================
                     MAIN
                ================================================== -->

                <div class="setting-main">


                    <!-- =================================================
                         BASIC
                    ================================================== -->

                    <div class="card setting-card">

                        <div class="card-header">

                            <h3>
                                사이트 기본 정보
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- SITE NAME -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="site_name">

                                        사이트명

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="setting-field">

                                    <input
                                        type="text"
                                        id="site_name"
                                        name="site_name"
                                        value="<?=setting_h($setting['site_name'])?>"
                                        maxlength="255"
                                        placeholder="WEEKENDS"
                                    >

                                    <p class="setting-help">
                                        사이트 공통 브랜드명으로 사용됩니다.
                                    </p>

                                </div>

                            </div>



                            <!-- CANONICAL -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="canonical_url">
                                        기본 URL
                                    </label>

                                </div>


                                <div class="setting-field">

                                    <input
                                        type="text"
                                        id="canonical_url"
                                        name="canonical_url"
                                        value="<?=setting_h($setting['canonical_url'])?>"
                                        maxlength="2048"
                                        placeholder="https://www.weekends.co.kr"
                                    >

                                    <p class="setting-help">
                                        사이트 대표 도메인을 입력해주세요.
                                    </p>

                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         SEO
                    ================================================== -->

                    <div class="card setting-card">

                        <div class="card-header">

                            <h3>
                                기본 SEO
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- SEO TITLE -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="seo_title">

                                        SEO Title

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="setting-field">

                                    <input
                                        type="text"
                                        id="seo_title"
                                        name="seo_title"
                                        value="<?=setting_h($setting['seo_title'])?>"
                                        maxlength="255"
                                        placeholder="WEEKENDS | 위켄즈"
                                    >

                                    <div class="setting-counter">

                                        <span id="seoTitleCount">
                                            0
                                        </span>

                                        / 255

                                    </div>

                                </div>

                            </div>



                            <!-- META DESCRIPTION -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="meta_description">
                                        Meta Description
                                    </label>

                                </div>


                                <div class="setting-field">

                                    <textarea
                                        id="meta_description"
                                        name="meta_description"
                                        maxlength="1000"
                                        placeholder="사이트 기본 설명을 입력해주세요."
                                    ><?=setting_h($setting['meta_description'])?></textarea>


                                    <div class="setting-counter">

                                        <span id="metaDescriptionCount">
                                            0
                                        </span>

                                        / 1000

                                    </div>

                                </div>

                            </div>



                            <!-- META KEYWORDS -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="meta_keywords">
                                        Meta Keywords
                                    </label>

                                </div>


                                <div class="setting-field">

                                    <textarea
                                        id="meta_keywords"
                                        name="meta_keywords"
                                        placeholder="위켄즈, WEEKENDS, 팝업스토어, 브랜드 경험"
                                    ><?=setting_h($setting['meta_keywords'])?></textarea>


                                    <p class="setting-help">
                                        키워드는 쉼표(,)로 구분해주세요.
                                    </p>

                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         OPEN GRAPH
                    ================================================== -->

                    <div class="card setting-card">

                        <div class="card-header">

                            <h3>
                                Open Graph
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- OG TITLE -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="og_title">
                                        OG Title
                                    </label>

                                </div>


                                <div class="setting-field">

                                    <input
                                        type="text"
                                        id="og_title"
                                        name="og_title"
                                        value="<?=setting_h($setting['og_title'])?>"
                                        maxlength="255"
                                        placeholder="WEEKENDS | 위켄즈"
                                    >


                                    <p class="setting-help">
                                        비워두면 기본 SEO Title을 사용하도록 처리할 수 있습니다.
                                    </p>

                                </div>

                            </div>



                            <!-- OG DESCRIPTION -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label for="og_description">
                                        OG Description
                                    </label>

                                </div>


                                <div class="setting-field">

                                    <textarea
                                        id="og_description"
                                        name="og_description"
                                        maxlength="1000"
                                        placeholder="SNS 공유 시 표시할 설명을 입력해주세요."
                                    ><?=setting_h($setting['og_description'])?></textarea>


                                    <p class="setting-help">
                                        비워두면 기본 Meta Description을 사용할 수 있습니다.
                                    </p>

                                </div>

                            </div>



                            <!-- OG IMAGE -->

                            <div class="setting-row">

                                <div class="setting-label">

                                    <label>
                                        기본 OG Image
                                    </label>

                                </div>


                                <div class="setting-field">


                                    <input
                                        type="hidden"
                                        name="current_og_media_id"
                                        value="<?=intval($setting['og_media_id'])?>"
                                    >


                                    <div
                                        class="setting-og-preview <?=($setting['og_image_path'] == '' ? 'empty' : '')?>"
                                        id="ogPreview"
                                    >


                                        <img
                                            id="ogPreviewImage"
                                            src="<?=setting_h($setting['og_image_path'])?>"
                                            alt=""
                                        >


                                        <div
                                            class="setting-og-empty"
                                            id="ogPreviewEmpty"
                                        >

                                            <i class="ri-image-line"></i>

                                            <span>
                                                등록된 OG 이미지가 없습니다.
                                            </span>

                                        </div>


                                    </div>


                                    <div class="setting-file-row">

                                        <input
                                            type="file"
                                            id="og_image"
                                            name="og_image"
                                            accept=".jpg,.jpeg,.png,.gif,.webp"
                                        >


                                        <?
                                        if(
                                            intval(
                                                $setting['og_media_id']
                                            ) > 0
                                        ){
                                        ?>

                                            <label class="setting-image-delete">

                                                <input
                                                    type="checkbox"
                                                    name="delete_og_image"
                                                    value="Y"
                                                    id="deleteOgImage"
                                                >

                                                <span>
                                                    기존 이미지 삭제
                                                </span>

                                            </label>

                                        <?
                                        }
                                        ?>

                                    </div>


                                    <p class="setting-help">
                                        JPG, PNG, GIF, WEBP / 권장 1200 × 630px
                                    </p>


                                    <?
                                    if(
                                        $setting['og_image_name'] != ''
                                    ){
                                    ?>

                                        <p class="setting-current-file">

                                            현재 파일 :

                                            <strong>

                                                <?=setting_h(
                                                    $setting['og_image_name']
                                                )?>

                                            </strong>

                                        </p>

                                    <?
                                    }
                                    ?>


                                </div>

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================================
                     SIDE
                ================================================== -->

                <div class="setting-side">


                    <!-- =================================================
                         PREVIEW
                    ================================================== -->

                    <div class="card setting-preview-card">

                        <div class="card-header">

                            <h3>
                                검색결과 미리보기
                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="setting-search-preview">

                                <span
                                    class="setting-search-url"
                                    id="previewUrl"
                                >
                                    <?=setting_h($setting['canonical_url'])?>
                                </span>


                                <strong id="previewTitle">

                                    <?=setting_h($setting['seo_title'])?>

                                </strong>


                                <p id="previewDescription">

                                    <?=setting_h($setting['meta_description'])?>

                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         INFO
                    ================================================== -->

                    <div class="card setting-preview-card">

                        <div class="card-header">

                            <h3>
                                설정 정보
                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="setting-info-list">


                                <div>

                                    <span>
                                        설정 구분
                                    </span>

                                    <strong>
                                        사이트 공통
                                    </strong>

                                </div>



                                <div>

                                    <span>
                                        설정 우선순위
                                    </span>

                                    <strong>
                                        개별 SEO 우선
                                    </strong>

                                </div>



                                <div>

                                    <span>
                                        마지막 수정
                                    </span>

                                    <strong>

                                        <?
                                        if(
                                            $setting['updated_at'] != ''
                                        ){
                                        ?>

                                            <?=setting_h(
                                                substr(
                                                    $setting['updated_at'],
                                                    0,
                                                    16
                                                )
                                            )?>

                                        <?
                                        }else{
                                        ?>

                                            -

                                        <?
                                        }
                                        ?>

                                    </strong>

                                </div>


                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         SAVE
                    ================================================== -->

                    <button
                        type="submit"
                        class="btn btn-primary setting-save"
                        id="settingSave"
                    >

                        <i class="ri-save-line"></i>

                        설정 저장

                    </button>


                </div>


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

var settingForm =
    document.getElementById(
        'settingForm'
    );


var settingSave =
    document.getElementById(
        'settingSave'
    );


var siteName =
    document.getElementById(
        'site_name'
    );


var seoTitle =
    document.getElementById(
        'seo_title'
    );


var metaDescription =
    document.getElementById(
        'meta_description'
    );


var canonicalUrl =
    document.getElementById(
        'canonical_url'
    );


var ogImage =
    document.getElementById(
        'og_image'
    );


var deleteOgImage =
    document.getElementById(
        'deleteOgImage'
    );


var ogPreview =
    document.getElementById(
        'ogPreview'
    );


var ogPreviewImage =
    document.getElementById(
        'ogPreviewImage'
    );


var seoTitleCount =
    document.getElementById(
        'seoTitleCount'
    );


var metaDescriptionCount =
    document.getElementById(
        'metaDescriptionCount'
    );


var previewUrl =
    document.getElementById(
        'previewUrl'
    );


var previewTitle =
    document.getElementById(
        'previewTitle'
    );


var previewDescription =
    document.getElementById(
        'previewDescription'
    );



/*
 * =========================================================
 * TEXT PREVIEW
 * =========================================================
 */

function updateSettingPreview()
{

    seoTitleCount.textContent =
        seoTitle.value.length;


    metaDescriptionCount.textContent =
        metaDescription.value.length;


    previewUrl.textContent =
        canonicalUrl.value != ''
            ? canonicalUrl.value
            : 'https://www.weekends.co.kr';


    previewTitle.textContent =
        seoTitle.value != ''
            ? seoTitle.value
            : siteName.value;


    previewDescription.textContent =
        metaDescription.value != ''
            ? metaDescription.value
            : '사이트 설명이 표시됩니다.';

}


seoTitle.addEventListener(
    'input',
    updateSettingPreview
);


metaDescription.addEventListener(
    'input',
    updateSettingPreview
);


canonicalUrl.addEventListener(
    'input',
    updateSettingPreview
);


siteName.addEventListener(
    'input',
    updateSettingPreview
);


updateSettingPreview();



/*
 * =========================================================
 * OG IMAGE PREVIEW
 * =========================================================
 */

ogImage.addEventListener(
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


        var reader =
            new FileReader();


        reader.onload =
            function(e){

                ogPreviewImage.src =
                    e.target.result;


                ogPreview.classList.remove(
                    'empty'
                );


                if(deleteOgImage){

                    deleteOgImage.checked =
                        false;

                }

            };


        reader.readAsDataURL(
            file
        );

    }
);



/*
 * =========================================================
 * DELETE IMAGE
 * =========================================================
 */

if(deleteOgImage){

    deleteOgImage.addEventListener(
        'change',
        function(){

            if(this.checked){

                ogImage.value =
                    '';


                ogPreview.classList.add(
                    'empty'
                );

            }else{

                <?
                if(
                    $setting['og_image_path'] != ''
                ){
                ?>

                    ogPreviewImage.src =
                        '<?=setting_h($setting['og_image_path'])?>';


                    ogPreview.classList.remove(
                        'empty'
                    );

                <?
                }
                ?>

            }

        }
    );

}



/*
 * =========================================================
 * SUBMIT
 * =========================================================
 */

settingForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();


        /*
         * SITE NAME
         */

        if(
            siteName.value.trim() == ''
        ){

            alert(
                '사이트명을 입력해주세요.',
                function(){

                    siteName.focus();

                }
            );

            return;

        }



        /*
         * SEO TITLE
         */

        if(
            seoTitle.value.trim() == ''
        ){

            alert(
                '기본 SEO Title을 입력해주세요.',
                function(){

                    seoTitle.focus();

                }
            );

            return;

        }



        /*
         * DUPLICATE REQUEST
         */

        if(
            settingSave.getAttribute(
                'data-loading'
            ) == 'Y'
        ){
            return;
        }


        settingSave.setAttribute(
            'data-loading',
            'Y'
        );


        settingSave.disabled =
            true;



        /*
         * AJAX
         */

        var formData =
            new FormData(
                settingForm
            );


        var xhr =
            new XMLHttpRequest();


        xhr.open(
            'POST',
            '/manage/proc/setting_save',
            true
        );


        xhr.onreadystatechange =
            function(){

                if(
                    xhr.readyState != 4
                ){
                    return;
                }


                settingSave.removeAttribute(
                    'data-loading'
                );


                settingSave.disabled =
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

                }catch(error){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                if(!result.code){

                    alert(
                        result.msg
                            ? result.msg
                            : '설정을 저장하지 못했습니다.'
                    );

                    return;

                }


                alert(
                    result.msg
                        ? result.msg
                        : '설정이 저장되었습니다.',
                    function(){

                        location.reload();

                    }
                );

            };


        xhr.send(
            formData
        );

    }
);

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>