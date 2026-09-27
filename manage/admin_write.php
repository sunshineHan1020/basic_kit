<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 등록 / 수정
 *
 * FILE
 * /manage/admin_write.php
 *
 * URL
 * /manage/admin_write
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



/*
 * =========================================================
 * SUPER ADMIN ONLY
 * =========================================================
 */

if(
    !isset($_SESSION['ADMIN_ROLE']) ||
    $_SESSION['ADMIN_ROLE'] != 'super'
){
    page_move("/manage/");
    exit;
}



/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function admin_write_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * ID
 * =========================================================
 */

$admin_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


$is_edit =
    $admin_id > 0
        ? true
        : false;



/*
 * =========================================================
 * DEFAULT
 * =========================================================
 */

$admin =
    array(

        'id'                   => 0,
        'login_id'             => '',
        'name'                 => '',
        'email'                => '',
        'role'                 => 'editor',
        'status'               => 'active',
        'failed_login_count'   => 0,
        'locked_until'         => '',
        'last_login_at'        => '',
        'last_login_ip'        => '',
        'password_changed_at'  => '',
        'created_at'           => '',
        'updated_at'           => ''

    );



/*
 * =========================================================
 * EDIT
 * =========================================================
 */

if($is_edit){

    $query = "

        SELECT

            id,
            login_id,
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
            updated_at

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


    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        );


    if(!$data){

        page_move(
            "/manage/admin_list"
        );

        exit;

    }


    $admin =
        $data;

}



/*
 * =========================================================
 * CURRENT USER
 * =========================================================
 */

