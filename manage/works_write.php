<?
/*
 * =========================================================
 * WEEKENDS CMS
 * WORKS 등록 / 수정
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


/*
 * =========================================================
 * Helper
 * =========================================================
 */

function work_write_h($str)
{

    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );

}


function work_write_datetime_local($str)
{

    if(
        $str == '' ||
        $str == null ||
        $str == '0000-00-00 00:00:00'
    ){
        return '';
    }


    $time =
        strtotime(
            $str
        );


    if(!$time){
        return '';
    }


    return date(
        'Y-m-d\TH:i',
        $time
    );

}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$work_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $work_id > 0
        ? true
        : false;



/*
 * =========================================================
 * 기본값
 * =========================================================
 */

$work =
    array(

        'id'                  => 0,
        'category_id'         => 0,
        'list_title'          => '',
        'sort_order'          => 0,
        'title'               => '',
        'slug'                => '',
        'summary'             => '',
        'status'              => 'draft',
        'visibility'          => 'private',
        'display_at'          => '',
        'publish_at'          => '',
        'author_display_name' =>
            isset($_SESSION['ADMIN_NAME'])
                ? $_SESSION['ADMIN_NAME']
                : ''

    );


$seo =
    array(

        'seo_title'       => '',
        'meta_description'=> '',
        'meta_keywords'   => '',
        'canonical_url'   => '',
        'og_title'        => '',
        'og_description'  => '',
        'og_media_id'     => 0,
        'allow_index'     => 1,
        'nofollow'        => 0

    );


$thumbnail =
    array(
        'media_id'      => 0,
        'storage_key'   => '',
        'original_name' => ''
    );


$og_image =
    array(
        'media_id'      => 0,
        'storage_key'   => '',
        'original_name' => ''
    );


$selected_tags =
    array();


$sections =
    array();



/*
 * =========================================================
 * WORK 수정 데이터
 * =========================================================
 */

if($is_edit){

    $query = "

        SELECT
            id,
            category_id,
            list_title,
            sort_order,
            title,
            slug,
            summary,
            status,
            visibility,
            display_at,
            publish_at,
            author_display_name

        FROM works

        WHERE
            id = ?
            AND deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $work_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/works_list'
        );

        exit;

    }


    $work =
        $data;



    /*
     * Thumbnail
     */

    $query = "

        SELECT
            wm.media_id,
            m.storage_key,
            m.original_name

        FROM work_media wm

        INNER JOIN media m
            ON m.id = wm.media_id

        WHERE
            wm.work_id = ?
            AND wm.role = 'thumbnail'
            AND m.deleted_at IS NULL

        ORDER BY
            wm.sort_order ASC,
            wm.id ASC

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $work_id
            )
        );


    $thumbnail_data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($thumbnail_data){

        $thumbnail =
            $thumbnail_data;

    }



    /*
     * Tag
     */

    $query = "

        SELECT
            tag_id

        FROM work_tag_map

        WHERE
            work_id = ?

        ORDER BY
            sort_order ASC,
            tag_id ASC

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $work_id
            )
        );


    while(
        $data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $selected_tags[] =
            intval(
                $data['tag_id']
            );

    }



    /*
     * SEO
     */

    $query = "

        SELECT
            work_id,
            seo_title,
            meta_description,
            meta_keywords,
            canonical_url,
            og_title,
            og_description,
            og_media_id,
            allow_index,
            nofollow

        FROM work_seo

        WHERE
            work_id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $work_id
            )
        );


    $seo_data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($seo_data){

        $seo =
            $seo_data;

    }



    /*
     * OG Image
     */

    if(
        intval(
            $seo['og_media_id']
        ) > 0
    ){

        $query = "

            SELECT
                id AS media_id,
                storage_key,
                original_name

            FROM media

            WHERE
                id = ?
                AND deleted_at IS NULL

            LIMIT 1

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    intval(
                        $seo['og_media_id']
                    )
                )
            );


        $og_data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        if($og_data){

            $og_image =
                $og_data;

        }

    }



    /*
     * =====================================================
     * Sections
     * =====================================================
     */

    $query = "

        SELECT
            id,
            work_id,
            section_type,
            sort_order,
            is_enabled,
            eyebrow,
            title,
            subtitle,
            body_html,
            admin_memo

        FROM work_sections

        WHERE
            work_id = ?

        ORDER BY
            sort_order ASC,
            id ASC

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $work_id
            )
        );


    while(
        $section =
            $ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $section['media'] =
            array();


        $section['items'] =
            array();


        /*
         * Section Media
         */

        $query_media = "

            SELECT
                sm.id,
                sm.media_id,
                sm.slot_key,
                sm.sort_order,
                sm.alt_text,
                sm.caption,

                m.storage_key,
                m.original_name

            FROM work_section_media sm

            INNER JOIN media m
                ON m.id = sm.media_id

            WHERE
                sm.section_id = ?
                AND m.deleted_at IS NULL

            ORDER BY
                sm.sort_order ASC,
                sm.id ASC

        ";


        $ps_media =
            pdo_query(
                $db,
                $query_media,
                array(
                    $section['id']
                )
            );


        while(
            $media_data =
                $ps_media->fetch(
                    PDO::FETCH_ASSOC
                )
        ){

            $slot_key =
                $media_data['slot_key'];


            $section['media'][$slot_key] =
                $media_data;

        }



        /*
         * Section Items
         */

        $query_items = "

            SELECT
                id,
                group_key,
                item_key,
                label,
                value_text,
                value_html,
                sort_order

            FROM work_section_items

            WHERE
                section_id = ?

            ORDER BY
                group_key ASC,
                sort_order ASC,
                id ASC

        ";


        $ps_items =
            pdo_query(
                $db,
                $query_items,
                array(
                    $section['id']
                )
            );


        while(
            $item_data =
                $ps_items->fetch(
                    PDO::FETCH_ASSOC
                )
        ){

            $group_key =
                $item_data['group_key'];


            if(
                !isset(
                    $section['items'][$group_key]
                )
            ){

                $section['items'][$group_key] =
                    array();

            }


            $section['items'][$group_key][] =
                $item_data;

        }


        $sections[] =
            $section;

    }

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
        is_active

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

    /*
     * 등록에서는 활성 카테고리만
     *
     * 수정에서는 기존 선택된 비활성 카테고리도 표시
     */

    if(
        intval($data['is_active']) == 0 &&
        intval($work['category_id']) != intval($data['id'])
    ){

        continue;

    }


    $categories[] =
        $data;

}



