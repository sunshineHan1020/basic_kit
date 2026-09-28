<?php
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';
$newsH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$newsList = array(); $newsListError = false;
try {
    $sql = "SELECT p.id, p.slug, p.title, p.display_at, p.publish_at, p.created_at, c.name AS category_name,
        m.storage_key, m.default_alt_text
        FROM posts p INNER JOIN boards b ON b.id = p.board_id AND b.code = ? AND b.is_active = 1
        LEFT JOIN post_categories c ON c.id = p.category_id AND c.board_id = p.board_id AND c.is_active = 1
        LEFT JOIN post_media pm ON pm.id = (SELECT pm2.id FROM post_media pm2
            INNER JOIN media m2 ON m2.id = pm2.media_id AND m2.deleted_at IS NULL
            WHERE pm2.post_id = p.id AND pm2.role = 'thumbnail' ORDER BY pm2.sort_order ASC, pm2.id ASC LIMIT 1)
        LEFT JOIN media m ON m.id = pm.media_id AND m.deleted_at IS NULL
        WHERE p.status = 'published' AND p.visibility = 'public' AND p.deleted_at IS NULL
        AND (p.publish_at IS NULL OR p.publish_at <= NOW())
        ORDER BY p.is_pinned DESC, p.display_at DESC, p.id DESC";
    $statement = pdo_query($db, $sql, array('BLOG'));
    if ($statement === false) { throw new RuntimeException('News list query failed.'); }
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        if (!is_string($row['slug']) || $row['slug'] === '' || preg_match('/[\x00-\x20\x7F\/\\\\]/', $row['slug'])) { continue; }
        $image = '/'.ltrim((string)$row['storage_key'], '/');
        $row['image_url'] = strpos($image, '/uploads/') === 0 && strpos($image, '..') === false
            && !preg_match('/[\x00-\x20\x7F\\\\?#]/', $image) ? $image : '';
        $row['alt'] = $row['default_alt_text'] ? $row['default_alt_text'] : $row['title'];
        $row['url'] = '/news/'.rawurlencode($row['slug']);
        $row['date'] = '';
        foreach (array('display_at','publish_at','created_at') as $dateField) {
            if (!empty($row[$dateField]) && substr($row[$dateField], 0, 10) !== '0000-00-00') {
                $row['date'] = str_replace('-', '.', substr($row[$dateField], 0, 10)); break;
            }
        }
        $newsList[] = $row;
    }
} catch (Exception $e) { error_log('News list: '.$e->getMessage()); $newsList = array(); $newsListError = true; }
include $_SERVER['DOCUMENT_ROOT'].'/src/view/news.php';
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';
?>

<!-- News scripts -->
<script src="/@resource/js/lib/swiper-bundle.min.js"></script>
<script src="/@resource/js/lib/gsap.min.js"></script>
<script src="/@resource/js/lib/ScrollTrigger.min.js"></script>
<script src="/@resource/js/lib/SplitText.min.js"></script>
<script src="/@resource/js/lib/lenis.min.js"></script>
<script src="/@resource/js/motion.js"></script>
<script>
(function () {
    'use strict';
    function initNewsPage() {
        var page = document.querySelector('.sub.news');
        if (!page || page.dataset.newsReady === '1') return;
        page.dataset.newsReady = '1';
        var isDetail = page.classList.contains('detail');
        var hasGsap = typeof window.gsap !== 'undefined';
        var hasScroll = typeof window.ScrollTrigger !== 'undefined';
        var started = false;
        var layoutStarted = false;
        var raf = 0;
        function refresh() {
            if (raf) return;
            raf = requestAnimationFrame(function () {
                raf = 0;
                if (window.lenis && typeof window.lenis.resize === 'function') window.lenis.resize();
                if (hasScroll) window.ScrollTrigger.refresh();
            });
        }
        function optional(name, arg) { if (typeof window[name] === 'function') window[name](arg); }
        if (hasGsap) {
            if (hasScroll) window.gsap.registerPlugin(window.ScrollTrigger);
            if (window.SplitText) window.gsap.registerPlugin(window.SplitText);
        }
        var content = page.querySelector(isDetail ? '.detail-layout' : '.page-content');
        var title = page.querySelector('.page-tit .split span');
        var timeline = hasGsap && content ? window.gsap.timeline({paused:true}) : null;
        if (timeline) {
            window.gsap.set(content, {y:60, autoAlpha:0});
            if (title) {
                window.gsap.set(title, {yPercent:102, autoAlpha:0});
                timeline.to(title, {yPercent:0, autoAlpha:1, duration:2.6, ease:'elastic.out(1, 0.7)'});
            }
            timeline.to(content, {y:0, autoAlpha:1, duration:2.6, ease:'elastic.out(1, 0.7)'}, title ? '<' : 0);
            if (isDetail && hasScroll && typeof window.fadeUpGsap === 'function') {
                window.gsap.set(page.querySelectorAll('[data-gsap="fade-up"]'), {y:30, autoAlpha:0});
            }
        }
        function playIntro() {
            if (started) return;
            started = true;
            if (timeline) timeline.play();
            if (isDetail && hasGsap && hasScroll) optional('fadeUpGsap');
            refresh();
        }
        function layoutReady() {
            if (layoutStarted) return;
            layoutStarted = true;
            optional('isCurrent',4);
            if (hasGsap && hasScroll) {
                optional('headerGsap'); optional('footerGsap');
                if (!isDetail) optional('headerBgGsap');
            }
            requestAnimationFrame(function () {
                function onFade(event) {
                    if (event.target !== document.body || event.propertyName !== 'opacity') return;
                    document.body.removeEventListener('transitionend', onFade);
                    playIntro();
                }
                document.body.addEventListener('transitionend', onFade);
                document.body.classList.add('loaded');
                if (window.lenis && typeof window.lenis.start === 'function') window.lenis.start();
                refresh();
                setTimeout(function () {
                    document.body.removeEventListener('transitionend', onFade);
                    playIntro();
                },900);
            });
        }
        if (!isDetail) {
            var cards = Array.prototype.slice.call(page.querySelectorAll('.page-content .grid-area > .grid-wrap > li'));
            var button = page.querySelector('#btnViewMore');
            var shown = Math.min(4,cards.length);
            function displayCards() {
                cards.forEach(function (card,index) {
                    card.classList.toggle('is-hidden', index >= shown);
                    card.classList.toggle('is-visible', index < shown);
                    card.hidden = index >= shown;
                });
                if (button) {
                    button.hidden = shown >= cards.length;
                    button.classList.toggle('is-hidden-btn', shown >= cards.length);
                    if (button.parentElement) button.parentElement.hidden = shown >= cards.length;
                }
                refresh();
            }
            displayCards();
            if (button) button.addEventListener('click',function () { shown = Math.min(shown+4,cards.length); displayCards(); });
        }
        Array.prototype.forEach.call(page.querySelectorAll('img'),function (image) {
            if (!image.complete) { image.addEventListener('load',refresh); image.addEventListener('error',refresh); }
        });
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(refresh);
        if (window.lenis && typeof window.lenis.stop === 'function') window.lenis.stop();
        // The server has already included header/footer; retain the existing layout hook when present.
        if (typeof window.isLayoutPath === 'function') { window.isLayoutPath(layoutReady); setTimeout(layoutReady,1200); }
        else layoutReady();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',initNewsPage);
    else initNewsPage();
}());

</script>
</body>
</html>
