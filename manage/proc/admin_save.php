<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 등록 / 수정
 *
 * FILE
 * /manage/proc/admin_save.php
 *
 * URL
 * /manage/proc/admin_save
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


header(
    'Content-Type: application/json; charset=utf-8'
);



/*
 * =========================================================
 * JSON
 * =========================================================
 */

function admin_save_json(
    $code,
    $msg,
    $id
){
    echo json_encode(
        array(
            'code' => $code,
            'msg'  => $msg,
            'id'   => intval($id)
        )
    );

    exit;
}



/*
 * =========================================================
 * SUPER ADMIN ONLY
 * =========================================================
 */

if(
    !isset($_SESSION['ADMIN_ROLE']) ||
    $_SESSION['ADMIN_ROLE'] != 'super'
){

    admin_save_json(
        false,
        '권한이 없습니다.',
        0
    );

}



/*
 * =========================================================
 * METHOD
 * =========================================================
 */

if(
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] != 'POST'
){

    admin_save_json(
        false,
        '잘못된 접근입니다.',
        0
    );

}



/*
 * =========================================================
 * POST
 * =========================================================
 */

$admin_id =
    isset($_POST['id'])
        ? intval($_POST['id'])
        : 0;


$login_id =
    isset($_POST['login_id'])
        ? strtolower(
            trim($_POST['login_id'])
        )
        : '';


$name =
    isset($_POST['name'])
        ? trim($_POST['name'])
        : '';


$email =
    isset($_POST['email'])
        ? trim($_POST['email'])
        : '';


$password =
    isset($_POST['password'])
        ? $_POST['password']
        : '';


$password_confirm =
    isset($_POST['password_confirm'])
        ? $_POST['password_confirm']
        : '';


$role =
    isset($_POST['role'])
        ? trim($_POST['role'])
        : '';


$status =
    isset($_POST['status'])
        ? trim($_POST['status'])
        : '';


$is_edit =
    $admin_id > 0
        ? true
        : false;



/*
 * =========================================================
 * LOGIN ID
 * =========================================================
 */

if(
    $login_id == ''
){

    admin_save_json(
        false,
        '관리자 아이디를 입력해주세요.',
        $admin_id
    );

}


if(
    !preg_match(
        '/^[a-z0-9_-]+$/',
        $login_id
    )
){

    admin_save_json(
        false,
        '관리자 아이디는 영문 소문자, 숫자, _, - 만 사용할 수 있습니다.',
        $admin_id
    );

}


