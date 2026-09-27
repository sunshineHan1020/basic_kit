<?php
/* WEEKENDS CMS | Fixed HQ / ART / PLAN / HLDYS text | PHP 5.4+
 * URL /manage/proc/member_roster_save
 * Stores literal text, including commas/spaces/newlines. Render escaped on the frontend.
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function member_roster_json($code, $msg, $revision)
{
    echo json_encode(array('code' => (bool)$code, 'msg' => $msg, 'revision' => (string)$revision));
    exit;
}
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); member_roster_json(false, 'POST 요청만 허용됩니다.', 0);
}
if (empty($_SESSION['ADMIN_ID']) || intval($_SESSION['ADMIN_ID']) <= 0) {
    http_response_code(401); member_roster_json(false, '관리자 로그인이 필요합니다.', 0);
}
$expected = isset($_SESSION['member_csrf']) && is_string($_SESSION['member_csrf']) ? $_SESSION['member_csrf'] : '';
$actual = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$diff = 0;
if ($expected === '' || strlen($actual) !== strlen($expected)) { $diff = 1; }
else { for ($i = 0; $i < strlen($expected); $i++) { $diff |= ord($expected[$i]) ^ ord($actual[$i]); } }
if ($diff !== 0) { http_response_code(403); member_roster_json(false, '요청이 만료되었습니다. 새로고침해주세요.', 0); }
$revision = isset($_POST['revision']) && is_string($_POST['revision']) ? $_POST['revision'] : '';
if (!preg_match('/^[1-9][0-9]{0,9}$/D', $revision) || (float)$revision >= 4294967295) {
    member_roster_json(false, '명단 버전을 확인할 수 없습니다. 새로고침해주세요.', 0);
}
$params = array();
foreach (array('hq_text', 'art_text', 'plan_text', 'hldys_text') as $key) {
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) { member_roster_json(false, '네 파트의 입력값을 모두 전달해주세요.', $revision); }
    $value = $_POST[$key];
    $characters = array();
    if (strlen($value) > 8000 || preg_match_all('/./us', $value, $characters) === false || count($characters[0]) > 2000 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
        member_roster_json(false, '각 파트는 2,000자 이하의 텍스트로 입력해주세요.', $revision);
    }
    // Do not trim, split, deduplicate, or rewrite the user-entered names.
    $params[] = $value;
}
$params[] = $revision;
session_write_close();
try {
    // Single atomic UPDATE: all four fields change together. Revision prevents lost updates.
    $query = 'UPDATE team_roster SET hq_text = ?, art_text = ?, plan_text = ?, hldys_text = ?, revision = revision + 1, updated_at = NOW() WHERE id = 1 AND revision = ?';
    $ps = pdo_query($db, $query, $params);
    if ($ps === false) { throw new RuntimeException('Roster update failed.'); }
    if ($ps->rowCount() !== 1) { member_roster_json(false, '명단이 다른 화면에서 변경되었거나 초기 데이터가 없습니다. 입력 내용을 복사한 뒤 새로고침해주세요.', $revision); }
    member_roster_json(true, '파트별 명단이 저장되었습니다.', sprintf('%.0f', (float)$revision + 1));
} catch (Exception $e) {
    error_log('member_roster_save: '.$e->getMessage());
    member_roster_json(false, '저장 중 오류가 발생했습니다. 새로고침 후 저장 내용을 확인해주세요.', $revision);
}
