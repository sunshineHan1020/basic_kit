<?php

include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';


if (empty($_SESSION['contact_csrf'])) {

    $_SESSION['contact_csrf'] =
        bin2hex(
            openssl_random_pseudo_bytes(32)
        );

}

$contact_csrf =
    $_SESSION['contact_csrf'];





/*
 * =========================================================
 * CONTACT OPTIONS
 *
 * step_no
 * 1 : Monday    / 프로젝트 유형
 * 2 : Tuesday   / 미팅 장소
 * 3 : Wednesday / 프로젝트 일정
 * 4 : Thursday  / 프로젝트 형태
 * =========================================================
 */

$inquiry_options =
    array(
        1 => array(),
        2 => array(),
        3 => array(),
        4 => array()
    );


$query = "

    SELECT

        id,
        step_no,
        option_code,
        title_en,
        title_ko,
        sort_order

    FROM inquiry_options

    WHERE
        is_active = 1

    ORDER BY
        step_no ASC,
        sort_order ASC,
        id ASC

";


$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


while(
    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        )
){

    $step_no =
        intval(
            $data['step_no']
        );


    if(
        $step_no < 1 ||
        $step_no > 4
    ){

        continue;

    }


    $inquiry_options[$step_no][] =
        $data;

}


$query="SELECT
    cb.id,
    cb.title,
    m.storage_key,
    m.original_name,
    m.mime_type,
    m.byte_size

FROM company_brochures cb

INNER JOIN media m
    ON m.id = cb.media_id
    AND m.deleted_at IS NULL

WHERE
    cb.is_active = 1

ORDER BY
    cb.sort_order ASC,
    cb.id DESC

LIMIT 1";

$ps =
    pdo_query(
        $db,
        $query,
        array()
    );


$intro_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$introduce_file =
    $intro_data
        ? $intro_data['storage_key']
        : '';

$recaptcha_site_key = '6LcdctEtAAAAADpulKdmQSYTCrEIjb59qma5vRic';

?>

<script src="https://www.google.com/recaptcha/api.js?render=<?=$recaptcha_site_key?>"></script>
<style>
#configBtn.is-loading {
	pointer-events: none;
	opacity: .7;
}

#configBtn .is-loading-icon {
	animation: contactSubmitLoading .7s linear infinite;
}

@keyframes contactSubmitLoading {

	to {
		transform: rotate(360deg);
	}

}
</style>
<?
/*
 * =========================================================
 * VIEW
 * =========================================================
 */

include $_SERVER['DOCUMENT_ROOT'].'/src/view/contact_form.php';



include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';

?>

?>

