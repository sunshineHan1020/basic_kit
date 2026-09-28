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

$keyword = member_get('keyword', '');
if (strlen($keyword) > 1000) { $keyword = ''; }
$is_active = member_get('is_active', '');
if ($is_active !== '0' && $is_active !== '1') { $is_active = ''; }
$page = max(1, intval(member_get('page', '1')));
$limit = 20;
$where = array(); $params = array();
if ($keyword !== '') { $where[] = 'c.member_name LIKE ?'; $params[] = '%'.$keyword.'%'; }
if ($is_active !== '') { $where[] = 'c.is_active = ?'; $params[] = intval($is_active); }
$where_sql = count($where) ? ' WHERE '.implode(' AND ', $where) : '';
$query = 'SELECT COUNT(*) AS total, COALESCE(SUM(is_active = 1), 0) AS active FROM team_members';
$ps = pdo_query($db, $query, array());
$summary = $ps->fetch(PDO::FETCH_ASSOC);
$query = 'SELECT COUNT(*) FROM team_members c'.$where_sql;
$ps = pdo_query($db, $query, $params);
$total_count = intval($ps->fetchColumn());
$total_page = max(1, intval(ceil($total_count / $limit)));
$page = min($page, $total_page); $offset = ($page - 1) * $limit;
// LEFT JOIN keeps rows manageable even when a shared media record was soft-deleted elsewhere.
$query = 'SELECT c.*, m.storage_key, m.original_name, mm.storage_key AS mobile_storage_key FROM team_members c
    LEFT JOIN media m ON m.id = c.media_id AND m.deleted_at IS NULL
    LEFT JOIN media mm ON mm.id = c.mobile_media_id AND mm.deleted_at IS NULL'.$where_sql.'
    ORDER BY c.sort_order ASC, c.id ASC LIMIT '.intval($offset).', '.intval($limit);
