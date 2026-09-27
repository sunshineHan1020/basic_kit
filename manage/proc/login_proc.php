<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 로그인 처리
 * /manage/login/login_proc.php
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';


if(session_id() == ''){
    session_start();
}


header('Content-Type: application/json; charset=utf-8');


/*
 * =========================================================
 * JSON Response
 * =========================================================
 */

function login_json($code, $msg, $url = '')
{
    echo json_encode(
        array(
            'code' => $code,
            'msg'  => $msg,
            'url'  => $url
        )
    );

    exit;
}


/*
 * =========================================================
 * POST 체크
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){
    login_json(
        false,
        '잘못된 접근입니다.'
    );
}


/*
 * =========================================================
 * 입력값
 * =========================================================
 */

$login_id =
    isset($_POST['login_id'])
        ? trim($_POST['login_id'])
        : '';


$password =
    isset($_POST['password'])
        ? $_POST['password']
        : '';


$token =
    isset($_POST['token'])
        ? trim($_POST['token'])
        : '';


$save_id =
    isset($_POST['save_id'])
        ? trim($_POST['save_id'])
        : 'N';


/*
 * =========================================================
 * 기본 체크
 * =========================================================
 */

if($login_id == ''){

    login_json(
        false,
        '아이디를 입력해주세요.'
    );
}


if($password == ''){

    login_json(
        false,
        '비밀번호를 입력해주세요.'
    );
}


/*
 * =========================================================
 * Login Token
 * =========================================================
 */

if(
    !isset($_SESSION['ADMIN_LOGIN_TOKEN']) ||
    $_SESSION['ADMIN_LOGIN_TOKEN'] == '' ||
    $token == '' ||
    $_SESSION['ADMIN_LOGIN_TOKEN'] != $token
){

    login_json(
        false,
        '로그인 정보가 만료되었습니다. 페이지를 새로고침해주세요.'
    );
}


/*
 * =========================================================
 * OpenSSL 설정 체크
 * =========================================================
 */

if(
    !defined('ADMIN_CRYPT_KEY') ||
    !defined('ADMIN_CRYPT_IV')
){

    login_json(
        false,
        '암호를  확인해주세요.'
    );
}


/*
 * =========================================================
 * 관리자 조회
 * =========================================================
 */

$query = "

    SELECT
        id,
        login_id,
        password_hash,
        name,
        email,
        role,
        status,
        failed_login_count,
        locked_until

    FROM admin_users

    WHERE
        login_id = ?
        AND deleted_at IS NULL

    LIMIT 1

";


$ps = pdo_query(
    $db,
    $query,
    array(
        $login_id
    )
);


$admin =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


/*
 * =========================================================
 * 계정 없음
 * =========================================================
 */

if(!$admin){

    login_json(
        false,
        '아이디 또는 비밀번호를 확인해주세요.'
    );
}


/*
 * =========================================================
 * 계정 상태
 * =========================================================
 */

if($admin['status'] == 'inactive'){

    login_json(
        false,
        '사용 중지된 관리자 계정입니다.'
    );
}


if($admin['status'] == 'locked'){

    login_json(
        false,
        '잠금 처리된 관리자 계정입니다.'
    );
}


/*
 * =========================================================
 * 일시 잠금 확인
 * =========================================================
 */

if(
    $admin['locked_until'] != '' &&
    $admin['locked_until'] != '0000-00-00 00:00:00'
){

    $locked_time =
        strtotime(
            $admin['locked_until']
        );


    if(
        $locked_time !== false &&
        $locked_time > time()
    ){

        login_json(
            false,
            '로그인 실패 횟수를 초과했습니다. 잠시 후 다시 시도해주세요.'
        );
    }
}


/*
 * =========================================================
 * 비밀번호 복호화
 * =========================================================
 */

$decrypt_password =
    openssl_decrypt(
        $admin['password_hash'],
        'AES-256-CBC',
        ADMIN_CRYPT_KEY,
        0,
        ADMIN_CRYPT_IV
    );


/*
 * =========================================================
 * 비밀번호 오류
 * =========================================================
 */

if(
    $decrypt_password === false ||
    $decrypt_password !== $password
){

    $failed_count =
        intval(
            $admin['failed_login_count']
        ) + 1;


    /*
     * 5회 이상 실패
     * 10분 잠금
     */

    if($failed_count >= 5){

        $query = "

            UPDATE admin_users

            SET
                failed_login_count = ?,
                locked_until = DATE_ADD(NOW(), INTERVAL 10 MINUTE),
                updated_at = NOW()

            WHERE id = ?

            LIMIT 1

        ";


        pdo_query(
            $db,
            $query,
            array(
                $failed_count,
                $admin['id']
            )
        );


        login_json(
            false,
            '로그인 실패 횟수를 초과했습니다. 10분 후 다시 시도해주세요.'
        );
    }


    /*
     * 일반 로그인 실패
     */

    $query = "

        UPDATE admin_users

        SET
            failed_login_count = ?,
            updated_at = NOW()

        WHERE id = ?

        LIMIT 1

    ";


    pdo_query(
        $db,
        $query,
        array(
            $failed_count,
            $admin['id']
        )
    );


    login_json(
        false,
        '아이디 또는 비밀번호를 확인해주세요.'
    );
}


/*
 * =========================================================
 * 로그인 성공
 * =========================================================
 */


/*
 * 접속 IP
 */

$login_ip =
    isset($_SERVER['REMOTE_ADDR'])
        ? $_SERVER['REMOTE_ADDR']
        : '';


/*
 * 로그인 정보 갱신
 */

$query = "

    UPDATE admin_users

    SET
        failed_login_count = 0,
        locked_until = NULL,
        last_login_at = NOW(),
        last_login_ip = ?,
        updated_at = NOW()

    WHERE id = ?

    LIMIT 1

";


pdo_query(
    $db,
    $query,
    array(
        $login_ip,
        $admin['id']
    )
);


/*
 * =========================================================
 * Session
 * =========================================================
 */

$_SESSION['ADMIN_ID'] =
    intval(
        $admin['id']
    );


$_SESSION['ADMIN_LOGIN_ID'] =
    $admin['login_id'];


$_SESSION['ADMIN_NAME'] =
    $admin['name'];


$_SESSION['ADMIN_ROLE'] =
    $admin['role'];


$_SESSION['ADMIN_EMAIL'] =
    $admin['email'];


/*
 * 로그인용 임시 TOKEN 제거
 */

unset(
    $_SESSION['ADMIN_LOGIN_TOKEN']
);


/*
 * =========================================================
 * 아이디 저장
 * =========================================================
 */

if($save_id == 'Y'){

    setcookie(
        'ADMIN_SAVE_ID',
        $admin['login_id'],
        time() + (86400 * 30),
        '/'
    );

}else{

    setcookie(
        'ADMIN_SAVE_ID',
        '',
        time() - 3600,
        '/'
    );
}


/*
 * =========================================================
 * 완료
 * =========================================================
 */

login_json(
    true,
    '로그인 되었습니다.',
    '/manage/'
);
?>