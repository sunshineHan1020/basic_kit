<?php
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';
$projectH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$projectList = array();
$projectListError = false;
try {
    // Correlated media ID picks one thumbnail without multiplying project rows.
    $sql = "SELECT w.id, w.slug, w.list_title, w.title, m.storage_key, wm.alt_text, m.default_alt_text
        FROM works w
        LEFT JOIN work_media wm ON wm.id = (
            SELECT wm2.id FROM work_media wm2 INNER JOIN media m2 ON m2.id = wm2.media_id AND m2.deleted_at IS NULL
            WHERE wm2.work_id = w.id AND wm2.role = 'thumbnail' ORDER BY wm2.sort_order ASC, wm2.id ASC LIMIT 1)
        LEFT JOIN media m ON m.id = wm.media_id AND m.deleted_at IS NULL
        WHERE w.status = 'published' AND w.visibility = 'public' AND w.deleted_at IS NULL
        AND (w.publish_at IS NULL OR w.publish_at <= NOW())
        ORDER BY w.sort_order ASC, w.id ASC";
    $statement = pdo_query($db, $sql, array());
    if ($statement === false) { throw new RuntimeException('Project list query failed.'); }
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        // The public router accepts one slug path segment; do not emit broken links.
        if (!is_string($row['slug']) || $row['slug'] === '' || preg_match('/[\x00-\x20\x7F\/\\\\]/', $row['slug'])) { continue; }
        $image = '/'.ltrim((string)$row['storage_key'], '/');
        $row['image_url'] = strpos($image, '/uploads/') === 0 && strpos($image, '..') === false
            && !preg_match('/[\x00-\x20\x7F\\\\?#]/', $image) ? $image : '';
        $row['name'] = trim((string)$row['list_title']) !== '' ? $row['list_title'] : $row['title'];
        $row['alt'] = $row['alt_text'] ? $row['alt_text'] : ($row['default_alt_text'] ? $row['default_alt_text'] : $row['name']);
        $row['url'] = '/project/'.rawurlencode($row['slug']);
        $projectList[] = $row;
    }
} catch (Exception $e) {
    error_log('Project list: '.$e->getMessage()); $projectListError = true; $projectList = array();
}
include $_SERVER['DOCUMENT_ROOT'].'/src/view/project.php';
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';
?>

<!-- Project scripts -->
<script src="/@resource/js/lib/swiper-bundle.min.js"></script>
<script src="/@resource/js/lib/gsap.min.js"></script>
<script src="/@resource/js/lib/ScrollTrigger.min.js"></script>
<script src="/@resource/js/lib/SplitText.min.js"></script>
<script src="/@resource/js/lib/lenis.min.js"></script>
<script src="/@resource/js/motion.js"></script>
<script>
(function () {
    'use strict';
    function initProjectPage() {
        var page = document.querySelector('.sub.project');
        if (!page || page.dataset.projectReady === '1') return;
        page.dataset.projectReady = '1';
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
            if (isDetail && hasGsap && hasScroll) optional('fadeUpGsap',96);
            refresh();
        }
        function layoutReady() {
            if (layoutStarted) return;
            layoutStarted = true;
            optional('isCurrent',2);
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
            var shown = Math.min(8,cards.length);
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
            if (hasGsap && cards.length) optional('motionHoverMarquee',{
                trigger:'.sub.project .page-content .grid-area > .grid-wrap > li',target:'.marquee .inner',cnt:20,speed:2
            });
        } else if (hasGsap && typeof window.motionMarquee === 'function') {
            // The bundled helper uses querySelector: initialize each section independently.
            Array.prototype.forEach.call(page.querySelectorAll('[data-project-marquee]'),function (element) {
                var text = element.getAttribute('data-project-marquee') || '';
                if (!text.trim()) return;
                window.motionMarquee({target:'#'+element.id,text:text,speed:2,cnt:20});
            });
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
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',initProjectPage);
    else initProjectPage();
}());

</script>
</body>
</html>
