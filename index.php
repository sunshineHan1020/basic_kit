<?php
/*****************************
    기본 라우터
*****************************/

// Include router class
require $_SERVER['DOCUMENT_ROOT'].'/src/Router.php';



// ========================================
// 메인
// ========================================

Router::add('GET', '/', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/main.php';

});


Router::add('GET', '/main', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/main.php';

});



// ========================================
// ABOUT
// ========================================

Router::add('GET', '/about', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/about.php';

});



// ========================================
// PROJECT
// 관리자 WORKS
// ========================================

Router::add('GET', '/project', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/project.php';

});


Router::add('GET', '/project/([^/]+)', function($slug) {

    $project_slug =
        rawurldecode(
            $slug
        );


    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/project_view.php';

});



// ========================================
// FILMS
// ========================================

Router::add('GET', '/films', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/films.php';

});



// ========================================
// NEWS
// 관리자 BLOG
// ========================================

Router::add('GET', '/news', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/news.php';

});


Router::add('GET', '/news/([^/]+)', function($slug) {

    $news_slug =
        rawurldecode(
            $slug
        );


    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/news_view.php';

});



// ========================================
// CONTACT
// ========================================

Router::add('GET', '/contact', function() {

    require $_SERVER['DOCUMENT_ROOT'].'/src/controller/contact.php';

});



// ========================================
// 404
// ========================================

Router::pathNotFound(function($path) {

    http_response_code(404);

    require $_SERVER['DOCUMENT_ROOT'].'/src/view/404.php';

});



// ========================================
// 405
// ========================================

Router::methodNotAllowed(function($path, $method) {

    http_response_code(405);

    require $_SERVER['DOCUMENT_ROOT'].'/src/view/error/405.php';

});



// ========================================
// Router 실행
// ========================================

Router::run();
