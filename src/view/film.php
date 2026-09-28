<main id="content" class="sub films">

	<div class="inner-area pack-down gap-106-60">


		<div class="page-header">

			<div class="gutter">

				<div class="max-1340">

					<div class="title-area">

						<h2 class="page-tit lh125 fs-120-48">

							<strong class="split">
								<span>FILMS</span>
							</strong>

						</h2>

					</div>

				</div>

			</div>

		</div>
		<!-- // [Page Header] -->


		<div
			class="page-content"
			data-gsap="bg-black"
		>

			<div class="grid-area">

				<ul class="grid-wrap col-5-2 gap-4">


					<?
					foreach(
						$film_list as $film
					){

						$film_title =
							isset($film['title'])
								? trim($film['title'])
								: '';


						$film_link =
							isset($film['link_url'])
								? trim($film['link_url'])
								: '';


						$film_image =
							isset($film['storage_key'])
								? trim($film['storage_key'])
								: '';
					?>

						<li data-gsap="fade-up">

							<a
								href="<?=$film_link != '' ? htmlspecialchars($film_link, ENT_QUOTES, 'UTF-8') : 'javascript:void(0);'?>"
								<? if($film_link != ''){ ?>
								target="_blank"
								rel="noopener noreferrer"
								<? } ?>
							>

								<div class="img-area">

									<picture class="img-wrap">

										<img
											src="<?=htmlspecialchars($film_image, ENT_QUOTES, 'UTF-8')?>"
											alt="<?=htmlspecialchars($film_title, ENT_QUOTES, 'UTF-8')?>"
										>

									</picture>

								</div>

							</a>

						</li>

					<?
					}
					?>


				</ul>

			</div>

		</div>
		<!-- // [Page Content] -->


	</div>

</main>