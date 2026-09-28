<?php
/* Included by authenticated admin pages only. */
if (empty($_SESSION['ADMIN_ID']) || !isset($contentEditorGroup) || !in_array($contentEditorGroup, array('main', 'about', 'team'), true)) { return; }
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/site_content.php';
if (empty($_SESSION['site_content_csrf'])) {
    $strong = false;
    $bytes = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(32, $strong) : false;
    if ($bytes === false || !$strong) { echo '<p>보안 토큰 생성에 실패했습니다.</p>'; return; }
    $_SESSION['site_content_csrf'] = bin2hex($bytes);
}
$wcToken = $_SESSION['site_content_csrf'];
$wcTextDefinitions = wc_text_defaults(); $wcImageDefinitions = wc_image_defaults();
$wcEditorTexts = array(); $wcEditorImages = array(); $wcEditorReady = true;
try {
    if ($contentEditorGroup !== 'team') { $wcEditorTexts = wc_load_texts($db, $contentEditorGroup, true); }
    if ($contentEditorGroup !== 'about') { $wcEditorImages = wc_load_images($db, $contentEditorGroup, true); }
} catch (Exception $e) { $wcEditorReady = false; }
?>
<link rel="stylesheet" href="/manage/css/site_content.css">
<section class="wc-editor">
    <?php if (!$wcEditorReady) { ?>
    <p class="wc-notice">콘텐츠 관리 초기 설정이 필요합니다. 전달된 CREATE SQL과 INSERT SQL을 순서대로 실행한 후 새로고침해주세요. 기존 프론트 화면은 그대로 유지됩니다.</p>
    <?php } else { ?>
    <?php if ($wcEditorTexts) { ?>
    <details class="wc-panel" <?=$contentEditorGroup === 'about' ? 'open' : ''?>>
        <summary><?=$contentEditorGroup === 'main' ? '메인' : '어바웃'?> 문구 관리</summary>
        <p>화면 순서대로 표시됩니다. 기존 애니메이션과 줄바꿈 구조를 유지하기 위해 텍스트 구간별로 입력합니다. HTML은 적용되지 않습니다.</p>
        <form class="wc-form" method="post" action="/manage/proc/site_content_save">
            <input type="hidden" name="csrf_token" value="<?=wc_h($wcToken)?>">
            <input type="hidden" name="mode" value="text">
            <input type="hidden" name="page" value="<?=wc_h($contentEditorGroup)?>">
            <?php $wcLastSection = ''; foreach ($wcTextDefinitions[$contentEditorGroup] as $wcKey => $wcDefinition) { ?>
                <?php if ($wcLastSection !== $wcDefinition['section']) { $wcLastSection = $wcDefinition['section']; ?><h3><?=wc_h($wcLastSection)?></h3><?php } ?>
                <div class="wc-field">
                    <label for="wc-<?=wc_h($contentEditorGroup.'-'.$wcKey)?>"><?=wc_h($wcDefinition['label'])?></label>
                    <textarea id="wc-<?=wc_h($contentEditorGroup.'-'.$wcKey)?>" name="texts[<?=wc_h($wcKey)?>]" rows="2" maxlength="<?=intval($wcDefinition['max'])?>"><?=wc_h($wcEditorTexts[$wcKey]['text_value'])?></textarea>
                    <input type="hidden" name="revisions[<?=wc_h($wcKey)?>]" value="<?=wc_h($wcEditorTexts[$wcKey]['revision'])?>">
                </div>
            <?php } ?>
            <button type="submit" class="btn btn-primary">문구 저장</button>
            <p class="wc-status" role="status" aria-live="polite"></p>
        </form>
    </details>
    <?php } ?>
    <?php if ($wcEditorImages) { ?>
    <details class="wc-panel" open>
        <summary><?=$contentEditorGroup === 'team' ? '단체사진 등록 / 수정' : '메인 사진 9개 등록 / 수정'?></summary>
        <p><?=$contentEditorGroup === 'team' ? '어바웃 첫 단체사진에 적용됩니다. PC와 모바일 사진을 각각 교체할 수 있습니다.' : '현재 3×3 사진의 위치와 모션을 유지합니다. 5번은 중앙 단체사진입니다.'?> 새 파일을 선택하지 않으면 현재 사진이 유지됩니다. 사진마다 개별 저장합니다. JPG·PNG·GIF, 최대 5MB / 4,096px.</p>
        <div class="wc-images">
        <?php foreach ($wcEditorImages as $wcKey => $wcImageRow) { ?>
            <form class="wc-form wc-image-form" method="post" enctype="multipart/form-data" action="/manage/proc/site_content_save">
                <h3><?=wc_h($wcImageDefinitions[$wcKey]['label'])?></h3>
                <img class="wc-preview" src="<?=wc_h($wcImageRow['storage_key'])?>" alt="<?=wc_h($wcImageRow['alt_text'])?>">
                <input type="hidden" name="csrf_token" value="<?=wc_h($wcToken)?>">
                <input type="hidden" name="mode" value="image">
                <input type="hidden" name="slot" value="<?=wc_h($wcKey)?>">
                <input type="hidden" name="revision" value="<?=wc_h($wcImageRow['revision'])?>">
                <label for="wc-file-<?=wc_h($wcKey)?>">교체 이미지</label>
                <input id="wc-file-<?=wc_h($wcKey)?>" type="file" name="image" accept="image/jpeg,image/png,image/gif">
                <label for="wc-alt-<?=wc_h($wcKey)?>">이미지 설명</label>
                <input id="wc-alt-<?=wc_h($wcKey)?>" type="text" name="alt_text" maxlength="500" value="<?=wc_h($wcImageRow['alt_text'])?>">
                <button type="submit" class="btn btn-primary">사진 저장</button>
                <p class="wc-status" role="status" aria-live="polite"></p>
            </form>
        <?php } ?>
        </div>
    </details>
    <?php } ?>
    <?php } ?>
</section>
<script src="/manage/js/site_content.js"></script>