$ps = pdo_query($db, $query, $params);
$team_members = $ps->fetchAll(PDO::FETCH_ASSOC);
$base_query = '&'.http_build_query(array('keyword' => $keyword, 'is_active' => $is_active), '', '&');
$start_page = intval(floor(($page - 1) / 10)) * 10 + 1;
$end_page = min($start_page + 9, $total_page);
$query = 'SELECT hq_text, art_text, plan_text, hldys_text, revision FROM team_roster WHERE id = 1';
$ps = pdo_query($db, $query, array());
$roster = $ps->fetch(PDO::FETCH_ASSOC);
if (!$roster) { http_response_code(500); exit('team_roster 초기 데이터를 먼저 설치해주세요.'); }
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<link rel="stylesheet" href="/manage/css/member.css">
<main class="content member-page"><div class="content-inner">
    <div class="member-page-head">
        <div><span class="member-page-eyebrow">ABOUT</span><h2>멤버 관리</h2><p>파트별 명단과 팀원 이미지를 관리합니다.</p></div>
        <a href="/manage/member_write" class="btn btn-primary">멤버 등록</a>
    </div>
    <section class="card member-roster-card">
        <h3>파트별 명단</h3>
        <p><a href="/manage/page_content?page=about" class="btn btn-white">어바웃 문구 관리</a></p>
        <p class="member-help">HQ · ART · PLAN · HLDYS는 고정입니다. 입력한 이름, 쉼표, 줄바꿈을 그대로 저장합니다. 아래 이미지 목록과는 별도로 관리됩니다.</p>
        <form id="member-roster-form" action="/manage/proc/member_roster_save" method="post">
            <input type="hidden" name="csrf_token" value="<?=member_h($member_csrf)?>">
            <input type="hidden" name="revision" value="<?=member_h($roster['revision'])?>">
            <div class="member-roster-grid">
                <?php foreach (array('hq' => 'HQ', 'art' => 'ART', 'plan' => 'PLAN', 'hldys' => 'HLDYS') as $key => $label) { ?>
                <div><label for="roster-<?=$key?>"><?=$label?></label><textarea id="roster-<?=$key?>" name="<?=$key?>_text" rows="3" maxlength="2000"><?=member_h($roster[$key.'_text'])?></textarea></div>
                <?php } ?>
            </div>
            <div class="member-form-actions"><button type="submit" id="member-roster-save" class="btn btn-primary">명단 저장</button></div>
        </form>
    </section>
    <?php $contentEditorGroup = 'team'; include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/content_editor.php'; ?>
    <div class="member-summary">
        <div class="member-summary-item"><span>전체 등록</span><strong><?=number_format($summary['total'])?></strong></div>
        <div class="member-summary-item"><span>노출</span><strong><?=number_format($summary['active'])?></strong></div>
        <div class="member-summary-item"><span>비노출</span><strong><?=number_format($summary['total'] - $summary['active'])?></strong></div>
        <p>정렬순서가 낮은 팀원 이미지부터 표시됩니다.</p>
    </div>
    <div class="card member-filter-card">
        <form method="get" action="/manage/member_list" class="member-filter">
            <label for="member-status-filter" class="member-sr-only">노출 상태</label>
            <select name="is_active" id="member-status-filter">
                <option value="">전체 상태</option>
                <option value="1" <?=$is_active === '1' ? 'selected' : ''?>>노출</option>
                <option value="0" <?=$is_active === '0' ? 'selected' : ''?>>비노출</option>
            </select>
            <label for="member-keyword" class="member-sr-only">이름</label>
            <input id="member-keyword" name="keyword" maxlength="255" value="<?=member_h($keyword)?>" placeholder="이름 검색">
            <button class="btn btn-primary" type="submit">검색</button>
            <a class="btn btn-white" href="/manage/member_list">초기화</a>
        </form>
    </div>
    <div class="card member-list-card">
        <div class="member-list-head">검색 결과 <strong><?=number_format($total_count)?></strong>개</div>
        <div class="member-table-wrap"><table class="member-table">
            <thead><tr><th scope="col">기본 / 추가 이미지</th><th scope="col">이름</th><th scope="col">정렬</th><th scope="col">상태</th><th scope="col">수정일</th><th scope="col">관리</th></tr></thead>
            <tbody>
            <?php foreach ($team_members as $member) { $logo = member_image_url($member['storage_key']); ?>
                <tr>
                    <td><a class="member-logo" href="/manage/member_write?id=<?=member_h($member['id'])?>">
                        <?php if ($logo !== '') { ?><img src="<?=member_h($logo)?>" alt="<?=member_h($member['member_name'])?>" loading="lazy"><?php } else { ?><span>이미지 없음</span><?php } ?>
                    </a>
                        <?php $mobile_logo = member_image_url($member['mobile_storage_key']); if ($mobile_logo !== '') { ?><a class="member-logo member-secondary-thumb" href="/manage/member_write?id=<?=member_h($member['id'])?>"><img src="<?=member_h($mobile_logo)?>" alt="<?=member_h($member['member_name'])?> 추가 이미지" loading="lazy"></a><?php } ?>
                    </td>
                    <td class="member-name"><a href="/manage/member_write?id=<?=member_h($member['id'])?>"><strong><?=member_h($member['member_name'])?></strong><span><?=member_h($member['original_name'])?></span></a></td>
                    <td><?=member_h($member['sort_order'])?></td>
                    <td><span class="member-status <?=intval($member['is_active']) === 1 ? 'active' : 'inactive'?>"><?=intval($member['is_active']) === 1 ? '노출' : '비노출'?></span></td>
                    <td class="member-date"><?=member_h(substr($member['updated_at'], 0, 16))?></td>
                    <td><div class="member-actions">
                        <a class="btn btn-white" href="/manage/member_write?id=<?=member_h($member['id'])?>">수정</a>
                        <button type="button" class="btn member-delete-btn" data-id="<?=member_h($member['id'])?>" data-name="<?=member_h($member['member_name'])?>">삭제</button>
                    </div></td>
                </tr>
            <?php } ?>
            <?php if (!$team_members) { ?><tr><td colspan="6" class="member-empty">조건에 맞는 멤버가 없습니다.</td></tr><?php } ?>
            </tbody>
        </table></div>
        <?php if ($total_page > 1) { ?>
        <nav class="member-pagination" aria-label="목록 페이지">
            <?php if ($page > 1) { ?><a href="?page=1<?=member_h($base_query)?>">처음</a><a href="?page=<?=$page - 1?><?=member_h($base_query)?>">이전</a><?php } ?>
            <?php for ($p = $start_page; $p <= $end_page; $p++) { ?>
                <a href="?page=<?=$p?><?=member_h($base_query)?>" <?=$p === $page ? 'class="active" aria-current="page"' : ''?>><?=$p?></a>
            <?php } ?>
            <?php if ($page < $total_page) { ?><a href="?page=<?=$page + 1?><?=member_h($base_query)?>">다음</a><a href="?page=<?=$total_page?><?=member_h($base_query)?>">마지막</a><?php } ?>
        </nav>
        <?php } ?>
    </div>
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
    var savedMessage = <?=json_encode(member_get('saved', '') === '1' ? '멤버가 저장되었습니다.' : '')?>;
    if (savedMessage) {
        window.alert(savedMessage);
        if (window.history && window.history.replaceState) { window.history.replaceState(null, '', '/manage/member_list'); }
    }
    var busy = false;
    var buttons = document.querySelectorAll('.member-delete-btn');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].onclick = function () {
            if (busy) { return; }
            var button = this;
            memberDialog('“' + button.getAttribute('data-name') + '” 멤버를 삭제하시겠습니까? 기존 팀원 이미지 파일은 보존됩니다.', true, function () {
                busy = true; button.disabled = true;
                var data = new FormData();
                data.append('id', button.getAttribute('data-id'));
        data.append('csrf_token', <?=json_encode($member_csrf)?>);
                memberRequest('/manage/proc/member_delete', data, function (result) {
                    if (result.success) { window.location.reload(); return; }
                    busy = false; button.disabled = false;
                    memberDialog(result.message, false);
                });
            });
        };
    }
}());
</script>

