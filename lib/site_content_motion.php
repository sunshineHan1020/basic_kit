<?php
/* Render escaped copy for the existing motion renderer; no extra DOM wrappers. */
$mainMotionCopy = array();
ob_start(); ?><?=wc_h(wc_text($pageContent, 'services_circle_01'))?> <br class="only-mo"> <?=wc_h(wc_text($pageContent, 'services_circle_02'))?><?php $mainMotionCopy['circle_title'] = ob_get_clean();
ob_start(); ?><span class="first">
								<?=wc_h(wc_text($pageContent, 'services_circle_03'))?> <br class="only-mo"> 
								<?=wc_h(wc_text($pageContent, 'services_circle_04'))?>
							 </span>
							 <span><?=wc_h(wc_text($pageContent, 'services_circle_05'))?></span>
							 <span><?=wc_h(wc_text($pageContent, 'services_circle_06'))?></span><?php $mainMotionCopy['circle_text'] = ob_get_clean();
ob_start(); ?><?=wc_h(wc_text($pageContent, 'services_triangle_01'))?><?php $mainMotionCopy['triangle_title'] = ob_get_clean();
ob_start(); ?><span class="first">
								<?=wc_h(wc_text($pageContent, 'services_triangle_02'))?> <br class="only-mo"> 
								<?=wc_h(wc_text($pageContent, 'services_triangle_03'))?>
							 </span>
							 <span>
							 	<?=wc_h(wc_text($pageContent, 'services_triangle_04'))?> <br> 
								<?=wc_h(wc_text($pageContent, 'services_triangle_05'))?>
							 </span><?php $mainMotionCopy['triangle_text'] = ob_get_clean();
ob_start(); ?><?=wc_h(wc_text($pageContent, 'services_square_01'))?><?php $mainMotionCopy['square_title'] = ob_get_clean();
ob_start(); ?><span class="first">
								<?=wc_h(wc_text($pageContent, 'services_square_02'))?> <br> 
								<?=wc_h(wc_text($pageContent, 'services_square_03'))?> <br class="only-mo"> <?=wc_h(wc_text($pageContent, 'services_square_04'))?>
							 </span>
							 <span><?=wc_h(wc_text($pageContent, 'services_square_05'))?></span><?php $mainMotionCopy['square_text'] = ob_get_clean();
$mainMotionCopy['gallery_marquee'] = wc_h(wc_text($pageContent, 'gallery_marquee'));
