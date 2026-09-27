<?
/*
 * =========================================================
 * WEEKENDS CMS
 * NEWS 등록 / 수정
 *
 * FILE
 * /manage/blog_write.php
 *
 * URL
 * /manage/blog_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * Helper
 * =========================================================
 */

function blog_write_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}


function blog_write_datetime_local($value)
{
    if($value == ''){
        return '';
    }

    $time =
        strtotime(
            $value
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
 * BLOG Board
 *
 * 내부 코드 / 파일명 = BLOG
 * 관리자 표시 = NEWS
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

$post_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $post_id > 0
        ? true
        : false;



/*
 * =========================================================
 * 관리자
 * =========================================================
 */

$admin_id =
    isset($_SESSION['ADMIN_ID'])
        ? intval($_SESSION['ADMIN_ID'])
        : 0;


$default_author_name =
    isset($_SESSION['ADMIN_NAME']) &&
    trim($_SESSION['ADMIN_NAME']) != ''
        ? trim($_SESSION['ADMIN_NAME'])
        : '관리자';



/*
 * =========================================================
 * 기본 데이터
 * =========================================================
 */

$post =
    array(
        'id'                  => 0,
        'board_id'            => $board_id,
        'category_id'         => 0,
        'is_pinned'           => 0,
        'title'               => '',
        'slug'                => '',
        'summary'             => '',
        'body_html'           => '',
        'status'              => 'draft',
        'visibility'          => 'private',
        'display_at'          => date('Y-m-d H:i:s'),
        'publish_at'          => '',
        'author_id'           => $admin_id,
        'author_display_name' => $default_author_name,
        'view_count'          => 0,
        'created_at'          => '',
        'updated_at'          => ''
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
            category_id,
            is_pinned,
            title,
            slug,
            summary,
            body_html,
            status,
            visibility,
            display_at,
            publish_at,
            author_id,
            author_display_name,
            view_count,
            created_at,
            updated_at

        FROM posts

        WHERE
            id = ?
            AND board_id = ?
            AND deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $post_id,
                $board_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        header(
            'Location: /manage/blog_list'
        );

        exit;

    }


    $post =
        $data;

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
        is_active,
        sort_order

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

    /*
     * 등록에서는 활성 카테고리만
     * 수정에서는 현재 선택된 비활성 카테고리도 표시
     */

    if(
        intval($data['is_active']) == 1 ||
        intval($data['id']) == intval($post['category_id'])
    ){

        $categories[] =
            $data;

    }

}



/*
 * =========================================================
 * 선택된 태그
 * =========================================================
 */

$selected_tags =
    array();


if($is_edit){

    $query = "

        SELECT
            tag_id

        FROM post_tag_map

        WHERE
            board_id = ?
            AND post_id = ?

        ORDER BY
            sort_order ASC,
            tag_id ASC

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $board_id,
                $post_id
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
        slug,
        sort_order,
        is_active

    FROM post_tags

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

    if(
        intval($data['is_active']) == 1 ||
        in_array(
            intval($data['id']),
            $selected_tags
        )
    ){

        $tags[] =
            $data;

    }

}



/*
 * =========================================================
 * 대표 이미지
 * =========================================================
 */

$thumbnail =
    array(
        'media_id'     => 0,
        'storage_key'  => '',
        'original_name'=> ''
    );


if($is_edit){

    $query = "

        SELECT

            pm.media_id,

            m.storage_key,
            m.original_name

        FROM post_media pm

        INNER JOIN media m
            ON m.id = pm.media_id

        WHERE
            pm.post_id = ?
            AND pm.role = 'thumbnail'
            AND m.deleted_at IS NULL

        ORDER BY
            pm.sort_order ASC,
            pm.id ASC

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $post_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($data){

        $thumbnail =
            $data;

    }

}



/*
 * =========================================================
 * SEO
 * =========================================================
 */

$seo =
    array(
        'seo_title'        => '',
        'meta_description' => '',
        'meta_keywords'    => '',
        'canonical_url'    => '',
        'og_title'         => '',
        'og_description'   => '',
        'og_media_id'      => 0,
        'allow_index'      => 1,
        'nofollow'         => 0
    );


if($is_edit){

    $query = "

        SELECT

            seo_title,
            meta_description,
            meta_keywords,
            canonical_url,
            og_title,
            og_description,
            og_media_id,
            allow_index,
            nofollow

        FROM post_seo

        WHERE
            post_id = ?

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $post_id
            )
        );


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($data){

        $seo =
            $data;

    }

}



