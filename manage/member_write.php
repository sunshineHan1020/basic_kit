<?php
/* WEEKENDS CMS | PHP 5.4+ | URLs intentionally omit .php. */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Cache-Control: no-store');
function member_h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function member_get($key, $default) {
    return isset($_GET[$key]) && is_string($_GET[$key]) ? trim($_GET[$key]) : $default;
}
function member_image_url($key) {
    $key = '/'.ltrim((string)$key, '/');
    return strpos($key, '/uploads/') === 0 && strpos($key, '..') === false ? $key : '';
}
if (empty($_SESSION['member_csrf'])) {
    $strong = false;
    $bytes = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(32, $strong) : false;
    if ($bytes === false || !$strong) { http_response_code(500); exit('보안 토큰을 생성할 수 없습니다.'); }
    $_SESSION['member_csrf'] = bin2hex($bytes);
}
$member_csrf = $_SESSION['member_csrf'];

$id = member_get('id', '');
if ($id !== '' && !preg_match('/^[1-9][0-9]{0,19}$/D', $id)) { http_response_code(400); exit('잘못된 멤버 번호입니다.'); }
$member = array('id' => '', 'member_name' => '', 'sort_order' => 0, 'is_active' => 0, 'storage_key' => '', 'mobile_storage_key' => '');
if ($id !== '') {
    $query = 'SELECT c.*, m.storage_key, mm.storage_key AS mobile_storage_key FROM team_members c LEFT JOIN media m ON m.id = c.media_id AND m.deleted_at IS NULL LEFT JOIN media mm ON mm.id = c.mobile_media_id AND mm.deleted_at IS NULL WHERE c.id = ?';
    $ps = pdo_query($db, $query, array($id));
    $member = $ps->fetch(PDO::FETCH_ASSOC);
    if (!$member) { http_response_code(404); exit('멤버가 존재하지 않습니다.'); }
}
$logo = member_image_url($member['storage_key']);
$mobile_logo = member_image_url($member['mobile_storage_key']);
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<link rel="stylesheet" href="/manage/css/member.css">
<main class="content member-page"><div class="content-inner">
    <div class="member-page-head"><div><span class="member-page-eyebrow">ABOUT</span><h2>멤버 <?=$id !== '' ? '수정' : '등록'?></h2><p>팀원 이미지와 노출 정보를 설정합니다.</p></div><a class="btn btn-white" href="/manage/member_list">목록</a></div>
    <form id="member-form" class="card member-form" method="post" action="/manage/proc/member_save" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="<?=member_h($member['id'])?>">
        <input type="hidden" name="csrf_token" value="<?=member_h($member_csrf)?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
        <div class="member-field">
            <label for="member-logo-file">팀원 이미지 <span class="member-required">필수</span></label>
            <div>
                <div class="member-preview">
                    <img id="member-preview-image" <?php if ($logo !== '') { ?>src="<?=member_h($logo)?>"<?php } else { ?>hidden<?php } ?> alt="팀원 이미지 미리보기">
                    <span id="member-preview-empty" <?=$logo !== '' ? 'hidden' : ''?>>팀원 이미지를 선택해주세요.</span>
                </div>
                <input type="file" id="member-logo-file" name="image" accept="image/png,image/jpeg,image/gif">
                <button type="button" id="member-logo-reset" class="btn btn-white">선택 취소</button>
                <p class="member-help">PNG, JPG, GIF · 최대 5MB / 4,096 × 4,096px. 가로 JPG 또는 정사각 PNG 등 원본 비율 그대로 저장합니다.</p>
                <p class="member-help">수정 시 새 파일을 선택하지 않으면 기존 팀원 이미지를 유지합니다.</p>
            </div>
        </div>
        <div class="member-field">
            <label for="member-mobile-file">모바일 이미지 <span class="member-help">선택</span></label>
            <div>
                <div class="member-preview"><img id="member-mobile-preview" <?php if ($mobile_logo !== '') { ?>src="<?=member_h($mobile_logo)?>"<?php } else { ?>hidden<?php } ?> alt="추가 이미지 미리보기"><span id="member-mobile-empty" <?=$mobile_logo !== '' ? 'hidden' : ''?>>추가 이미지 없음</span></div>
                <input type="file" id="member-mobile-file" name="mobile_image" accept="image/png,image/jpeg,image/gif">
                <button type="button" class="btn btn-white" id="member-mobile-reset">선택 취소</button>
                <label class="member-remove-label"><input type="checkbox" name="remove_mobile_image" value="1" id="member-mobile-remove"> 기존 추가 이미지 연결 해제</label>
                <p class="member-help">정사각 PNG 등 두 번째 이미지를 등록할 수 있습니다. 최대 5MB. 선택하지 않으면 기존 이미지를 유지합니다. </p>
            </div>
        </div>
        <div class="member-field"><label for="member-name">이름 <span class="member-required">필수</span></label><input type="text" id="member-name" name="member_name" maxlength="255" required value="<?=member_h($member['member_name'])?>"></div>
        <div class="member-field"><label for="member-order">정렬순서</label><div><input type="number" id="member-order" name="sort_order" min="-2147483648" max="2147483647" step="1" required value="<?=member_h($member['sort_order'])?>"><p class="member-help">작은 숫자부터 노출됩니다. 같은 숫자이면 먼저 등록한 순서로 표시됩니다.</p></div></div>
        <div class="member-field"><label for="member-active">노출여부</label><div><select id="member-active" name="is_active"><option value="1" <?=intval($member['is_active']) === 1 ? 'selected' : ''?>>노출</option><option value="0" <?=intval($member['is_active']) !== 1 ? 'selected' : ''?>>비노출</option></select><p class="member-help">노출로 설정한 멤버 이미지를 정렬순서에 따라 표시합니다.</p></div></div>
        <div class="member-form-actions"><a class="btn btn-white" href="/manage/member_list">취소</a><button type="submit" id="member-save" class="btn btn-primary">저장</button></div>
    </form>