<script src="/@resource//js/lib/swiper-bundle.min.js"></script>

	<script src="/@resource//js/lib/gsap.min.js"></script>
	<script src="/@resource//js/lib/ScrollTrigger.min.js"></script>
	<script src="/@resource//js/lib/SplitText.min.js"></script>
	<script src="/@resource//js/lib/lenis.min.js"></script>


	<script src="/@resource//js/motion.js"></script>
	<script>

	/* {GSAP Lib} - 이 페이지에서 사용하는 GSAP 플러그인 */
	gsap.registerPlugin(
		ScrollTrigger,
		SplitText
	);

	let mm =
		gsap.matchMedia();



	/*
	 * =========================================================
	 * INITIAL STATE
	 * =========================================================
	 */

	gsap.set(
		[
			'#typeNum',
			'#pageTit',
			'#pageSub',
			'#pageTxt'
		],
		{
			y: 40,
			autoAlpha: 0
		}
	);


	gsap.set(
		[
			'.field-area',
			'.form-buttons'
		],
		{
			y: 40,
			autoAlpha: 0
		}
	);


	let startWeekendsIntro =
		() => {};



	/*
	 * =========================================================
	 * LAYOUT READY
	 * =========================================================
	 */

	function isLayoutReady()
	{

		/* <Nav Current> */

		isCurrent(5);


		/* <Layout GSAP Motions> */

		headerGsap();
		footerGsap();


		requestAnimationFrame(
			() =>
			{

				window.lenis?.resize();

				ScrollTrigger.refresh();

				document.body.classList.add(
					'loaded'
				);

				window.lenis?.start();


				document.body.addEventListener(
					'transitionend',
					function onLoadedFade(e)
					{

						if(
							e.target !== document.body ||
							e.propertyName !== 'opacity'
						){

							return;

						}


						document.body.removeEventListener(
							'transitionend',
							onLoadedFade
						);


						startWeekendsIntro();

					}
				);


				setTimeout(
					startWeekendsIntro,
					900
				);

			}
		);

	}



	/*
	 * =========================================================
	 * LENIS
	 * =========================================================
	 */

	window.lenis?.stop();



	/*
	 * =========================================================
	 * LAYOUT START
	 * =========================================================
	 */

	typeof isLayoutPath === 'function'
		? isLayoutPath(isLayoutReady)
		: isLayoutReady();



	/*
	 * =========================================================
	 * DOCUMENT READY
	 * =========================================================
	 */

	$(function () {


		const page =
			document.querySelector(
				'.sub.weekends'
			);


		const weekends =
			{};



		/*
		 * =====================================================
		 * LAYOUTS
		 * =====================================================
		 */

		weekends.header =
			document.querySelector(
				'.page-header'
			);


		weekends.content =
			document.querySelector(
				'.page-content'
			);


		weekends.aside =
			document.querySelectorAll(
				'.step-aside .tag'
			);



		/*
		 * =====================================================
		 * TITLES
		 * =====================================================
		 */

		weekends.tit =
			document.querySelector(
				'#pageTit'
			);


		weekends.sub =
			document.querySelector(
				'#pageSub'
			);


		weekends.txt =
			document.querySelector(
				'#pageTxt'
			);


		weekends.num =
			document.querySelector(
				'#typeNum'
			);



		/*
		 * =====================================================
		 * FORMS
		 * =====================================================
		 */

		weekends.form =
			document.querySelector(
				'#dataForm'
			);


		weekends.fieldArea =
			document.querySelector(
				'.field-area'
			);


		weekends.field =
			document.querySelectorAll(
				'.field-wrap'
			);


		weekends.checkable =
			document.querySelectorAll(
				'.checkable-wrap'
			);



		/*
		 * =====================================================
		 * BUTTONS
		 * =====================================================
		 */

		weekends.prev =
			document.querySelector(
				'#prevBtn'
			);


		weekends.next =
			document.querySelector(
				'#nextBtn'
			);


		weekends.config =
			document.querySelector(
				'#configBtn'
			);


		weekends.formButtons =
			document.querySelector(
				'.form-buttons'
			);


		weekends.compButtons =
			document.querySelector(
				'.comp-buttons'
			);



		/*
		 * =====================================================
		 * FILE UPLOAD
		 * =====================================================
		 */

		weekends.file =
			document.querySelector(
				'#fileUpload'
			);


		weekends.name =
			document.querySelector(
				'#fileName'
			);


		weekends.nametag =
			document.querySelector(
				'#fileNameTag'
			);


		weekends.fileup =
			document.querySelector(
				'#fileUpBtn'
			);


		weekends.filedel =
			document.querySelector(
				'#fileDelBtn'
			);



		/*
		 * =====================================================
		 * TITLES DATA
		 * =====================================================
		 */

		const obj =
			[
				{
					number: '01',
					title: 'Monday',
					subTitle: '어떤 프로젝트를/만들고 싶으신가요?',
					text: '프로젝트 유형을 선택해주세요!'
				},
				{
					number: '02',
					title: 'Tuesday',
					subTitle: '어디에서 만나볼까요?',
					text: '미팅 장소를 선택해주세요!'
				},
				{
					number: '03',
					title: 'Wednesday',
					subTitle: '언제 시작할까요?',
					text: '프로젝트 일정을 선택해주세요!'
				},
				{
					number: '04',
					title: 'Thursday',
					subTitle: '무엇을 만들어내고/싶으신가요?',
					text: '프로젝트 형태를 선택해주세요!'
				},
				{
					number: '05',
					title: 'Friday',
					subTitle: '의뢰하시는 분의/정보를 알려주세요!',
					text: '지금 만나러 가겠습니다!'
				},
				{
					number: null,
					title: 'Weekends',
					subTitle: '문의가 완료되었습니다./이제 마음 편히 주말을 즐기세요.',
					text: '남은 고민은 저희에게 맡기시고 일상은 가볍게 누리세요./담당자가 내용을 확인한 후 빠르게 연락드리겠습니다.'
				}
			];



		/*
		 * =====================================================
		 * STATE
		 * =====================================================
		 */

		let currentStep =
			0;


		const FORM_STEP =
			weekends.checkable.length;


		const COMPLETE_STEP =
			obj.length - 1;


		let isScrolling =
			false;


		let isSubmitting =
				false;


			/*
			 * =====================================================
			 * SUBMIT LOADING
			 * =====================================================
			 */

			const submitButtonText =
				weekends.config
					? weekends.config.querySelector('span')
					: null;


			function setSubmitLoading(isLoading)
			{

				if(
					!weekends.config
				){

					return;

				}


				weekends.config.disabled =
					isLoading;


				weekends.config.classList.toggle(
					'is-loading',
					isLoading
				);


				if(
					submitButtonText
				){

					submitButtonText.textContent =
						isLoading
							? 'Sending...'
							: 'Submit';

				}

			}



		/*
		 * =====================================================
		 * TITLE GSAP
		 * =====================================================
		 */

		function titlesGsap()
		{

			const titleItems =
				[
					weekends.num,
					weekends.tit,
					weekends.sub,
					weekends.txt
				].filter(Boolean);


			gsap.killTweensOf(
				titleItems
			);


			gsap.set(
				titleItems,
				{
					y: 40,
					autoAlpha: 0
				}
			);


			gsap.to(
				titleItems,
				{
					y: 0,
					autoAlpha: 1,
					duration: 1.6,
					ease: 'elastic.out(0.55, 0.85)',
					stagger: 0.1
				}
			);

		}



		/*
		 * =====================================================
		 * FIELD GSAP
		 * =====================================================
		 */

		function fieldGsap(
			isComplete
		){

			const buttonArea =
				isComplete
					? weekends.compButtons
					: weekends.formButtons;


			const motionItems =
				[
					weekends.fieldArea,
					buttonArea
				].filter(Boolean);


			if(
				!motionItems.length
			){

				return;

			}


			gsap.killTweensOf(
				motionItems
			);


			gsap.set(
				motionItems,
				{
					y: 40,
					autoAlpha: 0
				}
			);


			gsap.to(
				motionItems,
				{
					y: 0,
					autoAlpha: 1,
					duration: 1.6,
					ease: 'elastic.out(0.55, 0.85)',
					stagger: 0.1
				}
			);

		}



		/*
		 * =====================================================
		 * TEXT RENDER
		 * =====================================================
		 */

		function renderText(
			element,
			text
		){

			if(
				!element
			){

				return;

			}


			element.replaceChildren();


			text
				.split('/')
				.forEach(
					(line, lineIndex) =>
					{

						if(
							lineIndex > 0
						){

							element.append(
								document.createElement(
									'br'
								)
							);

						}


						element.append(
							document.createTextNode(
								line.trim()
							)
						);

					}
				);

		}



		/*
		 * =====================================================
		 * RENDER
		 * =====================================================
		 */

		function renderObj()
		{

			const data =
				obj[currentStep];


			if(
				!data
			){

				return;

			}


			/*
			 * Number
			 */

			const numSpan =
				weekends.num
					? weekends.num.querySelector(
						'span'
					)
					: null;


			if(
				numSpan
			){

				numSpan.textContent =
					data.number
						? data.number
						: '';

			}



			/*
			 * Title
			 */

			const titleStrong =
				weekends.tit
					? weekends.tit.querySelector(
						'strong'
					)
					: null;


			if(
				titleStrong
			){

				titleStrong.textContent =
					data.title;

			}



			/*
			 * Sub / Text
			 */

			renderText(
				weekends.sub.querySelector(
					'span'
				),
				data.subTitle
			);


			renderText(
				weekends.txt.querySelector(
					'span'
				),
				data.text
			);



			/*
			 * Field
			 */

			renderField();



			const isFirstStep =
				currentStep === 0;


			const isFormStep =
				currentStep === FORM_STEP;


			const isComplete =
				currentStep === COMPLETE_STEP;



			/*
			 * Prev
			 */

			if(
				weekends.prev
			){

				weekends.prev.style.display =
					isFirstStep ||
					isComplete
						? 'none'
						: 'flex';

			}



			/*
			 * NEXT 버튼은 더 이상 사용하지 않음
			 */

			if(
				weekends.next
			){

				weekends.next.style.display =
					'none';

			}



			/*
			 * Submit
			 */

			if(
				weekends.config
			){

				weekends.config.style.display =
					isFormStep
						? 'flex'
						: 'none';

			}



			/*
			 * Button Area
			 */

			weekends.formButtons.style.display =
				isComplete
					? 'none'
					: 'flex';


			weekends.compButtons.style.display =
				isComplete
					? 'flex'
					: 'none';



			/*
			 * Complete
			 */

			page.classList.toggle(
				'is-complete',
				isComplete
			);



			renderAside();

			titlesGsap();

			fieldGsap(
				isComplete
			);

		}



		/*
		 * =====================================================
		 * ASIDE
		 * =====================================================
		 */

		function renderAside()
		{

			weekends.aside.forEach(
				(tag, index) =>
				{

					tag.classList.remove(
						'is-ing',
						'is-comp'
					);


					if(
						index === currentStep
					){

						tag.classList.add(
							'is-ing'
						);

					}else if(
						index < currentStep
					){

						tag.classList.add(
							'is-comp'
						);

					}

				}
			);

		}



		/*
		 * =====================================================
		 * FIELD
		 * =====================================================
		 */

		function renderField()
		{

			weekends.field.forEach(
				(field, fieldIndex) =>
				{

					field.hidden =
						fieldIndex !== currentStep;

				}
			);

		}



		/*
		 * =====================================================
		 * FORM VALIDATION
		 * =====================================================
		 */

		function validateForm()
		{

			/*
			 * 1~4 STEP
			 */

			for(
				let i = 0;
				i < weekends.checkable.length;
				i++
			){

				const checked =
					weekends.checkable[i]
						.querySelector(
							'input[type="checkbox"]:checked'
						);


				if(
					!checked
				){

					alert(
						'선택하지 않은 문의 항목이 있습니다.'
					);


					currentStep =
						i;


					renderObj();


					return false;

				}

			}



			/*
			 * 이름
			 */

			const userName =
				document.querySelector(
					'#userName'
				);


			if(
				!userName ||
				!userName.value.trim()
			){

				alert(
					'이름을 입력해주세요.'
				);


				userName?.focus();


				return false;

			}



			/*
			 * 회사명
			 */

			const companyName =
				document.querySelector(
					'#companyName'
				);


			if(
				!companyName ||
				!companyName.value.trim()
			){

				alert(
					'회사명을 입력해주세요.'
				);


				companyName?.focus();


				return false;

			}



			/*
			 * 연락처
			 */

			const phone =
				document.querySelector(
					'#userPhone'
				);


			if(
				!phone ||
				!phone.value.trim()
			){

				alert(
					'연락처를 입력해주세요.'
				);


				phone?.focus();


				return false;

			}


			const phoneNumber =
				phone.value
					.replace(
						/-/g,
						''
					)
					.trim();


			if(
				!/^01[016789][0-9]{7,8}$/.test(
					phoneNumber
				)
			){

				alert(
					'올바른 연락처를 입력해주세요.'
				);


				phone.focus();


				return false;

			}



			/*
			 * 이메일
			 */

			const email =
				document.querySelector(
					'#userEmail'
				);


			if(
				!email ||
				!email.value.trim()
			){

				alert(
					'이메일을 입력해주세요.'
				);


				email?.focus();


				return false;

			}


			if(
				!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
					email.value.trim()
				)
			){

				alert(
					'올바른 이메일을 입력해주세요.'
				);


				email.focus();


				return false;

			}


			return true;

		}



		/*
		 * =====================================================
		 * HEADER HEIGHT
		 * =====================================================
		 */

		function getHeaderHeight()
		{

			return weekends.header
				? weekends.header
					.getBoundingClientRect()
					.height
				: 0;

		}



		/*
		 * =====================================================
		 * REFRESH
		 * =====================================================
		 */

		function refreshLayout()
		{

			requestAnimationFrame(
				() =>
				{

					window.lenis?.resize();

					window.ScrollTrigger?.refresh();

				}
			);

		}



		/*
		 * =====================================================
		 * SCROLL TOP
		 * =====================================================
		 */

		function scrollToTop(
			onComplete
		){

			if(
				isScrolling
			){

				return;

			}


			isScrolling =
				true;


			let finished =
				false;


			let timer;


			let scrollEndHandler;


			const finish =
				() =>
				{

					if(
						finished
					){

						return;

					}


					finished =
						true;


					clearTimeout(
						timer
					);


					if(
						scrollEndHandler
					){

						window.removeEventListener(
							'scrollend',
							scrollEndHandler
						);

					}


					isScrolling =
						false;


					refreshLayout();


					if(
						typeof onComplete === 'function'
					){

						onComplete();

					}

				};



			if(
				window.lenis
			){

				window.lenis.scrollTo(
					0,
					{
						duration: 0.8,
						immediate: false,
						onComplete: finish
					}
				);


				timer =
					setTimeout(
						finish,
						1000
					);


				return;

			}



			window.scrollTo(
				{
					top: 0,
					behavior: 'smooth'
				}
			);


			scrollEndHandler =
				finish;


			window.addEventListener(
				'scrollend',
				scrollEndHandler,
				{
					once: true
				}
			);


			timer =
				setTimeout(
					finish,
					1000
				);

		}



		/*
		 * =====================================================
		 * FILE RESET
		 * =====================================================
		 */

		function resetFile()
		{

			if(
				weekends.file
			){

				weekends.file.value =
					'';

			}


			if(
				weekends.name
			){

				weekends.name.value =
					'';

			}


			if(
				weekends.nametag
			){

				weekends.nametag.innerHTML =
					'<strong>(선택)</strong> 첨부파일을 업로드해주세요.';

			}


			weekends.fileup?.classList.remove(
				'active',
				'd-none'
			);


			weekends.filedel?.classList.add(
				'd-none'
			);

		}



		/*
		 * =====================================================
		 * FILE UPLOAD
		 * =====================================================
		 */

		if(
			weekends.fileup &&
			weekends.file
		){

			weekends.fileup.addEventListener(
				'click',
				() =>
				{

					weekends.file.click();

				}
			);


			weekends.file.addEventListener(
				'change',
				() =>
				{

					const file =
						weekends.file.files
							? weekends.file.files[0]
							: null;


					if(
						!file
					){

						return;

					}


					if(
						weekends.name
					){

						weekends.name.value =
							file.name;

					}


					if(
						weekends.nametag
					){

						weekends.nametag.textContent =
							file.name;

					}


					weekends.fileup.classList.add(
						'active',
						'd-none'
					);


					weekends.filedel?.classList.remove(
						'd-none'
					);

				}
			);

		}



		if(
			weekends.filedel
		){

			weekends.filedel.addEventListener(
				'click',
				(event) =>
				{

					event.preventDefault();

					event.stopPropagation();

					resetFile();

				}
			);

		}



		/*
		 * =====================================================
		 * PREV
		 * =====================================================
		 */

		function prevBtnEvent()
		{

			if(
				isScrolling ||
				currentStep <= 0
			){

				return;

			}


			currentStep--;


			scrollToTop(
				() =>
				{

					renderObj();

					refreshLayout();

				}
			);

		}



		/*
		 * =====================================================
		 * OPTION SELECT
		 *
		 * 다중 선택 금지
		 * 선택 즉시 다음 단계 이동
		 * =====================================================
		 */

		weekends.checkable.forEach(
			(wrap, wrapIndex) =>
			{

				wrap.addEventListener(
					'change',
					(event) =>
					{

						const target =
							event.target;


						if(
							!target.matches(
								'input[type="checkbox"]'
							)
						){

							return;

						}


						/*
						 * 체크 해제는 허용하지 않음
						 */

						if(
							!target.checked
						){

							target.checked =
								true;


							return;

						}



						/*
						 * 같은 STEP 다른 항목 해제
						 */

						const checkboxes =
							wrap.querySelectorAll(
								'input[type="checkbox"]'
							);


						checkboxes.forEach(
							(checkbox) =>
							{

								if(
									checkbox !== target
								){

									checkbox.checked =
										false;

								}

							}
						);



						/*
						 * 현재 STEP이 맞을 때만
						 * 다음 STEP 이동
						 */

						if(
							wrapIndex !== currentStep
						){

							return;

						}


						if(
							currentStep >= FORM_STEP
						){

							return;

						}


						currentStep++;


						scrollToTop(
							() =>
								{

									renderObj();

									refreshLayout();

								}
						);

					}
				);

			}
		);



		/*
		 * =====================================================
		 * SUBMIT
		 * =====================================================
		 */

		/*
 * =====================================================
 * SUBMIT
 * =====================================================
 */

function configBtnEvent()
{

	if(
		isScrolling ||
		isSubmitting
	){

		return;

	}


	if(
		!validateForm()
	){

		return;

	}


	if(
		!weekends.form
	){

		alert(
			'문의 폼을 찾을 수 없습니다.'
		);

		return;

	}


	/*
	 * reCAPTCHA 확인
	 */

	if(
		typeof grecaptcha === 'undefined'
	){

		alert(
			'보안 인증을 불러오지 못했습니다. 잠시 후 다시 시도해주세요.'
		);

		return;

	}


	isSubmitting =
		true;


	weekends.config.disabled =
		true;



	/*
	 * =================================================
	 * reCAPTCHA v3
	 * =================================================
	 */

	grecaptcha.ready(
		function()
		{

			grecaptcha.execute(
				'<?=$recaptcha_site_key?>',
				{
					action: 'contact'
				}
			)
			.then(
				function(token)
				{

					/*
					 * 토큰 확인
					 */

					if(
						!token
					){

						isSubmitting =
							false;


						weekends.config.disabled =
							false;


						alert(
							'보안 인증에 실패했습니다.'
						);


						return;

					}



					/*
					 * hidden input에 token 저장
					 */

					const recaptchaInput =
						document.querySelector(
							'#recaptcha_token'
						);


					if(
						!recaptchaInput
					){

						isSubmitting =
							false;


						weekends.config.disabled =
							false;


						alert(
							'보안 인증 정보를 찾을 수 없습니다.'
						);


						return;

					}


					recaptchaInput.value =
						token;



					/*
					 * =================================================
					 * FORM DATA
					 *
					 * reCAPTCHA token 세팅 후 생성
					 * =================================================
					 */

					const formData =
						new FormData(
							weekends.form
						);



					/*
					 * =================================================
					 * AJAX
					 * =================================================
					 */

					const xhr =
						new XMLHttpRequest();


					xhr.open(
						'POST',
						'/proc/inquiry_save',
						true
					);


					xhr.onreadystatechange =
						function()
						{

							if(
								xhr.readyState != 4
							){

								return;

							}


							isSubmitting =
								false;


							weekends.config.disabled =
								false;



							/*
							 * HTTP ERROR
							 */

							if(
								xhr.status != 200
							){

								alert(
									'문의 접수 중 오류가 발생했습니다.'
								);

								return;

							}



							/*
							 * JSON
							 */

							let result;


							try{

								result =
									JSON.parse(
										xhr.responseText
									);

							}catch(e){

								alert(
									'문의 접수 중 오류가 발생했습니다.'
								);

								return;

							}



							/*
							 * ERROR
							 */

							if(
								!result.code
							){

								alert(
									result.msg
										? result.msg
										: (
											result.message
												? result.message
												: '문의를 접수하지 못했습니다.'
										)
								);

								return;

							}



							/*
							 * =================================================
							 * COMPLETE
							 * =================================================
							 */

							currentStep =
								COMPLETE_STEP;


							scrollToTop(
								function()
								{

									renderObj();

									refreshLayout();

								}
							);

						};



					/*
					 * NETWORK ERROR
					 */

					xhr.onerror =
						function()
						{

							isSubmitting =
								false;


							weekends.config.disabled =
								false;


							alert(
								'문의 접수 중 오류가 발생했습니다.'
							);

						};



					/*
					 * SEND
					 */

					xhr.send(
						formData
					);

				}
			)
			.catch(
				function()
				{

					isSubmitting =
						false;


					weekends.config.disabled =
						false;


					alert(
						'보안 인증에 실패했습니다. 잠시 후 다시 시도해주세요.'
					);

				}
			);

		}
	);

}



		/*
		 * =====================================================
		 * EVENTS
		 * =====================================================
		 */

		weekends.prev?.addEventListener(
			'click',
			prevBtnEvent
		);


		/*
		 * NEXT는 사용하지 않음
		 */

		weekends.next?.addEventListener(
			'click',
			function(e)
			{

				e.preventDefault();

			}
		);


		weekends.config?.addEventListener(
			'click',
			configBtnEvent
		);

		weekends.file.addEventListener(
	'change',
	() =>
	{

		const file =
			weekends.file.files
				? weekends.file.files[0]
				: null;


		if(
			!file
		){

			return;

		}


		const maxSize =
			5 * 1024 * 1024;


		const allowedExtensions =
			[
				'jpg',
				'jpeg',
				'png',
				'webp',
				'zip',
				'rar',
				'7z',
				'pdf',
				'ppt',
				'pptx',
				'doc',
				'docx',
				'hwp'
			];


		const fileName =
			file.name
				? file.name
				: '';


		const extension =
			fileName
				.split('.')
				.pop()
				.toLowerCase();



		if(
			allowedExtensions.indexOf(
				extension
			) === -1
		){

			alert(
				'이미지, 압축파일, PDF, PPT, PPTX, DOC, DOCX, HWP 파일만 업로드할 수 있습니다.'
			);


			resetFile();

			return;

		}



		if(
			file.size > maxSize
		){

			alert(
				'첨부파일은 5MB 이하만 업로드할 수 있습니다.'
			);


			resetFile();

			return;

		}



		if(
			weekends.name
		){

			weekends.name.value =
				file.name;

		}


		if(
			weekends.nametag
		){

			weekends.nametag.textContent =
				file.name;

		}


		weekends.fileup.classList.add(
			'active',
			'd-none'
		);


		weekends.filedel?.classList.remove(
			'd-none'
		);

	}
);




		/*
		 * =====================================================
		 * FIRST RENDER
		 * =====================================================
		 */

		let introStarted =
			false;


		startWeekendsIntro =
			() =>
			{

				if(
					introStarted
				){

					return;

				}


				introStarted =
					true;


				resetFile();

				renderObj();

				refreshLayout();

			};


	});


	/* // [Document Ready] */




</script>

</body>
</html>