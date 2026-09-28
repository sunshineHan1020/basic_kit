<?php
/* PHP 5.4+. head_project.php initializes common.php, then invokes this loader before HTML. */
$project = null;
$projectSections = array();
$projectTags = array();
$projectSeo = array();
$projectError = '';
$projectH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$projectUrl = function ($value) {
    $value = trim((string)$value);
    if ($value === '' || preg_match('/[\x00-\x20\x7F\\\\]/', $value)) { return ''; }
    if (substr($value, 0, 1) === '/' && substr($value, 0, 2) !== '//') { return $value; }
    $parts = parse_url($value);
    return $parts && isset($parts['scheme'], $parts['host']) && !isset($parts['user']) && !isset($parts['pass'])
        && in_array(strtolower($parts['scheme']), array('http', 'https'), true) ? $value : '';
};
$projectImage = function ($value) use ($projectUrl) {
    $value = '/'.ltrim((string)$value, '/');
    return strpos($value, '/uploads/') === 0 && strpos($value, '..') === false ? $projectUrl($value) : '';
};
$projectText = function ($value) use ($projectH) { return nl2br($projectH($value), false); };
// Inline-only rich text: retain editorial emphasis and links without breaking section wrappers.
$projectRich = function ($value) use ($projectH, $projectUrl) {
    $value = (string)$value;
    if (!class_exists('DOMDocument')) {
        return nl2br($projectH(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $value)), ENT_QUOTES, 'UTF-8')), false);
    }
    $previous = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $document->loadHTML('<?xml encoding="UTF-8"><html><body><div>'.$value.'</div></body></html>', LIBXML_NONET);
    $walk = function ($node) use (&$walk, $projectH, $projectUrl) {
        if ($node->nodeType === XML_TEXT_NODE) { return nl2br($projectH($node->nodeValue), false); }
        if ($node->nodeType !== XML_ELEMENT_NODE) { return ''; }
        $tag = strtolower($node->nodeName);
        if (in_array($tag, array('script','style','iframe','object','embed','svg','math','template'), true)) { return ''; }
        if ($tag === 'br') { return '<br>'; }
        $inside = '';
        foreach ($node->childNodes as $child) { $inside .= $walk($child); }
        if ($tag === 'a') {
            $url = $projectUrl($node->getAttribute('href'));
            return $url === '' ? $inside : '<a href="'.$projectH($url).'" target="_blank" rel="noopener noreferrer">'.$inside.'</a>';
        }
        if (in_array($tag, array('strong','b','em','i','u','s','sub','sup','span'), true)) { return '<'.$tag.'>'.$inside.'</'.$tag.'>'; }
        return $inside.(in_array($tag, array('p','div','li','h1','h2','h3','h4','blockquote'), true) ? '<br>' : '');
    };
    $result = '';
    $body = $document->getElementsByTagName('body')->item(0);
    if ($body) { foreach ($body->childNodes as $node) { $result .= $walk($node); } }
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    return $result;
};
// Router has already rawurldecode()d the path segment. Do not decode it again.
$projectRouteSlug = isset($project_slug) && is_string($project_slug) ? $project_slug : '';
$projectPrepare = function ($db) use (&$project, &$projectSections, &$projectTags, &$projectSeo, &$projectError, $projectImage, $projectUrl, $projectRouteSlug) {
    $query = function ($sql, $params) use ($db) {
        $statement = pdo_query($db, $sql, $params);
        if ($statement === false) { throw new RuntimeException('Project query failed.'); }
        return $statement;
    };
    $projectSeo = array('title' => '프로젝트를 찾을 수 없습니다 | WEEKENDS', 'description' => '', 'keywords' => '',
        'canonical' => '', 'og_title' => '', 'og_description' => '', 'image' => '/@resource/images/opengraph.jpg', 'robots' => 'noindex, nofollow');
    $slug = $projectRouteSlug;
    if ($slug === '' || strlen($slug) > 720 || preg_match('/[\x00-\x20\x7F\/\\\\]/', $slug)) {
        http_response_code(404); $projectError = '프로젝트를 찾을 수 없습니다.'; return;
    }
    try {
        $statement = $query("SELECT w.* FROM works w WHERE w.slug = ?
            AND w.deleted_at IS NULL AND w.status = 'published' AND w.visibility = 'public'
            AND (w.publish_at IS NULL OR w.publish_at <= NOW()) LIMIT 1", array($slug));
        $project = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$project) { http_response_code(404); $projectError = '프로젝트를 찾을 수 없습니다.'; return; }
        $id = $project['id'];
        $statement = $query('SELECT t.name FROM work_tag_map tm INNER JOIN work_tags t ON t.id = tm.tag_id
            WHERE tm.work_id = ? AND t.is_active = 1 ORDER BY tm.sort_order ASC, t.sort_order ASC, t.id ASC', array($id));
        $projectTags = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = $query('SELECT s.* FROM work_sections s WHERE s.work_id = ? AND s.is_enabled = 1 ORDER BY s.sort_order ASC, s.id ASC', array($id));
        $sections = $statement->fetchAll(PDO::FETCH_ASSOC);
        $media = array(); $items = array();
        $statement = $query('SELECT sm.section_id, sm.slot_key, sm.alt_text, sm.caption, m.storage_key, m.default_alt_text
            FROM work_section_media sm INNER JOIN work_sections s ON s.id = sm.section_id
            INNER JOIN media m ON m.id = sm.media_id AND m.deleted_at IS NULL
            WHERE s.work_id = ? AND s.is_enabled = 1 ORDER BY sm.sort_order ASC, sm.id ASC', array($id));
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $url = $projectImage($row['storage_key']);
            if ($url === '') { continue; }
            if (!isset($media[$row['section_id']][$row['slot_key']])) {
                $media[$row['section_id']][$row['slot_key']] = array('url' => $url,
                    'alt' => $row['alt_text'] !== null && $row['alt_text'] !== '' ? $row['alt_text'] : ($row['default_alt_text'] ? $row['default_alt_text'] : $project['title']));
            }
        }
        $statement = $query('SELECT i.* FROM work_section_items i INNER JOIN work_sections s ON s.id = i.section_id
            WHERE s.work_id = ? AND s.is_enabled = 1 ORDER BY i.sort_order ASC, i.id ASC', array($id));
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { $items[$row['section_id']][$row['group_key']][] = $row; }
        $typeMap = array('PROJECT_OVERVIEW'=>'overview','IMG_FULL_01'=>'full_image','IMG_FULL_02'=>'full_pair',
            'TEXT_01'=>'center_text','IMG_MAX_01'=>'contained_image','IMG_MAX_02'=>'contained_pair','IMG_MAX_CUSTOM'=>'image_collage',
            'TEXT_02'=>'split_text','MIXED_02_01'=>'image_text','TEXT_03'=>'right_text',
            'full_image'=>'full_image','overview'=>'overview','full_pair'=>'full_pair','center_text'=>'center_text',
            'contained_image'=>'contained_image','contained_pair'=>'contained_pair','center_body'=>'center_body',
            'center_title'=>'center_title','image_collage'=>'image_collage','split_text'=>'split_text','image_text'=>'image_text','right_text'=>'right_text');
        foreach ($sections as $section) {
            if (!isset($typeMap[$section['section_type']])) { continue; }
            $section['type'] = $typeMap[$section['section_type']];
            $section['media'] = isset($media[$section['id']]) ? $media[$section['id']] : array();
            $section['items'] = isset($items[$section['id']]) ? $items[$section['id']] : array();
            $section['images'] = array();
            foreach (array(array('image','image_01','image_1'),array('image_02','image_2'),array('image_03','image_3')) as $aliases) {
                $image = null;
                foreach ($aliases as $slot) { if (isset($section['media'][$slot])) { $image = $section['media'][$slot]; break; } }
                $section['images'][] = $image;
            }
            // Custom image sections can have one composite image in the current administrator.
            if (in_array($section['type'], array('full_image','full_pair','contained_image','contained_pair','image_collage','image_text'), true)
                && !$section['images'][0]) { continue; }
            // Incomplete image sets use the existing single-image layout, never an empty grid cell.
            if ($section['type'] === 'full_pair' && !$section['images'][1]) { $section['type'] = 'full_image'; }
            if ($section['type'] === 'contained_pair' && !$section['images'][1]) { $section['type'] = 'contained_image'; }
            if ($section['type'] === 'image_collage' && (!$section['images'][1] || !$section['images'][2])) { $section['type'] = 'contained_image'; }
            $projectSections[] = $section;
        }
        $statement = $query('SELECT seo.*, m.storage_key AS og_storage_key FROM work_seo seo
            LEFT JOIN media m ON m.id = seo.og_media_id AND m.deleted_at IS NULL WHERE seo.work_id = ? LIMIT 1', array($id));
        $seo = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$seo) { $seo = array(); }
        $value = function ($key, $fallback) use ($seo) { return isset($seo[$key]) && trim((string)$seo[$key]) !== '' ? trim($seo[$key]) : $fallback; };
        $plain = function ($value) { return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$value), ENT_QUOTES, 'UTF-8'))); };
        $title = $plain($project['title'] ? $project['title'] : $project['list_title']);
        $description = $plain($project['summary']);
        $image = isset($seo['og_storage_key']) ? $projectImage($seo['og_storage_key']) : '';
        if ($image === '') {
            $statement = $query("SELECT m.storage_key FROM work_media wm INNER JOIN media m ON m.id = wm.media_id AND m.deleted_at IS NULL
                WHERE wm.work_id = ? AND wm.role = 'thumbnail' ORDER BY wm.sort_order ASC, wm.id ASC LIMIT 1", array($id));
            $image = $projectImage($statement->fetchColumn());
        }
        $canonical = $projectUrl($value('canonical_url', ''));
        $projectSeo = array('title'=>$plain($value('seo_title', $title.' | WEEKENDS')),
            'description'=>$plain($value('meta_description',$description)), 'keywords'=>$plain($value('meta_keywords','')),
            'canonical'=>$canonical !== '' ? $canonical : '/project/'.rawurlencode($project['slug']),
            'og_title'=>$plain($value('og_title',$value('seo_title',$title.' | WEEKENDS'))),
            'og_description'=>$plain($value('og_description',$value('meta_description',$description))),
            'image'=>$image !== '' ? $image : '/@resource/images/opengraph.jpg',
            'robots'=>(isset($seo['allow_index']) && !$seo['allow_index'] ? 'noindex' : 'index').', '.(!empty($seo['nofollow']) ? 'nofollow' : 'follow'));
    } catch (Exception $e) {
        error_log('Project detail: '.$e->getMessage());
        http_response_code(500); $project = null; $projectSections = array(); $projectTags = array();
        $projectSeo['title'] = '프로젝트 | WEEKENDS'; $projectSeo['robots'] = 'noindex, nofollow';
        $projectError = '프로젝트를 불러오지 못했습니다. 잠시 후 다시 이용해주세요.';
    }
};
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head_project.php';
include $_SERVER['DOCUMENT_ROOT'].'/src/view/project_view.php';
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
