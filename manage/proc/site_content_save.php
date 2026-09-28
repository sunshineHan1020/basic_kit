<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/site_content.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function wc_reply($status, $code, $message, $extra = array())
{
    http_response_code($status);
    echo json_encode(array_merge(array('code' => $code, 'msg' => $message), $extra));
    exit;
}

function wc_post($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}

function wc_upload_image($file, &$newFiles)
{
    if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
        throw new InvalidArgumentException('이미지 업로드 형식이 올바르지 않습니다.');
    }
    if ((int)$file['error'] === UPLOAD_ERR_NO_FILE) { return ''; }
    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('사진 업로드에 실패했습니다. 5MB와 서버 업로드 제한을 확인해주세요.');
    }
    if (!isset($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new InvalidArgumentException('업로드 파일을 확인할 수 없습니다.');
    }
    $size = @filesize($file['tmp_name']); $info = @getimagesize($file['tmp_name']);
    $allowed = array(IMAGETYPE_JPEG => array('jpg', 'image/jpeg'), IMAGETYPE_PNG => array('png', 'image/png'), IMAGETYPE_GIF => array('gif', 'image/gif'));
    if ($size === false || $size < 1 || $size > 5242880 || !$info || !isset($allowed[$info[2]]) || $info[0] < 1 || $info[1] < 1 || $info[0] > 4096 || $info[1] > 4096) {
        throw new InvalidArgumentException('JPG·PNG·GIF만 가능합니다. 최대 5MB / 4,096 × 4,096px입니다.');
    }
    if (function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = @finfo_file($finfo, $file['tmp_name']); finfo_close($finfo);
            if ($mime !== $allowed[$info[2]][1]) { throw new InvalidArgumentException('이미지 형식이 일치하지 않습니다.'); }
        }
    }
    $strong = false;
    $random = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(20, $strong) : false;
    if ($random === false || !$strong) { throw new RuntimeException('Secure random unavailable.'); }
    $directory = '/upload/site_content/'.date('Ym');
    $absoluteDirectory = $_SERVER['DOCUMENT_ROOT'].$directory;
    if (!is_dir($absoluteDirectory) && !@mkdir($absoluteDirectory, 0755, true) && !is_dir($absoluteDirectory)) {
        throw new InvalidArgumentException('/upload/ 폴더의 쓰기 권한을 확인해주세요.');
    }
    $relative = $directory.'/'.bin2hex($random).'.'.$allowed[$info[2]][0];
    $absolute = $_SERVER['DOCUMENT_ROOT'].$relative;
    $handle = @fopen($absolute, 'x');
    if (!$handle) { throw new RuntimeException('Cannot reserve image destination.'); }
    fclose($handle); $newFiles[] = $absolute;
    $hash = @hash_file('sha256', $file['tmp_name']);
    if ($hash === false || !move_uploaded_file($file['tmp_name'], $absolute)) { throw new RuntimeException('Image move failed.'); }
    clearstatcache(true, $absolute);
    if (@filesize($absolute) !== $size || @hash_file('sha256', $absolute) !== $hash || !@getimagesize($absolute)) { throw new RuntimeException('Image verification failed.'); }
    return $relative;
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST'); wc_reply(405, false, 'POST 요청만 허용됩니다.');
}
if (empty($_SESSION['ADMIN_ID']) || (int)$_SESSION['ADMIN_ID'] <= 0) { wc_reply(401, false, '관리자 로그인이 필요합니다.'); }
if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) { wc_reply(413, false, '서버의 업로드 허용 용량을 초과했습니다.'); }
$expected = isset($_SESSION['site_content_csrf']) ? $_SESSION['site_content_csrf'] : '';
if (!wc_token_equal($expected, wc_post('csrf_token'))) { wc_reply(403, false, '요청이 만료되었습니다. 새로고침해주세요.'); }
$mode = wc_post('mode'); $page = wc_post('page'); $slot = wc_post('slot');
$definitions = wc_text_defaults(); $imageDefinitions = wc_image_defaults();
$values = array(); $revisions = array();
if ($mode === 'text') {
    if (!isset($definitions[$page]) || !isset($_POST['texts'], $_POST['revisions']) || !is_array($_POST['texts']) || !is_array($_POST['revisions'])) { wc_reply(422, false, '문구 입력값을 확인해주세요.'); }
    if (count($_POST['texts']) !== count($definitions[$page]) || count($_POST['revisions']) !== count($definitions[$page])) { wc_reply(422, false, '입력 항목이 누락되었습니다. 입력값을 보관한 뒤 새로고침해주세요.'); }
    foreach ($definitions[$page] as $key => $definition) {
        if (!isset($_POST['texts'][$key], $_POST['revisions'][$key]) || !wc_valid_text($_POST['texts'][$key], $definition['max']) || !wc_valid_revision($_POST['revisions'][$key])) {
            wc_reply(422, false, $definition['label'].' 항목의 글자 수와 입력값을 확인해주세요.');
        }
        $values[$key] = $_POST['texts'][$key]; $revisions[$key] = $_POST['revisions'][$key];
    }
} elseif ($mode === 'image') {
    if (!isset($imageDefinitions[$slot]) || !wc_valid_revision(wc_post('revision')) || !isset($_POST['alt_text']) || !wc_valid_text($_POST['alt_text'], 500)) { wc_reply(422, false, '사진 항목과 설명을 확인해주세요.'); }
} else { wc_reply(422, false, '잘못된 저장 요청입니다.'); }
session_write_close();
$newFiles = array(); $transactionOpen = false; $commitAttempted = false;
try {
    if (!$db->beginTransaction()) { throw new RuntimeException('Cannot start content transaction.'); }
    $transactionOpen = true;
    if ($mode === 'text') {
        $nextRevisions = array();
        foreach ($values as $key => $value) {
            $ps = wc_query($db, 'UPDATE site_content_texts SET text_value = ?, revision = revision + 1, updated_at = NOW() WHERE page_key = ? AND content_key = ? AND revision = ?', array($value, $page, $key, $revisions[$key]));
            if ($ps->rowCount() !== 1) { throw new UnexpectedValueException('Revision conflict.'); }
            $nextRevisions[$key] = sprintf('%.0f', (float)$revisions[$key] + 1);
        }
        $result = array('revisions' => $nextRevisions);
    } else {
        $ps = wc_query($db, 'SELECT storage_key, revision FROM site_content_images WHERE slot_key = ? FOR UPDATE', array($slot));
        $current = $ps->fetch(PDO::FETCH_ASSOC);
        if (!$current || (string)$current['revision'] !== wc_post('revision')) { throw new UnexpectedValueException('Revision conflict.'); }
        $image = isset($_FILES['image']) ? wc_upload_image($_FILES['image'], $newFiles) : '';
        if ($image === '') { $image = $current['storage_key']; }
        if (wc_safe_image_path($image) === '') { throw new InvalidArgumentException('현재 사진 경로가 올바르지 않습니다. 새 사진을 등록해주세요.'); }
        $ps = wc_query($db, 'UPDATE site_content_images SET storage_key = ?, alt_text = ?, revision = revision + 1, updated_at = NOW() WHERE slot_key = ? AND revision = ?', array($image, $_POST['alt_text'], $slot, wc_post('revision')));
        if ($ps->rowCount() !== 1) { throw new UnexpectedValueException('Revision conflict.'); }
        $result = array('revision' => sprintf('%.0f', (float)$current['revision'] + 1), 'image_url' => $image);
    }
    $commitAttempted = true;
    if (!$db->commit()) { throw new RuntimeException('Content commit failed.'); }
    $transactionOpen = false;
    wc_reply(200, true, '저장되었습니다. 프론트 페이지를 새로고침하면 적용됩니다.', $result);
} catch (Exception $e) {
    $rolledBack = false;
    if ($transactionOpen) {
        try { $rolledBack = $db->rollBack(); } catch (Exception $rollbackError) { error_log('site_content rollback failed.'); }
    }
    // Never delete existing/shared files. Preserve new files when commit outcome is uncertain.
    if ($rolledBack && !$commitAttempted) { foreach ($newFiles as $newFile) { if (is_file($newFile)) { @unlink($newFile); } } }
    if ($e instanceof UnexpectedValueException) { wc_reply(409, false, '다른 화면에서 수정되었거나 초기 INSERT가 누락되었습니다. 입력 내용을 보관한 뒤 새로고침해주세요.'); }
    if ($e instanceof InvalidArgumentException) { wc_reply(422, false, $e->getMessage()); }
    error_log('site_content_save: '.$e->getMessage());
    wc_reply(500, false, '저장 결과를 확인할 수 없습니다. DB 초기 설정과 업로드 권한을 확인하고 새로고침해주세요.');
}