<script>
(function () {
    var form = document.getElementById('member-roster-form');
    var button = document.getElementById('member-roster-save');
    var busy = false;
    form.onsubmit = function (event) {
        event.preventDefault(); if (busy) { return; }
        var data = new FormData(form);
        busy = true; button.disabled = true; button.textContent = '저장 중…';
        // Prevent editing during the request so the visible text matches the saved revision.
        var fields = form.querySelectorAll('textarea');
        for (var i = 0; i < fields.length; i++) { fields[i].readOnly = true; }
        var xhr = new XMLHttpRequest(); xhr.open('POST', form.action, true); xhr.timeout = 30000;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        function unlock() {
            busy = false; button.disabled = false; button.textContent = '명단 저장';
            for (var i = 0; i < fields.length; i++) { fields[i].readOnly = false; }
        }
        xhr.onload = function () {
            unlock(); var data;
            try { data = JSON.parse(xhr.responseText); } catch (e) { window.alert('응답을 확인할 수 없습니다. 새로고침 후 저장 내용을 확인해주세요.'); return; }
            if (data.code === true) { form.elements.revision.value = data.revision; }
            window.alert(data.msg || '저장 결과를 확인할 수 없습니다.');
        };
        xhr.onerror = xhr.ontimeout = function () { unlock(); window.alert('통신이 중단되었습니다. 새로고침 후 저장 내용을 확인해주세요.'); };
        xhr.send(data);
    };
}());
</script>
<?php
// Some WEEKENDS installations close the layout in footer.php; older ones do not have it.
$member_footer = $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
if (is_file($member_footer)) { include $member_footer; } else { echo '</body></html>'; }
?>

