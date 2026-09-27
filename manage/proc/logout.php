<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 로그아웃
 * /manage/login/logout.php
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';


/*
 * =========================================================
 * 관리자 세션 삭제
 * =========================================================
 */

unset($_SESSION['ADMIN_ID']);
unset($_SESSION['ADMIN_LOGIN_ID']);
unset($_SESSION['ADMIN_NAME']);
unset($_SESSION['ADMIN_ROLE']);
unset($_SESSION['ADMIN_EMAIL']);

unset($_SESSION['ADMIN_LOGIN_TOKEN']);


/*
 * =========================================================
 * 로그인 페이지 이동
 * =========================================================
 */

header(
    'Location: /manage/login.php?logout=Y'
);

exit;
?>