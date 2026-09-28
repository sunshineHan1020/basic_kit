<?php
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/site_content.php';
$pageContent = wc_load_texts($db, 'about');
$pageImages = wc_load_images($db, 'team');

$aboutEscape = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$aboutImageUrl = function ($key) {
    $key = '/'.ltrim((string)$key, '/');
    // Only serve local media managed by the CMS.
    if (strpos($key, '/uploads/') !== 0 || strpos($key, '..') !== false
        || preg_match('/[\x00-\x20\x7F\\\\?#]/', $key)) { return ''; }
    return $key;
};
$aboutQuery = function ($query, $params) use ($db) {
    $statement = pdo_query($db, $query, $params);
    if ($statement === false) { throw new RuntimeException('ABOUT database query failed.'); }
    return $statement;
};

// Fixed sections: preserve the administrator's commas, spaces and line breaks.
$aboutRoster = array();
$statement = $aboutQuery('SELECT hq_text, art_text, plan_text, hldys_text FROM team_roster WHERE id = ?', array(1));
$roster = $statement->fetch(PDO::FETCH_ASSOC);
foreach (array('HQ' => 'hq_text', 'ART' => 'art_text', 'PLAN' => 'plan_text', 'HLDYS' => 'hldys_text') as $label => $field) {
    $aboutRoster[] = array('label' => $label, 'text' => $roster ? $roster[$field] : '');
}

// Keep the original group photo first. Personal images follow the CMS order.
$aboutTeamImages = array(array(
    'name' => wc_image($pageImages, 'team_group_desktop', 'alt_text'),
    'image_url' => wc_image($pageImages, 'team_group_desktop', 'storage_key'),
    'mobile_image_url' => wc_image($pageImages, 'team_group_mobile', 'storage_key')
));
$statement = $aboutQuery('SELECT t.member_name, m.storage_key, mm.storage_key AS mobile_storage_key
    FROM team_members t
    INNER JOIN media m ON m.id = t.media_id AND m.deleted_at IS NULL
    LEFT JOIN media mm ON mm.id = t.mobile_media_id AND mm.deleted_at IS NULL
    WHERE t.is_active = ? ORDER BY t.sort_order ASC, t.id ASC', array(1));
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $imageUrl = $aboutImageUrl($row['storage_key']);
    if ($imageUrl === '') { continue; }
    $mobileUrl = $aboutImageUrl($row['mobile_storage_key']);
    $aboutTeamImages[] = array('name' => $row['member_name'], 'image_url' => $imageUrl,
        'mobile_image_url' => $mobileUrl !== '' ? $mobileUrl : $imageUrl);
}

$aboutClients = array();
$statement = $aboutQuery('SELECT c.client_name, m.storage_key
    FROM clients c INNER JOIN media m ON m.id = c.media_id AND m.deleted_at IS NULL
    WHERE c.is_active = ? ORDER BY c.sort_order ASC, c.id ASC LIMIT 20', array(1));
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $imageUrl = $aboutImageUrl($row['storage_key']);
    if ($imageUrl === '') { continue; }
    $aboutClients[] = array('name' => $row['client_name'], 'image_url' => $imageUrl);
}





include $_SERVER['DOCUMENT_ROOT'].'/src/view/about.php';


include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';

?>
	<script src="/@resource/js/lib/swiper-bundle.min.js"></script>

	<script src="/@resource/js/lib/gsap.min.js"></script>
	<script src="/@resource/js/lib/ScrollTrigger.min.js"></script>
	<script src="/@resource/js/lib/SplitText.min.js"></script>
	<script src="/@resource/js/lib/lenis.min.js"></script>


	<script type="module">
		import * as THREE from '../@resource/js/lib/three.module.min.js';
		window.THREE = THREE;
		window.dispatchEvent(new Event('three:ready'));
	</script>

	<script src="/@resource/js/lib/matter.min.js"></script>

	<script src="/@resource/js/motion.js"></script>
	<script>
    (function () {
        var media = window.matchMedia('(max-width: 767px)');
        var images = document.querySelectorAll('.ours > .img-area > .img-wrap > img[data-mobile-src]');
        function updateImages() {
            for (var i = 0; i < images.length; i++) {
                var image = images[i];
                var url = image.getAttribute(media.matches ? 'data-mobile-src' : 'data-desktop-src');
                if (image.getAttribute('src') !== url) { image.setAttribute('src', url); }
            }
        }
        // If an optional mobile resource is absent, retain the desktop image.
        for (var i = 0; i < images.length; i++) {
            images[i].addEventListener('error', function () {
                var fallback = this.getAttribute('data-desktop-src');
                if (this.getAttribute('src') !== fallback) { this.setAttribute('src', fallback); }
            });
        }
        updateImages();
        if (media.addEventListener) { media.addEventListener('change', updateImages); }
        else { media.addListener(updateImages); }
    }());
    </script>
	<script src="/@resource/js/about.js"></script>
	<script>
	
	</script>
</body>
</html>