$is_me =
    $is_edit &&
    isset($_SESSION['ADMIN_ID']) &&
    intval($_SESSION['ADMIN_ID']) ==
    intval($admin_id);



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/admin.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="admin-manage-page-head">

            <div class="admin-manage-page-title">

                <span>
                    시스템 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <span>
                    관리자 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>

                    <?=$is_edit ? '관리자 수정' : '관리자 등록'?>

                </h2>

            </div>


            <a
                href="/manage/admin_list"
                class="btn btn-white"
            >

                <i class="ri-list-check-3"></i>

                목록

            </a>

        </div>



        <!-- =================================================
             FORM
        ================================================== -->

        <form
            id="adminForm"
            autocomplete="off"
        >

            <input
                type="hidden"
                name="id"
                value="<?=intval($admin_id)?>"
            >


            <div class="admin-write-layout">


                <!-- =================================================
                     MAIN
                ================================================== -->

                <div class="admin-write-main">


                    <!-- =================================================
                         ACCOUNT
                    ================================================== -->

                    <div class="card admin-write-card">

                        <div class="card-header">

                            <h3>
                                계정 정보
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- LOGIN ID -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label for="login_id">

                                        관리자 아이디

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <input
                                        type="text"
                                        id="login_id"
                                        name="login_id"
                                        value="<?=admin_write_h($admin['login_id'])?>"
                                        maxlength="100"
                                        placeholder="관리자 로그인 아이디"
                                        <?=$is_edit ? 'readonly' : ''?>
                                    >


                                    <p class="admin-write-help">

                                        영문, 숫자, _, - 조합으로 입력해주세요.

                                        <?
                                        if($is_edit){
                                        ?>

                                            관리자 아이디는 등록 후 변경할 수 없습니다.

                                        <?
                                        }
                                        ?>

                                    </p>

                                </div>

                            </div>



                            <!-- PASSWORD -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label for="password">

                                        비밀번호

                                        <?
                                        if(!$is_edit){
                                        ?>

                                            <strong>
                                                *
                                            </strong>

                                        <?
                                        }
                                        ?>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <div class="admin-password-field">

                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            autocomplete="new-password"
                                            placeholder="<?=$is_edit ? '변경할 경우에만 입력해주세요.' : '비밀번호를 입력해주세요.'?>"
                                        >


                                        <button
                                            type="button"
                                            class="admin-password-toggle"
                                            data-target="password"
                                        >

                                            <i class="ri-eye-line"></i>

                                        </button>

                                    </div>


                                    <?
                                    if($is_edit){
                                    ?>

                                        <p class="admin-write-help">
                                            비밀번호를 변경하지 않을 경우 비워두세요.
                                        </p>

                                    <?
                                    }else{
                                    ?>

                                        <p class="admin-write-help">
                                            관리자 로그인에 사용할 비밀번호를 입력해주세요.
                                        </p>

                                    <?
                                    }
                                    ?>

                                </div>

                            </div>



                            <!-- PASSWORD CONFIRM -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label for="password_confirm">

                                        비밀번호 확인

                                        <?
                                        if(!$is_edit){
                                        ?>

                                            <strong>
                                                *
                                            </strong>

                                        <?
                                        }
                                        ?>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <div class="admin-password-field">

                                        <input
                                            type="password"
                                            id="password_confirm"
                                            name="password_confirm"
                                            autocomplete="new-password"
                                            placeholder="비밀번호를 다시 입력해주세요."
                                        >


                                        <button
                                            type="button"
                                            class="admin-password-toggle"
                                            data-target="password_confirm"
                                        >

                                            <i class="ri-eye-line"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>



                    <!-- =================================================
                         BASIC
                    ================================================== -->

                    <div class="card admin-write-card">

                        <div class="card-header">

                            <h3>
                                관리자 정보
                            </h3>

                        </div>


                        <div class="card-body">


                            <!-- NAME -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label for="name">

                                        이름

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="<?=admin_write_h($admin['name'])?>"
                                        maxlength="100"
                                        placeholder="관리자 이름"
                                    >

                                </div>

                            </div>



                            <!-- EMAIL -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label for="email">

                                        이메일

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="<?=admin_write_h($admin['email'])?>"
                                        maxlength="255"
                                        placeholder="admin@weekends.co.kr"
                                    >

                                </div>

                            </div>



                            <!-- ROLE -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label>

                                        관리자 권한

                                        <strong>
                                            *
                                        </strong>

                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <div class="admin-role-options">


                                        <label class="admin-role-option">

                                            <input
                                                type="radio"
                                                name="role"
                                                value="super"
                                                <?=$admin['role'] == 'super' ? 'checked' : ''?>
                                            >

                                            <span class="admin-role-option-box">

                                                <i class="ri-shield-keyhole-line"></i>

                                                <strong>
                                                    최고 관리자
                                                </strong>

                                                <small>
                                                    모든 관리자 기능 접근
                                                </small>

                                            </span>

                                        </label>



                                        <label class="admin-role-option">

                                            <input
                                                type="radio"
                                                name="role"
                                                value="admin"
                                                <?=$admin['role'] == 'admin' ? 'checked' : ''?>
                                            >

                                            <span class="admin-role-option-box">

                                                <i class="ri-admin-line"></i>

                                                <strong>
                                                    관리자
                                                </strong>

                                                <small>
                                                    일반 CMS 관리
                                                </small>

                                            </span>

                                        </label>



                                        <label class="admin-role-option">

                                            <input
                                                type="radio"
                                                name="role"
                                                value="editor"
                                                <?=$admin['role'] == 'editor' ? 'checked' : ''?>
                                            >

                                            <span class="admin-role-option-box">

                                                <i class="ri-edit-box-line"></i>

                                                <strong>
                                                    에디터
                                                </strong>

                                                <small>
                                                    콘텐츠 관리 중심
                                                </small>

                                            </span>

                                        </label>


                                    </div>


                                    <?
                                    if($is_me){
                                    ?>

                                        <p class="admin-write-warning">

                                            <i class="ri-information-line"></i>

                                            현재 로그인한 계정입니다. 자신의 권한 변경은 저장 시 제한됩니다.

                                        </p>

                                    <?
                                    }
                                    ?>

                                </div>

                            </div>



                            <!-- STATUS -->

                            <div class="admin-write-row">

                                <div class="admin-write-label">

                                    <label>
                                        상태
                                    </label>

                                </div>


                                <div class="admin-write-field">

                                    <div class="admin-status-options">


                                        <label>

                                            <input
                                                type="radio"
                                                name="status"
                                                value="active"
                                                <?=$admin['status'] == 'active' ? 'checked' : ''?>
                                            >

                                            <span>
                                                사용
                                            </span>

                                        </label>



                                        <label>

                                            <input
                                                type="radio"
                                                name="status"
                                                value="inactive"
                                                <?=$admin['status'] == 'inactive' ? 'checked' : ''?>
                                            >

                                            <span>
                                                사용중지
                                            </span>

                                        </label>



                                        <label>

                                            <input
                                                type="radio"
                                                name="status"
                                                value="locked"
                                                <?=$admin['status'] == 'locked' ? 'checked' : ''?>
                                            >

                                            <span>
                                                잠금
                                            </span>

                                        </label>


                                    </div>


                                    <?
                                    if($is_me){
                                    ?>

                                        <p class="admin-write-warning">

                                            <i class="ri-information-line"></i>

                                            현재 로그인한 자신의 계정은 사용중지 또는 잠금 처리할 수 없습니다.

                                        </p>

                                    <?
                                    }
                                    ?>

                                </div>

                            </div>


                        </div>

                    </div>


                </div>



                <!-- =================================================
                     SIDE
                ================================================== -->

                <div class="admin-write-side">


                    <div class="card admin-write-side-card">

                        <div class="card-header">

                            <h3>
                                계정 상태
                            </h3>

                        </div>


                        <div class="card-body">


                            <div class="admin-write-summary">

                                <div>

                                    <span>
                                        구분
                                    </span>

                                    <strong>

                                        <?=$is_edit ? '등록된 관리자' : '신규 관리자'?>

                                    </strong>

                                </div>


                                <?
                                if($is_edit){
                                ?>

                                    <div>

                                        <span>
                                            등록일
                                        </span>

                                        <strong>

                                            <?=admin_write_h(
                                                substr(
                                                    $admin['created_at'],
                                                    0,
                                                    16
                                                )
                                            )?>

                                        </strong>

                                    </div>



                                    <div>

                                        <span>
                                            최근 로그인
                                        </span>

                                        <strong>

                                            <?
                                            if(
                                                $admin['last_login_at'] != ''
                                            ){
                                            ?>

                                                <?=admin_write_h(
                                                    substr(
                                                        $admin['last_login_at'],
                                                        0,
                                                        16
                                                    )
                                                )?>

                                            <?
                                            }else{
                                            ?>

                                                -

                                            <?
                                            }
                                            ?>

                                        </strong>

                                    </div>



                                    <div>

                                        <span>
                                            최근 접속 IP
                                        </span>

                                        <strong>

                                            <?=$admin['last_login_ip'] != '' ? admin_write_h($admin['last_login_ip']) : '-'?>

                                        </strong>

                                    </div>



                                    <div>

                                        <span>
                                            로그인 실패
                                        </span>

                                        <strong>

                                            <?=number_format(
                                                intval(
                                                    $admin['failed_login_count']
                                                )
                                            )?>

                                            회

                                        </strong>

                                    </div>



                                    <div>

                                        <span>
                                            비밀번호 변경
                                        </span>

                                        <strong>

                                            <?
                                            if(
                                                $admin['password_changed_at'] != ''
                                            ){
                                            ?>

                                                <?=admin_write_h(
                                                    substr(
                                                        $admin['password_changed_at'],
                                                        0,
                                                        10
                                                    )
                                                )?>

                                            <?
                                            }else{
                                            ?>

                                                -

                                            <?
                                            }
                                            ?>

                                        </strong>

                                    </div>

                                <?
                                }
                                ?>

                            </div>


                        </div>

                    </div>



                    <button
                        type="submit"
                        class="btn btn-primary admin-write-save"
                        id="adminSave"
                    >

                        <i class="ri-save-line"></i>

                        <?=$is_edit ? '관리자 정보 저장' : '관리자 등록'?>

                    </button>


                    <a
                        href="/manage/admin_list"
                        class="btn btn-white admin-write-cancel"
                    >
                        취소
                    </a>


                </div>


            </div>

        </form>


    </div>

