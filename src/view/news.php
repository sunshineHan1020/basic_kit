
			<main id="content" class="sub news">
				<div class="gutter">
					<div class="max-1340 inner-area pack-down gap-106-60">
						<div class="page-header">
							<div class="title-area">
								<h2 class="page-tit lh125 fs-120-48">
									<strong class="split">
										<span>NEWS</span>
									</strong>
								</h2>
							</div>
						</div>
						<!-- // [Page Header] -->

						<div class="page-content">
							<div class="grid-area">
								<ul class="grid-wrap col-1">
<?php foreach ($newsList as $item) { ?>
<li><a href="<?=$newsH($item['url'])?>" class="items news">
    <div class="img-area"><picture class="img-wrap">
        <?php if ($item['image_url'] !== '') { ?><img src="<?=$newsH($item['image_url'])?>" alt="<?=$newsH($item['alt'])?>"><?php } else { ?><span class="pack-center" style="aspect-ratio: 16 / 9; background: var(--zinc-100);">이미지 준비 중</span><?php } ?>
    </picture></div>
    <div class="text-area"><div class="text-wrap">
        <span class="cate"><span><?=$newsH($item['category_name'])?></span></span>
        <h3 class="title ellipsis line-2"><span><?=$newsH($item['title'])?></span></h3>
        <span class="data"><span><?=$newsH($item['date'])?></span></span>
    </div></div>
</a></li>
<?php } ?>
</ul>
<?php if (!$newsList) { ?><p class="text ta-c" role="status"><?=$newsListError ? '뉴스를 불러오지 못했습니다. 잠시 후 다시 이용해주세요.' : '등록된 뉴스가 없습니다.'?></p><?php } ?>

								<div class="button-wrap center"<?=count($newsList) <= 4 ? ' hidden' : ''?>>
									<button id="btnViewMore" type="button" class="btn solid-zinc small r-100 uppercase fs-14-12 gap-4">
										<span class="fw600">More View</span>
										<i class="ico mask-arrow-r bg-ntr-700 rotate-90"></i>
									</button>
								</div>
							</div>
						</div>
						<!-- // [Page Content] -->
					</div>
				</div>
			</main>
			