/*
 * =========================================================
 * OG Image
 * =========================================================
 */

$og_image =
    array(
        'media_id'      => 0,
        'storage_key'   => '',
        'original_name' => ''
    );


if(
    intval($seo['og_media_id']) > 0
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


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($data){

        $og_image =
            $data;

    }

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/blog.css"
>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7.9.1/tinymce.min.js"></script>


<main class="content">

<form
    id="blogForm"
    method="post"
    enctype="multipart/form-data"
    action="javascript:void(0);"
>

    <input
        type="hidden"
        name="id"
        value="<?=intval($post['id'])?>"
    >

    <input
        type="hidden"
        name="thumbnail_remove"
        id="thumbnailRemove"
        value="0"
    >

    <input
        type="hidden"
        name="og_image_remove"
        id="ogImageRemove"
        value="0"
    >


    <div class="content-inner">


        <!-- =================================================
             Page Header
        ================================================== -->

        <div class="blog-page-head blog-write-head">

            <div class="blog-page-title">

                <span>
                    NEWS
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>

                    <?
                    if($is_edit){
                    ?>

                        NEWS 수정

                    <?
                    }else{
                    ?>

                        NEWS 등록

                    <?
                    }
                    ?>

                </h2>

            </div>


            <div class="blog-page-action">


                <button
                    type="button"
                    class="btn btn-white"
                    id="previewButton"
                >

                    <i class="ri-external-link-line"></i>

                    미리보기

                </button>


                <select
                    name="status"
                    class="blog-status-select"
                >

                    <option
                        value="draft"
                        <?=$post['status'] == 'draft' ? 'selected' : ''?>
                    >
                        임시저장
                    </option>

                    <option
                        value="published"
                        <?=$post['status'] == 'published' ? 'selected' : ''?>
                    >
                        게시
                    </option>

                    <option
                        value="archived"
                        <?=$post['status'] == 'archived' ? 'selected' : ''?>
                    >
                        보관
                    </option>

                </select>


                <button
                    type="submit"
                    class="btn btn-primary"
                    id="blogSubmit"
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

        </div>



        <!-- =================================================
             Editor Layout
        ================================================== -->

        <div class="editor-layout">


            <!-- =================================================
                 LEFT
            ================================================== -->

            <section class="editor-main">


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


                        <!-- Title -->

                        <div class="form-row">

                            <label>

                                제목

                                <em>*</em>

                            </label>


                            <div class="form-control">

                                <input
                                    type="text"
                                    name="title"
                                    value="<?=blog_write_h($post['title'])?>"
                                    placeholder="제목을 입력해주세요."
                                    maxlength="500"
                                >

                            </div>

                        </div>



                        <!-- Slug -->

                        <div class="form-row">

                            <label>

                                URL Slug

                                <em>*</em>

                            </label>


                            <div class="form-control">

                                <div class="blog-write-slug">

                                    <span>
                                        /news/
                                    </span>

                                    <input
                                        type="text"
                                        name="slug"
                                        value="<?=blog_write_h($post['slug'])?>"
                                        placeholder="weekends-news"
                                        maxlength="180"
                                    >

                                </div>


                                <p class="form-help">

                                    영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.

                                </p>

                            </div>

                        </div>



                        <!-- Category -->

                        <div class="form-row">

                            <label>

                                카테고리

                                <em>*</em>

                            </label>


                            <div class="form-control">

                                <select
                                    name="category_id"
                                >

                                    <option value="0">

                                        카테고리를 선택하세요.

                                    </option>


                                    <?
                                    foreach(
                                        $categories as
                                        $category
                                    ){
                                    ?>

                                        <option
                                            value="<?=$category['id']?>"
                                            <?=intval($post['category_id']) == intval($category['id']) ? 'selected' : ''?>
                                        >

                                            <?=blog_write_h(
                                                $category['name']
                                            )?>

                                        </option>

                                    <?
                                    }
                                    ?>

                                </select>

                            </div>

                        </div>



                        <!-- Tags -->

                        <div class="form-row">

                            <label>
                                태그
                            </label>


                            <div class="form-control">

                                <?
                                if(count($tags) > 0){
                                ?>

                                    <div class="blog-tag-selector">

                                        <?
                                        foreach(
                                            $tags as
                                            $tag
                                        ){

                                            $tag_checked =
                                                in_array(
                                                    intval($tag['id']),
                                                    $selected_tags
                                                );
                                        ?>

                                            <label class="blog-tag-option">

                                                <input
                                                    type="checkbox"
                                                    name="tag_ids[]"
                                                    value="<?=$tag['id']?>"
                                                    <?=$tag_checked ? 'checked' : ''?>
                                                >

                                                <span>

                                                    #<?=blog_write_h(
                                                        $tag['name']
                                                    )?>

                                                </span>

                                            </label>

                                        <?
                                        }
                                        ?>

                                    </div>

                                <?
                                }else{
                                ?>

                                    <div class="blog-tag-empty">

                                        등록된 태그가 없습니다.

                                        <a href="/manage/blog_tag_write">
                                            태그 등록
                                        </a>

                                    </div>

                                <?
                                }
                                ?>

                            </div>

                        </div>



                        <!-- Thumbnail -->

                        <div class="form-row">

                            <label>
                                대표 이미지
                            </label>


                            <div class="form-control">


                                <div
                                    class="blog-upload-area"
                                    id="thumbnailUploadArea"
                                >

                                    <input
                                        type="file"
                                        name="thumbnail"
                                        id="thumbnailInput"
                                        accept="image/jpeg,image/png,image/gif,image/webp"
                                    >


                                    <button
                                        type="button"
                                        class="blog-upload-button"
                                        id="thumbnailButton"
                                    >

                                        <i class="ri-add-line"></i>

                                        <span>
                                            이미지 업로드
                                        </span>

                                    </button>

                                </div>


                                <div class="upload-guide">

                                    <p>
                                        권장 사이즈 : 1200 × 630px (1.91:1)
                                    </p>

                                    <p>
                                        JPG, PNG, GIF, WEBP
                                    </p>

                                </div>



                                <!-- Preview -->

                                <div
                                    class="blog-image-preview <?=intval($thumbnail['media_id']) > 0 ? 'active' : ''?>"
                                    id="thumbnailPreview"
                                >

                                    <img
                                        src="<?=blog_write_h($thumbnail['storage_key'])?>"
                                        alt=""
                                        id="thumbnailPreviewImage"
                                        <?=intval($thumbnail['media_id']) <= 0 ? 'style="display:none;"' : ''?>
                                    >


                                    <button
                                        type="button"
                                        class="blog-image-remove"
                                        id="thumbnailRemoveButton"
                                        <?=intval($thumbnail['media_id']) <= 0 ? 'style="display:none;"' : ''?>
                                    >

                                        <i class="ri-delete-bin-line"></i>

                                        이미지 제거

                                    </button>

                                </div>


                            </div>

                        </div>



                        <!-- Summary -->

                        <div class="form-row">

                            <label>
                                요약 설명
                            </label>


                            <div class="form-control">

                                <textarea
                                    name="summary"
                                    id="summary"
                                    rows="4"
                                    maxlength="200"
                                    placeholder="NEWS에 대한 간단한 요약을 입력해주세요."
                                ><?=blog_write_h($post['summary'])?></textarea>


                                <div
                                    class="text-counter"
                                    id="summaryCounter"
                                >

                                    <?=mb_strlen($post['summary'], 'UTF-8')?> / 200

                                </div>

                            </div>

                        </div>



                        <!-- Body -->

                      <!-- Body -->

