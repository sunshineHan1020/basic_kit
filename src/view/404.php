<?php

include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';

?>

<main id="content" class="sub others error">
				<section class="section error">
					<div class="actions-wrap pack-down items-center gap-40-24">
						<div class="title-wrap pack-down items-center ta-c gap-20-14">
							<p class="page-txt fs-32-18 lh110">
								<strong class="split">
									<span>404</span>
								</strong>
								<strong class="split">
									<span>ERROE PAGE</span>
								</strong>
							</p>
							<h2 class="page-tit fs-68-24 lh125 fw700 uppercase">
								<span class="split">
									<span>Page Not Found</span>
								</span>
							</h2>
						</div>
						<div class="button-wrap center">
							<a href="/" class="btn line-b small r-100 lh100">
								<strong>Back To <span class="underline">Home</span></strong>
							</a>
						</div>
					</div>

					<div class="error-wrap">
						<h3 class="title">
							<strong>404</strong>
						</h3>
					</div>

				</div>

				</section>
			</main>



<?
include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';
?>
	<script src="/@resource/js/lib/swiper-bundle.min.js"></script>

	<script src="/@resource/js/lib/gsap.min.js"></script>
	<script src="/@resource/js/lib/ScrollTrigger.min.js"></script>
	<script src="/@resource/js/lib/SplitText.min.js"></script>
	<script src="/@resource/js/lib/lenis.min.js"></script>


	<script src="/@resource/js/motion.js"></script>
	<script>
		/* {GSAP Lib} - 이 페이지에서 사용하는 GSAP 플러그인 */
		gsap.registerPlugin(ScrollTrigger, SplitText);
		let mm = gsap.matchMedia();

		/* {isLayoutReady} - header/footer include 완료 뒤 실행 */
		function isLayoutReady() {
			/* <Nav Current> */
			isCurrent(0);

			/* <Layout GSAP Motions> */
			headerGsap();
			footerGsap();

			/*
				include로 삽입된 header/footer의 실제 높이가 브라우저에 반영된 다음,
				Lenis와 ScrollTrigger의 최초 위치를 한 번만 측정한다.
			*/
			requestAnimationFrame(() => {
				window.lenis?.resize();
				ScrollTrigger.refresh();
				document.body.classList.add('loaded');
				window.lenis?.start();
			});
		}

		/* {Lenis} - 초기 멈춤 */
		window.lenis?.stop();

		/* {isLayoutReady} - 함수 실행 */
		typeof isLayoutPath === 'function'
			? isLayoutPath(isLayoutReady)
			: isLayoutReady();

		/* [Document Ready] (DOM) */
		/* Description: DOM 로드 시 실행 함수 */
		$(function(){

			const pageTl  	= gsap.timeline({});
			
			const pageTit 	= $('.page-tit .split span');
			const pageTxt 	= $('.page-txt .split span');
			const pageBtn 	= $('.button-wrap');
			const errorTit 	= $('.error-wrap .title');

			gsap.set([pageTit,pageTxt,pageBtn], { yPercent: 105, autoAlpha: 0 });
			gsap.set(errorTit, { filter: 'blur(10px)', scale: 0.8, })

			pageTl.to([pageTit, pageTxt, pageBtn],{	// 페이지 타이틀
				yPercent: 0,
				autoAlpha: 1,
				duration: 1.2,
				ease: "cubic-bezier(0.22, 1, 0.36, 1)",
				stagger: 0.1,
				delay:0.5,
			}).to(errorTit, {
				filter: 'blur(0px)', 
				scale: 1,
				duration: 1.25,
				// ease: "power4.out"
			},"<");


			const sectionError = document.querySelector('.section.error');
			gsap.to(errorTit, {
				scrollTrigger: {
					trigger: sectionError,
					start: 'top top',
					end: 'bottom center',
					scrub: true,
					//markers: true,
				},
				yPercent: -20,
				ease: 'none'
			});

		});
		/* // [Document Ready] - DOM 로드 시 실행 함수 종료 */
	</script>

</body>
</html>