<?include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/head.php';?>
<!-- ===========================
    Sidebar
============================ -->

<header class="admin-header">

    <button class="sidebar-toggle">
        <i class="ri-menu-line"></i>
    </button>

    <a class="logo" href="#">
        <img src="./images/logo-white.svg">
    </a>

    <div class="header-right">
        ...
    </div>

</header>

<aside class="sidebar">

    <!-- Logo -->
	

      


    <!-- Navigation -->
    <nav class="sidebar-nav">

        <ul class="gnb">

            <li>
                <a href="dashboard.html">
                    <i class="ri-home-5-line"> <span> 대시보드</span></i>
                    
                </a>
            </li>


            <!-- 페이지관리 -->
			

			  <li class="">

                <a href="/manage/">

                    <div class="menu-left">
                        <i class="ri-dashboard-line"></i>
                        <span>대시보드</span>
                    </div>


                </a>

            </li>



    


            <!-- Works -->
            <li class="has-sub">

                <a href="#">

                    <div class="menu-left">
                        <i class="ri-box-3-line"></i>
                        <span>WORKS</span>
                    </div>

                    <i class="ri-arrow-down-s-line"></i>

                </a>

				 <ul class="sub-menu">

                    <li>
                        <a href="/manage/works_list">
                            게시글 목록
                        </a>
                    </li>

                    <li>
                        <a href="/manage/works_write">
                            게시글 등록
                        </a>
                    </li>

                    <li>
                        <a href="/manage/works_category_list">
                            카테고리 관리
                        </a>
                    </li>

                    <li>
                        <a href="/manage/works_tag_list">
                            태그 관리
                        </a>
                    </li>

                </ul>


            </li>



            <!-- HLDYS -->
			<!--
            <li class="">

                <a href="#">

                    <div class="menu-left">
                        <i class="ri-home-smile-line"></i>
                        <span>HLDYS</span>
                    </div>


                </a>

            </li>
			-->



            <!-- BLOG -->
            <li class="has-sub  ">

                <a href="#">

                    <div class="menu-left">
                        <i class="ri-article-line"></i>
                        <span>NEWS</span>
                    </div>

                    <i class="ri-arrow-down-s-line"></i>

                </a>

                <ul class="sub-menu">

                    <li>
                        <a href="/manage/blog_list">
                            게시글 목록
                        </a>
                    </li>

                    <li>
                        <a href="/manage/blog_write">
                            게시글 등록
                        </a>
                    </li>

                    <li>
                        <a href="/manage/blog_category_list">
                            카테고리 관리
                        </a>
                    </li>

                    <li>
                        <a href="/manage/blog_tag_list">
                            태그 관리
                        </a>
                    </li>

                </ul>

            </li>


			
			<li class="">

                <a href="/manage/film_list">

                    <div class="menu-left">
                        <i class="ri-film-line"></i>
                        <span>FLIM 관리</span>
                    </div>


                </a>

            </li>

			<li class="">

                <a href="/manage/client_list.php">

                    <div class="menu-left">
                        <i class="ri-building-4-line"></i>
                        <span>Client 관리</span>
                    </div>


                </a>

            </li>

			<li class="">

                <a href="/manage/member_list.php">

                    <div class="menu-left">
                        <i class="ri-team-line"></i>
                        <span>멤버 관리</span>
                    </div>


                </a>

            </li>




			<?if($_SESSION['ADMIN_ROLE']=='super' ||  $_SESSION['ADMIN_ROLE']=='admin'  ){?>
			 <li class="">

                <a href="/manage/inquiry_option_list">

                    <div class="menu-left">
                        <i class="ri-question-answer-line"></i>
                        <span>문의 유형 관리</span>
                    </div>


                </a>

            </li>

            <li class="">

                <a href="/manage/inquiry_list.php">

                    <div class="menu-left">
                        <i class="ri-list-check-3"></i>
                        <span>문의 관리</span>
                    </div>


                </a>

            </li>
			<?}?>
			

			


        



    
		

			<li class="">

                <a href="/manage/seo_list">

                    <div class="menu-left">
                        <i class="ri-search-eye-line"></i>
                        <span>SEO 관리</span>
                    </div>


                </a>

            </li>
		<?if($_SESSION['ADMIN_ROLE']=='super'){?>
			<li class="">

                <a href="/manage/admin_list">

                    <div class="menu-left">
                        <i class="ri-team-line"></i>
                        <span>관리자 관리</span>
                    </div>


                </a>

            </li>
			<?}?>

				
				<?if($_SESSION['ADMIN_ROLE']=='super' ||  $_SESSION['ADMIN_ROLE']=='admin'  ){?>
				<li class="">

                <a href="/manage/setting.php">

                    <div class="menu-left">
                        <i class="ri-settings-3-line"></i>
                        <span>설정</span>
                    </div>


                </a>

            </li>

			<?}?>





       


	






        
        </ul>

    </nav>



    <!-- User -->
    <div class="sidebar-user">

        <div class="user-info">

            <div class="avatar">
                W
            </div>

            <div class="user-text">

                <strong>관리자</strong>

                <span>weekends.co.kr</span>

            </div>

        </div>

        <button class="btn-logout"  id="adminLogout">

            <i class="ri-logout-box-r-line"></i>

            로그아웃

        </button>

    </div>

</aside>
<div class="sidebar-overlay"></div>