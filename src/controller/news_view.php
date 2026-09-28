<?php
$news = null; $newsSeo = array(); $newsError = ''; $newsBody = '';
$newsH = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$newsUrl = function ($value) {
    $value = trim((string)$value);
    if ($value === '' || preg_match('/[\x00-\x20\x7F\\\\]/', $value)) { return ''; }
    if (substr($value,0,1) === '/' && substr($value,0,2) !== '//') { return $value; }
    $parts = parse_url($value);
    return $parts && isset($parts['scheme'],$parts['host']) && !isset($parts['user']) && !isset($parts['pass'])
        && in_array(strtolower($parts['scheme']),array('http','https'),true) ? $value : '';
};
$newsImage = function ($value) use ($newsUrl) {
    $value = '/'.ltrim((string)$value,'/');
    return strpos($value,'/uploads/') === 0 && strpos($value,'..') === false ? $newsUrl($value) : '';
};
// Retain editor paragraphs, images, links, tables, emphasis and basic text formatting.
$newsHtml = function ($html) use ($newsH,$newsUrl) {
    if (!class_exists('DOMDocument')) {
        return nl2br($newsH(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>/i',"\n",(string)$html)),ENT_QUOTES,'UTF-8')),false);
    }
    $previous = libxml_use_internal_errors(true);
    $doc = new DOMDocument('1.0','UTF-8');
    $doc->loadHTML('<?xml encoding="UTF-8"><html><body>'.(string)$html.'</body></html>',LIBXML_NONET);
    $walk = function ($node) use (&$walk,$newsH,$newsUrl) {
        if ($node->nodeType === XML_TEXT_NODE) { return $newsH($node->nodeValue); }
        if ($node->nodeType !== XML_ELEMENT_NODE) { return ''; }
        $tag = strtolower($node->nodeName);
        if (in_array($tag,array('script','style','iframe','object','embed','svg','math','template','form','input','button','textarea'),true)) { return ''; }
        $children = '';
        foreach ($node->childNodes as $child) { $children .= $walk($child); }
        if (!in_array($tag,array('p','div','span','br','hr','h1','h2','h3','h4','h5','h6','strong','b','em','i','u','s','del','sub','sup','a','img','ul','ol','li','blockquote','pre','code','table','thead','tbody','tfoot','tr','td','th','caption','colgroup','col','figure','figcaption'),true)) { return $children; }
        $attrs = '';
        if ($tag === 'a') {
            $url = $newsUrl($node->getAttribute('href'));
            if ($url === '') { return $children; }
            $attrs .= ' href="'.$newsH($url).'"';
            if ($node->getAttribute('target') === '_blank') { $attrs .= ' target="_blank" rel="noopener noreferrer"'; }
        }
        if ($tag === 'img') {
            $url = $newsUrl($node->getAttribute('src'));
            if ($url === '') { return ''; }
            $attrs .= ' src="'.$newsH($url).'" alt="'.$newsH($node->getAttribute('alt')).'"';
        }
        foreach (array('width','height','colspan','rowspan') as $attribute) {
            $v = $node->getAttribute($attribute);
            if (preg_match('/^[1-9][0-9]{0,3}$/D',$v)) { $attrs .= ' '.$attribute.'="'.$v.'"'; }
        }
        $styles = array();
        foreach (explode(';',$node->getAttribute('style')) as $declaration) {
            $pair = explode(':',$declaration,2);
            if (count($pair) !== 2) { continue; }
            $property = strtolower(trim($pair[0])); $v = strtolower(trim($pair[1])); $valid = false;
            if (in_array($property,array('color','background-color'),true)) { $valid = preg_match('/^(#[0-9a-f]{3,8}|[a-z]+|rgba?\([0-9.,% ]+\))$/D',$v); }
            elseif ($property === 'text-align') { $valid = in_array($v,array('left','right','center','justify'),true); }
            elseif ($property === 'font-weight') { $valid = preg_match('/^(normal|bold|[1-9]00)$/D',$v); }
            elseif ($property === 'font-style') { $valid = in_array($v,array('normal','italic'),true); }
            elseif ($property === 'text-decoration') { $valid = in_array($v,array('none','underline','line-through'),true); }
            elseif (in_array($property,array('font-size','line-height','width','height','max-width','padding-left','margin-left'),true)) { $valid = preg_match('/^[0-9]{1,4}(\.[0-9]{1,2})?(px|em|rem|%|pt)?$/D',$v); }
            if ($valid) { $styles[] = $property.':'.$v; }
        }
        if ($tag === 'img') { $styles[] = 'max-width:100%'; $styles[] = 'height:auto'; }
        if ($styles) { $attrs .= ' style="'.$newsH(implode(';',$styles)).'"'; }
        if (in_array($tag,array('img','br','hr','col'),true)) { return '<'.$tag.$attrs.'>'; }
        return '<'.$tag.$attrs.'>'.$children.'</'.$tag.'>';
    };
    $result = ''; $body = $doc->getElementsByTagName('body')->item(0);
    if ($body) { foreach ($body->childNodes as $node) { $result .= $walk($node); } }
    libxml_clear_errors(); libxml_use_internal_errors($previous);
    return $result;
};
// Already decoded once by Router; never accept a query ID in place of this slug.
$newsRouteSlug = isset($news_slug) && is_string($news_slug) ? $news_slug : '';
$newsPrepare = function ($db) use (&$news,&$newsSeo,&$newsError,&$newsBody,$newsRouteSlug,$newsImage,$newsUrl,$newsHtml) {
    $newsSeo = array('title'=>'뉴스를 찾을 수 없습니다 | WEEKENDS','description'=>'','keywords'=>'','canonical'=>'',
        'og_title'=>'WEEKENDS','og_description'=>'','image'=>'/@resource/images/opengraph.jpg','robots'=>'noindex, nofollow');
    $slug = $newsRouteSlug;
    if ($slug === '' || strlen($slug)>720 || preg_match('/[\x00-\x20\x7F\/\\\\]/',$slug)) {
        http_response_code(404); $newsError = '뉴스를 찾을 수 없습니다.'; return;
    }
    $query = function ($sql,$params) use ($db) {
        $statement = pdo_query($db,$sql,$params);
        if ($statement === false) { throw new RuntimeException('News query failed.'); }
        return $statement;
    };
    try {
        $statement = $query("SELECT p.*, c.name AS category_name FROM posts p
            INNER JOIN boards b ON b.id = p.board_id AND b.code = ? AND b.is_active = 1
            LEFT JOIN post_categories c ON c.id = p.category_id AND c.board_id = p.board_id AND c.is_active = 1
            WHERE p.slug = ? AND p.status = 'published' AND p.visibility = 'public' AND p.deleted_at IS NULL
            AND (p.publish_at IS NULL OR p.publish_at <= NOW()) LIMIT 1",array('BLOG',$slug));
        $news = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$news) { http_response_code(404); $newsError = '뉴스를 찾을 수 없습니다.'; return; }
        $news['date'] = '';
        foreach (array('display_at','publish_at','created_at') as $field) {
            if (!empty($news[$field]) && substr($news[$field],0,10) !== '0000-00-00') { $news['date'] = str_replace('-','.',substr($news[$field],0,10)); break; }
        }
        $newsBody = $newsHtml($news['body_html']);
        $statement = $query('SELECT seo.*, m.storage_key AS og_storage_key FROM post_seo seo
            LEFT JOIN media m ON m.id = seo.og_media_id AND m.deleted_at IS NULL WHERE seo.post_id = ? LIMIT 1',array($news['id']));
        $seo = $statement->fetch(PDO::FETCH_ASSOC); if (!$seo) { $seo = array(); }
        $value = function ($key,$fallback) use ($seo) { return isset($seo[$key]) && trim((string)$seo[$key]) !== '' ? trim($seo[$key]) : $fallback; };
        $plain = function ($text) { return trim(preg_replace('/\s+/u',' ',html_entity_decode(strip_tags((string)$text),ENT_QUOTES,'UTF-8'))); };
        $description = $plain($news['summary']);
        if ($description === '') { $description = $plain($news['body_html']); preg_match('/^.{0,160}/us',$description,$excerpt); $description = isset($excerpt[0]) ? $excerpt[0] : ''; }
        $image = isset($seo['og_storage_key']) ? $newsImage($seo['og_storage_key']) : '';
        if ($image === '') {
            $statement = $query("SELECT m.storage_key FROM post_media pm INNER JOIN media m ON m.id = pm.media_id AND m.deleted_at IS NULL
                WHERE pm.post_id = ? AND pm.role = 'thumbnail' ORDER BY pm.sort_order ASC, pm.id ASC LIMIT 1",array($news['id']));
            $image = $newsImage($statement->fetchColumn());
        }
        $canonical = $newsUrl($value('canonical_url',''));
        $newsSeo = array('title'=>$plain($value('seo_title',$news['title'].' | WEEKENDS')),
            'description'=>$plain($value('meta_description',$description)),'keywords'=>$plain($value('meta_keywords','')),
            'canonical'=>$canonical !== '' ? $canonical : '/news/'.rawurlencode($news['slug']),
            'og_title'=>$plain($value('og_title',$value('seo_title',$news['title'].' | WEEKENDS'))),
            'og_description'=>$plain($value('og_description',$value('meta_description',$description))),
            'image'=>$image !== '' ? $image : '/@resource/images/opengraph.jpg',
            'robots'=>(isset($seo['allow_index']) && !$seo['allow_index'] ? 'noindex' : 'index').', '.(!empty($seo['nofollow']) ? 'nofollow' : 'follow'));
    } catch (Exception $e) {
        error_log('News detail: '.$e->getMessage()); http_response_code(500); $news = null; $newsBody = '';
        $newsSeo['title'] = '뉴스 | WEEKENDS'; $newsSeo['robots'] = 'noindex, nofollow';
        $newsError = '뉴스를 불러오지 못했습니다. 잠시 후 다시 이용해주세요.';
    }
};
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head_news.php';
include $_SERVER['DOCUMENT_ROOT'].'/src/view/news_view.php';
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