<div class="form-row editor-row">

    <label>

        본문

        <em>*</em>

    </label>


    <div class="form-control">


        <!-- Editor Tools -->

        <div class="blog-editor-tools">

            <div class="blog-editor-tools-left">

                <button
                    type="button"
                    class="btn btn-white btn-sm"
                    id="editorImageButton"
                >

                    <i class="ri-image-add-line"></i>

                    이미지 업로드

                </button>


                <input
                    type="file"
                    id="editorImageInput"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    multiple
                >

            </div>


            <div class="blog-editor-tools-guide">

                JPG, PNG, GIF, WEBP · 최대 5MB

            </div>

        </div>



        <!-- Editor -->

        <textarea
            name="body_html"
            id="editor"
        ><?=blog_write_h($post['body_html'])?></textarea>


    </div>

</div>



                <!-- =================================================
                     공개 설정
                ================================================== -->

                <div class="card">

                    <div class="card-header">

                        <h3>
                            공개 설정
                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="setting-grid">


                            <!-- Visibility -->

                            <div class="setting-item">

                                <label>
                                    공개 여부
                                </label>


                                <div class="radio-group">

                                    <label>

                                        <input
                                            type="radio"
                                            name="visibility"
                                            value="public"
                                            <?=$post['visibility'] == 'public' ? 'checked' : ''?>
                                        >

                                        공개

                                    </label>


                                    <label>

                                        <input
                                            type="radio"
                                            name="visibility"
                                            value="private"
                                            <?=$post['visibility'] == 'private' ? 'checked' : ''?>
                                        >

                                        비공개

                                    </label>

                                </div>

                            </div>



                            <!-- Pin -->

                            <div class="setting-item">

                                <label>
                                    상단 고정
                                </label>


                                <div class="check-group">

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="is_pinned"
                                            value="1"
                                            <?=intval($post['is_pinned']) == 1 ? 'checked' : ''?>
                                        >

                                        NEWS 목록 상단에 고정

                                    </label>

                                </div>

                            </div>



                            <!-- Display Date -->

                            <div class="setting-item">

                                <label>
                                    작성일
                                </label>


                                <input
                                    type="datetime-local"
                                    name="display_at"
                                    value="<?=blog_write_datetime_local($post['display_at'])?>"
                                >

                            </div>



                            <!-- Publish Date -->

                            <div class="setting-item">

                                <label>
                                    예약 게시
                                </label>


                                <input
                                    type="datetime-local"
                                    name="publish_at"
                                    value="<?=blog_write_datetime_local($post['publish_at'])?>"
                                >


                                <p class="form-help">

                                    예약 시간을 설정하면 해당 시간 이후 게시되도록 사용할 수 있습니다.

                                </p>

                            </div>



                            <!-- Author -->

                            <div class="setting-item">

                                <label>
                                    작성자
                                </label>


                                <input
                                    type="text"
                                    name="author_display_name"
                                    value="<?=blog_write_h($post['author_display_name'])?>"
                                    maxlength="100"
                                >

                            </div>


                            <?
                            if($is_edit){
                            ?>

                                <div class="setting-item">

                                    <label>
                                        조회수
                                    </label>


                                    <input
                                        type="text"
                                        value="<?=number_format(intval($post['view_count']))?>"
                                        readonly
                                    >

                                </div>

                            <?
                            }
                            ?>


                        </div>

                    </div>

                </div>


            </section>



            <!-- =================================================
                 SEO
            ================================================== -->

            <aside class="editor-side">

                <div class="card seo-card">

                    <div class="card-header">

                        <h3>
                            SEO 관리
                        </h3>

                    </div>


                    <div class="card-body">


                        <!-- Preview -->

                        <div class="seo-preview">

                            <h4>
                                미리보기
                            </h4>


                            <div class="preview-box">

                                <small id="seoPreviewUrl">

                                    https://www.weekends.co.kr/news/<?=blog_write_h($post['slug'])?>

                                </small>


                                <strong id="seoPreviewTitle">

                                    <?
                                    if($seo['seo_title'] != ''){
                                    ?>

                                        <?=blog_write_h($seo['seo_title'])?>

                                    <?
                                    }else if($post['title'] != ''){
                                    ?>

                                        <?=blog_write_h($post['title'])?> | WEEKENDS

                                    <?
                                    }else{
                                    ?>

                                        NEWS 제목 | WEEKENDS

                                    <?
                                    }
                                    ?>

                                </strong>


                                <p id="seoPreviewDescription">

                                    <?
                                    if($seo['meta_description'] != ''){
                                    ?>

                                        <?=blog_write_h($seo['meta_description'])?>

                                    <?
                                    }else{
                                    ?>

                                        NEWS의 요약 설명이 노출되는 영역입니다.

                                    <?
                                    }
                                    ?>

                                </p>

                            </div>

                        </div>



                        <!-- SEO Title -->

                        <div class="form-group">

                            <label>
                                SEO Title
                            </label>


                            <input
                                type="text"
                                name="seo_title"
                                id="seoTitle"
                                value="<?=blog_write_h($seo['seo_title'])?>"
                                maxlength="255"
                            >

                        </div>



                        <!-- Description -->

                        <div class="form-group">

                            <label>
                                Meta Description
                            </label>


                            <textarea
                                name="meta_description"
                                id="metaDescription"
                                rows="4"
                            ><?=blog_write_h($seo['meta_description'])?></textarea>

                        </div>



                        <!-- Keywords -->

                        <div class="form-group">

                            <label>
                                Meta Keywords
                            </label>


                            <input
                                type="text"
                                name="meta_keywords"
                                value="<?=blog_write_h($seo['meta_keywords'])?>"
                                placeholder="news, weekends, project"
                            >

                        </div>



                        <!-- Canonical -->

                        <div class="form-group">

                            <label>
                                Canonical URL
                            </label>


                            <input
                                type="text"
                                name="canonical_url"
                                value="<?=blog_write_h($seo['canonical_url'])?>"
                                placeholder="https://www.weekends.co.kr/news/..."
                            >

                        </div>



                        <!-- OG Title -->

                        <div class="form-group">

                            <label>
                                OG Title
                            </label>


                            <input
                                type="text"
                                name="og_title"
                                value="<?=blog_write_h($seo['og_title'])?>"
                                maxlength="255"
                            >

                        </div>



                        <!-- OG Description -->

                        <div class="form-group">

                            <label>
                                OG Description
                            </label>


                            <textarea
                                name="og_description"
                                rows="4"
                            ><?=blog_write_h($seo['og_description'])?></textarea>

                        </div>



                        <!-- OG Image -->

                        <div class="form-group">

                            <label>
                                OG Image
                            </label>


                            <div
                                class="blog-upload-area blog-upload-small"
                            >

                                <input
                                    type="file"
                                    name="og_image"
                                    id="ogImageInput"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                >


                                <button
                                    type="button"
                                    class="blog-upload-button"
                                    id="ogImageButton"
                                >

                                    <i class="ri-add-line"></i>

                                    <span>
                                        이미지 업로드
                                    </span>

                                </button>

                            </div>


                            <div
                                class="blog-image-preview blog-og-preview <?=intval($og_image['media_id']) > 0 ? 'active' : ''?>"
                                id="ogImagePreview"
                            >

                                <img
                                    src="<?=blog_write_h($og_image['storage_key'])?>"
                                    alt=""
                                    id="ogImagePreviewImage"
                                    <?=intval($og_image['media_id']) <= 0 ? 'style="display:none;"' : ''?>
                                >


                                <button
                                    type="button"
                                    class="blog-image-remove"
                                    id="ogImageRemoveButton"
                                    <?=intval($og_image['media_id']) <= 0 ? 'style="display:none;"' : ''?>
                                >

                                    <i class="ri-delete-bin-line"></i>

                                    이미지 제거

                                </button>

                            </div>

                        </div>



                        <!-- Robots -->

                        <div class="form-group">

                            <label>
                                로봇 설정
                            </label>


                            <div class="check-group blog-robot-setting">


                                <label>

                                    <input
                                        type="checkbox"
                                        name="allow_index"
                                        value="1"
                                        <?=intval($seo['allow_index']) == 1 ? 'checked' : ''?>
                                    >

                                    Index 허용

                                </label>


                                <label>

                                    <input
                                        type="checkbox"
                                        name="nofollow"
                                        value="1"
                                        <?=intval($seo['nofollow']) == 1 ? 'checked' : ''?>
                                    >

                                    No Follow

                                </label>


                            </div>

                        </div>


                    </div>

                </div>

            </aside>


        </div>


    </div>

