<main id="content" class="sub about header-white">
				
				<section class="section visual">
					<h2 class="blind"> <span><?=wc_h(wc_text($pageContent, 'visual_01'))?></span> </h2>
					<div class="gutter">
						<div class="inner-area">
							<div class="canvas-wrap">
								<canvas id="hero"></canvas>
							</div>

							<div class="text-area">
								<div class="text-wrap">
									<p class="text">
										<span><?=wc_h(wc_text($pageContent, 'visual_02'))?></span>
										<span><?=wc_h(wc_text($pageContent, 'visual_03'))?></span>
										<span><?=wc_h(wc_text($pageContent, 'visual_04'))?></span>
										<span>
											<?=wc_h(wc_text($pageContent, 'visual_05'))?>
										</span>
										<span><?=wc_h(wc_text($pageContent, 'visual_06'))?></span>
									</p>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- // [Section Visual End] 섹션 종료 -->

				<section class="section gradient">
					<h2 class="blind"> <span><?=wc_h(wc_text($pageContent, 'gradient_01'))?></span> </h2>
					<div class="img-area">
						<picture class="img-wrap">
							<source srcset="/@resource/images/@common/gradient.webp" type="image/webp">
							<img 	src="/@resource/images/@common/gradient.png" alt="Weekends">
						</picture>
					</div>
				</section>
				<!-- // [Section Gradient End] 섹션 종료 -->


				<section class="section thought" data-gsap="bg-white">
					<div class="inner-area">
						<div class="thought-area">

							<div class="thought-inner">

								<div class="thought-wrap thought-01">
									<div class="gutter pack-down items-center gap-40">
										<div class="title-wrap">
											<h2 class="title fs-100-44 lh110 ta-c">
												<span class="span-01"><?=wc_h(wc_text($pageContent, 'thought_01'))?></span>
												<span>
													<span class="svg-box">
														<i class="ico mask-diamond bg-orange"></i>
													</span>
												</span>
												<span><?=wc_h(wc_text($pageContent, 'thought_02'))?></span>
											</h2>
										</div>
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span><?=wc_h(wc_text($pageContent, 'thought_03'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_04'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_05'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_06'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_07'))?></span>
											</p>
										</div>
									</div>
								</div>

								<div class="thought-wrap thought-02">
									<div class="gutter pack-down items-center gap-40">
										<div class="title-wrap">
											<h2 class="title fs-100-44 lh110 pack-down items-center ta-c">
												<span><?=wc_h(wc_text($pageContent, 'thought_08'))?></span>
												<span class="pack-center">
													<span><?=wc_h(wc_text($pageContent, 'thought_09'))?></span>
													<span class="svg-box">
														<i class="ico mask-wave bg-orange"></i>
													</span> 
													<span><?=wc_h(wc_text($pageContent, 'thought_10'))?></span>
												</span>
												<span><?=wc_h(wc_text($pageContent, 'thought_11'))?></span>
											</h2>
										</div>
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span><?=wc_h(wc_text($pageContent, 'thought_12'))?> </span>
												<span><?=wc_h(wc_text($pageContent, 'thought_13'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_14'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_15'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_16'))?></span>
											</p>
										</div>
									</div>
								</div>


								<div class="thought-wrap thought-03">
									<div class="gutter ">
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span><?=wc_h(wc_text($pageContent, 'thought_17'))?> </span>
												<span><?=wc_h(wc_text($pageContent, 'thought_18'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_19'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_20'))?></span>
												<span><?=wc_h(wc_text($pageContent, 'thought_21'))?></span>
											</p>
										</div>
									</div>

									<div class="matter-area">
										<div class="matter-wrap" id="matterFall">
											<?php foreach (preg_split('//u', wc_text($pageContent, 'falling_text'), -1, PREG_SPLIT_NO_EMPTY) as $fallChar) { if (preg_match('/\s/u', $fallChar)) { continue; } ?><span class="char" data-char><?=wc_h($fallChar)?></span><?php } ?>
											<img src="/@resource/images/about/weekends_text.svg" class="char" data-char alt="Weekends">
										</div>
									</div>
								</div>

							</div>
							
						</div>
					</div>
				</section>
				<!-- // [Section Thought End] 섹션 종료 --> 

				<section class="section ours header-white">
					<div class="img-area">
						<div class="img-wrap">
							<?php foreach ($aboutTeamImages as $index => $teamImage) { ?>
                            <img src="<?=$aboutEscape($teamImage['image_url'])?>"
                                 data-desktop-src="<?=$aboutEscape($teamImage['image_url'])?>"
                                 data-mobile-src="<?=$aboutEscape($teamImage['mobile_image_url'])?>"
                                 alt="<?=$aboutEscape($teamImage['name'])?>"<?=$index === 0 ? ' class="first"' : ''?>>
                            <?php } ?>
						</div>
					</div>

					<div class="gutter">
						<div class="max-1250 inner-area pack-down gap-240-120">
							<div class="partners-area pack-down gap-160-60">
								<div class="title-wrap pack-down gap-120-24 ta-c">
									<h2 class="title fs-120-44 uppercase lh110 fc-white fw600" data-gsap="fade-up">
										<span>
											<?=wc_h(wc_text($pageContent, 'ours_01'))?>  <br>
											<?=wc_h(wc_text($pageContent, 'ours_02'))?> <br>
											<?=wc_h(wc_text($pageContent, 'ours_03'))?>
										</span>
									</h2>
									<p class="text fc-white fs-32-16 fw400 lh125" data-gsap="fade-up">
										<span>
											<?=wc_h(wc_text($pageContent, 'ours_04'))?> <br>
											<?=wc_h(wc_text($pageContent, 'ours_05'))?>
										</span>
									</p>
								</div>
								<ul class="partner-list grid-wrap col-5-3">
                                    <?php foreach ($aboutClients as $client) { ?>
                                    <li data-gsap="fade-up">
                                        <div class="img-wrap">
                                            <img src="<?=$aboutEscape($client['image_url'])?>" alt="<?=$aboutEscape($client['name'])?>">
                                        </div>
                                    </li>
                                    <?php } ?>
                                </ul>
							</div>

							<div class="team-area pack-down gap-160-60">
								<div class="title-wrap pack-down gap-120-24 ta-c">
									<h2 class="title fs-120-44 uppercase lh110 fc-white fw600" data-gsap="fade-up">
										<span>
											<?=wc_h(wc_text($pageContent, 'ours_06'))?>
										</span>
									</h2>
								</div>

								<div class="team-wrap pack-down items-center gap-180-60">
									<div class="team-list gap-40 grid-wrap col-4-1">
                                        <?php foreach ($aboutRoster as $part) { ?>
                                        <dl class="pack-down gap-4" data-gsap="fade-up">
                                            <dt class="fc-white fs-16" data-gsap="fade-up"><strong><?=$aboutEscape($part['label'])?></strong></dt>
                                            <dd class="fc-white-80 fs-20-18 fw400 pack-wrap gap-4" data-gsap="fade-up"><span style="white-space: pre-wrap; overflow-wrap: anywhere;"><?=$aboutEscape($part['text'])?></span></dd>
                                        </dl>
                                        <?php } ?>
                                    </div>


									<ul class="team-note pack-down gap-60">
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												<?=wc_h(wc_text($pageContent, 'ours_07'))?> <br>
												<?=wc_h(wc_text($pageContent, 'ours_08'))?>
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												<?=wc_h(wc_text($pageContent, 'ours_09'))?> <br>
												<?=wc_h(wc_text($pageContent, 'ours_10'))?>
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												<?=wc_h(wc_text($pageContent, 'ours_11'))?> <br>
												<?=wc_h(wc_text($pageContent, 'ours_12'))?>
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												<?=wc_h(wc_text($pageContent, 'ours_13'))?> <br>
												<?=wc_h(wc_text($pageContent, 'ours_14'))?>
											</span>
										</li>
										
									</ul>
								</div>

							</div>
						</div>
					</div>
				</section>
				<!-- // [Section Ours End] 섹션 종료 -->
			</main>


