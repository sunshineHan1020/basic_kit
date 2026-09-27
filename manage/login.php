<?

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';


/*
 * 이미 로그인 되어 있으면 관리자 메인으로 이동
 */
if(
    isset($_SESSION['ADMIN_ID']) &&
    $_SESSION['ADMIN_ID']
){
    header('Location: /manage/');
    exit;
}


/*
 * CSRF Token
 * PHP 5.4 호환
 */
if(
    !isset($_SESSION['ADMIN_LOGIN_TOKEN']) ||
    !$_SESSION['ADMIN_LOGIN_TOKEN']
){
    $_SESSION['ADMIN_LOGIN_TOKEN'] = md5(
        uniqid(mt_rand(), true)
    );
}



$login_token = $_SESSION['ADMIN_LOGIN_TOKEN'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>

<meta charset="utf-8">
<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<title>WEEKENDS CMS</title>

<link
    rel="stylesheet"
    href="/manage/css/layout.css"
>

<link
    rel="stylesheet"
    href="/manage/css/common.css"
>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css"
>

<style>

/* =========================================================
   Login
   ========================================================= */

html,
body {
    width: 100%;
    height: 100%;
    min-height: 100%;
}

body {
    margin: 0;

    display: block;

    background: #f5f5f7;

    color: #222;

    font-family:
        "Pretendard",
        "Noto Sans KR",
        sans-serif;
}

.login-wrap {
    width: 100%;
    min-height: 100vh;

    display: grid;

    grid-template-columns:
        minmax(420px, 46%)
        minmax(520px, 54%);
}


/* ---------------------------------------------------------
   Brand
   --------------------------------------------------------- */

.login-brand {
    position: relative;

    min-height: 100vh;

    padding: 64px;

    display: flex;
    flex-direction: column;
    justify-content: space-between;

    overflow: hidden;

    background: #111316;

    color: #fff;
}

.login-brand::before {
    content: "";

    position: absolute;

    top: -180px;
    right: -180px;

    width: 520px;
    height: 520px;

    border: 1px solid rgba(255,255,255,.07);
    border-radius: 50%;
}

.login-brand::after {
    content: "";

    position: absolute;

    left: -220px;
    bottom: -300px;

    width: 620px;
    height: 620px;

    border: 1px solid rgba(255,255,255,.05);
    border-radius: 50%;
}

.login-brand-logo {
    position: relative;
    z-index: 2;

    width: 190px;
}

.login-brand-logo img {
    width: 100%;
    height: auto;
}

.login-brand-content {
    position: relative;
    z-index: 2;

    max-width: 520px;
}

.login-brand-label {
    display: block;

    margin-bottom: 20px;

    color: #888;

    font-size: 12px;
    font-weight: 600;

    letter-spacing: .18em;
}

.login-brand-content h1 {
    margin: 0;

    font-size: 42px;
    line-height: 1.15;
    font-weight: 700;

    letter-spacing: -.045em;
}

.login-brand-content p {
    margin: 24px 0 0;

    color: #999;

    font-size: 14px;
    line-height: 1.8;
}

.login-brand-footer {
    position: relative;
    z-index: 2;

    color: #555;

    font-size: 12px;
}


/* ---------------------------------------------------------
   Login Area
   --------------------------------------------------------- */

.login-main {
    min-height: 100vh;

    padding: 60px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #fff;
}

.login-box {
    width: 100%;
    max-width: 420px;
}

.login-head {
    margin-bottom: 42px;
}

.login-head .eyebrow {
    display: block;

    margin-bottom: 12px;

    color: #999;

    font-size: 12px;
    font-weight: 600;

    letter-spacing: .12em;
}

.login-head h2 {
    margin: 0;

    color: #111;

    font-size: 32px;
    line-height: 1.25;
    font-weight: 700;

    letter-spacing: -.04em;
}

.login-head p {
    margin: 12px 0 0;

    color: #888;

    font-size: 14px;
    line-height: 1.6;
}


/* ---------------------------------------------------------
   Form
   --------------------------------------------------------- */

.login-form .form-group {
    margin-bottom: 20px;
}

.login-form .form-label {
    display: block;

    margin-bottom: 9px;

    color: #333;

    font-size: 13px;
    font-weight: 600;
}

.login-input {
    position: relative;
}

.login-input i {
    position: absolute;

    top: 50%;
    left: 15px;

    z-index: 2;

    color: #aaa;

    font-size: 18px;

    transform: translateY(-50%);
}

.login-input .form-control {
    height: 52px;

    padding-left: 45px;
    padding-right: 45px;

    border-radius: 8px;

    font-size: 14px;
}

.login-input .password-toggle {
    position: absolute;

    top: 50%;
    right: 12px;

    width: 34px;
    height: 34px;

    padding: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #aaa;

    transform: translateY(-50%);
}

.login-input .password-toggle:hover {
    color: #222;
}

.login-input .password-toggle i {
    position: static;

    transform: none;
}


/* ---------------------------------------------------------
   Option
   --------------------------------------------------------- */

.login-option {
    margin: 4px 0 28px;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.login-checkbox {
    display: inline-flex;
    align-items: center;

    gap: 7px;

    color: #777;

    font-size: 13px;

    cursor: pointer;
}

.login-checkbox input {
    width: 16px;
    height: 16px;

    margin: 0;

    accent-color: #111;
}


/* ---------------------------------------------------------
   Button
   --------------------------------------------------------- */

.login-submit {
    width: 100%;
    height: 52px;

    border-radius: 8px;

    background: #111316;

    color: #fff;

    font-size: 14px;
    font-weight: 600;

    transition: .2s;
}

.login-submit:hover {
    background: #282b30;
}

.login-submit:disabled {
    opacity: .55;
    cursor: default;
}


/* ---------------------------------------------------------
   Message
   --------------------------------------------------------- */

.login-message {
    display: none;

    margin-bottom: 18px;
    padding: 13px 15px;

    border-radius: 7px;

    font-size: 13px;
    line-height: 1.5;
}

.login-message.error {
    display: block;

    color: #c93535;

    background: #fff3f3;
    border: 1px solid #f4d2d2;
}

.login-message.success {
    display: block;

    color: #187743;

    background: #f1faf5;
    border: 1px solid #cdebd9;
}


/* ---------------------------------------------------------
   Bottom
   --------------------------------------------------------- */

.login-info {
    margin-top: 28px;

    color: #aaa;

    font-size: 12px;
    line-height: 1.6;

    text-align: center;
}


/* ---------------------------------------------------------
   Loading
   --------------------------------------------------------- */

.login-submit .loading-icon {
    display: none;

    font-size: 18px;

    animation: loginSpin .8s linear infinite;
}

.login-submit.loading .loading-icon {
    display: inline-block;
}

.login-submit.loading .button-text {
    display: none;
}

@keyframes loginSpin {

    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }

}


/* ---------------------------------------------------------
   Responsive
   --------------------------------------------------------- */

@media (max-width: 1000px) {

    .login-wrap {
        grid-template-columns: 1fr;
    }

    .login-brand {
        display: none;
    }

    .login-main {
        padding: 40px 24px;
    }

}


@media (max-width: 480px) {

    .login-main {
        padding: 30px 20px;
    }

    .login-box {
        max-width: none;
    }

    .login-head {
        margin-bottom: 34px;
    }

    .login-head h2 {
        font-size: 28px;
    }

}

</style>
<script src="/manage/js/common.js"></script>
</head>

<body>

<div class="login-wrap">


    <!-- =====================================================
        Brand
    ====================================================== -->

    <section class="login-brand">

        <div class="login-brand-logo">

            <img
                src="/manage/images/logo-white.svg"
                alt="WEEKENDS"
            >

        </div>


        <div class="login-brand-content">

            <span class="login-brand-label">
                WEEKENDS CONTENT MANAGEMENT SYSTEM
            </span>

            <h1>
                Make good things<br>
                happen.
            </h1>

            <p>
                WEEKENDS 웹사이트의 콘텐츠와<br>
                서비스를 관리하는 관리자 시스템입니다.
            </p>

        </div>


        <div class="login-brand-footer">

            © <?=date('Y')?> WEEKENDS.
            All rights reserved.

        </div>

    </section>



    <!-- =====================================================
        Login
    ====================================================== -->

    <main class="login-main">

        <div class="login-box">

            <div class="login-head">

                <span class="eyebrow">
                    ADMINISTRATOR
                </span>

                <h2>관리자 로그인</h2>

                <p>
                    관리자 계정으로 로그인해주세요.
                </p>

            </div>


            <div
                class="login-message"
                id="loginMessage"
            ></div>


            <form
                id="loginForm"
                class="login-form"
                method="post"
                autocomplete="off"
            >

                <input
                    type="hidden"
                    name="token"
                    value="<?=$login_token?>"
                >


                <!-- ID -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="login_id"
                    >
                        아이디
                    </label>

                    <div class="login-input">

                        <i class="ri-user-3-line"></i>

                        <input
                            type="text"
                            class="form-control"
                            id="login_id"
                            name="login_id"
                            placeholder="아이디를 입력해주세요."
                            autocomplete="username"
                        >

                    </div>

                </div>



                <!-- Password -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="password"
                    >
                        비밀번호
                    </label>

                    <div class="login-input">

                        <i class="ri-lock-2-line"></i>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="비밀번호를 입력해주세요."
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            tabindex="-1"
                            aria-label="비밀번호 보기"
                        >
                            <i class="ri-eye-line"></i>
                        </button>

                    </div>

                </div>



                <!-- Option -->

                <div class="login-option">

                    <label class="login-checkbox">

                        <input
                            type="checkbox"
                            name="save_id"
                            id="save_id"
                            value="Y"
                        >

                        <span>아이디 저장</span>

                    </label>

                </div>



                <!-- Login Button -->

                <button
                    type="submit"
                    class="login-submit"
                    id="loginButton"
                >

                    <i
                        class="ri-loader-4-line loading-icon"
                    ></i>

                    <span class="button-text">
                        로그인
                    </span>

                </button>

            </form>


            <div class="login-info">

                관리자 계정 관련 문의는
                시스템 담당자에게 문의해주세요.

            </div>

        </div>

    </main>

</div>


<script>

(function(){


    var form = document.getElementById('loginForm');

    var loginId = document.getElementById('login_id');
    var password = document.getElementById('password');

    var loginButton = document.getElementById('loginButton');
    var loginMessage = document.getElementById('loginMessage');

    var passwordToggle =
        document.getElementById('passwordToggle');


    /* -----------------------------------------------------
       Password Toggle
    ----------------------------------------------------- */

    passwordToggle.onclick = function(){

        if(password.type == 'password'){

            password.type = 'text';

            passwordToggle.innerHTML =
                '<i class="ri-eye-off-line"></i>';

        }else{

            password.type = 'password';

            passwordToggle.innerHTML =
                '<i class="ri-eye-line"></i>';

        }

    };


    /* -----------------------------------------------------
       Message
    ----------------------------------------------------- */

    function showMessage(message, type){

        loginMessage.className =
            'login-message ' + type;

        loginMessage.innerHTML = message;

    }


    function hideMessage(){

        loginMessage.className = 'login-message';
        loginMessage.innerHTML = '';

    }


    /* -----------------------------------------------------
       Submit
    ----------------------------------------------------- */

    form.onsubmit = function(e){

        e.preventDefault();

        hideMessage();


        if(!loginId.value){

            showMessage(
                '아이디를 입력해주세요.',
                'error'
            );

            loginId.focus();

            return false;
        }


        if(!password.value){

            showMessage(
                '비밀번호를 입력해주세요.',
                'error'
            );

            password.focus();

            return false;
        }


        loginButton.disabled = true;
        loginButton.className =
            'login-submit loading';


        var xhr = new XMLHttpRequest();

        xhr.open(
            'POST',
            '/manage/proc/login_proc',
            true
        );


        xhr.onreadystatechange = function(){

            if(xhr.readyState != 4){
                return;
            }


            loginButton.disabled = false;
            loginButton.className = 'login-submit';


            if(xhr.status != 200){

                showMessage(
                    '로그인 처리 중 오류가 발생했습니다.',
                    'error'
                );

                return;
            }


            var result = null;


            try{

                result = JSON.parse(xhr.responseText);

            }catch(e){

                showMessage(
                    '로그인 처리 중 오류가 발생했습니다.',
                    'error'
                );

                return;
            }


            if(result.code){

                showMessage(
                    result.msg
                        ? result.msg
                        : '로그인 되었습니다.',
                    'success'
                );


                setTimeout(function(){

                    if(result.url){

                        location.href = result.url;

                    }else{

                        location.href = '/manage/';

                    }

                }, 300);


                return;
            }


            showMessage(
                result.msg
                    ? result.msg
                    : '아이디 또는 비밀번호를 확인해주세요.',
                'error'
            );

        };


        xhr.send(
            new FormData(form)
        );


        return false;

    };


    /* -----------------------------------------------------
       Enter
    ----------------------------------------------------- */

    loginId.focus();

})();

</script>

</body>
</html>