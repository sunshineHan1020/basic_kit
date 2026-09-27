<?php
/* WEEKENDS CMS | PHP 5.4+ | URLs intentionally omit .php. */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Cache-Control: no-store');
function client_h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function client_get($key, $default) {
    return isset($_GET[$key]) && is_string($_GET[$key]) ? trim($_GET[$key]) : $default;
}
function client_image_url($key) {
    $key = '/'.ltrim((string)$key, '/');
    return strpos($key, '/uploads/') === 0 && strpos($key, '..') === false ? $key : '';
}
if (empty($_SESSION['client_csrf'])) {
    $strong = false;
    $bytes = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(32, $strong) : false;
    if ($bytes === false || !$strong) { http_response_code(500); exit('보안 토큰을 생성할 수 없습니다.'); }
    $_SESSION['client_csrf'] = bin2hex($bytes);
}
$client_csrf = $_SESSION['client_csrf'];

$id = client_get('id', '');
if ($id !== '' && !preg_match('/^[1-9][0-9]{0,19}$/D', $id)) { http_response_code(400); exit('잘못된 클라이언트 번호입니다.'); }
$client = array('id' => '', 'client_name' => '', 'sort_order' => 0, 'is_active' => 0, 'storage_key' => '');
if ($id !== '') {
    $query = 'SELECT c.*, m.storage_key FROM clients c LEFT JOIN media m ON m.id = c.media_id AND m.deleted_at IS NULL WHERE c.id = ?';
    $ps = pdo_query($db, $query, array($id));
    $client = $ps->fetch(PDO::FETCH_ASSOC);
    if (!$client) { http_response_code(404); exit('클라이언트가 존재하지 않습니다.'); }
}
$logo = client_image_url($client['storage_key']);
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<link rel="stylesheet" href="/manage/css/client.css">
<main class="content client-page"><div class="content-inner">
    <div class="client-page-head"><div><span class="client-page-eyebrow">ABOUT</span><h2>클라이언트 <?=$id !== '' ? '수정' : '등록'?></h2><p>로고와 노출 정보를 설정합니다.</p></div><a class="btn btn-white" href="/manage/client_list">목록</a></div>
    <form id="client-form" class="card client-form" method="post" action="/manage/proc/client_save" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="<?=client_h($client['id'])?>">
        <input type="hidden" name="csrf_token" value="<?=client_h($client_csrf)?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
        <div class="client-field">
            <label for="client-logo-file">로고 이미지 <span class="client-required">필수</span></label>
            <div>
                <div class="client-preview">
                    <img id="client-preview-image" <?php if ($logo !== '') { ?>src="<?=client_h($logo)?>"<?php } else { ?>hidden<?php } ?> alt="로고 미리보기">
                    <span id="client-preview-empty" <?=$logo !== '' ? 'hidden' : ''?>>로고를 선택해주세요.</span>
                </div>
                <input type="file" id="client-logo-file" name="image" accept="image/png,image/jpeg,image/gif">
                <button type="button" id="client-logo-reset" class="btn btn-white">선택 취소</button>
                <p class="client-help">PNG, JPG, GIF · 최대 5MB / 4,096 × 4,096px. 투명 PNG를 권장합니다.</p>
                <p class="client-help">수정 시 새 파일을 선택하지 않으면 기존 로고를 유지합니다.</p>
            </div>
        </div>
        <div class="client-field"><label for="client-name">클라이언트명 <span class="client-required">필수</span></label><input type="text" id="client-name" name="client_name" maxlength="255" required value="<?=client_h($client['client_name'])?>"></div>
        <div class="client-field"><label for="client-order">정렬순서</label><div><input type="number" id="client-order" name="sort_order" min="-2147483648" max="2147483647" step="1" required value="<?=client_h($client['sort_order'])?>"><p class="client-help">작은 숫자부터 노출됩니다. 같은 숫자이면 먼저 등록한 순서로 표시됩니다.</p></div></div>
        <div class="client-field"><label for="client-active">노출여부</label><div><select id="client-active" name="is_active"><option value="1" <?=intval($client['is_active']) === 1 ? 'selected' : ''?>>노출</option><option value="0" <?=intval($client['is_active']) !== 1 ? 'selected' : ''?>>비노출</option></select><p class="client-help">노출 클라이언트는 최대 20개입니다. 비노출 등록은 개수 제한이 없습니다.</p></div></div>
        <div class="client-form-actions"><a class="btn btn-white" href="/manage/client_list">취소</a><button type="submit" id="client-save" class="btn btn-primary">저장</button></div>
    </form>
