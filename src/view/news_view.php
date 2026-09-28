<?php if (!$news) { ?>
<main id="content" class="sub news detail"><div class="gutter"><div class="max-1100 inner-area pack-down gap-60"><h2 class="fs-48-20"><?=$newsH($newsError)?></h2><a href="/news" class="btn solid-zinc small r-100">뉴스 목록</a></div></div></main>
<?php } else { ?>

			<main id="content" class="sub news detail">
				<div class="gutter">
					<div class="max-1100 inner-area pack-down gap-160-60">
						<article class="detail-layout">
							<div class="detail-header" data-gsap="fade-up">
								<div class="title-area">
									<span class="cate" data-gsap="fade-up"><span><?=$newsH($news['category_name'])?></span></span>
									<h2 class="title" data-gsap="fade-up"><span><?=nl2br($newsH($news['title']), false)?></span></h2>
									<span class="data" data-gsap="fade-up"><span><?=$newsH($news['date'])?></span></span>
								</div>
							</div>
							<!-- // [Detail Header] -->

							<div class="detail-content" data-gsap="fade-up">
								<!-- [D] 실제 에디터 내용 -->
								<div class="edit-boxed"><?=$newsBody?></div>
							</div>
							<!-- // [Detail Content] -->
						</article>
						<!-- // [Detail Layout] -->

						<div class="button-wrap center">
							<a 	href="/news" data-gsap="fade-up"
								class="btn solid-zinc small r-100 fs-14-12 uppercase gap-4" >
								<span class="fw600">GO TO LIST</span>
								<i class="ico mask-arrow-r bg-ntr-700"></i>
							</a>
						</div>
					</div>
				</div>
			</main>
		
<?php } ?>