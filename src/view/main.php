<?
/*
 * =========================================================
 * MAIN VIEW
 * =========================================================
 */

if(
    !function_exists(
        'main_view_h'
    )
){

    function main_view_h($str)
    {

        return htmlspecialchars(
            $str,
            ENT_QUOTES,
            'UTF-8'
        );

    }

}
?>


<!-- [D] header-white 클래스  Main / About 페이지 필수 -->
<main id="content" class="main header-white">

    <section class="section visual">
        <div class="canvas-wrap">
            <canvas id="heros"></canvas>
        </div>

        <div class="gutter is-full">
            <div class="visual-wrap">
                <div class="img-area pack-center is-full">
                    <div class="img-wrap">
                        <img src="../@resource/images/@common/ani-logo.png" alt="Weekends | 위켄즈">
                    </div>
                </div>
            </div>

            <div class="shape-area">
                <div class="shape-inner">
                    <div class="shape-wrap changes changes-01">
                        <div class="text-wrap pack-down items-center gap-40-20 ta-c">
                            <h3 class="title fc-white lh100">
                                <strong><?=wc_h(wc_text($pageContent, 'visual_01'))?> <br class="only-mo"> <?=wc_h(wc_text($pageContent, 'visual_02'))?></strong>
                            </h3>
                            <p class="desc fc-white lh160">
                                <span><?=wc_h(wc_text($pageContent, 'visual_03'))?></span>
                            </p>
                        </div>
                    </div>

                    <div class="shape-wrap changes changes-02">
                        <div class="text-wrap pack-down items-center gap-40-20 ta-c">
                            <h3 class="title fc-white lh100">
                                <strong><?=wc_h(wc_text($pageContent, 'visual_04'))?> <br class="only-mo"> <?=wc_h(wc_text($pageContent, 'visual_05'))?></strong>
                            </h3>
                            <p class="desc fc-white lh160">
                                <span><?=wc_h(wc_text($pageContent, 'visual_06'))?></span>
                            </p>
                        </div>
                    </div>

                    <div class="shape-wrap changes changes-03">
                        <div class="text-wrap pack-down items-center gap-40-20 ta-c">
                            <h3 class="title fc-white lh100">
                                <strong><?=wc_h(wc_text($pageContent, 'visual_07'))?> <br class="only-mo"> <?=wc_h(wc_text($pageContent, 'visual_08'))?></strong>
                            </h3>
                            <p class="desc fc-white lh160">
                                <span><?=wc_h(wc_text($pageContent, 'visual_09'))?></span>
                            </p>
                        </div>
                    </div>


                    <div class="shape-wrap lines lines-01">
                        <div class="text-wrap">
                            <p class="text">
                                <span><?=wc_h(wc_text($pageContent, 'visual_10'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_11'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_12'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_13'))?></span>
                            </p>
                        </div>
                    </div>

                    <div class="shape-wrap lines lines-02">
                        <div class="text-wrap">
                            <p class="text">
                                <span><?=wc_h(wc_text($pageContent, 'visual_14'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_15'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_16'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_17'))?></span>
                                <span><?=wc_h(wc_text($pageContent, 'visual_18'))?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- // [Section Visual End] 섹션 종료 -->



    <section class="section gradient">
        <div class="img-area">
            <picture class="img-wrap">
                <source srcset="../@resource/images/@common/gradient.webp" type="image/webp">
                <img src="../@resource/images/@common/gradient.png" alt="Weekends">
            </picture>
        </div>
    </section>
    <!-- // [Section Gradient End] 섹션 종료 -->



    <section class="section matters" data-gsap="bg-white">
        <div class="matter-area">
            <div class="matter-wrap">
            </div>
        </div>
    </section>
    <!-- // [Section Matters End] 섹션 종료 -->



    <section class="section collab">
        <div class="collab-area">


            <?
            if(
                isset($main_exhibitions) &&
                is_array($main_exhibitions) &&
                count($main_exhibitions) > 0
            ){

                $collab_index =
                    0;


                foreach(
                    $main_exhibitions as $data
                ){

                    $collab_index++;


                    /*
                     * =========================================
                     * NUMBER
                     * 01 / 02 / 03 / 04
                     * =========================================
                     */

                    $collab_no =
                        str_pad(
                            $collab_index,
                            2,
                            '0',
                            STR_PAD_LEFT
                        );



                    /*
                     * =========================================
                     * BASIC DATA
                     * =========================================
                     */

                    $display_title =
                        isset($data['display_title'])
                            ? trim(
                                $data['display_title']
                            )
                            : '';


                    $intro_text =
                        isset($data['intro_text'])
                            ? trim(
                                $data['intro_text']
                            )
                            : '';


                    $summary =
                        isset($data['summary'])
                            ? trim(
                                $data['summary']
                            )
                            : '';


                    $image_path =
                        isset($data['image_path'])
                            ? trim(
                                $data['image_path']
                            )
                            : '';


                    $video_path =
                        isset($data['video_path'])
                            ? trim(
                                $data['video_path']
                            )
                            : '';


                    $video_mime =
                        isset($data['video_mime']) &&
                        trim($data['video_mime']) != ''
                            ? trim(
                                $data['video_mime']
                            )
                            : 'video/mp4';


                    $band_color =
                        isset($data['band_color'])
                            ? $data['band_color']
                            : '#FFFFFF';


                    $text_color =
                        isset($data['text_color'])
                            ? $data['text_color']
                            : '#FFFFFF';


                    $line_text =
                        isset($data['line_text'])
                            ? trim(
                                $data['line_text']
                            )
                            : '';



                    /*
                     * =========================================
                     * CLIENTS
                     * =========================================
                     */

                    $client_names =
                        isset($data['client_names']) &&
                        is_array($data['client_names'])
                            ? $data['client_names']
                            : array();



                    /*
                     * =========================================
                     * WORK IMAGES
                     * =========================================
                     */

                    $body_images =
                        isset($data['body_images']) &&
                        is_array($data['body_images'])
                            ? $data['body_images']
                            : array();

            ?>


                <div class="collab-wrap collab-<?=$collab_no?>">

                    <div
                        class="collab-bg bg-base"
                        style="--bg-url: url('<?=main_view_h($image_path)?>')"
                    ></div>


                    <div class="collab-dim"></div>


                    <div class="collab-line">

                        <!-- [D] 컬러 코드 값 및 SVG ID 개별 값으로
                            --stroke: 데이터 컬러 코드값 ,
                            --fills: 데이터 컬러 코드값
                        -->

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="2784"
                            height="4467"
                            viewBox="0 0 2784 4467"
                            fill="none"
                            style="--stroke: <?=main_view_h($band_color)?>; --fills: <?=main_view_h($text_color)?>;"
                            class="collab-svg"
                        >

                            <path
                                id="collabPath<?=$collab_no?>"
                                class="collab-path"
                                d="M51.3784 440.497C29.716 666.234 -4.73643 899.728 183.227 1018.54C495.101 1215.68 898.466 903.999 1193.48 488.497C1488.5 72.9958 1897.5 -41.2274 2244.5 79.4939C2591.5 200.215 2871.5 542.994 2694.5 910.497C2517.5 1278 1904 1226.19 1904 1707.49C1904 2188.8 2974.83 2803.6 2601.04 3343.69C2353.96 3700.7 1790.07 3721.82 1351.12 3015.18C934.501 2344.48 44.1229 3124.26 581.162 3619.64C893.127 3907.41 1129.8 4173.85 1228.46 4432.5"
                                stroke="var(--stroke)"
                                stroke-width="68"
                                stroke-linecap="round"
                            />


                            <use
                                href="#collabPath<?=$collab_no?>"
                                class="collab-line-rail"
                                aria-hidden="true"
                            />


                            <g id="collabTextGroup<?=$collab_no?>">

                                <text
                                    class="collab-line-text"
                                    dominant-baseline="middle"
                                    alignment-baseline="middle"
                                    style="fill: var(--fills)"
                                >

                                    <textPath
                                        id="collabTextPath<?=$collab_no?>"
                                        href="#collabPath<?=$collab_no?>"
                                        startOffset="0%"
                                        dy="0.30em"
                                        method="align"
                                        spacing="auto"
                                    >

                                        <!-- [D] 데이터로 값이 들어옴 -->

                                        <?=main_view_h($line_text)?>

                                    </textPath>

                                </text>

                            </g>

                        </svg>

                    </div>



                    <div class="gutter">

                        <div class="max-1520 max-wrap">

                            <div class="collab-inner">


                                <!-- =================================================
                                     TITLE
                                ================================================== -->

                               <div class="title-wrap">

									<h2 class="title">

										<strong>
											<?=nl2br(main_view_h($display_title))?>
										</strong>

									</h2>

								</div>



                                <!-- =================================================
                                     TEXT
                                ================================================== -->

                                <div class="text-wrap">


                                    <h3 class="sub-title">

                                        <strong>

                                            <?
                                            if(
                                                $intro_text != ''
                                            ){

                                                echo nl2br(
                                                    main_view_h(
                                                        $intro_text
                                                    )
                                                );

                                            }
                                            ?>

                                        </strong>

                                    </h3>



                                    <p class="text fs-18-12">

                                        <span>

                                            <?
                                            if(
                                                $summary != ''
                                            ){

                                                echo nl2br(
                                                    main_view_h(
                                                        $summary
                                                    )
                                                );

                                            }
                                            ?>

                                        </span>

                                    </p>

                                </div>



                                <!-- =================================================
                                     WORK IMAGES
                                ================================================== -->

                              <?
									if(
										isset($data['clients']) &&
										is_array($data['clients']) &&
										count($data['clients']) > 0
									){
									?>

										<div class="img-area">

											<?
											foreach(
												$data['clients'] as $client
											){

												$client_name =
													isset($client['client_name'])
														? trim($client['client_name'])
														: '';


												$logo_path =
													isset($client['logo_path'])
														? trim($client['logo_path'])
														: '';


												if(
													$logo_path == ''
												){
													continue;
												}
											?>

												<div class="img-wrap">

													<img
														src="<?=main_view_h($logo_path)?>"
														alt="<?=main_view_h($client_name)?>"
													>

												</div>

											<?
											}
											?>

										</div>

									<?
									}
									?>



                                <!-- =================================================
                                     VIDEO
                                ================================================== -->

                                <?
                                if(
                                    $video_path != ''
                                ){
                                ?>

                                    <div class="video-area">

                                        <div class="video-wrap">

                                            <video autoplay muted playsinline loop>

                                                <source
                                                    src="<?=main_view_h($video_path)?>"
                                                    type="<?=main_view_h($video_mime)?>"
                                                >

                                            </video>

                                        </div>

                                    </div>

                                <?
                                }
                                ?>


                            </div>

                        </div>

                    </div>

                </div>

                <!-- // Collaboration <?=$collab_no?> -->


            <?
                }
            }
            ?>


        </div>
    </section>
    <!-- // [Section Collaboration End] 섹션 종료 -->



    <section class="section gallery">

        <div class="gallery-area grid-wrap col-3">

            <div class="gallery-wrap side-01">

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_01', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_01', 'alt_text'))?>">
                </div>

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_04', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_04', 'alt_text'))?>">
                </div>

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_07', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_07', 'alt_text'))?>">
                </div>

            </div>


            <div class="gallery-wrap gallery">

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_02', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_02', 'alt_text'))?>">
                </div>

                <div class="img-wrap gallery">
                    <span class="blind"><?=wc_h(wc_text($pageContent, 'gallery_01'))?></span>
                    <div class="img" style="background-image: url('<?=wc_h(wc_image($pageImages, 'main_gallery_05', 'storage_key'))?>')"></div>
                </div>

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_08', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_08', 'alt_text'))?>">
                </div>

            </div>


            <div class="gallery-wrap side-03">

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_03', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_03', 'alt_text'))?>">
                </div>

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_06', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_06', 'alt_text'))?>">
                </div>

                <div class="img-wrap">
                    <img src="<?=wc_h(wc_image($pageImages, 'main_gallery_09', 'storage_key'))?>" alt="<?=wc_h(wc_image($pageImages, 'main_gallery_09', 'alt_text'))?>">
                </div>

            </div>


            <div class="marquee-wrap">

                <div class="marquee">

                    <p class="inner uppercase fw700 fc-white"></p>

                </div>

            </div>

        </div>

    </section>
    <!-- // [Section Gallery End] 섹션 종료 -->



    <section class="section achieve">

        <div class="gutter is-full">

            <div class="achieve-inner">

                <div class="title-area pack-center">

                    <h2 class="title fc-white uppercase ta-c fw400 lh130">

                        <span class="split only-pc">
                            <span><?=wc_h(wc_text($pageContent, 'achieve_01'))?></span>
                        </span>


                        <span class="split only-mo">
                            <span><?=wc_h(wc_text($pageContent, 'achieve_02'))?></span>
                        </span>

                        <span class="split only-mo">
                            <span><?=wc_h(wc_text($pageContent, 'achieve_03'))?></span>
                        </span>

                        <span class="split">
                            <span><?=wc_h(wc_text($pageContent, 'achieve_04'))?></span>
                        </span>

                    </h2>

                </div>



                <div class="achieve-area">


                    <div class="achieve-wrap credit">

                        <div class="text-area">

                            <div class="text-wrap pack-down gap-10">

                                <h3 class="title fc-white lh125 fw-base">

                                    <strong>

                                        <?=wc_h(wc_text($pageContent, 'achieve_05'))?> <br>

                                        <span class="fw600">
                                            <?=wc_h(wc_text($pageContent, 'achieve_06'))?>
                                        </span>

                                    </strong>

                                </h3>


                                <p class="text fc-white-60 lh125 fw400">

                                    <span>

                                        <?=wc_h(wc_text($pageContent, 'achieve_07'))?> <br>
                                        <?=wc_h(wc_text($pageContent, 'achieve_08'))?>

                                    </span>

                                </p>

                            </div>


                            <div class="total-wrap">

                                <p class="totals fc-white lh100">

                                    <strong>
                                        <?=wc_h(wc_text($pageContent, 'achieve_09'))?>
                                    </strong>

                                </p>

                            </div>

                        </div>

                    </div>



                    <div class="achieve-wrap partner">

                        <div class="text-area">

                            <div class="text-wrap pack-down gap-10">

                                <h3 class="title fc-white lh125">

                                    <strong>
                                        <?=wc_h(wc_text($pageContent, 'achieve_10'))?>
                                    </strong>

                                </h3>


                                <p class="text fc-white-60 lh125 fw400">

                                    <span>
                                        <?=wc_h(wc_text($pageContent, 'achieve_11'))?>
                                    </span>

                                </p>

                            </div>


                            <div class="total-wrap">

                                <p class="totals fc-white lh100">

                                    <strong>
                                        <?=wc_h(wc_text($pageContent, 'achieve_12'))?>
                                    </strong>

                                </p>

                            </div>

                        </div>

                    </div>



                    <div class="achieve-wrap people">

                        <div class="text-area">

                            <div class="text-wrap pack-down gap-10">

                                <h3 class="title fc-white lh125">

                                    <span>
                                        <?=wc_h(wc_text($pageContent, 'achieve_13'))?>
                                    </span>

                                </h3>


                                <p class="text fc-white-60 lh125 fw400">

                                    <span>
                                        <?=wc_h(wc_text($pageContent, 'achieve_14'))?>
                                    </span>

                                </p>

                            </div>


                            <div class="total-wrap">

                                <p class="totals fc-white lh100">

                                    <strong>
                                        <?=wc_h(wc_text($pageContent, 'achieve_15'))?>
                                    </strong>

                                </p>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </div>

    </section>
    <!-- // [Section Achieve End] 섹션 종료 -->

</main>