</form>

</main>



<script>

/*
 * =========================================================
 * TinyMCE
 * =========================================================
 */

/*
 * =========================================================
 * TinyMCE
 * =========================================================
 */

if(
    typeof tinymce != 'undefined'
){

    tinymce.init({

        selector: '#editor',

        height: 560,

        menubar: false,

        branding: false,

        promotion: false,

        plugins:
            'lists link table code autoresize image',

        /*
         * 이미지 아이콘은 바깥으로 뺐기 때문에
         * toolbar에서는 image 제거
         */

        toolbar:
            'undo redo | ' +
            'blocks | ' +
            'bold italic underline strikethrough | ' +
            'bullist numlist blockquote | ' +
            'alignleft aligncenter alignright | ' +
            'link table | code',

        /*
         * 드래그 / 붙여넣기 이미지 업로드는
         * 기존 기능 그대로 사용
         */

        automatic_uploads: true,

        images_upload_url:
            '/manage/proc/editor_image_upload',

        images_upload_credentials:
            true,

        paste_data_images:
            true,

        convert_urls:
            false,

        relative_urls:
            false,

        remove_script_host:
            false,

        content_style:
            'body {' +
                'font-family: Arial, sans-serif;' +
                'font-size: 15px;' +
                'line-height: 1.8;' +
                'padding: 16px;' +
            '}' +

            'img {' +
                'max-width: 100%;' +
                'height: auto;' +
            '}',

        setup:
            function(editor){

                /*
                 * 에디터 로드 완료
                 */

                editor.on(
                    'init',
                    function(){

                        editor.getBody().setAttribute(
                            'data-editor-ready',
                            'Y'
                        );

                    }
                );

            }

    });

}