</main>



<script>

/*
 * =========================================================
 * ELEMENT
 * =========================================================
 */

var adminForm =
    document.getElementById(
        'adminForm'
    );


var adminSave =
    document.getElementById(
        'adminSave'
    );


var loginId =
    document.getElementById(
        'login_id'
    );


var adminName =
    document.getElementById(
        'name'
    );


var adminEmail =
    document.getElementById(
        'email'
    );


var password =
    document.getElementById(
        'password'
    );


var passwordConfirm =
    document.getElementById(
        'password_confirm'
    );



/*
 * =========================================================
 * PASSWORD TOGGLE
 * =========================================================
 */

var passwordToggles =
    document.querySelectorAll(
        '.admin-password-toggle'
    );


for(
    var i = 0;
    i < passwordToggles.length;
    i++
){

    passwordToggles[i].addEventListener(
        'click',
        function(){

            var targetId =
                this.getAttribute(
                    'data-target'
                );


            var target =
                document.getElementById(
                    targetId
                );


            if(!target){
                return;
            }


            var icon =
                this.querySelector(
                    'i'
                );


            if(
                target.type == 'password'
            ){

                target.type =
                    'text';


                if(icon){

                    icon.className =
                        'ri-eye-off-line';

                }

            }else{

                target.type =
                    'password';


                if(icon){

                    icon.className =
                        'ri-eye-line';

                }

            }

        }
    );

}