</div></main>

<div id="member-dialog" class="member-dialog" hidden>
    <section class="member-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="member-dialog-title" aria-describedby="member-dialog-message" tabindex="-1">
        <h3 id="member-dialog-title">삭제 확인</h3>
        <p id="member-dialog-message"></p>
        <div class="member-actions">
            <button type="button" class="btn btn-white" id="member-dialog-cancel">취소</button>
            <button type="button" class="btn btn-primary" id="member-dialog-ok">확인</button>
        </div>
    </section>
</div>
<script>
(function () {
    'use strict';
    var layer = document.getElementById('member-dialog');
    var ok = document.getElementById('member-dialog-ok');
    var cancel = document.getElementById('member-dialog-cancel');
    var callback = null, previousFocus = null, isConfirm = false;
    function close(accepted) {
        layer.hidden = true;
        if (previousFocus && document.documentElement.contains(previousFocus)) { previousFocus.focus(); }
        var fn = callback; callback = null;
        if (accepted && fn) { fn(); }
    }
    window.memberDialog = function (message, confirmMode, fn) {
        // The existing admin common.js overrides window.alert.
        if (!confirmMode) { window.alert(message); return; }
        previousFocus = document.activeElement;
        callback = fn || null; isConfirm = !!confirmMode;
        document.getElementById('member-dialog-title').textContent = isConfirm ? '삭제 확인' : '알림';
        document.getElementById('member-dialog-message').textContent = message;
        cancel.hidden = !isConfirm;
        layer.hidden = false;
        (isConfirm ? cancel : ok).focus();
    };
    ok.onclick = function () { close(true); };
    cancel.onclick = function () { close(false); };
    document.addEventListener('keydown', function (event) {
        if (layer.hidden) { return; }
        if (event.key === 'Escape' || event.keyCode === 27) { event.preventDefault(); close(!isConfirm); }
        if (event.key === 'Tab' || event.keyCode === 9) {
            event.preventDefault();
            (isConfirm && document.activeElement === ok ? cancel : ok).focus();
        }
    });
    document.addEventListener('focusin', function (event) {
        if (!layer.hidden && !layer.contains(event.target)) { (isConfirm ? cancel : ok).focus(); }
    });
    window.memberRequest = function (url, data, done) {
        var xhr = new XMLHttpRequest();
        var finished = false;
        function finish(result) { if (!finished) { finished = true; done(result); } }
        xhr.open('POST', url, true);
        xhr.timeout = 60000;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            var result;
            try { result = JSON.parse(xhr.responseText); } catch (error) {
                finish({success:false, message:'응답을 확인할 수 없습니다. 로그인 상태와 목록을 확인한 뒤 다시 시도해주세요.'}); return;
            }
            if (!result || typeof result.code !== 'boolean') {
                finish({success:false, message:'서버 응답 형식이 올바르지 않습니다.'}); return;
            }
            finish({success:result.code, message:result.msg, id:result.id});
        };
        xhr.onerror = xhr.ontimeout = function () {
            finish({success:false, message:'통신이 중단되었습니다. 저장 여부를 목록에서 확인한 뒤 다시 시도해주세요.'});
        };
        xhr.send(data);
    };
}());
</script>