/*
 * =========================================================
 * Elements
 * =========================================================
 */

var blogForm =
    document.getElementById(
        'blogForm'
    );


var blogSubmit =
    document.getElementById(
        'blogSubmit'
    );


var summary =
    document.getElementById(
        'summary'
    );


var summaryCounter =
    document.getElementById(
        'summaryCounter'
    );


var thumbnailInput =
    document.getElementById(
        'thumbnailInput'
    );


var thumbnailButton =
    document.getElementById(
        'thumbnailButton'
    );


var thumbnailPreview =
    document.getElementById(
        'thumbnailPreview'
    );


var thumbnailPreviewImage =
    document.getElementById(
        'thumbnailPreviewImage'
    );


var thumbnailRemoveButton =
    document.getElementById(
        'thumbnailRemoveButton'
    );


var thumbnailRemove =
    document.getElementById(
        'thumbnailRemove'
    );


var ogImageInput =
    document.getElementById(
        'ogImageInput'
    );


var ogImageButton =
    document.getElementById(
        'ogImageButton'
    );


var ogImagePreview =
    document.getElementById(
        'ogImagePreview'
    );


var ogImagePreviewImage =
    document.getElementById(
        'ogImagePreviewImage'
    );


var ogImageRemoveButton =
    document.getElementById(
        'ogImageRemoveButton'
    );


