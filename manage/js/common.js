/*!
 * ==========================================================
 * Weekends CMS - common.js
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", function () {

    const body = document.body;
    const sidebar = document.querySelector(".sidebar");
    const sidebarNav = document.querySelector(".sidebar-nav");
    const overlay = document.querySelector(".sidebar-overlay");
    const toggle = document.querySelector(".sidebar-toggle");

    /* ==========================
       Sidebar Toggle
    ========================== */

    if (toggle) {

        toggle.addEventListener("click", function () {

            if (window.innerWidth <= 1024) {

                body.classList.toggle("mobile-open");

            } else {

                
				if(body.classList.toggle("sidebar-collapse")){
					sidebarNav.querySelector('li.open').classList.remove('open');
				}else{
					sidebarNav.querySelector('li:has(.active)').classList.add('open');
				}
            }

        });

    }

    /* ==========================
       Overlay Close
    ========================== */

    if (overlay) {

        overlay.addEventListener("click", function () {

            body.classList.remove("mobile-open");

        });

    }

    /* ==========================
       ESC Close
    ========================== */

    document.addEventListener("keydown", function (e) {

        if (e.key === "Escape") {

            body.classList.remove("mobile-open");

        }

    });

    /* ==========================
       Accordion Menu
    ========================== */

    const accordionMenus = document.querySelectorAll(".has-sub > a");

    accordionMenus.forEach(function (menu) {

        menu.addEventListener("click", function (e) {

            e.preventDefault();

            const parent = this.parentElement;

            const opened = parent.classList.contains("open");

            document.querySelectorAll(".has-sub.open").forEach(function (item) {

                if (item !== parent) {

                    item.classList.remove("open");

                }

            });

            if (opened) {

                parent.classList.remove("open");

            } else {

                parent.classList.add("open");

            }

        });

    });

    /* ==========================
       Active Menu
    ========================== */

    const currentPage = location.pathname.split("/").pop();

    document.querySelectorAll(".sidebar a[href]").forEach(function (link) {

        const href = link.getAttribute("href");

        if (!href || href === "#") return;

        if (href === currentPage) {

            const li = link.closest("li");

            if (li) {

                li.classList.add("active");

            }

            const subMenu = link.closest(".sub-menu");

            if (subMenu) {

                const parent = subMenu.closest(".has-sub");

                if (parent) {

                    parent.classList.add("open");
                    parent.classList.add("active");

                }

            }

        }

    });

    /* ==========================
       Resize
    ========================== */

    window.addEventListener("resize", function () {

        if (window.innerWidth > 1024) {

            body.classList.remove("mobile-open");

        }

    });

});


/**
 * ==========================================================
 *  Common List Script
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {

    initCheckAll();
    initTableHover();
    initPageSize();

});

/*----------------------------------------------------------
    전체선택
----------------------------------------------------------*/

function initCheckAll(){

    const checkAll = document.querySelector('.check-all');

    if(!checkAll) return;

    const checks = document.querySelectorAll('.row-check');

    checkAll.addEventListener('change', function(){

        checks.forEach(item=>{
            item.checked = this.checked;
        });

    });

}

/*----------------------------------------------------------
    행 클릭효과
----------------------------------------------------------*/

function initTableHover(){

    document.querySelectorAll('.table-list tbody tr').forEach(tr=>{

        tr.addEventListener('click', function(e){

            if(
                e.target.tagName === 'INPUT' ||
                e.target.tagName === 'BUTTON' ||
                e.target.tagName === 'A'
            ) return;

            const checkbox = this.querySelector('.row-check');

            if(!checkbox) return;

            checkbox.checked = !checkbox.checked;

        });

    });

}

/*----------------------------------------------------------
    페이지당 보기
----------------------------------------------------------*/

function initPageSize(){

    const select = document.querySelector('.page-size');

    if(!select) return;

    select.addEventListener('change', function(){

        console.log('Page Size : ', this.value);

        /*
            실제 적용

            location.href =
                '?page=1&pageSize=' + this.value;

        */

    });

}


