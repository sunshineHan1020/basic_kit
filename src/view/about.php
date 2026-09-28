<main id="content" class="sub about header-white">
				
				<section class="section visual">
					<h2 class="blind"> <span>Weekends</span> </h2>
					<div class="gutter">
						<div class="inner-area">
							<div class="canvas-wrap">
								<canvas id="hero"></canvas>
							</div>

							<div class="text-area">
								<div class="text-wrap">
									<p class="text">
										<span>모두가 오프라인 이벤트는 이제 죽었다고 말했던 때도 있었습니다.</span>
										<span>하지만 세상이 단절되었던 2015년 메르스, 2020년 코비드도 이겨냈습니다.</span>
										<span>또 다시 새로운 국면, AI 시대를 맞이합니다.</span>
										<span>
											발전하는 기술을 주도적으로 활용하여 우리가 자랑하는 실체 있는 경험으로, 
											앞으로도 우리는 승리할것입니다.
										</span>
										<span>여전히 휴먼 터치는 유효합니다.</span>
									</p>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- // [Section Visual End] 섹션 종료 -->

				<section class="section gradient">
					<h2 class="blind"> <span>Weekends</span> </h2>
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
												<span class="span-01">진짜를</span>
												<span>
													<span class="svg-box">
														<i class="ico mask-diamond bg-orange"></i>
													</span>
												</span>
												<span>보여드리죠.</span>
											</h2>
										</div>
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span>브랜드를 만나면 우리는 그 즉시 그 브랜드의 오타쿠가 됩니다.</span>
												<span>N시간의 플레이타임과 뷰잉타임, N끼니의 식사 경험, N만원의 소비 경험.</span>
												<span>직장인이 아닌 직업인을 지향하는 우리의 킥-오프.</span>
												<span>애정 없이 일을 못해요. 하니 남다른 디테일과 수준이 나옵니다.</span>
												<span>위켄즈의 킥-오프는 언제나 브랜드를 향한 애정과 존중입니다.</span>
											</p>
										</div>
									</div>
								</div>

								<div class="thought-wrap thought-02">
									<div class="gutter pack-down items-center gap-40">
										<div class="title-wrap">
											<h2 class="title fs-100-44 lh110 pack-down items-center ta-c">
												<span>원피스를</span>
												<span class="pack-center">
													<span>찾아</span>
													<span class="svg-box">
														<i class="ico mask-wave bg-orange"></i>
													</span> 
													<span>항해하는</span>
												</span>
												<span>중입니다.</span>
											</h2>
										</div>
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span>누구는 오올블루를 찾아서, 누구는 최고의 검사가 되기 위해, </span>
												<span>또 누구는 언제나 원했던 것이어서.</span>
												<span>목적이 달라도 함께 원피스를 찾아가는 이야기가 너무 좋았어요.</span>
												<span>그래서 그런 회사를 만들기로 했습니다.</span>
												<span>임직원은 물론 고객사, 협력사와 함께, 오늘도 모험을 시작합니다.</span>
											</p>
										</div>
									</div>
								</div>


								<div class="thought-wrap thought-03">
									<div class="gutter ">
										<div class="text-wrap">
											<p class="text fs-24-18 fw400">
												<span>누구는 오올블루를 찾아서, 누구는 최고의 검사가 되기 위해, </span>
												<span>또 누구는 언제나 원했던 것이어서.</span>
												<span>목적이 달라도 함께 원피스를 찾아가는 이야기가 너무 좋았어요.</span>
												<span>그래서 그런 회사를 만들기로 했습니다.</span>
												<span>임직원은 물론 고객사, 협력사와 함께, 오늘도 모험을 시작합니다.</span>
											</p>
										</div>
									</div>

									<div class="matter-area">
										<div class="matter-wrap" id="matterFall">
											<span class="char" data-char>그</span>
											<span class="char" data-char>래</span>
											<span class="char" data-char>도</span>
											<span class="char" data-char>내</span>
											<span class="char" data-char>일</span>
											<span class="char" data-char>은</span>
											<span class="char" data-char>옵</span>
											<span class="char" data-char>니</span>
											<span class="char" data-char>다</span>
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
											Creating  <br>
											Experiences <br>
											Together.
										</span>
									</h2>
									<p class="text fc-white fs-32-16 fw400 lh125" data-gsap="fade-up">
										<span>
											다양한 브랜드와 함께 사람들의 기억에 오래 남을 <br>
											공간과 경험을 만들어가고 있습니다.
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
											Our Team
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
												아쉽게도?! 엄마/아빠보다, 남편/아내보다, 자녀보다 더 많은 시간을 동료와  함께 보내게 됩니다. <br>
												의리와 온정은 필수입니다. 험난한 세상을 함께 헤쳐나갈 수 있는 서로에게 좋은 동료가 됩시다.
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												프로라면 프로답게, 아직 프로가 되지 못했다면 최대한 빠르게 프로가 되어야합니다. <br>
												명함이 품질보증이 될 수 있도록 프로 퍼포먼스를 보여줍시다.
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												한 명도, 한 시간도 부당하게 일하지 않습니다. <br>
												식대, 연/월/반차, 연장 근로 수당, 성과급, DC형 퇴직금 등 법적 기준을 완벽히 충족하는 근무 조건에서 일합니다.
											</span>
										</li>
										<li class="fs-24-16 fw400 lh180 fc-white" data-gsap="fade-up">
											<span class="dot bg-white"></span>
											<span class="text">
												창립 이래 매년 한번은 워크샵을 갑니다. 맛있는 것도 가능한 자주 먹습니다. <br>
												함께하는 시간이 젊은 날의 찬란한 기억이 될 수 있도록 합시다. 피할 수 없어요. 즐겨요!
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