/*
 * =========================================================
 * LOGIN ID
 * =========================================================
 */

<?
if(!$is_edit){
?>

loginId.addEventListener(
    'input',
    function(){

        this.value =
            this.value
                .toLowerCase()
                .replace(
                    /[^a-z0-9_-]/g,
                    ''
                );

    }
);

<?
}
?>



/*
 * =========================================================
 * SUBMIT
 * =========================================================
 */

adminForm.addEventListener(
    'submit',
    function(e){

        e.preventDefault();


        /*
         * ID
         */

        if(
            loginId.value.trim() == ''
        ){

            alert(
                '관리자 아이디를 입력해주세요.',
                function(){

                    loginId.focus();

                }
            );

            return;

        }



        /*
         * NAME
         */

        if(
            adminName.value.trim() == ''
        ){

            alert(
                '관리자 이름을 입력해주세요.',
                function(){

                    adminName.focus();

                }
            );

            return;

        }



        /*
         * EMAIL
         */

        if(
            adminEmail.value.trim() == ''
        ){

            alert(
                '이메일을 입력해주세요.',
                function(){

                    adminEmail.focus();

                }
            );

            return;

        }



        /*
         * PASSWORD
         */

        var isEdit =
            <?=($is_edit ? 'true' : 'false')?>;


        if(
            !isEdit &&
            password.value == ''
        ){

            alert(
                '비밀번호를 입력해주세요.',
                function(){

                    password.focus();

                }
            );

            return;

        }



        if(
            password.value != '' &&
            password.value !=
            passwordConfirm.value
        ){

            alert(
                '비밀번호가 일치하지 않습니다.',
                function(){

                    passwordConfirm.focus();

                }
            );

            return;

        }



        /*
         * DUPLICATE REQUEST
         */

        if(
            adminSave.getAttribute(
                'data-loading'
            ) == 'Y'
        ){

            return;

        }


        adminSave.setAttribute(
            'data-loading',
            'Y'
        );


        adminSave.disabled =
            true;



        /*
         * AJAX
         */

        var formData =
            new FormData(
                adminForm
            );


        var xhr =
            new XMLHttpRequest();


        xhr.open(
            'POST',
            '/manage/proc/admin_save',
            true
        );


        xhr.onreadystatechange =
            function(){

                if(
                    xhr.readyState != 4
                ){
                    return;
                }


                adminSave.removeAttribute(
                    'data-loading'
                );


                adminSave.disabled =
                    false;


                if(
                    xhr.status != 200
                ){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                var result;


                try{

                    result =
                        JSON.parse(
                            xhr.responseText
                        );

                }catch(error){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                if(!result.code){

                    alert(
                        result.msg
                            ? result.msg
                            : '관리자 정보를 저장하지 못했습니다.'
                    );

                    return;

                }


                alert(
                    result.msg
                        ? result.msg
                        : '관리자 정보가 저장되었습니다.',
                    function(){

                        location.href =
                            '/manage/admin_list';

                    }
                );

            };


        xhr.send(
            formData
        );

    }
);

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>