(function(window, document){

    'use strict';


    /*
     * =====================================================
     * 기존 Native 함수 보관
     * =====================================================
     */

    var nativeAlert =
        window.alert;

    var nativeConfirm =
        window.confirm;


    /*
     * WEEKENDS Logo
     *
     * 실제 로고 경로가 다르면
     * 이 값만 변경
     */

    var COMMON_MODAL_LOGO =
        '/@resource/images/@common/logo.svg';



    /*
     * =====================================================
     * Modal 생성
     * =====================================================
     */

    function createCommonModal()
    {

        var modal =
            document.getElementById(
                'common-modal'
            );


        if(modal){
            return modal;
        }


        /*
         * body 생성 전 호출된 경우
         */

        if(!document.body){
            return null;
        }


        var html = '';


        html +=
            '<div ' +
                'class="common-modal" ' +
                'id="common-modal" ' +
                'aria-hidden="true"' +
            '>';


        html +=
                '<div ' +
                    'class="common-modal-wrap" ' +
                    'role="dialog" ' +
                    'aria-modal="true"' +
                '>';


        /*
         * 닫기
         */

        html +=
                    '<button ' +
                        'type="button" ' +
                        'class="common-modal-close" ' +
                        'id="common-modal-close" ' +
                        'aria-label="닫기"' +
                    '>';

        html +=
                        '<i class="ri-close-line"></i>';

        html +=
                    '</button>';



        /*
         * WEEKENDS Logo
         */

        html +=
                    '<div ' +
                        'class="common-modal-logo"' +
                    '>';

        html +=
                        '<img ' +
                            'src="' + COMMON_MODAL_LOGO + '" ' +
                            'alt="WEEKENDS"' +
                        '>';

        html +=
                    '</div>';



        /*
         * Message
         *
         * 별도의 "알림", "확인" 타이틀 없음
         * alert() / confirm()에 전달한 내용이
         * 그대로 메인 문구가 됨
         */

        html +=
                    '<div ' +
                        'class="common-modal-title" ' +
                        'id="common-modal-title"' +
                    '>';

        html +=
                    '</div>';



        /*
         * Buttons
         */

        html +=
                    '<div ' +
                        'class="common-modal-buttons" ' +
                        'id="common-modal-buttons"' +
                    '>';

        html +=
                    '</div>';


        html +=
                '</div>';


        html +=
            '</div>';



        var wrap =
            document.createElement(
                'div'
            );


        wrap.innerHTML = html;


        document.body.appendChild(
            wrap.firstChild
        );


        modal =
            document.getElementById(
                'common-modal'
            );


        /*
         * Callback 저장
         */

        modal._okCallback =
            null;

        modal._cancelCallback =
            null;


        return modal;
    }



    /*
     * =====================================================
     * Modal 닫기
     * =====================================================
     */

    function closeCommonModal()
    {

        var modal =
            document.getElementById(
                'common-modal'
            );


        if(!modal){
            return;
        }


        modal.classList.remove(
            'is-open'
        );


        modal.classList.remove(
            'confirm'
        );


        modal.setAttribute(
            'aria-hidden',
            'true'
        );

    }



    /*
     * =====================================================
     * Alert Override
     *
     * alert('내용');
     *
     * alert(
     *     '내용',
     *     function(){
     *         // 확인 후 실행
     *     }
     * );
     * =====================================================
     */

    window.alert = function(
        messageText,
        callback
    ){

        var modal =
            createCommonModal();


        /*
         * Modal 생성 불가 시
         * Native Alert
         */

        if(!modal){

            nativeAlert(
                messageText
            );


            if(
                typeof callback ===
                'function'
            ){

                callback();

            }


            return;
        }


        var title =
            document.getElementById(
                'common-modal-title'
            );


        var buttons =
            document.getElementById(
                'common-modal-buttons'
            );


        /*
         * Alert Style
         */

        modal.classList.remove(
            'confirm'
        );


        /*
         * 전달된 메시지를
         * 메인 문구로 사용
         */

        title.innerHTML =
            messageText;


        /*
         * 확인 버튼
         */

        buttons.innerHTML =

            '<button ' +
                'type="button" ' +
                'class="common-modal-button primary" ' +
                'id="common-modal-ok"' +
            '>' +

                '확인' +

            '</button>';


        /*
         * Callback
         */

        modal._okCallback =
            typeof callback ===
            'function'
                ? callback
                : null;


        modal._cancelCallback =
            null;


        /*
         * Open
         */

        modal.classList.add(
            'is-open'
        );


        modal.setAttribute(
            'aria-hidden',
            'false'
        );


        /*
         * 확인 버튼 Focus
         */

        setTimeout(
            function(){

                var ok =
                    document.getElementById(
                        'common-modal-ok'
                    );


                if(ok){

                    ok.focus();

                }

            },
            50
        );

    };



    /*
     * =====================================================
     * Confirm Override
     *
     * confirm(
     *     '삭제하시겠습니까?',
     *     function(){
     *
     *         // 확인
     *
     *     }
     * );
     *
     *
     * 취소 Callback 필요 시
     *
     * confirm(
     *     '삭제하시겠습니까?',
     *
     *     function(){
     *         // 확인
     *     },
     *
     *     function(){
     *         // 취소
     *     }
     * );
     * =====================================================
     */

    window.confirm = function(
        messageText,
        callback,
        cancelCallback
    ){

        var modal =
            createCommonModal();


        /*
         * Modal 생성 불가 시
         * Native Confirm
         */

        if(!modal){

            var nativeResult =
                nativeConfirm(
                    messageText
                );


            if(
                nativeResult &&
                typeof callback ===
                'function'
            ){

                callback();

            }


            if(
                !nativeResult &&
                typeof cancelCallback ===
                'function'
            ){

                cancelCallback();

            }


            return;
        }


        var title =
            document.getElementById(
                'common-modal-title'
            );


        var buttons =
            document.getElementById(
                'common-modal-buttons'
            );


        /*
         * Confirm Style
         */

        modal.classList.add(
            'confirm'
        );


        /*
         * 전달된 메시지를
         * 메인 문구로 사용
         */

        title.innerHTML =
            messageText;


        /*
         * 취소 / 확인
         */

        buttons.innerHTML =

            '<button ' +
                'type="button" ' +
                'class="common-modal-button secondary" ' +
                'id="common-modal-cancel"' +
            '>' +

                '취소' +

            '</button>' +


            '<button ' +
                'type="button" ' +
                'class="common-modal-button primary" ' +
                'id="common-modal-ok"' +
            '>' +

                '확인' +

            '</button>';


        /*
         * Callback
         */

        modal._okCallback =
            typeof callback ===
            'function'
                ? callback
                : null;


        modal._cancelCallback =
            typeof cancelCallback ===
            'function'
                ? cancelCallback
                : null;


        /*
         * Open
         */

        modal.classList.add(
            'is-open'
        );


        modal.setAttribute(
            'aria-hidden',
            'false'
        );


        /*
         * 확인 버튼 Focus
         */

        setTimeout(
            function(){

                var ok =
                    document.getElementById(
                        'common-modal-ok'
                    );


                if(ok){

                    ok.focus();

                }

            },
            50
        );

    };



    /*
     * =====================================================
     * Click Event
     * =====================================================
     */

    document.addEventListener(
        'click',
        function(e){

            var modal =
                document.getElementById(
                    'common-modal'
                );


            if(!modal){
                return;
            }


            var target =
                e.target;


            /*
             * 닫기 아이콘 내부 <i> 클릭 대응
             */

            if(
                target.parentNode &&
                target.parentNode.id ==
                    'common-modal-close'
            ){

                target =
                    target.parentNode;

            }



            /*
             * =================================================
             * 확인
             * =================================================
             */

            if(
                target.id ==
                'common-modal-ok'
            ){

                var callback =
                    modal._okCallback;


                modal._okCallback =
                    null;

                modal._cancelCallback =
                    null;


                closeCommonModal();


                if(
                    typeof callback ===
                    'function'
                ){

                    callback();

                }


                return;
            }



            /*
             * =================================================
             * 취소
             * =================================================
             */

            if(
                target.id ==
                'common-modal-cancel'
            ){

                var cancelCallback =
                    modal._cancelCallback;


                modal._okCallback =
                    null;

                modal._cancelCallback =
                    null;


                closeCommonModal();


                if(
                    typeof cancelCallback ===
                    'function'
                ){

                    cancelCallback();

                }


                return;
            }



            /*
             * =================================================
             * X 닫기
             * =================================================
             */

            if(
                target.id ==
                'common-modal-close'
            ){

                var closeCallback =
                    modal._cancelCallback;


                modal._okCallback =
                    null;

                modal._cancelCallback =
                    null;


                closeCommonModal();


                if(
                    typeof closeCallback ===
                    'function'
                ){

                    closeCallback();

                }


                return;
            }

        }
    );



    /*
     * =====================================================
     * Keyboard Event
     * =====================================================
     */

    document.addEventListener(
        'keydown',
        function(e){

            var modal =
                document.getElementById(
                    'common-modal'
                );


            if(
                !modal ||
                !modal.classList.contains(
                    'is-open'
                )
            ){

                return;
            }



            /*
             * ESC
             */

            if(
                e.keyCode == 27
            ){

                var cancelCallback =
                    modal._cancelCallback;


                modal._okCallback =
                    null;

                modal._cancelCallback =
                    null;


                closeCommonModal();


                if(
                    typeof cancelCallback ===
                    'function'
                ){

                    cancelCallback();

                }


                return;
            }



            /*
             * Enter
             */

            if(
                e.keyCode == 13
            ){

                var ok =
                    document.getElementById(
                        'common-modal-ok'
                    );


                if(ok){

                    ok.click();

                }

            }

        }
    );


})(window, document);