var ogImageRemove =
    document.getElementById(
        'ogImageRemove'
    );



/*
 * =========================================================
 * Summary Counter
 * =========================================================
 */

function refreshSummaryCounter()
{

    var length =
        summary.value.length;


    summaryCounter.innerHTML =
        length +
        ' / 200';

}


summary.addEventListener(
    'input',
    refreshSummaryCounter
);



/*
 * =========================================================
 * Slug
 * =========================================================
 */

blogForm.slug.addEventListener(
    'input',
    function(){

        var value =
            this.value;


        value =
            value.toLowerCase();


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


        refreshSeoPreview();

    }
);



/*
 * =========================================================
 * Thumbnail
 * =========================================================
 */

thumbnailButton.addEventListener(
    'click',
    function(){

        thumbnailInput.click();

    }
);


thumbnailInput.addEventListener(
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

                thumbnailPreviewImage.src =
                    e.target.result;


                thumbnailPreviewImage.style.display =
                    'block';


                thumbnailRemoveButton.style.display =
                    'inline-flex';


                thumbnailPreview.classList.add(
                    'active'
                );


                thumbnailRemove.value =
                    '0';

            };


        reader.readAsDataURL(
            file
        );

    }
);



thumbnailRemoveButton.addEventListener(
    'click',
    function(){

        thumbnailInput.value =
            '';


        thumbnailPreviewImage.src =
            '';


        thumbnailPreviewImage.style.display =
            'none';


        thumbnailRemoveButton.style.display =
            'none';


        thumbnailPreview.classList.remove(
            'active'
        );


        thumbnailRemove.value =
            '1';

    }
);



/*
 * =========================================================
 * OG Image
 * =========================================================
 */

ogImageButton.addEventListener(
    'click',
    function(){

        ogImageInput.click();

    }
);


ogImageInput.addEventListener(
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

                ogImagePreviewImage.src =
                    e.target.result;


                ogImagePreviewImage.style.display =
                    'block';


                ogImageRemoveButton.style.display =
                    'inline-flex';


                ogImagePreview.classList.add(
                    'active'
                );


                ogImageRemove.value =
                    '0';

            };


        reader.readAsDataURL(
            file
        );

    }
);



ogImageRemoveButton.addEventListener(
    'click',
    function(){

        ogImageInput.value =
            '';


        ogImagePreviewImage.src =
            '';


        ogImagePreviewImage.style.display =
            'none';


        ogImageRemoveButton.style.display =
            'none';


        ogImagePreview.classList.remove(
            'active'
        );


        ogImageRemove.value =
            '1';

    }
);



/*
 * =========================================================
 * SEO Preview
 * =========================================================
 */

function refreshSeoPreview()
{

    var title =
        blogForm.title.value;


    var slug =
        blogForm.slug.value;


    var seoTitle =
        document.getElementById(
            'seoTitle'
        ).value;


    var description =
        document.getElementById(
            'metaDescription'
        ).value;


    document.getElementById(
        'seoPreviewUrl'
    ).innerHTML =
        'https://www.weekends.co.kr/news/' +
        slug;


    if(seoTitle != ''){

        document.getElementById(
            'seoPreviewTitle'
        ).innerHTML =
            seoTitle;

    }else if(title != ''){

        document.getElementById(
            'seoPreviewTitle'
        ).innerHTML =
            title +
            ' | WEEKENDS';

    }else{

        document.getElementById(
            'seoPreviewTitle'
        ).innerHTML =
            'NEWS 제목 | WEEKENDS';

    }


    if(description != ''){

        document.getElementById(
            'seoPreviewDescription'
        ).innerHTML =
            description;

    }else{

        document.getElementById(
            'seoPreviewDescription'
        ).innerHTML =
            'NEWS의 요약 설명이 노출되는 영역입니다.';

    }

}


