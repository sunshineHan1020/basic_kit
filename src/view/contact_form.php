<?
/*
 * =========================================================
 * CONTACT FORM VIEW
 * =========================================================
 */

if(
    !function_exists(
        'contact_form_h'
    )
){

    function contact_form_h($str)
    {

        return htmlspecialchars(
            $str,
            ENT_QUOTES,
            'UTF-8'
        );

    }

}
?>


<main id="content" class="sub weekends">

    <div class="gutter">


        <!-- =================================================
             STEP ASIDE
        ================================================== -->

        <aside class="step-aside">

            <ul class="pack-basic items-center">

                <li>

                    <span class="tag line-ntr-20 size is-ing r-100">

                        <span>
                            Mon
                        </span>

                    </span>

                </li>


                <li>

                    <span class="tag line-ntr-20 size r-100">

                        <span>
                            Tue
                        </span>

                    </span>

                </li>


                <li>

                    <span class="tag line-ntr-20 size r-100">

                        <span>
                            Wed
                        </span>

                    </span>

                </li>


                <li>

                    <span class="tag line-ntr-20 size r-100">

                        <span>
                            Thu
                        </span>

                    </span>

                </li>


                <li>

                    <span class="tag line-ntr-20 size r-100">

                        <span>
                            Fri
                        </span>

                    </span>

                </li>


                <li>

                    <span class="tag line-ntr-20 size r-100">

                        <span>
                            Weekends
                        </span>

                    </span>

                </li>

            </ul>

        </aside>



        <div class="max-1340 inner-area">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header">

                <div class="title-area pack-down gap-60-24">


                    <div class="title-wrap pack-down">


                        <div class="tag-wrap">

                            <span
                                id="typeNum"
                                class="tag solid-ntr fw700 lh100 size r-100"
                            >

                                <span>
                                    01
                                </span>

                            </span>

                        </div>



                        <h2
                            id="pageTit"
                            class="page-tit fs-88-48 lh100"
                        >

                            <strong>
                                Monday
                            </strong>

                        </h2>


                    </div>



                    <div class="text-wrap pack-down gap-12">


                        <h3
                            id="pageSub"
                            class="page-sub-tit fs-32-24 lh125"
                        >

                            <span>

                                어떤 프로젝트를 <br>
                                만들고 싶으신가요?

                            </span>

                        </h3>


                        <p
                            id="pageTxt"
                            class="page-txt fs-18-14"
                        >

                            <span>
                                프로젝트 유형을 선택해주세요!
                            </span>

                        </p>


                    </div>

                </div>

            </div>



            <!-- =================================================
                 PAGE CONTENT
            ================================================== -->

            <div class="page-content">

                <div class="form-wrap">


                    <form
                        id="dataForm"
                        method="post"
                        enctype="multipart/form-data"
                    >

                        <fieldset class="pack-down gap-60">

							<input
								type="hidden"
								name="contact_csrf"
								value="<?=contact_form_h($contact_csrf)?>"
							>
								<input
								type="hidden"
								name="recaptcha_token"
								id="recaptcha_token"
								value=""
							>


                            <div class="field-area">


                                <!-- =================================================
                                     01
                                     MONDAY
                                ================================================== -->

                                <div class="field-wrap">

                                    <div class="checkable-wrap grid-wrap col-3-2 gap-20-14">


                                        <?
                                        if(
                                            isset($inquiry_options[1]) &&
                                            is_array($inquiry_options[1])
                                        ){

                                            foreach(
                                                $inquiry_options[1] as $option
                                            ){

                                                $option_id =
                                                    intval(
                                                        $option['id']
                                                    );


                                                $title_en =
                                                    isset($option['title_en'])
                                                        ? trim(
                                                            $option['title_en']
                                                        )
                                                        : '';


                                                $title_ko =
                                                    isset($option['title_ko'])
                                                        ? trim(
                                                            $option['title_ko']
                                                        )
                                                        : '';
                                        ?>

                                            <label class="checkable-label checkable-form">

                                                <input
                                                    type="checkbox"
                                                    class="checkbox"
                                                    name="answers[1][]"
                                                    value="<?=$option_id?>"
                                                    hidden
                                                >


                                                <div class="text-wrap pack-down gap-4">

                                                    <h4 class="title">

                                                        <span>
                                                            <?=nl2br(contact_form_h($title_en))?>
                                                        </span>

                                                    </h4>


                                                    <p class="text">

                                                        <span>
                                                            <?=contact_form_h($title_ko)?>
                                                        </span>

                                                    </p>

                                                </div>

                                            </label>

                                        <?
                                            }

                                        }
                                        ?>


                                    </div>

                                </div>

                                <!-- // 01 -->



                                <!-- =================================================
                                     02
                                     TUESDAY
                                ================================================== -->

                                <div class="field-wrap">

                                    <div class="checkable-wrap grid-wrap col-3-2 gap-20-14">


                                        <?
                                        if(
                                            isset($inquiry_options[2]) &&
                                            is_array($inquiry_options[2])
                                        ){

                                            foreach(
                                                $inquiry_options[2] as $option
                                            ){

                                                $option_id =
                                                    intval(
                                                        $option['id']
                                                    );


                                                $title_en =
                                                    isset($option['title_en'])
                                                        ? trim(
                                                            $option['title_en']
                                                        )
                                                        : '';


                                                $title_ko =
                                                    isset($option['title_ko'])
                                                        ? trim(
                                                            $option['title_ko']
                                                        )
                                                        : '';
                                        ?>

                                            <label class="checkable-label checkable-form">

                                                <input
                                                    type="checkbox"
                                                    class="checkbox"
                                                    name="answers[2][]"
                                                    value="<?=$option_id?>"
                                                    hidden
                                                >


                                                <div class="text-wrap pack-down gap-4">

                                                    <h4 class="title">

                                                        <span>
                                                            <?=nl2br(contact_form_h($title_en))?>
                                                        </span>

                                                    </h4>


                                                    <p class="text">

                                                        <span>
                                                            <?=contact_form_h($title_ko)?>
                                                        </span>

                                                    </p>

                                                </div>

                                            </label>

                                        <?
                                            }

                                        }
                                        ?>


                                    </div>

                                </div>

                                <!-- // 02 -->



                                <!-- =================================================
                                     03
                                     WEDNESDAY
                                ================================================== -->

                                <div class="field-wrap">

                                    <div class="checkable-wrap grid-wrap col-3-2 gap-20-14">


                                        <?
                                        if(
                                            isset($inquiry_options[3]) &&
                                            is_array($inquiry_options[3])
                                        ){

                                            foreach(
                                                $inquiry_options[3] as $option
                                            ){

                                                $option_id =
                                                    intval(
                                                        $option['id']
                                                    );


                                                $title_en =
                                                    isset($option['title_en'])
                                                        ? trim(
                                                            $option['title_en']
                                                        )
                                                        : '';


                                                $title_ko =
                                                    isset($option['title_ko'])
                                                        ? trim(
                                                            $option['title_ko']
                                                        )
                                                        : '';
                                        ?>

                                            <label class="checkable-label checkable-form">

                                                <input
                                                    type="checkbox"
                                                    class="checkbox"
                                                    name="answers[3][]"
                                                    value="<?=$option_id?>"
                                                    hidden
                                                >


                                                <div class="text-wrap pack-down gap-4">

                                                    <h4 class="title">

                                                        <span>
                                                            <?=nl2br(contact_form_h($title_en))?>
                                                        </span>

                                                    </h4>


                                                    <p class="text">

                                                        <span>
                                                            <?=contact_form_h($title_ko)?>
                                                        </span>

                                                    </p>

                                                </div>

                                            </label>

                                        <?
                                            }

                                        }
                                        ?>


                                    </div>

                                </div>

                                <!-- // 03 -->



                                <!-- =================================================
                                     04
                                     THURSDAY
                                ================================================== -->

                                <div class="field-wrap">

                                    <div class="checkable-wrap grid-wrap col-3-2 gap-20-14">


                                        <?
                                        if(
                                            isset($inquiry_options[4]) &&
                                            is_array($inquiry_options[4])
                                        ){

                                            foreach(
                                                $inquiry_options[4] as $option
                                            ){

                                                $option_id =
                                                    intval(
                                                        $option['id']
                                                    );


                                                $title_en =
                                                    isset($option['title_en'])
                                                        ? trim(
                                                            $option['title_en']
                                                        )
                                                        : '';


                                                $title_ko =
                                                    isset($option['title_ko'])
                                                        ? trim(
                                                            $option['title_ko']
                                                        )
                                                        : '';
                                        ?>

                                            <label class="checkable-label checkable-form">

                                                <input
                                                    type="checkbox"
                                                    class="checkbox"
                                                    name="answers[4][]"
                                                    value="<?=$option_id?>"
                                                    hidden
                                                >


                                                <div class="text-wrap pack-down gap-4">

                                                    <h4 class="title">

                                                        <span>
                                                            <?=nl2br(contact_form_h($title_en))?>
                                                        </span>

                                                    </h4>


                                                    <p class="text">

                                                        <span>
                                                            <?=contact_form_h($title_ko)?>
                                                        </span>

                                                    </p>

                                                </div>

                                            </label>

                                        <?
                                            }

                                        }
                                        ?>


                                    </div>

                                </div>

                                <!-- // 04 -->



                                <!-- =================================================
                                     05
                                     FRIDAY
                                ================================================== -->

                                <div class="field-wrap pack-down gap-20-14 form-field">


                                    <div class="field">

                                        <label class="form-label">

                                            <input
                                                type="text"
                                                class="input xlarge"
                                                placeholder="이름을 입력해주세요"
                                                id="userName"
                                                name="userName"
                                            >

                                        </label>

                                    </div>



                                    <div class="field">

                                        <label class="form-label">

                                            <input
                                                type="text"
                                                class="input xlarge"
                                                placeholder="회사명을 입력해주세요"
                                                id="companyName"
                                                name="companyName"
                                            >

                                        </label>

                                    </div>



                                    <div class="field">

                                        <label class="form-label">

                                            <input
                                                type="tel"
                                                class="input xlarge"
                                                placeholder="연락처를 입력해주세요"
                                                id="userPhone"
                                                name="userPhone"
                                                oninput="isMaxlength(this, 11)"
                                            >

                                        </label>

                                    </div>



                                    <div class="field">

                                        <label class="form-label">

                                            <input
                                                type="email"
                                                class="input xlarge"
                                                placeholder="이메일을 입력해주세요"
                                                id="userEmail"
                                                name="userEmail"
                                            >

                                        </label>

                                    </div>



                                    <!-- FILE -->

                                    <div class="field">

                                        <div class="input xlarge upload-wrap">


                                            <div class="upload-label ellipsis">

                                                <input
														id="fileUpload"
														name="attachment"
														type="file"
														accept=".jpg,.jpeg,.png,.webp,.zip,.rar,.7z,.pdf,.ppt,.pptx,.doc,.docx,.hwp"
														hidden
													>


                                                <input
                                                    id="fileName"
                                                    type="text"
                                                    hidden
                                                    readonly
                                                >


                                                <span
                                                    id="fileNameTag"
                                                    class="pack-basic items-center gap-4 ellipsis"
                                                >

                                                    <strong>
                                                        (선택)
                                                    </strong>

                                                    첨부파일을 업로드해주세요.

                                                </span>

                                            </div>



                                            <div class="button-wrap uploads">


                                                <button
                                                    id="fileUpBtn"
                                                    type="button"
                                                    class="btn file-up small r-100 solid-b fs-14-12 fw400"
                                                >

                                                    <span>
                                                        파일 업로드
                                                    </span>

                                                </button>



                                                <button
                                                    id="fileDelBtn"
                                                    type="button"
                                                    class="btn file-del d-none"
                                                >

                                                    <i class="ico mask-cirClose bg-ntr-700"></i>

                                                </button>


                                            </div>


                                        </div>

                                    </div>


                                </div>

                                <!-- // 05 -->


                            </div>



                            <!-- =================================================
                                 FORM BUTTON
                            ================================================== -->

                            <div class="button-area center gap-20-14 form-buttons">


                                <button
                                    id="prevBtn"
                                    type="button"
                                    class="btn solid-zinc small r-100 fs-14-12 gap-4"
                                >

                                    <i class="ico mask-arrow-r bg-ntr-700 rotate-180"></i>

                                    <span>
                                        Prev
                                    </span>

                                </button>



                                <button
                                    id="nextBtn"
                                    type="button"
                                    class="btn solid-zinc small r-100 fs-14-12 gap-4"
                                >

                                    <span>
                                        NEXT
                                    </span>

                                    <i class="ico mask-arrow-r bg-ntr-700"></i>

                                </button>



                                <button
                                    id="configBtn"
                                    type="button"
                                    class="btn solid-b small r-100 fs-14-12 gap-4"
                                >

                                    <span>
                                        Submit
                                    </span>

                                    <i class="ico mask-arrow-r bg-white"></i>

                                </button>


                            </div>



                            <!-- =================================================
                                 COMPLETE BUTTON
                            ================================================== -->

                            <div class="button-area center gap-20-14 comp-buttons">


                              <? if($introduce_file != ''){ ?>

								<a
									href="<?=$introduce_file?>"
									download="weekends_brochure.pdf"
									class="btn solid-orange small r-100 fs-14-12 gap-4"
								>
									<span>
										회사소개 다운로드
									</span>

									<i class="ico mask-download bg-white"></i>
								</a>

								<? } ?>


                                <a
                                    href="/"
                                    class="btn solid-zinc small r-100 fs-14-12 gap-4 uppercase fw600"
                                >

                                    <span>
                                        GO To MAIN
                                    </span>

                                    <i class="ico mask-arrow-r bg-ntr-700"></i>

                                </a>


                            </div>


                        </fieldset>

                    </form>


                </div>

            </div>


        </div>

    </div>

</main>