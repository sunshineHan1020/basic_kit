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

$keyword = client_get('keyword', '');
if (strlen($keyword) > 1000) { $keyword = ''; }
$is_active = client_get('is_active', '');
if ($is_active !== '0' && $is_active !== '1') { $is_active = ''; }
$page = max(1, intval(client_get('page', '1')));
$limit = 20;
$where = array(); $params = array();
if ($keyword !== '') { $where[] = 'c.client_name LIKE ?'; $params[] = '%'.$keyword.'%'; }
if ($is_active !== '') { $where[] = 'c.is_active = ?'; $params[] = intval($is_active); }
$where_sql = count($where) ? ' WHERE '.implode(' AND ', $where) : '';
$query = 'SELECT COUNT(*) AS total, COALESCE(SUM(is_active = 1), 0) AS active FROM clients';
$ps = pdo_query($db, $query, array());
$summary = $ps->fetch(PDO::FETCH_ASSOC);
$query = 'SELECT COUNT(*) FROM clients c'.$where_sql;
$ps = pdo_query($db, $query, $params);
$total_count = intval($ps->fetchColumn());
$total_page = max(1, intval(ceil($total_count / $limit)));
$page = min($page, $total_page); $offset = ($page - 1) * $limit;
// LEFT JOIN keeps rows manageable even when a shared media record was soft-deleted elsewhere.
$query = 'SELECT c.*, m.storage_key, m.original_name FROM clients c
    LEFT JOIN media m ON m.id = c.media_id AND m.deleted_at IS NULL'.$where_sql.'
    ORDER BY c.sort_order ASC, c.id ASC LIMIT '.intval($offset).', '.intval($limit);