/*
 * =========================================================
 * 태그
 * =========================================================
 */

$tags =
    array();


$query = "

    SELECT
        id,
        name,
        is_active

    FROM work_tags

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

    if(
        intval($data['is_active']) == 0 &&
        !in_array(
            intval($data['id']),
            $selected_tags
        )
    ){

        continue;

    }


    $tags[] =
        $data;

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
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

                        WORK 수정

                    <?
                    }else{
                    ?>

                        WORK 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="works-page-action">

                <a
                    href="/manage/works_list"
                    class="btn btn-white"
                >

                    <i class="ri-list-check"></i>

                    목록

                </a>


                <button
                    type="button"
                    class="btn btn-primary"
                    id="workSaveTop"
                >

                    <i class="ri-save-line"></i>

                    <?
                    if($is_edit){
                    ?>
                        수정 저장
                    <?
                    }else{
                    ?>
                        WORK 등록
                    <?
                    }
                    ?>

                </button>

            </div>

        </div>



        <!-- =================================================
             Form
        ================================================== -->

        <form
            id="workForm"
            method="post"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($work['id'])?>"
            >


            <input
                type="hidden"
                name="thumbnail_existing_media_id"
                value="<?=intval($thumbnail['media_id'])?>"
            >


            <input
                type="hidden"
                name="thumbnail_remove"
                id="thumbnailRemove"
                value="0"
            >


            <input
                type="hidden"
                name="og_existing_media_id"
                value="<?=intval($og_image['media_id'])?>"
            >


            <input
                type="hidden"
                name="og_remove"
                id="ogRemove"
                value="0"
            >



            <div class="work-write-layout">


                <!-- =================================================
                     LEFT
                ================================================== -->

                <section class="work-write-main">


                    <!-- =================================================
                         기본 정보
                    ================================================== -->

                    <div class="card">

                        <div class="card-header">

                            <h3>
                                기본 정보
                            </h3>

                        </div>


                        <div class="card-body">


                            <div class="work-form-row">

                                <div class="work-form-field">

                                    <label>
                                        리스트 제목
                                        <em>*</em>
                                    </label>

                                    <input
                                        type="text"
                                        name="list_title"
                                        value="<?=work_write_h($work['list_title'])?>"
                                        placeholder="목록에서 보여질 프로젝트명을 입력해주세요."
                                        maxlength="255"
                                    >

                                </div>

                            </div>



                            <div class="work-form-row">

                                <div class="work-form-field">

                                    <label>
                                        상세 제목
                                        <em>*</em>
                                    </label>

                                    <input
                                        type="text"
                                        name="title"
                                        value="<?=work_write_h($work['title'])?>"
                                        placeholder="프로젝트 상세 페이지 제목"
                                        maxlength="500"
                                    >

                                </div>

                            </div>



                            <div class="work-form-row cols-2">

                                <div class="work-form-field">

                                    <label>
                                        카테고리
                                        <em>*</em>
                                    </label>

                                    <select
                                        name="category_id"
                                    >

                                        <option value="0">
                                            카테고리를 선택해주세요.
                                        </option>


                                        <?
                                        foreach(
                                            $categories as
                                            $category
                                        ){
                                        ?>

                                            <option
                                                value="<?=$category['id']?>"
                                                <?=intval($work['category_id']) == intval($category['id']) ? 'selected' : ''?>
                                            >

                                                <?=work_write_h($category['name'])?>

                                                <?
                                                if(
                                                    intval(
                                                        $category['is_active']
                                                    ) == 0
                                                ){
                                                ?>
                                                    (미사용)
                                                <?
                                                }
                                                ?>

                                            </option>

                                        <?
                                        }
                                        ?>

                                    </select>

                                </div>


                                <div class="work-form-field">

                                    <label>
                                        노출 순서
                                    </label>

                                    <input
                                        type="number"
                                        name="sort_order"
                                        value="<?=intval($work['sort_order'])?>"
                                        min="0"
                                    >

                                </div>

                            </div>



                           <div class="work-form-row">

								<div class="work-form-field">

									<label>
										URL Slug
										<em>*</em>
									</label>

									<div class="works-slug-input">

										<span>
											/project/
										</span>

										<input
											type="text"
											name="slug"
											value="<?=work_write_h($work['slug'])?>"
											placeholder="project-name"
											maxlength="180"
										>

									</div>

									<p class="form-help">
										영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.
									</p>

								</div>

							</div>



                            <div class="work-form-row">

                                <div class="work-form-field">

                                    <label>
                                        요약
                                    </label>

                                    <textarea
                                        name="summary"
                                        rows="5"
                                        placeholder="프로젝트에 대한 짧은 설명을 입력해주세요."
                                    ><?=work_write_h($work['summary'])?></textarea>

                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         대표 이미지
                    ================================================== -->

                    <div class="card">

                        <div class="card-header">

                            <h3>
                                대표 이미지
                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="work-image-upload">

                                <div
                                    class="work-image-preview"
                                    id="thumbnailPreview"
                                >

                                    <?
                                    if(
                                        $thumbnail['storage_key'] != ''
                                    ){
                                    ?>

                                        <img
                                            src="<?=work_write_h($thumbnail['storage_key'])?>"
                                            alt=""
                                        >

                                    <?
                                    }else{
                                    ?>

                                        <div class="work-image-empty">

                                            <i class="ri-image-add-line"></i>

                                            <span>
                                                대표 이미지
                                            </span>

                                        </div>

                                    <?
                                    }
                                    ?>

                                </div>


                                <div class="work-image-control">

                                    <input
                                        type="file"
                                        name="thumbnail"
                                        id="thumbnailFile"
                                        accept="image/jpeg,image/png,image/gif,image/webp"
                                    >


                                    <button
                                        type="button"
                                        class="btn btn-white btn-sm"
                                        id="thumbnailDelete"
                                    >
                                        이미지 삭제
                                    </button>


                                    <p class="form-help">
                                        WORK 목록에서 사용하는 대표 썸네일입니다.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         상세 구성
                    ================================================== -->

                    <div class="card work-section-card">

                        <div class="card-header">

                            <div>

                                <h3>
                                    상세 페이지 구성
                                </h3>

                                <p>
                                    필요한 섹션을 선택하여 상세 페이지를 구성합니다.
                                </p>

                            </div>

                        </div>


                        <div class="card-body">


                            <!-- Section Types -->

                            <div class="work-section-types">

                                <?
                                foreach(
                                    $WORK_SECTION_TYPES as
                                    $type_code =>
                                    $type
                                ){
                                ?>

                                    <button
                                        type="button"
                                        class="work-section-type"
                                        data-type="<?=work_write_h($type_code)?>"
                                    >

                                        <span class="work-section-type-icon">

                                            <img
                                                src="<?=work_write_h($type['icon'])?>"
                                                alt=""
                                            >

                                        </span>


                                        <span class="work-section-type-name">

                                            <?=work_write_h($type['name'])?>

                                        </span>

                                    </button>

                                <?
                                }
                                ?>

                            </div>



                            <!-- Added Sections -->

                            <div
                                id="workSections"
                                class="work-sections"
                            ></div>



                            <div
                                id="workSectionEmpty"
                                class="work-section-empty"
                            >

                                <i class="ri-layout-masonry-line"></i>

                                <strong>
                                    등록된 상세 섹션이 없습니다.
                                </strong>

                                <span>
                                    위의 섹션 타입을 선택해 추가해주세요.
                                </span>

                            </div>


                        </div>

                    </div>


                </section>



                <!-- =================================================
                     RIGHT
                ================================================== -->

                <aside class="work-write-side">


                    <!-- =================================================
                         게시 설정
                    ================================================== -->

                    <div class="card">

                        <div class="card-header">

                            <h3>
                                게시 설정
                            </h3>

                        </div>


                        <div class="card-body">


                            <div class="work-form-field">

                                <label>
                                    상태
                                </label>

                                <select
                                    name="status"
                                >

                                    <option
                                        value="draft"
                                        <?=$work['status'] == 'draft' ? 'selected' : ''?>
                                    >
                                        임시저장
                                    </option>

                                    <option
                                        value="published"
                                        <?=$work['status'] == 'published' ? 'selected' : ''?>
                                    >
                                        게시
                                    </option>

                                    <option
                                        value="archived"
                                        <?=$work['status'] == 'archived' ? 'selected' : ''?>
                                    >
                                        보관
                                    </option>

                                </select>

                            </div>



                            <div class="work-form-field">

                                <label>
                                    노출 여부
                                </label>

                                <div class="works-radio-group">

                                    <label>

                                        <input
                                            type="radio"
                                            name="visibility"
                                            value="public"
                                            <?=$work['visibility'] == 'public' ? 'checked' : ''?>
                                        >

                                        <span>
                                            공개
                                        </span>

                                    </label>


                                    <label>

                                        <input
                                            type="radio"
                                            name="visibility"
                                            value="private"
                                            <?=$work['visibility'] == 'private' ? 'checked' : ''?>
                                        >

                                        <span>
                                            비공개
                                        </span>

                                    </label>

                                </div>

                            </div>



                            <div class="work-form-field">

                                <label>
                                    노출 기준일
                                </label>

                                <input
                                    type="datetime-local"
                                    name="display_at"
                                    value="<?=work_write_h(work_write_datetime_local($work['display_at']))?>"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    예약 게시일
                                </label>

                                <input
                                    type="datetime-local"
                                    name="publish_at"
                                    value="<?=work_write_h(work_write_datetime_local($work['publish_at']))?>"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    작성자
                                </label>

                                <input
                                    type="text"
                                    name="author_display_name"
                                    value="<?=work_write_h($work['author_display_name'])?>"
                                    maxlength="100"
                                >

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         Tags
                    ================================================== -->

                    <div class="card">

                        <div class="card-header">

                            <h3>
                                태그
                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="work-tag-list">

                                <?
                                if(count($tags) > 0){

                                    foreach(
                                        $tags as
                                        $tag
                                    ){
                                ?>

                                    <label class="work-tag-check">

                                        <input
                                            type="checkbox"
                                            name="tag_ids[]"
                                            value="<?=$tag['id']?>"
                                            <?=in_array(intval($tag['id']), $selected_tags) ? 'checked' : ''?>
                                        >

                                        <span>
                                            <?=work_write_h($tag['name'])?>
                                        </span>

                                    </label>

                                <?
                                    }

                                }else{
                                ?>

                                    <p class="work-tag-empty">
                                        등록된 태그가 없습니다.
                                    </p>

                                <?
                                }
                                ?>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         SEO
                    ================================================== -->

                    <div class="card">

                        <div class="card-header">

                            <h3>
                                SEO
                            </h3>

                        </div>


                        <div class="card-body">


                            <div class="work-form-field">

                                <label>
                                    SEO Title
                                </label>

                                <input
                                    type="text"
                                    name="seo_title"
                                    value="<?=work_write_h($seo['seo_title'])?>"
                                    maxlength="255"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    Meta Description
                                </label>

                                <textarea
                                    name="meta_description"
                                    rows="4"
                                ><?=work_write_h($seo['meta_description'])?></textarea>

                            </div>



                            <div class="work-form-field">

                                <label>
                                    Meta Keywords
                                </label>

                                <input
                                    type="text"
                                    name="meta_keywords"
                                    value="<?=work_write_h($seo['meta_keywords'])?>"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    Canonical URL
                                </label>

                                <input
                                    type="text"
                                    name="canonical_url"
                                    value="<?=work_write_h($seo['canonical_url'])?>"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    OG Title
                                </label>

                                <input
                                    type="text"
                                    name="og_title"
                                    value="<?=work_write_h($seo['og_title'])?>"
                                    maxlength="255"
                                >

                            </div>



                            <div class="work-form-field">

                                <label>
                                    OG Description
                                </label>

                                <textarea
                                    name="og_description"
                                    rows="4"
                                ><?=work_write_h($seo['og_description'])?></textarea>

                            </div>



                            <!-- OG Image -->

                            <div class="work-form-field">

                                <label>
                                    OG Image
                                </label>


                                <div
                                    class="work-og-preview"
                                    id="ogPreview"
                                >

                                    <?
                                    if(
                                        $og_image['storage_key'] != ''
                                    ){
                                    ?>

                                        <img
                                            src="<?=work_write_h($og_image['storage_key'])?>"
                                            alt=""
                                        >

                                    <?
                                    }else{
                                    ?>

                                        <span>
                                            등록된 OG 이미지가 없습니다.
                                        </span>

                                    <?
                                    }
                                    ?>

                                </div>


                                <input
                                    type="file"
                                    name="og_image"
                                    id="ogFile"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                >


                                <button
                                    type="button"
                                    class="btn btn-white btn-sm"
                                    id="ogDelete"
                                >
                                    OG 이미지 삭제
                                </button>

                            </div>



                            <div class="work-form-field">

                                <label>
                                    검색엔진 설정
                                </label>


                                <label class="work-option-check">

                                    <input
                                        type="checkbox"
                                        name="allow_index"
                                        value="1"
                                        <?=intval($seo['allow_index']) == 1 ? 'checked' : ''?>
                                    >

                                    <span>
                                        Index 허용
                                    </span>

                                </label>


                                <label class="work-option-check">

                                    <input
                                        type="checkbox"
                                        name="nofollow"
                                        value="1"
                                        <?=intval($seo['nofollow']) == 1 ? 'checked' : ''?>
                                    >

                                    <span>
                                        Nofollow
                                    </span>

                                </label>

                            </div>


                        </div>

                    </div>


                </aside>


            </div>



            <!-- =================================================
                 Bottom Actions
            ================================================== -->

            <div class="works-form-actions work-write-actions">

                <a
                    href="/manage/works_list"
                    class="btn btn-white"
                >
                    취소
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="workSubmit"
                >

                    <?
                    if($is_edit){
                    ?>
                        수정 저장
                    <?
                    }else{
                    ?>
                        WORK 등록
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
 * DATA
 * =========================================================
 */

var WORK_SECTION_TYPES =
    <?=json_encode($WORK_SECTION_TYPES)?>;


var INITIAL_SECTIONS =
    <?=json_encode($sections)?>;


var sectionIndex =
    0;



/*
 * =========================================================
 * HTML Escape
 * =========================================================
 */

function workEscape(value)
{

    if(
        value === null ||
        typeof value === 'undefined'
    ){
        return '';
    }


    value =
        String(value);


    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

}



/*
 * =========================================================
 * Slug
 * =========================================================
 */

var slugInput =
    document.querySelector(
        'input[name="slug"]'
    );


if(slugInput){

    slugInput.addEventListener(
        'input',
        function(){

            var value =
                this.value.toLowerCase();


            value =
                value.replace(
                    /\s+/g,
                    '-'
                );


            value =
                value.replace(
                    /[^a-z0-9\-]/g,
                    ''
                );


            value =
                value.replace(
                    /\-+/g,
                    '-'
                );


            value =
                value.replace(
                    /^\-+/,
                    ''
                );


            this.value =
                value;

        }
    );

}



/*
 * =========================================================
 * Thumbnail
 * =========================================================
 */

var thumbnailFile =
    document.getElementById(
        'thumbnailFile'
    );


var thumbnailPreview =
    document.getElementById(
        'thumbnailPreview'
    );


var thumbnailRemove =
    document.getElementById(
        'thumbnailRemove'
    );


if(thumbnailFile){

    thumbnailFile.addEventListener(
        'change',
        function(){

            if(
                !this.files ||
                !this.files[0]
            ){
                return;
            }


            thumbnailRemove.value =
                '0';


            var reader =
                new FileReader();


            reader.onload =
                function(e){

                    thumbnailPreview.innerHTML =
                        '<img src="' +
                        e.target.result +
                        '" alt="">';

                };


            reader.readAsDataURL(
                this.files[0]
            );

        }
    );

}



var thumbnailDelete =
    document.getElementById(
        'thumbnailDelete'
    );


if(thumbnailDelete){

    thumbnailDelete.addEventListener(
        'click',
        function(){

            thumbnailFile.value =
                '';


            thumbnailRemove.value =
                '1';


            thumbnailPreview.innerHTML =

                '<div class="work-image-empty">' +

                    '<i class="ri-image-add-line"></i>' +

                    '<span>대표 이미지</span>' +

                '</div>';

        }
    );

}



/*
 * =========================================================
 * OG
 * =========================================================
 */

var ogFile =
    document.getElementById(
        'ogFile'
    );


var ogPreview =
    document.getElementById(
        'ogPreview'
    );


var ogRemove =
    document.getElementById(
        'ogRemove'
    );


if(ogFile){

    ogFile.addEventListener(
        'change',
        function(){

            if(
                !this.files ||
                !this.files[0]
            ){
                return;
            }


            ogRemove.value =
                '0';


            var reader =
                new FileReader();


            reader.onload =
                function(e){

                    ogPreview.innerHTML =
                        '<img src="' +
                        e.target.result +
                        '" alt="">';

                };


            reader.readAsDataURL(
                this.files[0]
            );

        }
    );

}



var ogDelete =
    document.getElementById(
        'ogDelete'
    );


if(ogDelete){

    ogDelete.addEventListener(
        'click',
        function(){

            ogFile.value =
                '';


            ogRemove.value =
                '1';


            ogPreview.innerHTML =
                '<span>등록된 OG 이미지가 없습니다.</span>';

        }
    );

}



/*
 * =========================================================
 * Section Type Click
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
                    'work-section-type'
                )
            ){

                var type =
                    target.getAttribute(
                        'data-type'
                    );


                addWorkSection(
                    type,
                    null
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
 * Section Add
 * =========================================================
 */

function addWorkSection(
    type,
    data
){

    if(
        !WORK_SECTION_TYPES[type]
    ){

        return;

    }


    var index =
        sectionIndex++;


    var typeData =
        WORK_SECTION_TYPES[type];


    if(!data){

        data =
            {
                id: 0,
                section_type: type,
                sort_order: 0,
                is_enabled: 1,
                title: '',
                body_html: '',
                media: {},
                items: {}
            };

    }


    if(!data.media){
        data.media = {};
    }


    if(!data.items){
        data.items = {};
    }


    var html =
        '';


    html +=
        '<div class="work-section-item" ' +
        'data-index="' + index + '" ' +
        'data-section-id="' + workEscape(data.id) + '">';


    html +=
        '<input type="hidden" ' +
        'name="sections[' + index + '][id]" ' +
        'value="' + workEscape(data.id) + '">';


    html +=
        '<input type="hidden" ' +
        'name="sections[' + index + '][section_type]" ' +
        'value="' + workEscape(type) + '">';


    html +=
        '<input type="hidden" ' +
        'class="section-sort-order" ' +
        'name="sections[' + index + '][sort_order]" ' +
        'value="0">';



    /*
     * Header
     */

    html +=
        '<div class="work-section-head">';


    html +=
        '<div class="work-section-head-left">';


    html +=
        '<span class="work-section-handle">' +
            '<i class="ri-drag-move-2-line"></i>' +
        '</span>';


    html +=
        '<div class="work-section-head-icon">' +
            '<img src="' +
            workEscape(typeData.icon) +
            '" alt="">' +
        '</div>';


    html +=
        '<div class="work-section-head-title">' +
            '<strong>' +
                workEscape(typeData.name) +
            '</strong>' +
            '<span>' +
                workEscape(type) +
            '</span>' +
        '</div>';


    html +=
        '</div>';



    html +=
        '<div class="work-section-head-actions">';


    html +=
        '<label class="work-section-enable">' +

            '<input ' +
                'type="checkbox" ' +
                'name="sections[' + index + '][is_enabled]" ' +
                'value="1" ' +
                (
                    parseInt(
                        data.is_enabled,
                        10
                    ) == 1
                        ? 'checked'
                        : ''
                ) +
            '>' +

            '<span>사용</span>' +

        '</label>';


    html +=
        '<button type="button" ' +
            'class="work-section-up" ' +
            'title="위로">' +
            '<i class="ri-arrow-up-line"></i>' +
        '</button>';


    html +=
        '<button type="button" ' +
            'class="work-section-down" ' +
            'title="아래로">' +
            '<i class="ri-arrow-down-line"></i>' +
        '</button>';


    html +=
        '<button type="button" ' +
            'class="work-section-remove" ' +
            'title="삭제">' +
            '<i class="ri-delete-bin-line"></i>' +
        '</button>';


    html +=
        '</div>';


    html +=
        '</div>';



    /*
     * Body
     */

    html +=
        '<div class="work-section-body">';


    var fields =
        typeData.fields;


    for(
        var i = 0;
        i < fields.length;
        i++
    ){

        html +=
            renderSectionField(
                index,
                fields[i],
                data
            );

    }


    html +=
        '</div>';


    html +=
        '</div>';



    var container =
        document.getElementById(
            'workSections'
        );


    container.insertAdjacentHTML(
        'beforeend',
        html
    );


    refreshSectionSort();

}



/*
 * =========================================================
 * Field
 * =========================================================
 */

function renderSectionField(
    index,
    field,
    data
){

    var html =
        '';


    /*
     * Title
     */

    if(field == 'title'){

        html +=
            '<div class="work-section-field">' +

                '<label>제목</label>' +

                '<input ' +
                    'type="text" ' +
                    'name="sections[' + index + '][title]" ' +
                    'value="' +
                    workEscape(
                        data.title
                            ? data.title
                            : ''
                    ) +
                    '">' +

            '</div>';


        return html;

    }



    /*
     * Body
     */

    if(field == 'body_html'){

        html +=
            '<div class="work-section-field">' +

                '<label>내용</label>' +

                '<textarea ' +
                    'name="sections[' + index + '][body_html]" ' +
                    'rows="8">' +

                    workEscape(
                        data.body_html
                            ? data.body_html
                            : ''
                    ) +

                '</textarea>' +

            '</div>';


        return html;

    }



    /*
     * Project Items
     */

    if(
        field == 'summary_items' ||
        field == 'detail_items'
    ){

        var title =
            field == 'summary_items'
                ? '프로젝트 요약'
                : '프로젝트 상세 정보';


        html +=
            '<div class="work-section-field work-section-repeat-field">';


        html +=
            '<div class="work-repeat-head">' +

                '<label>' +
                    title +
                '</label>' +

                '<button ' +
                    'type="button" ' +
                    'class="btn btn-white btn-sm work-item-add" ' +
                    'data-group="' + field + '">' +

                    '<i class="ri-add-line"></i>' +

                    ' 항목 추가' +

                '</button>' +

            '</div>';


        html +=
            '<div class="work-item-list" ' +
            'data-group="' + field + '">';


        var items =
            data.items &&
            data.items[field]
                ? data.items[field]
                : [];


        for(
            var j = 0;
            j < items.length;
            j++
        ){

            html +=
                renderRepeatItem(
                    index,
                    field,
                    j,
                    items[j]
                );

        }


        html +=
            '</div>';


        html +=
            '</div>';


        return html;

    }



    /*
     * Image
     */

    if(
        field == 'image' ||
        field == 'image_01' ||
        field == 'image_02' ||
        field == 'logo'
    ){

        var label =
            '이미지';


        if(field == 'image_01'){
            label = '이미지 1';
        }


        if(field == 'image_02'){
            label = '이미지 2';
        }


        if(field == 'logo'){
            label = '프로젝트 로고';
        }


        var media =
            data.media &&
            data.media[field]
                ? data.media[field]
                : null;


        html +=
            '<div class="work-section-field work-section-image-field">';


        html +=
            '<label>' +
                label +
            '</label>';


        html +=
            '<input ' +
                'type="hidden" ' +
                'class="section-existing-media" ' +
                'name="sections[' + index + '][existing_media][' + field + ']" ' +
                'value="' +
                (
                    media
                        ? workEscape(media.media_id)
                        : '0'
                ) +
            '">';


        html +=
            '<input ' +
                'type="hidden" ' +
                'class="section-remove-media" ' +
                'name="sections[' + index + '][remove_media][' + field + ']" ' +
                'value="0">';


        html +=
            '<div class="work-section-image-preview">';


        if(
            media &&
            media.storage_key
        ){

            html +=
                '<img src="' +
                workEscape(
                    media.storage_key
                ) +
                '" alt="">';

        }else{

            html +=
                '<div class="work-section-image-empty">' +

                    '<i class="ri-image-add-line"></i>' +

                    '<span>이미지를 선택해주세요.</span>' +

                '</div>';

        }


        html +=
            '</div>';


        html +=
            '<div class="work-section-image-control">' +

                '<input ' +
                    'type="file" ' +
                    'class="section-image-file" ' +
                    'name="sections[' + index + '][media][' + field + ']" ' +
                    'accept="image/jpeg,image/png,image/gif,image/webp">' +

                '<button ' +
                    'type="button" ' +
                    'class="btn btn-white btn-sm section-image-remove">' +

                    '삭제' +

                '</button>' +

            '</div>';


        html +=
            '</div>';


        return html;

    }


    return html;

}



/*
 * =========================================================
 * Repeat Item
 * =========================================================
 */

function renderRepeatItem(
    sectionIndexValue,
    group,
    itemIndex,
    item
){

    if(!item){

        item =
            {
                id: 0,
                label: '',
                value_text: ''
            };

    }


    var html =
        '';


    html +=
        '<div class="work-item-row">';


    html +=
        '<input ' +
            'type="hidden" ' +
            'class="work-item-id" ' +
            'value="' +
            workEscape(
                item.id
                    ? item.id
                    : 0
            ) +
        '">';


    html +=
        '<input ' +
            'type="text" ' +
            'class="work-item-label" ' +
            'placeholder="항목명" ' +
            'value="' +
            workEscape(
                item.label
                    ? item.label
                    : ''
            ) +
        '">';


    html +=
        '<input ' +
            'type="text" ' +
            'class="work-item-value" ' +
            'placeholder="내용" ' +
            'value="' +
            workEscape(
                item.value_text
                    ? item.value_text
                    : ''
            ) +
        '">';


    html +=
        '<button ' +
            'type="button" ' +
            'class="work-item-remove">' +

            '<i class="ri-close-line"></i>' +

        '</button>';


    html +=
        '</div>';


    return html;

}



/*
 * =========================================================
 * Item Add
 * =========================================================
 */

document.addEventListener(
    'click',
    function(e){

        var button =
            e.target;


        while(
            button &&
            button !== document
        ){

            if(
                button.classList &&
                button.classList.contains(
                    'work-item-add'
                )
            ){

                var section =
                    button.closest(
                        '.work-section-item'
                    );


                var index =
                    section.getAttribute(
                        'data-index'
                    );


                var group =
                    button.getAttribute(
                        'data-group'
                    );


                var list =
                    section.querySelector(
                        '.work-item-list[data-group="' +
                        group +
                        '"]'
                    );


                list.insertAdjacentHTML(
                    'beforeend',
                    renderRepeatItem(
                        index,
                        group,
                        0,
                        null
                    )
                );


                refreshRepeatNames(
                    section,
                    group
                );


                return;

            }


            button =
                button.parentNode;

        }

    }
);



/*
 * =========================================================
 * Item Remove
 * =========================================================
 */

document.addEventListener(
    'click',
    function(e){

        var button =
            e.target;


        while(
            button &&
            button !== document
        ){

            if(
                button.classList &&
                button.classList.contains(
                    'work-item-remove'
                )
            ){

                var section =
                    button.closest(
                        '.work-section-item'
                    );


                var list =
                    button.closest(
                        '.work-item-list'
                    );


                var group =
                    list.getAttribute(
                        'data-group'
                    );


                var row =
                    button.closest(
                        '.work-item-row'
                    );


                row.parentNode.removeChild(
                    row
                );


                refreshRepeatNames(
                    section,
                    group
                );


                return;

            }


            button =
                button.parentNode;

        }

    }
);



/*
 * =========================================================
 * Repeat Name
 * =========================================================
 */

function refreshRepeatNames(
    section,
    group
){

    var sectionIndexValue =
        section.getAttribute(
            'data-index'
        );


    var rows =
        section.querySelectorAll(
            '.work-item-list[data-group="' +
            group +
            '"] .work-item-row'
        );


    for(
        var i = 0;
        i < rows.length;
        i++
    ){

        rows[i]
            .querySelector(
                '.work-item-id'
            )
            .setAttribute(
                'name',
                'sections[' +
                sectionIndexValue +
                '][items][' +
                group +
                '][' +
                i +
                '][id]'
            );


        rows[i]
            .querySelector(
                '.work-item-label'
            )
            .setAttribute(
                'name',
                'sections[' +
                sectionIndexValue +
                '][items][' +
                group +
                '][' +
                i +
                '][label]'
            );


        rows[i]
            .querySelector(
                '.work-item-value'
            )
            .setAttribute(
                'name',
                'sections[' +
                sectionIndexValue +
                '][items][' +
                group +
                '][' +
                i +
                '][value_text]'
            );

    }

}



/*
 * =========================================================
 * Section Remove / Sort
 * =========================================================
 */

document.addEventListener(
    'click',
    function(e){

        var button =
            e.target;


        while(
            button &&
            button !== document
        ){

            /*
             * Remove
             */

            if(
                button.classList &&
                button.classList.contains(
                    'work-section-remove'
                )
            ){

                var section =
                    button.closest(
                        '.work-section-item'
                    );


                confirm(
                    '이 섹션을 삭제하시겠습니까?',
                    function(){

                        var sectionId =
                            parseInt(
                                section.getAttribute(
                                    'data-section-id'
                                ),
                                10
                            );


                        if(sectionId > 0){

                            var input =
                                document.createElement(
                                    'input'
                                );


                            input.type =
                                'hidden';


                            input.name =
                                'deleted_section_ids[]';


                            input.value =
                                sectionId;


                            document
                                .getElementById(
                                    'workForm'
                                )
                                .appendChild(
                                    input
                                );

                        }


                        section.parentNode.removeChild(
                            section
                        );


                        refreshSectionSort();

                    }
                );


                return;

            }



            /*
             * Up
             */

            if(
                button.classList &&
                button.classList.contains(
                    'work-section-up'
                )
            ){

                var section =
                    button.closest(
                        '.work-section-item'
                    );


                var previous =
                    section.previousElementSibling;


                if(previous){

                    section.parentNode.insertBefore(
                        section,
                        previous
                    );

                }


                refreshSectionSort();

                return;

            }



            /*
             * Down
             */

            if(
                button.classList &&
                button.classList.contains(
                    'work-section-down'
                )
            ){

                var section =
                    button.closest(
                        '.work-section-item'
                    );


                var next =
                    section.nextElementSibling;


                if(next){

                    section.parentNode.insertBefore(
                        next,
                        section
                    );

                }


                refreshSectionSort();

                return;

            }


            button =
                button.parentNode;

        }

    }
);



/*
 * =========================================================
 * Section Image
 * =========================================================
 */

document.addEventListener(
    'change',
    function(e){

        var input =
            e.target;


        if(
            !input.classList ||
            !input.classList.contains(
                'section-image-file'
            )
        ){

            return;

        }


        if(
            !input.files ||
            !input.files[0]
        ){

            return;

        }


        var field =
            input.closest(
                '.work-section-image-field'
            );


        var preview =
            field.querySelector(
                '.work-section-image-preview'
            );


        var removeInput =
            field.querySelector(
                '.section-remove-media'
            );


        removeInput.value =
            '0';


        var reader =
            new FileReader();


        reader.onload =
            function(event){

                preview.innerHTML =
                    '<img src="' +
                    event.target.result +
                    '" alt="">';

            };


        reader.readAsDataURL(
            input.files[0]
        );

    }
);



document.addEventListener(
    'click',
    function(e){

        var button =
            e.target;


        while(
            button &&
            button !== document
        ){

            if(
                button.classList &&
                button.classList.contains(
                    'section-image-remove'
                )
            ){

                var field =
                    button.closest(
                        '.work-section-image-field'
                    );


                field
                    .querySelector(
                        '.section-image-file'
                    )
                    .value =
                    '';


                field
                    .querySelector(
                        '.section-remove-media'
                    )
                    .value =
                    '1';


                field
                    .querySelector(
                        '.work-section-image-preview'
                    )
                    .innerHTML =

                    '<div class="work-section-image-empty">' +

                        '<i class="ri-image-add-line"></i>' +

                        '<span>이미지를 선택해주세요.</span>' +

                    '</div>';


                return;

            }


            button =
                button.parentNode;

        }

    }
);



/*
 * =========================================================
 * Refresh Section
 * =========================================================
 */

function refreshSectionSort()
{

    var sections =
        document.querySelectorAll(
            '#workSections .work-section-item'
        );


    for(
        var i = 0;
        i < sections.length;
        i++
    ){

        sections[i]
            .querySelector(
                '.section-sort-order'
            )
            .value =
            i;


        var repeatLists =
            sections[i]
                .querySelectorAll(
                    '.work-item-list'
                );


        for(
            var j = 0;
            j < repeatLists.length;
            j++
        ){

            refreshRepeatNames(
                sections[i],
                repeatLists[j].getAttribute(
                    'data-group'
                )
            );

        }

    }


    var empty =
        document.getElementById(
            'workSectionEmpty'
        );


    if(sections.length > 0){

        empty.style.display =
            'none';

    }else{

        empty.style.display =
            '';

    }

}



/*
 * =========================================================
 * Existing Sections
 * =========================================================
 */

for(
    var initialIndex = 0;
    initialIndex < INITIAL_SECTIONS.length;
    initialIndex++
){

    addWorkSection(
        INITIAL_SECTIONS[initialIndex].section_type,
        INITIAL_SECTIONS[initialIndex]
    );

}


refreshSectionSort();



/*
 * =========================================================
 * Top Save
 * =========================================================
 */

var workForm =
    document.getElementById(
        'workForm'
    );


var workSubmit =
    document.getElementById(
        'workSubmit'
    );


var workSaveTop =
    document.getElementById(
        'workSaveTop'
    );


if(workSaveTop){

    workSaveTop.addEventListener(
        'click',
        function(){

            workForm.dispatchEvent(
                new Event(
                    'submit',
                    {
                        cancelable: true
                    }
                )
            );

        }
    );

}



/*
 * =========================================================
 * Submit
 * =========================================================
 */

workForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();


        /*
         * List Title
         */

        if(
            workForm.list_title.value.replace(
                /^\s+|\s+$/g,
                ''
            ) == ''
        ){

            alert(
                '리스트 제목을 입력해주세요.',
                function(){

                    workForm.list_title.focus();

                }
            );

            return;

        }



        /*
         * Title
         */

        if(
            workForm.title.value.replace(
                /^\s+|\s+$/g,
                ''
            ) == ''
        ){

            alert(
                '상세 제목을 입력해주세요.',
                function(){

                    workForm.title.focus();

                }
            );

            return;

        }



        /*
         * Category
         */

        if(
            parseInt(
                workForm.category_id.value,
                10
            ) <= 0
        ){

            alert(
                '카테고리를 선택해주세요.',
                function(){

                    workForm.category_id.focus();

                }
            );

            return;

        }



        /*
         * Slug
         */

        var slug =
            workForm.slug.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(slug == ''){

            alert(
                'URL Slug를 입력해주세요.',
                function(){

                    workForm.slug.focus();

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
                'URL Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.',
                function(){

                    workForm.slug.focus();

                }
            );

            return;

        }


        saveWork();

    }
);



/*
 * =========================================================
 * Save
 * =========================================================
 */

function saveWork()
{

    if(
        workSubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    workSubmit.setAttribute(
        'data-loading',
        'Y'
    );


    workSubmit.disabled =
        true;


    workSaveTop.disabled =
        true;


    var formData =
        new FormData(
            workForm
        );


    /*
     * unchecked checkbox 보정
     */

    if(
        !workForm.querySelector(
            'input[name="allow_index"]:checked'
        )
    ){

        formData.append(
            'allow_index',
            '0'
        );

    }


    if(
        !workForm.querySelector(
            'input[name="nofollow"]:checked'
        )
    ){

        formData.append(
            'nofollow',
            '0'
        );

    }



    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/work_save',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){
                return;
            }


            workSubmit.removeAttribute(
                'data-loading'
            );


            workSubmit.disabled =
                false;


            workSaveTop.disabled =
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
                        : 'WORK를 저장하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : 'WORK가 저장되었습니다.',
                function(){

                    location.href =
                        '/manage/works_list';

                }
            );

        };


    xhr.send(
        formData
    );

}


/*
 * =========================================================
 * Section Drag Sort
 * =========================================================
 */

var workSectionsElement =
    document.getElementById(
        'workSections'
    );


if(
    workSectionsElement &&
    typeof Sortable != 'undefined'
){

    Sortable.create(
        workSectionsElement,
        {

            /*
             * 드래그 핸들
             */

            handle:
                '.work-section-handle',


            /*
             * 애니메이션
             */

            animation:
                180,


            /*
             * 드래그 상태 Class
             */

            ghostClass:
                'work-section-ghost',

            chosenClass:
                'work-section-chosen',

            dragClass:
                'work-section-drag',


            /*
             * 드래그 감도
             */

            swapThreshold:
                0.65,


            /*
             * BODY 기준 드래그
             */

            fallbackOnBody:
                true,


            /*
             * 완료
             */

            onEnd:
                function(){

                    refreshSectionSort();

                }

        }
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>