if(
    strlen($login_id) > 100
){

    admin_save_json(
        false,
        '관리자 아이디가 너무 깁니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * NAME
 * =========================================================
 */

if(
    $name == ''
){

    admin_save_json(
        false,
        '관리자 이름을 입력해주세요.',
        $admin_id
    );

}


if(
    function_exists('mb_strlen')
){

    if(
        mb_strlen(
            $name,
            'UTF-8'
        ) > 100
    ){

        admin_save_json(
            false,
            '관리자 이름은 100자 이내로 입력해주세요.',
            $admin_id
        );

    }

}else{

    if(
        strlen($name) > 300
    ){

        admin_save_json(
            false,
            '관리자 이름이 너무 깁니다.',
            $admin_id
        );

    }

}



/*
 * =========================================================
 * EMAIL
 * =========================================================
 */

if(
    $email == ''
){

    admin_save_json(
        false,
        '이메일을 입력해주세요.',
        $admin_id
    );

}


if(
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
){

    admin_save_json(
        false,
        '이메일 형식이 올바르지 않습니다.',
        $admin_id
    );

}


if(
    strlen($email) > 255
){

    admin_save_json(
        false,
        '이메일이 너무 깁니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * ROLE
 * =========================================================
 */

$role_list =
    array(
        'super',
        'admin',
        'editor'
    );


if(
    !in_array(
        $role,
        $role_list,
        true
    )
){

    admin_save_json(
        false,
        '관리자 권한이 올바르지 않습니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * STATUS
 * =========================================================
 */

$status_list =
    array(
        'active',
        'inactive',
        'locked'
    );


if(
    !in_array(
        $status,
        $status_list,
        true
    )
){

    admin_save_json(
        false,
        '관리자 상태가 올바르지 않습니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * PASSWORD
 * =========================================================
 */

if(
    !$is_edit &&
    $password == ''
){

    admin_save_json(
        false,
        '비밀번호를 입력해주세요.',
        0
    );

}


if(
    $password != '' &&
    $password != $password_confirm
){

    admin_save_json(
        false,
        '비밀번호가 일치하지 않습니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * EXISTING ADMIN
 * =========================================================
 */

$current_admin =
    false;


if($is_edit){

    $query = "

        SELECT

            id,
            login_id,
            password_hash,
            name,
            email,
            role,
            status

        FROM admin_users

        WHERE
            id = ?
            AND deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $admin_id
            )
        );


    $current_admin =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$current_admin){

        admin_save_json(
            false,
            '존재하지 않는 관리자입니다.',
            $admin_id
        );

    }


    /*
     * 수정에서는 로그인 아이디 변경 불가
     */

    $login_id =
        $current_admin['login_id'];

}



/*
 * =========================================================
 * SELF PROTECTION
 * =========================================================
 */

$is_me =
    $is_edit &&
    isset($_SESSION['ADMIN_ID']) &&
    intval($_SESSION['ADMIN_ID']) ==
    intval($admin_id);


if($is_me){

    if(
        $role !=
        $current_admin['role']
    ){

        admin_save_json(
            false,
            '현재 로그인한 계정의 권한은 변경할 수 없습니다.',
            $admin_id
        );

    }


    if(
        $status != 'active'
    ){

        admin_save_json(
            false,
            '현재 로그인한 계정은 사용중지 또는 잠금 처리할 수 없습니다.',
            $admin_id
        );

    }

}



/*
 * =========================================================
 * LOGIN ID DUPLICATE
 * =========================================================
 */

if(!$is_edit){

    $query = "

        SELECT
            id

        FROM admin_users

        WHERE
            login_id = ?
            AND deleted_at IS NULL

        LIMIT 1

    ";


    $ps =
        pdo_query(
            $db,
            $query,
            array(
                $login_id
            )
        );


    $duplicate =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if($duplicate){

        admin_save_json(
            false,
            '이미 사용 중인 관리자 아이디입니다.',
            0
        );

    }

}



/*
 * =========================================================
 * PASSWORD ENCRYPT
 * =========================================================
 */

$password_hash =
    '';


if(
    $password != ''
){

    $password_hash =
        admin_password_encrypt(
            $password
        );


    if(
        $password_hash === false ||
        $password_hash === ''
    ){

        admin_save_json(
            false,
            '비밀번호 암호화 처리 중 오류가 발생했습니다.',
            $admin_id
        );

    }

}



/*
 * =========================================================
 * SAVE
 * =========================================================
 */

try {


    /*
     * =====================================================
     * INSERT
     * =====================================================
     */

    if(!$is_edit){


        $query = "

            INSERT INTO admin_users
            (
                login_id,
                password_hash,
                name,
                email,
                role,
                status,
                failed_login_count,
                locked_until,
                last_login_at,
                last_login_ip,
                password_changed_at,
                created_at,
                updated_at,
                deleted_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                0,
                NULL,
                NULL,
                NULL,
                NOW(),
                NOW(),
                NOW(),
                NULL
            )

        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array(
                    $login_id,
                    $password_hash,
                    $name,
                    $email,
                    $role,
                    $status
                )
            );


        if(!$ps){

            admin_save_json(
                false,
                '관리자 등록에 실패했습니다.',
                0
            );

        }



        /*
         * LAST INSERT ID
         */

        $query = "
            SELECT
                LAST_INSERT_ID() AS id
        ";


        $ps =
            pdo_query(
                $db,
                $query,
                array()
            );


        $insert_data =
            $ps->fetch(
                PDO::FETCH_ASSOC
            );


        $admin_id =
            $insert_data
                ? intval(
                    $insert_data['id']
                )
                : 0;


        admin_save_json(
            true,
            '관리자가 등록되었습니다.',
            $admin_id
        );

    }



    /*
     * =====================================================
     * UPDATE
     * =====================================================
     */

    if(
        $password != ''
    ){


        /*
         * 비밀번호 변경 포함
         */

        if(
            $status == 'active'
        ){

            $query = "

                UPDATE admin_users

                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    status = ?,
                    password_hash = ?,
                    password_changed_at = NOW(),
                    failed_login_count = 0,
                    locked_until = NULL,
                    updated_at = NOW()

                WHERE
                    id = ?
                    AND deleted_at IS NULL

                LIMIT 1

            ";


            $params =
                array(
                    $name,
                    $email,
                    $role,
                    $status,
                    $password_hash,
                    $admin_id
                );

        }else{

            $query = "

                UPDATE admin_users

                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    status = ?,
                    password_hash = ?,
                    password_changed_at = NOW(),
                    updated_at = NOW()

                WHERE
                    id = ?
                    AND deleted_at IS NULL

                LIMIT 1

            ";


            $params =
                array(
                    $name,
                    $email,
                    $role,
                    $status,
                    $password_hash,
                    $admin_id
                );

        }


    }else{


        /*
         * 비밀번호 유지
         */

        if(
            $status == 'active'
        ){

            $query = "

                UPDATE admin_users

                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    status = ?,
                    failed_login_count = 0,
                    locked_until = NULL,
                    updated_at = NOW()

                WHERE
                    id = ?
                    AND deleted_at IS NULL

                LIMIT 1

            ";


            $params =
                array(
                    $name,
                    $email,
                    $role,
                    $status,
                    $admin_id
                );

        }else{

            $query = "

                UPDATE admin_users

                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    status = ?,
                    updated_at = NOW()

                WHERE
                    id = ?
                    AND deleted_at IS NULL

                LIMIT 1

            ";


            $params =
                array(
                    $name,
                    $email,
                    $role,
                    $status,
                    $admin_id
                );

        }

    }



    $ps =
        pdo_query(
            $db,
            $query,
            $params
        );


    if(!$ps){

        admin_save_json(
            false,
            '관리자 정보를 저장하지 못했습니다.',
            $admin_id
        );

    }



} catch(Exception $e) {


    admin_save_json(
        false,
        '관리자 정보 저장 중 오류가 발생했습니다.',
        $admin_id
    );

}



/*
 * =========================================================
 * SUCCESS
 * =========================================================
 */

admin_save_json(
    true,
    '관리자 정보가 저장되었습니다.',
    $admin_id
);
?>