<script>
(function () {
    var form = document.getElementById('member-form');
    var fileInput = document.getElementById('member-logo-file');
    var preview = document.getElementById('member-preview-image');
    var empty = document.getElementById('member-preview-empty');
    var original = preview.getAttribute('src') || '';
    var save = document.getElementById('member-save');
    var busy = false, readerVersion = 0;
    function resetPreview() {
        readerVersion++;
        if (original) { preview.src = original; } else { preview.removeAttribute('src'); }
        preview.hidden = !original; empty.hidden = !!original;
    }
    document.getElementById('member-logo-reset').onclick = function () { fileInput.value = ''; resetPreview(); };
    fileInput.onchange = function () {
        resetPreview();
        if (!fileInput.files.length) { return; }
        var file = fileInput.files[0], version = readerVersion;
        if (file.size > 5242880 || !/\.(png|jpe?g|gif)$/i.test(file.name)) {
            fileInput.value = ''; memberDialog('5MB 이하의 PNG, JPG, GIF 이미지를 선택해주세요.', false); return;
        }
        var reader = new FileReader();
        reader.onload = function (event) {
            if (version !== readerVersion) { return; }
            preview.src = event.target.result; preview.hidden = false; empty.hidden = true;
        };
        reader.onerror = function () { if (version === readerVersion) { fileInput.value = ''; resetPreview(); memberDialog('이미지를 읽을 수 없습니다.', false); } };
        reader.readAsDataURL(file);
    };
    form.onsubmit = function (event) {
        event.preventDefault();
        if (busy) { return; }
        var name = document.getElementById('member-name');
        var order = document.getElementById('member-order');
        if (!name.value.replace(/^\s+|\s+$/g, '')) { memberDialog('이름을 입력해주세요.', false, function () { name.focus(); }); return; }
        if (!/^-?\d+$/.test(order.value) || Number(order.value) < -2147483648 || Number(order.value) > 2147483647) { memberDialog('정렬순서를 올바른 정수로 입력해주세요.', false, function () { order.focus(); }); return; }
        if (!original && !fileInput.files.length) { memberDialog('팀원 이미지를 선택해주세요.', false); return; }
        var data = new FormData(form);
        busy = true; save.disabled = true; save.textContent = '저장 중…';
        memberRequest('/manage/proc/member_save', data, function (result) {
            if (result.success) { window.location.href = '/manage/member_list?saved=1'; return; }
            busy = false; save.disabled = false; save.textContent = '저장';
            memberDialog(result.message, false);
        });
    };
}());
</script>

<script>
(function () {
    var input = document.getElementById('member-mobile-file');
    var image = document.getElementById('member-mobile-preview');
    var empty = document.getElementById('member-mobile-empty');
    var remove = document.getElementById('member-mobile-remove');
    var original = image.getAttribute('src') || '', version = 0;
    function restore() {
        version++; if (original && !remove.checked) { image.src = original; } else { image.removeAttribute('src'); }
        image.hidden = !original || remove.checked; empty.hidden = !image.hidden;
    }
    document.getElementById('member-mobile-reset').onclick = function () { input.value = ''; restore(); };
    remove.onchange = function () { if (remove.checked) { input.value = ''; } restore(); };
    input.onchange = function () {
        restore(); if (!input.files.length) { return; }
        var file = input.files[0], current = version;
        if (file.size > 5242880 || !/\.(png|jpe?g|gif)$/i.test(file.name)) { input.value = ''; window.alert('5MB 이하 PNG, JPG, GIF를 선택해주세요.'); return; }
        remove.checked = false;
        var reader = new FileReader(); reader.onload = function (e) { if (current === version) { image.src = e.target.result; image.hidden = false; empty.hidden = true; } };
        reader.onerror = function () { if (current === version) { input.value = ''; restore(); window.alert('이미지를 읽을 수 없습니다.'); } };
        reader.readAsDataURL(file);
    };
}());
</script>
<?php
// Some WEEKENDS installations close the layout in footer.php; older ones do not have it.
$member_footer = $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
if (is_file($member_footer)) { include $member_footer; } else { echo '</body></html>'; }
?>
