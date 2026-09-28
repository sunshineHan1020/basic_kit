	<main id="content" class="sub project">
				<div class="gutter">
					<div class="max-1340 inner-area pack-down gap-106-60">
						<div class="page-header">
							<div class="title-area">
								<h2 class="page-tit lh125 fs-120-48">
									<strong class="split">
										<span>PROJECT</span>
									</strong>
								</h2>
							</div>
						</div>
						<!-- // [Page Header] -->

						<div class="page-content">
							<div class="grid-area pack-down gap-106-60">
								<ul class="grid-wrap col-2-1 gap-4">
<?php foreach ($projectList as $item) { ?>
                                    <li>
                                        <a href="<?=$projectH($item['url'])?>" class="items">
                                            <div class="img-area"><div class="img-wrap">
                                                <?php if ($item['image_url'] !== '') { ?><img src="<?=$projectH($item['image_url'])?>" alt="<?=$projectH($item['alt'])?>"><?php } else { ?><span class="pack-center" style="aspect-ratio: 670 / 460; background: var(--zinc-100);">이미지 준비 중</span><?php } ?>
                                            </div></div>
                                            <div class="marquee-wrap"><div class="marquee" data-text="<?=$projectH($item['name'])?>"><p class="inner uppercase fw700 fc-white"></p></div></div>
                                            <div class="text-wrap"><h3 class="sub-title"><strong><?=$projectH($item['name'])?></strong></h3></div>
                                        </a>
                                    </li>
                                    <?php } ?>
</ul>
<?php if (!$projectList) { ?><p class="text ta-c" role="status"><?=$projectListError ? '프로젝트를 불러오지 못했습니다. 잠시 후 다시 이용해주세요.' : '등록된 프로젝트가 없습니다.'?></p><?php } ?>

								<div class="button-wrap center"<?=count($projectList) <= 8 ? ' hidden' : ''?>>
									<button id="btnViewMore" type="button" 
											class="btn solid-zinc small r-100 uppercase fs-14-12 gap-4">
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