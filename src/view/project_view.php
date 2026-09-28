<?php if (!$project) { ?>
<main id="content" class="sub project detail"><div class="gutter"><div class="max-1440 inner-area pack-down gap-60"><h2 class="fs-48-20"><?=$projectH($projectError)?></h2><a href="/project" class="btn solid-zinc small r-100">프로젝트 목록</a></div></div></main>
<?php } else { ?>

<main id="content" class="sub project detail">
	<div class="inner-area pack-down gap-160-60">
		<!-- [D] 클래스 projects는 프로젝트 상세 페이지에서만 사용 -->
		<article class="detail-layout projects">
			<div class="detail-header">
				<div class="gutter">
					<div class="max-1440">
						<div class="title-area pack-down gap-40-24">
							<div class="title-wrap pack-down items-center ta-c gap-40-24">
								<h2 class="title" data-gsap="fade-up"><span><?=$projectText($project['title'])?></span></h2>
								<p class="text" data-gsap="fade-up"><span><?=$projectText($project['summary'])?></span></p>
							</div>
							<div class="tag-wrap pack-center" data-gsap="fade-up">
<?php foreach ($projectTags as $tag) { ?><span class="tag line-ntr r-100 size"><span><?=$projectH($tag['name'])?></span></span><?php } ?>
</div>
						</div>
					</div>
				</div>
			</div>
			<!-- // [Detail Header] -->

			<div class="detail-content">
				<!-- [D] 실제 에디터 내용 -->
				<div class="edit-boxed pack-down gap-240-60">

					<!-- [D] max-type 경우에는
					<div class="gutter">
						<div class="max-1440">
							타입 별 마크업이 들어가는 자리	
						</div>
					</div>
					마크업 필수 -->



					<?php foreach ($projectSections as $section) { switch ($section['type']) { case 'full_image': ?>
<div class="img-area full-type-01" data-gsap="fade-up">
						<div class="img-wrap">
							<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
						</div>
					</div>
<?php break; ?>
<?php case 'overview': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area default-type pack-down gap-120-40">
								<div class="text-wrap text-wrap-01 pack-basic mo-col gap-120-40">
									<div class="boxed-01" data-gsap="fade-up">
										<div class="logo-wrap pack-basic">
											<?php if (!empty($section['media']['logo'])) { ?><img src="<?=$projectH($section['media']['logo']['url'])?>" alt="<?=$projectH($section['media']['logo']['alt'])?>"><?php } ?>
										</div>
									</div>
									<div class="boxed-02" data-gsap="fade-up">
										<h3 class="fs-48-20 lh125 fw400"><span><?=$projectText($section['title'])?></span></h3>
									</div>
								</div>
								<div class="text-wrap text-wrap-02 pack-basic items-end gap-120-40">
									<div class="boxed-01" data-gsap="fade-up">
										<ul class="fs-18-14 gap-10-4 pack-down">
<?php foreach (isset($section['items']['summary_items']) ? $section['items']['summary_items'] : array() as $info) { ?>
<li><?php if ($info['label'] !== '') { ?><span><?=$projectH($info['label'])?> </span><?php } ?><?php $infoUrl = $projectUrl($info['value_text']); if ($infoUrl !== '') { ?><a href="<?=$projectH($infoUrl)?>" target="_blank" rel="noopener noreferrer"><span><?=$projectH($info['value_text'])?></span></a><?php } else { ?><span><?=$projectText($info['value_text'])?></span><?php } ?></li>
<?php } ?>
</ul>
									</div>
									<div class="boxed-02" data-gsap="fade-up">
										<p class="fs-20-14 fw400"><span><?=$projectRich($section['body_html'])?><?php foreach (isset($section['items']['detail_items']) ? $section['items']['detail_items'] : array() as $info) { ?><br><?php if ($info['label'] !== '') { ?><strong><?=$projectH($info['label'])?></strong> <?php } ?><?=$projectText($info['value_text'])?><?php } ?></span></p>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'full_pair': ?>
<div class="img-area full-type-02 grid-wrap col-2" data-gsap="fade-up">
						<div class="img-wrap">
							<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
						</div>
						<div class="img-wrap">
							<?php if (!empty($section['images'][1])) { ?><img src="<?=$projectH($section['images'][1]['url'])?>" alt="<?=$projectH($section['images'][1]['alt'])?>"><?php } ?>
						</div>
					</div>
<?php break; ?>
<?php case 'center_text': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area text-type-01">
								<div class="text-wrap pack-down gap-40 ta-c">
									<?php if (trim((string)$section['title']) !== '') { ?><h3 class="fs-68-24 fw400 lh125" data-gsap="fade-up"><span><?=$projectText($section['title'])?></span></h3><?php } ?>
									<?php if (trim((string)$section['body_html']) !== '') { ?><p class="fs-20-14" data-gsap="fade-up"><span><?=$projectRich($section['body_html'])?></span></p><?php } ?>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'contained_image': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="img-area max-type-01" data-gsap="fade-up">
								<div class="img-wrap">
									<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'contained_pair': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="img-area max-type-02 grid-wrap col-2" data-gsap="fade-up">
								<div class="img-wrap">
									<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
								</div>
								<div class="img-wrap">
									<?php if (!empty($section['images'][1])) { ?><img src="<?=$projectH($section['images'][1]['url'])?>" alt="<?=$projectH($section['images'][1]['alt'])?>"><?php } ?>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'center_body': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area text-type-01">
								<div class="text-wrap pack-down gap-40 ta-c">
									<p class="fs-20-14" data-gsap="fade-up"><span><?=$projectRich($section['body_html'])?></span></p>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'center_title': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area text-type-01">
								<div class="text-wrap pack-down gap-40 ta-c">
									<h3 class="fs-68-24 fw400 lh125" data-gsap="fade-up"><span><?=$projectText($section['title'])?></span></h3>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'image_collage': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="img-area max-custom-type" data-gsap="fade-up">
								<div class="img-wrap img-01">
									<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
								</div>
								<div class="img-wrap img-02">
									<?php if (!empty($section['images'][1])) { ?><img src="<?=$projectH($section['images'][1]['url'])?>" alt="<?=$projectH($section['images'][1]['alt'])?>"><?php } ?>
								</div>
								<div class="img-wrap img-03">
									<?php if (!empty($section['images'][2])) { ?><img src="<?=$projectH($section['images'][2]['url'])?>" alt="<?=$projectH($section['images'][2]['alt'])?>"><?php } ?>
								</div>
								<div class="marquee-wrap project-marquee">
									<div class="marquee">
										<p class="inner uppercase" id="project-marquee-<?=$projectH($section['id'])?>" data-project-marquee="<?=$projectH($section['subtitle'] ? $section['subtitle'] : ($section['eyebrow'] ? $section['eyebrow'] : $section['title']))?>"></p>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'split_text': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area text-type-02">
								<div class="text-wrap pack-basic items-end mo-col gap-120-40">
									<div class="boxed-01" data-gsap="fade-up">
										<p class="fs-20-16 fw400"><span><?=$projectRich($section['body_html'])?></span></p>
									</div>
									
									<div class="boxed-02" data-gsap="fade-up">
										<h3 class="fs-48-20 fw400 lh125"><span><?=$projectText($section['title'])?></span></h3>
									</div>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'image_text': ?>
<div class="mixed-area grid-wrap col-2-1">
						<div class="img-area">
							<div class="img-wrap">
								<?php if (!empty($section['images'][0])) { ?><img src="<?=$projectH($section['images'][0]['url'])?>" alt="<?=$projectH($section['images'][0]['alt'])?>"><?php } ?>
							</div>
						</div>

						<div class="text-area pack-center">
							<div class="text-wrap pack-down ta-c gap-40">
								<h3 class="fs-48-20 fw400 lh125"><span><?=$projectText($section['title'])?></span></h3>
								<p class="fs-20-14 fw400"><span><?=$projectRich($section['body_html'])?></span></p>
							</div>
						</div>
					</div>
<?php break; ?>
<?php case 'right_text': ?>
<div class="gutter">
						<div class="max-1440">
							<div class="text-area text-type-03">
								<div class="text-wrap grid-wrap col-2-1">
									<div class="only-pc"></div>
									<p class="fs-20-14 fw400"><span><?=$projectRich($section['body_html'])?></span></p>
								</div>
							</div>
						</div>
					</div>
<?php break; ?>
<?php } } ?>
				</div>
			</div>
			<!-- // [Detail Content] -->
		</article>
		<!-- // [Detail Layout] -->

		<div class="button-wrap center">
			<a 	href="/project" 
				class="btn solid-zinc small r-100 fs-14-12 uppercase gap-4">
				<span class="fw600">GO TO LIST</span>
				<i class="ico mask-arrow-r bg-ntr-700"></i>
			</a>
		</div>

	</div>
</main>




	
<?php } ?>
