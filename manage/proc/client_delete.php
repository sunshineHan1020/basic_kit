<?php
/*
 * =========================================================
 * WEEKENDS CMS | 클라이언트 삭제 | PHP 5.4+
 * FILE /manage/proc/client_delete.php
 * URL  /manage/proc/client_delete
 * Deletes ONLY clients; shared media records/files are preserved.
 * =========================================================
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function client_delete_json($code, $msg, $id)
{
    echo json_encode(array('code' => (bool)$code, 'msg' => $msg, 'id' => (string)$id));
    exit;
}
function client_delete_query($db, $query, $params)
{
    $ps = pdo_query($db, $query, $params);
    if ($ps === false) { throw new RuntimeException('Database query failed.'); }
    return $ps;
}
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); client_delete_json(false, 'POST 요청만 허용됩니다.', 0);
}
if (empty($_SESSION['ADMIN_ID']) || intval($_SESSION['ADMIN_ID']) <= 0) {
    http_response_code(401); client_delete_json(false, '관리자 로그인이 필요합니다.', 0);
}
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$expected = isset($_SESSION['client_csrf']) && is_string($_SESSION['client_csrf']) ? $_SESSION['client_csrf'] : '';
$diff = 0;
if ($expected === '' || strlen($token) !== strlen($expected)) { $diff = 1; }
else { for ($i = 0; $i < strlen($expected); $i++) { $diff |= ord($token[$i]) ^ ord($expected[$i]); } }
if ($diff !== 0) { http_response_code(403); client_delete_json(false, '요청이 만료되었습니다. 페이지를 새로고침해주세요.', 0); }
$client_id = isset($_POST['id']) && is_string($_POST['id']) ? trim($_POST['id']) : '';
if (!preg_match('/^[1-9][0-9]{0,19}$/D', $client_id)) { client_delete_json(false, '잘못된 클라이언트 번호입니다.', 0); }
session_write_close();
$locked = false; $committed = false; $lock_name = ''; $message = '';
try {
    $ps = client_delete_query($db, "SELECT CONCAT('weekends.clients.', MD5(DATABASE()))", array());
    $lock_name = $ps->fetchColumn();
    $ps = client_delete_query($db, 'SELECT GET_LOCK(?, 10)', array($lock_name));
    if ((string)$ps->fetchColumn() !== '1') { throw new InvalidArgumentException('다른 관리자가 처리 중입니다. 잠시 후 다시 시도해주세요.'); }
    $locked = true;
    if (!$db->beginTransaction()) { throw new RuntimeException('Cannot begin transaction.'); }
    $ps = client_delete_query($db, 'SELECT id FROM clients WHERE id = ? FOR UPDATE', array($client_id));
    if (!$ps->fetchColumn()) { throw new InvalidArgumentException('존재하지 않거나 이미 삭제된 클라이언트입니다.'); }
    client_delete_query($db, 'DELETE FROM clients WHERE id = ?', array($client_id));
    // No UPDATE/DELETE media, and no unlink(): the media may be shared.
    if (!$db->commit()) { throw new RuntimeException('Commit failed.'); }
    $committed = true;
} catch (Exception $e) {
    try { if ($db->inTransaction()) { $db->rollBack(); } } catch (Exception $rollback_error) { error_log('client_delete rollback: '.$rollback_error->getMessage()); }
    error_log('client_delete: '.$e->getMessage());
    $message = $e instanceof InvalidArgumentException ? $e->getMessage() : '삭제 중 오류가 발생했습니다. 목록을 확인한 후 다시 시도해주세요.';
}
if ($locked) {
    try { client_delete_query($db, 'SELECT RELEASE_LOCK(?)', array($lock_name)); }
    catch (Exception $release_error) { error_log('client_delete release lock: '.$release_error->getMessage()); }
}
client_delete_json($committed, $committed ? '클라이언트가 삭제되었습니다.' : $message, $client_id);
