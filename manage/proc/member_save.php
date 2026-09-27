<?php
/*
 * =========================================================
 * WEEKENDS CMS | 멤버 등록 / 수정 | PHP 5.4+
 * FILE /manage/proc/member_save.php
 * URL  /manage/proc/member_save
 * Requires InnoDB team_members/media and the existing pdo_query().
 * =========================================================
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function member_save_json($code, $msg, $id)
{
    echo json_encode(array('code' => (bool)$code, 'msg' => $msg, 'id' => (string)$id));
    exit;
}

function member_save_post($key, $default)
{
    if (!isset($_POST[$key])) { return $default; }
    if (!is_string($_POST[$key])) { member_save_json(false, '잘못된 입력 형식입니다.', 0); }
    return trim($_POST[$key]);
}

function member_save_query($db, $query, $params)
{
    $ps = pdo_query($db, $query, $params);
    if ($ps === false) { throw new RuntimeException('Database query failed.'); }
    return $ps;
}

function member_save_last_id($db)
{
    $ps = member_save_query($db, 'SELECT LAST_INSERT_ID() AS id', array());
    $id = $ps->fetchColumn();
    if (!$id || !preg_match('/^[1-9][0-9]*$/D', (string)$id)) {
        throw new RuntimeException('Insert ID unavailable.');
    }
    return (string)$id;
}

function member_save_token_equal($expected, $actual)
{
    if (!is_string($expected) || !is_string($actual) || strlen($expected) !== strlen($actual)) { return false; }
    $diff = 0;
    for ($i = 0; $i < strlen($expected); $i++) { $diff |= ord($expected[$i]) ^ ord($actual[$i]); }
    return $diff === 0;
}

function member_upload_image($db, $file, $uploaded_by, &$uploaded_files)
{
    if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
        throw new InvalidArgumentException('이미지 업로드 형식이 올바르지 않습니다.');
    }
    $error = intval($file['error']);
    if ($error === UPLOAD_ERR_NO_FILE) { return ''; }
    if ($error !== UPLOAD_ERR_OK) {
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new InvalidArgumentException('이미지 업로드 허용 용량을 초과했습니다. (최대 5MB)');
        }
        throw new InvalidArgumentException('이미지 업로드가 완료되지 않았습니다. 다시 선택해주세요.');
    }
    if (!isset($file['tmp_name'], $file['name']) || !is_string($file['tmp_name']) || !is_string($file['name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new InvalidArgumentException('업로드 파일을 확인할 수 없습니다.');
    }
    $size = @filesize($file['tmp_name']);
    if ($size === false || $size <= 0 || $size > 5 * 1024 * 1024) {
        throw new InvalidArgumentException('이미지는 0바이트보다 크고 5MB 이하여야 합니다.');
    }
    $info = @getimagesize($file['tmp_name']);
    if (!$info || $info[0] < 1 || $info[1] < 1 || $info[0] > 4096 || $info[1] > 4096) {
        throw new InvalidArgumentException('정상적인 이미지인지 확인해주세요. 최대 크기는 4,096 × 4,096px입니다.');
    }
    // PNG/JPEG/GIF are decoded by PHP 5.4 getimagesize(). WEBP is not reliably supported by that version.
    $allowed = array(IMAGETYPE_PNG => array('png', 'image/png'), IMAGETYPE_JPEG => array('jpg', 'image/jpeg'), IMAGETYPE_GIF => array('gif', 'image/gif'));
    if (!isset($allowed[$info[2]])) { throw new InvalidArgumentException('PNG, JPG, GIF 이미지만 업로드할 수 있습니다.'); }
    $extension = $allowed[$info[2]][0];
    $mime_type = $allowed[$info[2]][1];
    if (function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected = @finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if ($detected !== $mime_type) { throw new InvalidArgumentException('이미지 파일 형식이 일치하지 않습니다.'); }
        }
    }
    $original_name = basename(str_replace('\\', '/', $file['name']));
    $original_name = preg_replace('/[\x00-\x1F\x7F]/', '', $original_name);
    $characters = array();
    if (preg_match_all('/./us', $original_name, $characters) === false || $original_name === '') {
        throw new InvalidArgumentException('이미지 파일명이 올바르지 않습니다.');
    }
    if (count($characters[0]) > 255) { $original_name = implode('', array_slice($characters[0], 0, 255)); }
    $hash = @hash_file('sha256', $file['tmp_name']);
    if ($hash === false) { throw new RuntimeException('Cannot hash upload.'); }
    $upload_path = '/uploads/member/'.date('Ym');
    $upload_dir = $_SERVER['DOCUMENT_ROOT'].$upload_path;
    if (!is_dir($upload_dir) && !@mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
        throw new InvalidArgumentException('이미지 저장 폴더를 만들 수 없습니다. 업로드 폴더 권한을 확인해주세요.');
    }
    $strong = false;
    $bytes = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(20, $strong) : false;
    if ($bytes === false || !$strong) { throw new RuntimeException('Secure random unavailable.'); }
    $file_name = date('YmdHis').'_'.bin2hex($bytes).'.'.$extension;
    $save_file = $upload_dir.'/'.$file_name;
    // Exclusive reservation prevents overwriting an existing file, even in concurrent requests.
    $handle = @fopen($save_file, 'x');
    if (!$handle) { throw new RuntimeException('Cannot reserve upload destination.'); }
    fclose($handle);
    $uploaded_files[] = $save_file;
    if (!move_uploaded_file($file['tmp_name'], $save_file)) { throw new RuntimeException('Cannot move uploaded file.'); }
    clearstatcache(true, $save_file);
    if (@filesize($save_file) !== $size || @hash_file('sha256', $save_file) !== $hash || !@getimagesize($save_file)) {
        throw new RuntimeException('Uploaded image verification failed.');
    }
    $query = 'INSERT INTO media
        (storage_key, original_name, mime_type, byte_size, width, height, default_alt_text, uploaded_by, created_at, deleted_at)
        VALUES (?, ?, ?, ?, ?, ?, NULL, ?, NOW(), NULL)';
    member_save_query($db, $query, array($upload_path.'/'.$file_name, $original_name, $mime_type, $size, $info[0], $info[1], $uploaded_by));
    return member_save_last_id($db);
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); member_save_json(false, 'POST 요청만 허용됩니다.', 0);
}
$admin_id = isset($_SESSION['ADMIN_ID']) ? intval($_SESSION['ADMIN_ID']) : 0;
if ($admin_id <= 0) { http_response_code(401); member_save_json(false, '관리자 로그인이 필요합니다.', 0); }
if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
    http_response_code(413); member_save_json(false, '요청이 서버 업로드 제한을 초과했거나 올바르지 않습니다.', 0);
}
$token = member_save_post('csrf_token', '');
if (empty($_SESSION['member_csrf']) || !member_save_token_equal($_SESSION['member_csrf'], $token)) {
    http_response_code(403); member_save_json(false, '요청이 만료되었습니다. 페이지를 새로고침해주세요.', 0);
}
$member_id = member_save_post('id', '');
if ($member_id === '0') { $member_id = ''; }
if ($member_id !== '' && !preg_match('/^[1-9][0-9]{0,19}$/D', $member_id)) { member_save_json(false, '잘못된 멤버 번호입니다.', 0); }
$member_name = member_save_post('member_name', '');
$characters = array();
if ($member_name === '' || strlen($member_name) > 1020 || preg_match_all('/./us', $member_name, $characters) === false || count($characters[0]) > 255 || preg_match('/[\x00-\x1F\x7F]/', $member_name)) {
    member_save_json(false, '이름을 1~255자로 입력해주세요.', $member_id);
}
$sort_order = member_save_post('sort_order', '0');
if (!preg_match('/^-?[0-9]{1,10}$/D', $sort_order) || (float)$sort_order < -2147483648 || (float)$sort_order > 2147483647) {
    member_save_json(false, '정렬순서는 -2147483648~2147483647 사이의 정수여야 합니다.', $member_id);
}
$is_active = member_save_post('is_active', '');
if ($is_active !== '0' && $is_active !== '1') { member_save_json(false, '노출여부를 선택해주세요.', $member_id); }
$remove_mobile = member_save_post('remove_mobile_image', '0');
if ($remove_mobile !== '0' && $remove_mobile !== '1') { member_save_json(false, '추가 이미지 삭제 값이 올바르지 않습니다.', $member_id); }
session_write_close();

$uploaded_files = array();
$locked = false; $committed = false; $commit_attempted = false; $transaction_started = false;
$lock_name = ''; $failure_message = ''; $was_edit = $member_id !== '';
try {
    // Same database-scoped named lock in save/delete: works even when team_members is empty.
    // Take the lock BEFORE starting the transaction so the active-count snapshot is current.
    $ps = member_save_query($db, "SELECT CONCAT('weekends.team_members.', MD5(DATABASE()))", array());
    $lock_name = $ps->fetchColumn();
    $ps = member_save_query($db, 'SELECT GET_LOCK(?, 10)', array($lock_name));
    if ((string)$ps->fetchColumn() !== '1') { throw new InvalidArgumentException('다른 관리자가 처리 중입니다. 잠시 후 다시 시도해주세요.'); }
    $locked = true;
    if (!$db->beginTransaction()) { throw new RuntimeException('Cannot begin transaction.'); }
    $transaction_started = true;
    $existing = false;
    if ($was_edit) {
        $ps = member_save_query($db, 'SELECT id, media_id, mobile_media_id, is_active FROM team_members WHERE id = ? FOR UPDATE', array($member_id));
        $existing = $ps->fetch(PDO::FETCH_ASSOC);
        if (!$existing) { throw new InvalidArgumentException('존재하지 않거나 이미 삭제된 멤버입니다.'); }
    }
    $media_id = isset($_FILES['image']) ? member_upload_image($db, $_FILES['image'], $admin_id, $uploaded_files) : '';
    if ($media_id === '') {
        if (!$existing) { throw new InvalidArgumentException('팀원 이미지를 등록해주세요.'); }
        $ps = member_save_query($db, 'SELECT id FROM media WHERE id = ? AND deleted_at IS NULL FOR UPDATE', array($existing['media_id']));
        if (!$ps->fetchColumn()) { throw new InvalidArgumentException('기존 팀원 이미지를 사용할 수 없습니다. 새 이미지를 선택해주세요.'); }
        $media_id = $existing['media_id'];
    }
    $mobile_media_id = $existing ? $existing['mobile_media_id'] : null;
    $new_mobile_id = isset($_FILES['mobile_image']) ? member_upload_image($db, $_FILES['mobile_image'], $admin_id, $uploaded_files) : '';
    if ($new_mobile_id !== '' && $remove_mobile === '1') { throw new InvalidArgumentException('추가 이미지 교체와 연결 해제를 동시에 선택할 수 없습니다.'); }
    if ($new_mobile_id !== '') { $mobile_media_id = $new_mobile_id; }
    elseif ($remove_mobile === '1') { $mobile_media_id = null; }
    if ($was_edit) {
        $query = 'UPDATE team_members SET media_id = ?, mobile_media_id = ?, member_name = ?, sort_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?';
        member_save_query($db, $query, array($media_id, $mobile_media_id, $member_name, $sort_order, intval($is_active), $member_id));
    } else {
        $query = 'INSERT INTO team_members (media_id, mobile_media_id, member_name, sort_order, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())';
        member_save_query($db, $query, array($media_id, $mobile_media_id, $member_name, $sort_order, intval($is_active)));
        $member_id = member_save_last_id($db);
    }
    // Never delete or soft-delete the former media record: other content may share it.
    $commit_attempted = true;
    if (!$db->commit()) { throw new RuntimeException('Commit failed.'); }
    $committed = true;
} catch (Exception $e) {
    $rolled_back = false;
    try { if ($db->inTransaction()) { $rolled_back = $db->rollBack(); } } catch (Exception $rollback_error) { error_log('member_save rollback: '.$rollback_error->getMessage()); }
    // Remove ONLY files created by this request after a known rollback.
    // Preserve files on uncertain commit outcomes to avoid deleting a committed media asset.
    if ($rolled_back || (!$transaction_started && !$commit_attempted)) {
        foreach ($uploaded_files as $uploaded_file) { if (is_file($uploaded_file)) { @unlink($uploaded_file); } }
    }
    error_log('member_save: '.$e->getMessage());
    $failure_message = $e instanceof InvalidArgumentException ? $e->getMessage() : '저장 중 오류가 발생했습니다. 목록에서 저장 여부를 확인한 후 다시 시도해주세요.';
}
// PHP 5.4 has no finally; release on both success and failure, before responding.
if ($locked) {
    try { member_save_query($db, 'SELECT RELEASE_LOCK(?)', array($lock_name)); }
    catch (Exception $release_error) { error_log('member_save release lock: '.$release_error->getMessage()); }
}
if (!$committed) { member_save_json(false, $failure_message, $member_id); }
member_save_json(true, $was_edit ? '멤버가 수정되었습니다.' : '멤버가 등록되었습니다.', $member_id);
