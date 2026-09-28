<?php
// Common initialization belongs in the head, as in the existing head.php.
include $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
if (isset($projectPrepare) && is_callable($projectPrepare)) { $projectPrepare($db); }
$headH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$headDefaults = array('title'=>'WEEKENDS','description'=>'','keywords'=>'','canonical'=>'',
    'og_title'=>'Weekends | 위켄즈','og_description'=>'','image'=>'/@resource/images/opengraph.jpg','robots'=>'noindex, nofollow');
$projectSeo = array_merge($headDefaults, isset($projectSeo) && is_array($projectSeo) ? $projectSeo : array());
// Deployment host allowlist prevents untrusted Host headers appearing in SEO URLs.
$headHost = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
$headAllowed = array('weekends.hanbom.com','weekends.co.kr','www.weekends.co.kr');
if (!in_array($headHost, $headAllowed, true)) { $headHost = 'weekends.co.kr'; }
$headHttps = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off';
$headOrigin = ($headHost === 'weekends.hanbom.com' && !$headHttps ? 'http://' : 'https://').$headHost;
$headAbsolute = function ($url) use ($headOrigin) {
    if ($url === '') { return ''; }
    return substr($url, 0, 1) === '/' ? $headOrigin.$url : $url;
};
$headCanonical = $headAbsolute($projectSeo['canonical']);
$headImage = $headAbsolute($projectSeo['image']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <title><?=$headH($projectSeo['title'])?></title>
    <meta name="description" content="<?=$headH($projectSeo['description'])?>">
    <meta name="keywords" content="<?=$headH($projectSeo['keywords'])?>">
    <meta name="author" content="Weekends | 위켄즈">
    <meta name="robots" content="<?=$headH($projectSeo['robots'])?>">
    <?php if ($headCanonical !== '') { ?><link rel="canonical" href="<?=$headH($headCanonical)?>"><?php } ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?=$headH($projectSeo['og_title'])?>">
    <meta property="og:description" content="<?=$headH($projectSeo['og_description'])?>">
    <meta property="og:image" content="<?=$headH($headImage)?>">
    <meta property="og:url" content="<?=$headH($headCanonical)?>">
    <meta property="og:site_name" content="Weekends | 위켄즈">
    <meta property="og:locale" content="ko_KR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?=$headH($projectSeo['og_title'])?>">
    <meta name="twitter:description" content="<?=$headH($projectSeo['og_description'])?>">
    <meta name="twitter:image" content="<?=$headH($headImage)?>">
    <link rel="icon" href="/@resource/images/favicon.ico" sizes="any">
    <link rel="icon" href="/@resource/images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="/@resource/images/favicon.ico">
    <link rel="stylesheet" href="/@resource/css/lib/swiper-bundle.min.css">
    <link rel="stylesheet" href="/@resource/css/lib/lenis.css">
    <link rel="stylesheet" href="/@resource/css/weekends.css">
    <link rel="stylesheet" href="/@resource/css/weekends_dev.css">
    <script src="/@resource/js/lib/jquery-4.0.0.min.js"></script>
    <script src="/@resource/js/weekends.js"></script>
</head>
<body>
    <div id="wrap" class="wrap">
        <div id="container" class="container">
            <div class="skip-nav"><a href="#content"><span>본문바로가기</span></a></div>
            <header id="header" class="header">
                <div class="header-inner is-full"><div class="gutter">
                    <div class="header-left"><h1 class="logo"><a href="/" class="pack-basic">
                        <i class="ico mask-logo jello"></i><i class="ico mask-logo_mo jello"></i><span class="blind">Weekends | 위켄즈</span>
                    </a></h1></div>
                    <nav id="nav" class="nav">
                        <ul class="gnb-list">
                            <li><a href="/about" class="depth-1"><span class="split"><span>About</span></span></a></li>
                            <li><a href="/project" class="depth-1"><span class="split"><span>Project</span></span></a></li>
                            <li><a href="/films" class="depth-1"><span class="split"><span>Films</span></span></a></li>
                            <li><a href="/news" class="depth-1"><span class="split"><span>News</span></span></a></li>
                            <li><a href="/contact" class="depth-1"><span class="split"><span>Contact</span></span></a></li>
                        </ul>
                        <div class="utils-wrap pack-down items-center">
                            <a href="/contact" class="btn solid-w gap-10 large fc-ntr-700 w-full fs-18-16"><span>간편한 문의하기</span><i class="ico arrow-r"></i></a>
                            <p class="copy fc-white lh125 fs-18-16 pack-center w-full"><span class="split"><span>© Weekends Inc.</span></span></p>
                        </div>
                    </nav>
                    <div class="header-right"><div class="button-area util-wrap">
                        <button type="button" class="btn menu"><i class="ico mask-menu"></i><i class="ico mask-close"></i></button>
                    </div></div>
                </div></div>
            </header>
