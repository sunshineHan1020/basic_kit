<?include $_SERVER['DOCUMENT_ROOT'].'/admin/inc/header.php';?>

<main class="content">

    <!-- Page Header -->
    <div class="page-header">

        <div class="page-title">
            <span>BLOG</span>
            <i class="ri-arrow-right-s-line"></i>
            <h2>게시글 등록</h2>
        </div>

        <div class="page-action">

            <button class="btn btn-white">
                미리보기
            </button>

            <select class="select-status">
                <option>임시저장</option>
            </select>

            <button class="btn btn-primary">
                등록하기
            </button>

        </div>

    </div>


    <div class="editor-layout">


        <!-- ========================= -->
        <!-- LEFT -->
        <!-- ========================= -->

        <section class="editor-main">

            <!-- 기본정보 -->
            <div class="card">

                <div class="card-header">
                    <h3>기본 정보</h3>
                </div>

                <div class="card-body">

                    <!-- 제목 -->
                    <div class="form-row">

                        <label>
                            제목
                            <em>*</em>
                        </label>

                        <div class="form-control">

                            <input
                                type="text"
                                placeholder="제목을 입력해주세요."
                            >

                        </div>

                    </div>


                    <!-- 카테고리 -->
                    <div class="form-row">

                        <label>
                            카테고리
                            <em>*</em>
                        </label>

                        <div class="form-control">

                            <select>
                                <option>카테고리를 선택하세요.</option>
                            </select>

                        </div>

                    </div>


                    <!-- 태그 -->
                    <div class="form-row">

                        <label>
                            태그
                        </label>

                        <div class="form-control">

                            <input
                                type="text"
                                placeholder="태그를 입력 후 Enter를 눌러주세요."
                            >

                        </div>

                    </div>


                    <!-- 대표이미지 -->
                    <div class="form-row">

                        <label>대표 이미지</label>

                        <div class="form-control">

                            <div class="upload-box">

                                <button>
                                    +
                                    <span>이미지 업로드</span>
                                </button>

                            </div>

                            <div class="upload-guide">

                                <p>권장 사이즈 : 1200 × 630px (1.91:1)</p>
                                <p>JPG, PNG, WEBP (최대 5MB)</p>

                            </div>

                        </div>

                    </div>


                    <!-- 요약 -->
                    <div class="form-row">

                        <label>요약 설명</label>

                        <div class="form-control">

                            <textarea
                                rows="4"
                                placeholder="게시글에 대한 간단한 요약을 입력해주세요."
                            ></textarea>

                            <div class="text-counter">
                                0 / 200
                            </div>

                        </div>

                    </div>


                    <!-- 본문 -->
                    <div class="form-row editor-row">

                        <label>
                            본문
                            <em>*</em>
                        </label>

                        <div class="form-control">

                            <textarea id="editor"></textarea>

                        </div>

                    </div>

                </div>

            </div>



            <!-- 공개설정 -->
            <div class="card">

                <div class="card-header">
                    <h3>공개 설정</h3>
                </div>

                <div class="card-body">


                    <div class="setting-grid">


                        <div class="setting-item">

                            <label>공개 여부</label>

                            <div class="radio-group">

                                <label>
                                    <input type="radio" name="open" checked>
                                    공개
                                </label>

                                <label>
                                    <input type="radio" name="open">
                                    비공개
                                </label>

                            </div>

                        </div>



                        <div class="setting-item">

                            <label>작성일</label>

                            <input type="datetime-local">

                        </div>



                        <div class="setting-item">

                            <label>예약 게시</label>

                            <div class="reserve-group">

                                <input
                                    type="date"
                                >

                                <input
                                    type="time"
                                >

                            </div>

                        </div>



                        <div class="setting-item">

                            <label>작성자</label>

                            <input
                                type="text"
                                value="관리자(admin)"
                            >

                        </div>


                    </div>

                </div>

            </div>

        </section>





        <!-- ========================= -->
        <!-- SEO -->
        <!-- ========================= -->

        <aside class="editor-side">

            <div class="card seo-card">

                <div class="card-header">
                    <h3>SEO 관리</h3>
                </div>


                <div class="card-body">


                    <!-- Preview -->

                    <div class="seo-preview">

                        <h4>미리보기</h4>

                        <div class="preview-box">

                            <small>
                                https://www.weekends.co.kr/blog/sample-post
                            </small>

                            <strong>
                                블로그 게시글 제목 예시 | WEEKENDS
                            </strong>

                            <p>
                                이것은 메타 설명이 노출되는 영역입니다.
                            </p>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>SEO Title</label>

                        <input type="text">

                    </div>


                    <div class="form-group">

                        <label>Meta Description</label>

                        <textarea rows="4"></textarea>

                    </div>


                    <div class="form-group">

                        <label>Meta Keywords</label>

                        <input
                            type="text"
                            placeholder="Enter 입력"
                        >

                    </div>


                    <div class="form-group">

                        <label>Canonical URL</label>

                        <input type="text">
                    </div>


                    <div class="form-group">

                        <label>OG Title</label>

                        <input type="text">

                    </div>


                    <div class="form-group">

                        <label>OG Description</label>

                        <textarea rows="4"></textarea>

                    </div>


                    <div class="form-group">

                        <label>OG Image</label>

                        <div class="upload-box">

                            <button>
                                +
                                <span>이미지 업로드</span>
                            </button>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>로봇 설정</label>

                        <div class="check-group">

                            <label>
                                <input type="checkbox" checked>
                                Index 허용
                            </label>

                            <label>
                                <input type="checkbox">
                                No Follow
                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </aside>

    </div>

</main>

</body>
</html>