$ps = pdo_query($db, $query, $params);
$clients = $ps->fetchAll(PDO::FETCH_ASSOC);
$base_query = '&'.http_build_query(array('keyword' => $keyword, 'is_active' => $is_active), '', '&');
$start_page = intval(floor(($page - 1) / 10)) * 10 + 1;
$end_page = min($start_page + 9, $total_page);
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<link rel="stylesheet" href="/manage/css/client.css">
<main class="content client-page"><div class="content-inner">
    <div class="client-page-head">
        <div><span class="client-page-eyebrow">ABOUT</span><h2>클라이언트 관리</h2><p>ABOUT 페이지의 클라이언트 로고를 관리합니다.</p></div>
        <a href="/manage/client_write" class="btn btn-primary">클라이언트 등록</a>
    </div>
    <div class="client-summary">
        <div class="client-summary-item"><span>전체 등록</span><strong><?=number_format($summary['total'])?></strong></div>
        <div class="client-summary-item"><span>노출</span><strong><?=number_format($summary['active'])?> <small>/ 20</small></strong></div>
        <div class="client-summary-item"><span>비노출</span><strong><?=number_format($summary['total'] - $summary['active'])?></strong></div>
        <p>최대 20개까지 노출할 수 있습니다.<br>정렬순서가 낮은 로고부터 표시됩니다.</p>
    </div>
    <div class="card client-filter-card">
        <form method="get" action="/manage/client_list" class="client-filter">
            <label for="client-status-filter" class="client-sr-only">노출 상태</label>
            <select name="is_active" id="client-status-filter">
                <option value="">전체 상태</option>
                <option value="1" <?=$is_active === '1' ? 'selected' : ''?>>노출</option>
                <option value="0" <?=$is_active === '0' ? 'selected' : ''?>>비노출</option>
            </select>
            <label for="client-keyword" class="client-sr-only">클라이언트명</label>
            <input id="client-keyword" name="keyword" maxlength="255" value="<?=client_h($keyword)?>" placeholder="클라이언트명 검색">
            <button class="btn btn-primary" type="submit">검색</button>
            <a class="btn btn-white" href="/manage/client_list">초기화</a>
        </form>
    </div>
    <div class="card client-list-card">
        <div class="client-list-head">검색 결과 <strong><?=number_format($total_count)?></strong>개</div>
        <div class="client-table-wrap"><table class="client-table">
            <thead><tr><th scope="col">로고</th><th scope="col">클라이언트명</th><th scope="col">정렬</th><th scope="col">상태</th><th scope="col">수정일</th><th scope="col">관리</th></tr></thead>
            <tbody>
            <?php foreach ($clients as $client) { $logo = client_image_url($client['storage_key']); ?>
                <tr>
                    <td><a class="client-logo" href="/manage/client_write?id=<?=client_h($client['id'])?>">
                        <?php if ($logo !== '') { ?><img src="<?=client_h($logo)?>" alt="<?=client_h($client['client_name'])?>" loading="lazy"><?php } else { ?><span>이미지 없음</span><?php } ?>
                    </a></td>
                    <td class="client-name"><a href="/manage/client_write?id=<?=client_h($client['id'])?>"><strong><?=client_h($client['client_name'])?></strong><span><?=client_h($client['original_name'])?></span></a></td>
                    <td><?=client_h($client['sort_order'])?></td>
                    <td><span class="client-status <?=intval($client['is_active']) === 1 ? 'active' : 'inactive'?>"><?=intval($client['is_active']) === 1 ? '노출' : '비노출'?></span></td>
                    <td class="client-date"><?=client_h(substr($client['updated_at'], 0, 16))?></td>
                    <td><div class="client-actions">
                        <a class="btn btn-white" href="/manage/client_write?id=<?=client_h($client['id'])?>">수정</a>
                        <button type="button" class="btn client-delete-btn" data-id="<?=client_h($client['id'])?>" data-name="<?=client_h($client['client_name'])?>">삭제</button>
                    </div></td>
                </tr>
            <?php } ?>
            <?php if (!$clients) { ?><tr><td colspan="6" class="client-empty">조건에 맞는 클라이언트가 없습니다.</td></tr><?php } ?>
            </tbody>
        </table></div>
        <?php if ($total_page > 1) { ?>
        <nav class="client-pagination" aria-label="목록 페이지">
            <?php if ($page > 1) { ?><a href="?page=1<?=client_h($base_query)?>">처음</a><a href="?page=<?=$page - 1?><?=client_h($base_query)?>">이전</a><?php } ?>
            <?php for ($p = $start_page; $p <= $end_page; $p++) { ?>
                <a href="?page=<?=$p?><?=client_h($base_query)?>" <?=$p === $page ? 'class="active" aria-current="page"' : ''?>><?=$p?></a>
            <?php } ?>
            <?php if ($page < $total_page) { ?><a href="?page=<?=$page + 1?><?=client_h($base_query)?>">다음</a><a href="?page=<?=$total_page?><?=client_h($base_query)?>">마지막</a><?php } ?>
        </nav>
        <?php } ?>
    </div>
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
    var savedMessage = <?=json_encode(client_get('saved', '') === '1' ? '클라이언트가 저장되었습니다.' : '')?>;
    if (savedMessage) {
        window.alert(savedMessage);
        if (window.history && window.history.replaceState) { window.history.replaceState(null, '', '/manage/client_list'); }
    }
    var busy = false;
    var buttons = document.querySelectorAll('.client-delete-btn');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].onclick = function () {
            if (busy) { return; }
            var button = this;
            clientDialog('“' + button.getAttribute('data-name') + '” 클라이언트를 삭제하시겠습니까? 기존 로고 파일은 보존됩니다.', true, function () {
                busy = true; button.disabled = true;
                var data = new FormData();
                data.append('id', button.getAttribute('data-id'));
        data.append('csrf_token', <?=json_encode($client_csrf)?>);
                clientRequest('/manage/proc/client_delete', data, function (result) {
                    if (result.success) { window.location.reload(); return; }
                    busy = false; button.disabled = false;
                    clientDialog(result.message, false);
                });
            });
        };
    }
}());
</script>
<?php
// Some WEEKENDS installations close the layout in footer.php; older ones do not have it.
$client_footer = $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
if (is_file($client_footer)) { include $client_footer; } else { echo '</body></html>'; }
?>
