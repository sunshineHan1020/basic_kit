<?php

include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';

include $_SERVER['DOCUMENT_ROOT'].'/src/view/contact.php';


include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';

?>


<script src="/@resource//js/lib/swiper-bundle.min.js"></script>

	<script src="/@resource//js/lib/gsap.min.js"></script>
	<script src="/@resource//js/lib/ScrollTrigger.min.js"></script>
	<script src="/@resource//js/lib/SplitText.min.js"></script>
	<script src="/@resource//js/lib/lenis.min.js"></script>


	<script src="/@resource//js/motion.js"></script>
	<script>
		/* {GSAP Lib} - 이 페이지에서 사용하는 GSAP 플러그인 */
		gsap.registerPlugin(ScrollTrigger, SplitText);
		let mm = gsap.matchMedia();

		/*
			숨기는 것은 body가 보이기 전에 즉시, 재생하는 것만 나중에(transitionend 이후) 해야 한다.
			둘 다 나중으로 미루면 body 페이드인 0.8s 동안 타이틀/리스트가 안 숨겨진 채로 그대로
			보이다가 뒤늦게 숨겨졌다 다시 재생되는 것처럼 보인다.
		*/
		gsap.set('.page-tit .split-line span', { yPercent: 102, autoAlpha: 0 });
		gsap.set('[data-gsap="fade-up"]', { y: 30, autoAlpha: 0 });

		/* {isLayoutReady} - header/footer include 완료 뒤 실행 */
		function isLayoutReady() {
			/* <Nav Current> */
			isCurrent(5);

			/* <Layout GSAP Motions> */
			headerGsap();
			footerGsap();

			requestAnimationFrame(() => {
				window.lenis?.resize();
				ScrollTrigger.refresh();
				document.body.classList.add('loaded');
				window.lenis?.start();

				
				let introStarted = false;
				function startContactIntro() {
					if (introStarted) return;
					introStarted = true;

					const page 		= document.querySelector('.sub.contact');
					const contacts 	= {}

					// Page Header
					contacts.header	 = page.querySelector('.page-header');
					contacts.tit	 = gsap.utils.toArray('.page-tit .split-line span');

					// GSAP
					contacts.tl = gsap.timeline({});

					contacts.tl
						.to	(contacts.tit, 		{
							yPercent: 0,
							autoAlpha: 1,
							duration: 2.6,
							ease: 'elastic.out(1, 0.7)',
							stagger: 0.1,
						});
					fadeUpGsap(96); // GSAP Fade Up Motions
				}

				document.body.addEventListener('transitionend', function onLoadedFade(e) {
					if (e.target !== document.body || e.propertyName !== 'opacity') return;
					document.body.removeEventListener('transitionend', onLoadedFade);
					startContactIntro();
				});
				setTimeout(startContactIntro, 900);
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
		});
		/* // [Document Ready] - DOM 로드 시 실행 함수 종료 */
	</script>

</body>
</html>