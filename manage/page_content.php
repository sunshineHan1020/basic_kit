<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';
header('Cache-Control: no-store');
$contentEditorGroup = isset($_GET['page']) && $_GET['page'] === 'main' ? 'main' : 'about';
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>
<main class="content"><div class="content-inner">
    <h2><?=$contentEditorGroup === 'main' ? '메인' : '어바웃'?> 문구 관리</h2>
    <p><a href="/manage/main_exhibition_list">메인 전시 관리</a> · <a href="/manage/member_list">멤버 관리</a></p>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/content_editor.php'; ?>
</div></main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php'; ?>