</div></main>

<div id="client-dialog" class="client-dialog" hidden>
    <section class="client-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="client-dialog-title" aria-describedby="client-dialog-message" tabindex="-1">
        <h3 id="client-dialog-title">삭제 확인</h3>
        <p id="client-dialog-message"></p>
        <div class="client-actions">
            <button type="button" class="btn btn-white" id="client-dialog-cancel">취소</button>
            <button type="button" class="btn btn-primary" id="client-dialog-ok">확인</button>
        </div>
    </section>
</div>
<script>
(function () {
    'use strict';
    var layer = document.getElementById('client-dialog');
    var ok = document.getElementById('client-dialog-ok');
    var cancel = document.getElementById('client-dialog-cancel');
    var callback = null, previousFocus = null, isConfirm = false;
    function close(accepted) {
        layer.hidden = true;
        if (previousFocus && document.documentElement.contains(previousFocus)) { previousFocus.focus(); }
        var fn = callback; callback = null;
        if (accepted && fn) { fn(); }
    }
    window.clientDialog = function (message, confirmMode, fn) {
        // The existing admin common.js overrides window.alert.
        if (!confirmMode) { window.alert(message); return; }
        previousFocus = document.activeElement;
        callback = fn || null; isConfirm = !!confirmMode;
        document.getElementById('client-dialog-title').textContent = isConfirm ? '삭제 확인' : '알림';
        document.getElementById('client-dialog-message').textContent = message;
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
    window.clientRequest = function (url, data, done) {
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
    var form = document.getElementById('client-form');
    var fileInput = document.getElementById('client-logo-file');
    var preview = document.getElementById('client-preview-image');
    var empty = document.getElementById('client-preview-empty');
    var original = preview.getAttribute('src') || '';
    var save = document.getElementById('client-save');
    var busy = false, readerVersion = 0;
    function resetPreview() {
        readerVersion++;
        if (original) { preview.src = original; } else { preview.removeAttribute('src'); }
        preview.hidden = !original; empty.hidden = !!original;
    }
    document.getElementById('client-logo-reset').onclick = function () { fileInput.value = ''; resetPreview(); };
    fileInput.onchange = function () {
        resetPreview();
        if (!fileInput.files.length) { return; }
        var file = fileInput.files[0], version = readerVersion;
        if (file.size > 5242880 || !/\.(png|jpe?g|gif)$/i.test(file.name)) {
            fileInput.value = ''; clientDialog('5MB 이하의 PNG, JPG, GIF 이미지를 선택해주세요.', false); return;
        }
        var reader = new FileReader();
        reader.onload = function (event) {
            if (version !== readerVersion) { return; }
            preview.src = event.target.result; preview.hidden = false; empty.hidden = true;
        };
        reader.onerror = function () { if (version === readerVersion) { fileInput.value = ''; resetPreview(); clientDialog('이미지를 읽을 수 없습니다.', false); } };
        reader.readAsDataURL(file);
    };
    form.onsubmit = function (event) {
        event.preventDefault();
        if (busy) { return; }
        var name = document.getElementById('client-name');
        var order = document.getElementById('client-order');
        if (!name.value.replace(/^\s+|\s+$/g, '')) { clientDialog('클라이언트명을 입력해주세요.', false, function () { name.focus(); }); return; }
        if (!/^-?\d+$/.test(order.value) || Number(order.value) < -2147483648 || Number(order.value) > 2147483647) { clientDialog('정렬순서를 올바른 정수로 입력해주세요.', false, function () { order.focus(); }); return; }
        if (!original && !fileInput.files.length) { clientDialog('로고 이미지를 선택해주세요.', false); return; }
        var data = new FormData(form);
        busy = true; save.disabled = true; save.textContent = '저장 중…';
        clientRequest('/manage/proc/client_save', data, function (result) {
            if (result.success) { window.location.href = '/manage/client_list?saved=1'; return; }
            busy = false; save.disabled = false; save.textContent = '저장';
            clientDialog(result.message, false);
        });
    };
}());
</script>
<?php
// Some WEEKENDS installations close the layout in footer.php; older ones do not have it.
$client_footer = $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
if (is_file($client_footer)) { include $client_footer; } else { echo '</body></html>'; }
?>