blogForm.title.addEventListener(
    'input',
    refreshSeoPreview
);


document.getElementById(
    'seoTitle'
).addEventListener(
    'input',
    refreshSeoPreview
);


document.getElementById(
    'metaDescription'
).addEventListener(
    'input',
    refreshSeoPreview
);



/*
 * =========================================================
 * Preview
 * =========================================================
 */

document.getElementById(
    'previewButton'
).addEventListener(
    'click',
    function(){

        var slug =
            blogForm.slug.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(slug == ''){

            alert(
                '미리보기 전에 URL Slug를 입력해주세요.'
            );

            return;

        }


        window.open(
            '/news/' +
            encodeURIComponent(
                slug
            ),
            '_blank'
        );

    }
);



/*
 * =========================================================
 * Submit
 * =========================================================
 */

blogForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();



        /*
         * TinyMCE -> textarea
         */

        if(
            typeof tinymce != 'undefined'
        ){

            tinymce.triggerSave();

        }



        /*
         * Title
         */

        var title =
            blogForm.title.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(title == ''){

            alert(
                '제목을 입력해주세요.',
                function(){

                    blogForm.title.focus();

                }
            );

            return;

        }



        /*
         * Slug
         */

        var slug =
            blogForm.slug.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(slug == ''){

            alert(
                'URL Slug를 입력해주세요.',
                function(){

                    blogForm.slug.focus();

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
                'Slug는 영문 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다.'
            );

            return;

        }



        /*
         * Category
         */

        if(
            parseInt(
                blogForm.category_id.value,
                10
            ) <= 0
        ){

            alert(
                '카테고리를 선택해주세요.',
                function(){

                    blogForm.category_id.focus();

                }
            );

            return;

        }



        /*
         * Body
         */

        var bodyHtml =
            blogForm.body_html.value.replace(
                /^\s+|\s+$/g,
                ''
            );


        if(bodyHtml == ''){

            alert(
                '본문을 입력해주세요.'
            );

            return;

        }



        /*
         * Save
         */

        saveBlog();

    }
);



/*
 * =========================================================
 * Save
 * =========================================================
 */

function saveBlog()
{

    if(
        blogSubmit.getAttribute(
            'data-loading'
        ) == 'Y'
    ){

        return;

    }


    blogSubmit.setAttribute(
        'data-loading',
        'Y'
    );


    blogSubmit.disabled =
        true;



    var formData =
        new FormData(
            blogForm
        );


    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/blog_save',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){

                return;

            }


            blogSubmit.removeAttribute(
                'data-loading'
            );


            blogSubmit.disabled =
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
                        : 'NEWS를 저장하지 못했습니다.'
                );

                return;

            }


            alert(
                result.msg
                    ? result.msg
                    : 'NEWS가 저장되었습니다.',
                function(){

                    location.href =
                        '/manage/blog_list';

                }
            );

        };


    xhr.send(
        formData
    );

}


/*
 * =========================================================
 * Editor Image Upload
 * =========================================================
 */

var editorImageButton =
    document.getElementById(
        'editorImageButton'
    );


var editorImageInput =
    document.getElementById(
        'editorImageInput'
    );



/*
 * 버튼 -> 파일 선택
 */

if(editorImageButton){

    editorImageButton.addEventListener(
        'click',
        function(){

            if(
                editorImageButton.getAttribute(
                    'data-loading'
                ) == 'Y'
            ){

                return;

            }


            editorImageInput.click();

        }
    );

}



/*
 * 파일 선택
 */

if(editorImageInput){

    editorImageInput.addEventListener(
        'change',
        function(){

            if(
                !this.files ||
                this.files.length <= 0
            ){

                return;

            }


            var files =
                [];


            for(
                var i = 0;
                i < this.files.length;
                i++
            ){

                files.push(
                    this.files[i]
                );

            }


            /*
             * input 초기화
             *
             * 같은 파일 다시 선택 가능
             */

            this.value =
                '';


            uploadEditorImages(
                files,
                0
            );

        }
    );

}



/*
 * =========================================================
 * 여러 이미지 순차 업로드
 * =========================================================
 */

