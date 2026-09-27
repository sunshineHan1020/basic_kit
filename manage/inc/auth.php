<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 로그인 체크
 * /manage/inc/auth.php
 * =========================================================
 */


/*
 * 로그인 여부
 */

if(
    !isset($_SESSION['ADMIN_ID']) ||
    intval($_SESSION['ADMIN_ID']) <= 0
){

    header(
        'Location: /manage/login.php?required=Y'
    );

    exit;
}


/*
 * 관리자 상태 체크가 필요하면
 * 추후 여기에서 DB 조회해서
 * inactive / locked 계정도 차단 가능
 */
?>