function uploadEditorImages(
    files,
    index
){

    if(
        index >= files.length
    ){

        editorImageButton.removeAttribute(
            'data-loading'
        );


        editorImageButton.disabled =
            false;


        editorImageButton.innerHTML =
            '<i class="ri-image-add-line"></i> 이미지 업로드';


        return;

    }



    var file =
        files[index];


    /*
     * 파일 크기
     */

    if(
        file.size >
        5 * 1024 * 1024
    ){

        editorImageButton.removeAttribute(
            'data-loading'
        );


        editorImageButton.disabled =
            false;


        editorImageButton.innerHTML =
            '<i class="ri-image-add-line"></i> 이미지 업로드';


        alert(
            '이미지는 최대 5MB까지 업로드할 수 있습니다.'
        );


        return;

    }



    /*
     * MIME
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

        editorImageButton.removeAttribute(
            'data-loading'
        );


        editorImageButton.disabled =
            false;


        editorImageButton.innerHTML =
            '<i class="ri-image-add-line"></i> 이미지 업로드';


        alert(
            'JPG, PNG, GIF, WEBP 이미지만 업로드할 수 있습니다.'
        );


        return;

    }



    /*
     * Loading
     */

    editorImageButton.setAttribute(
        'data-loading',
        'Y'
    );


    editorImageButton.disabled =
        true;


    editorImageButton.innerHTML =
        '<i class="ri-loader-4-line blog-spin"></i> 업로드 중';



    /*
     * FormData
     */

    var formData =
        new FormData();


    formData.append(
        'file',
        file
    );



    /*
     * AJAX
     */

    var xhr =
        new XMLHttpRequest();


    xhr.open(
        'POST',
        '/manage/proc/editor_image_upload',
        true
    );


    xhr.onreadystatechange =
        function(){

            if(
                xhr.readyState != 4
            ){

                return;

            }



            /*
             * HTTP ERROR
             */

            if(
                xhr.status != 200
            ){

                editorImageButton.removeAttribute(
                    'data-loading'
                );


                editorImageButton.disabled =
                    false;


                editorImageButton.innerHTML =
                    '<i class="ri-image-add-line"></i> 이미지 업로드';


                var errorMessage =
                    '이미지 업로드 중 오류가 발생했습니다.';


                try{

                    var errorResult =
                        JSON.parse(
                            xhr.responseText
                        );


                    if(
                        errorResult.message
                    ){

                        errorMessage =
                            errorResult.message;

                    }

                }catch(e){

                }


                alert(
                    errorMessage
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

                editorImageButton.removeAttribute(
                    'data-loading'
                );


                editorImageButton.disabled =
                    false;


                editorImageButton.innerHTML =
                    '<i class="ri-image-add-line"></i> 이미지 업로드';


                alert(
                    '이미지 업로드 응답을 확인할 수 없습니다.'
                );


                return;

            }



            /*
             * ERROR
             */

            if(
                !result.location
            ){

                editorImageButton.removeAttribute(
                    'data-loading'
                );


                editorImageButton.disabled =
                    false;


                editorImageButton.innerHTML =
                    '<i class="ri-image-add-line"></i> 이미지 업로드';


                alert(
                    result.message
                        ? result.message
                        : '이미지 업로드에 실패했습니다.'
                );


                return;

            }



            /*
             * TinyMCE
             */

            var editor =
                tinymce.get(
                    'editor'
                );


            if(!editor){

                editorImageButton.removeAttribute(
                    'data-loading'
                );


                editorImageButton.disabled =
                    false;


                editorImageButton.innerHTML =
                    '<i class="ri-image-add-line"></i> 이미지 업로드';


                alert(
                    '에디터를 찾을 수 없습니다.'
                );


                return;

            }



            /*
             * 현재 커서 위치에 이미지 삽입
             */

            editor.insertContent(
                '<p>' +
                    '<img ' +
                        'src="' +
                        escapeHtmlAttribute(
                            result.location
                        ) +
                        '" ' +
                        'alt=""' +
                    '>' +
                '</p>'
            );



            /*
             * 다음 이미지
             */

            uploadEditorImages(
                files,
                index + 1
            );

        };


    xhr.send(
        formData
    );

}



/*
 * =========================================================
 * Attribute Escape
 * =========================================================
 */

function escapeHtmlAttribute(
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
        /"/g,
        '&quot;'
    )
    .replace(
        /</g,
        '&lt;'
    )
    .replace(
        />/g,
        '&gt;'
    );

}


</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>