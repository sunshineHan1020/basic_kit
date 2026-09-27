<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 관리자 관리
 *
 * FILE
 * /manage/admin_list.php
 *
 * URL
 * /manage/admin_list
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';



if($_SESSION['ADMIN_ROLE']!='super'){
	page_move("/manage/");
	exit;
}

/*
 * =========================================================
 * ESCAPE
 * =========================================================
 */

function admin_list_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * CONFIG
 * =========================================================
 */

$role_config =
    array(

        'super' => array(
            'label' => '최고 관리자',
            'class' => 'super'
        ),

        'admin' => array(
            'label' => '관리자',
            'class' => 'admin'
        ),

        'editor' => array(
            'label' => '에디터',
            'class' => 'editor'
        )

    );


$status_config =
    array(

        'active' => array(
            'label' => '사용',
            'class' => 'active'
        ),

        'inactive' => array(
            'label' => '사용중지',
            'class' => 'inactive'
        ),

        'locked' => array(
            'label' => '잠금',
            'class' => 'locked'
        )

    );



/*
 * =========================================================
 * PARAM
 * =========================================================
 */

$page =
    isset($_GET['page'])
        ? intval($_GET['page'])
        : 1;


if($page < 1){
    $page = 1;
}


$limit =
    isset($_GET['limit'])
        ? intval($_GET['limit'])
        : 20;


if(
    $limit != 20 &&
    $limit != 50 &&
    $limit != 100
){
    $limit = 20;
}


$keyword =
    isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';


$role =
    isset($_GET['role'])
        ? trim($_GET['role'])
        : '';


if(
    $role != '' &&
    !isset(
        $role_config[$role]
    )
){
    $role = '';
}


$status =
    isset($_GET['status'])
        ? trim($_GET['status'])
        : '';


if(
    $status != '' &&
    !isset(
        $status_config[$status]
    )
){
    $status = '';
}



/*
 * =========================================================
 * WHERE
 * =========================================================
 */

$where =
    array();


$params =
    array();


$where[] =
    'a.deleted_at IS NULL';



/*
 * Keyword
 */

if(
    $keyword != ''
){

    $where[] = "

        (
            a.login_id LIKE ?
            OR a.name LIKE ?
            OR a.email LIKE ?
        )

    ";


    $search_keyword =
        '%'.$keyword.'%';


    $params[] = $search_keyword;
    $params[] = $search_keyword;
    $params[] = $search_keyword;

}



/*
 * Role
 */

if(
    $role != ''
){

    $where[] =
        'a.role = ?';


    $params[] =
        $role;

}



/*
 * Status
 */

if(
    $status != ''
){

    $where[] =
        'a.status = ?';


    $params[] =
        $status;

}


$where_sql =
    implode(
        ' AND ',
        $where
    );



/*
 * =========================================================
 * SUMMARY
 * =========================================================
 */

$summary =
    array(
        'all'      => 0,
        'active'   => 0,
        'inactive' => 0,
        'locked'   => 0
    );


$query = "

    SELECT

        status,
        COUNT(*) AS cnt

    FROM admin_users

    WHERE
        deleted_at IS NULL

    GROUP BY
        status

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

    $data_status =
        $data['status'];


    if(
        isset(
            $summary[$data_status]
        )
    ){

        $summary[$data_status] =
            intval(
                $data['cnt']
            );

    }


    $summary['all'] +=
        intval(
            $data['cnt']
        );

}



/*
 * =========================================================
 * TOTAL
 * =========================================================
 */

$query = "

    SELECT
        COUNT(*) AS total

    FROM admin_users a

    WHERE
        ".$where_sql."

";


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


$total_data =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


$total =
    $total_data
        ? intval(
            $total_data['total']
        )
        : 0;



/*
 * =========================================================
 * PAGING
 * =========================================================
 */

$total_page =
    ceil(
        $total /
        $limit
    );


if(
    $total_page < 1
){
    $total_page = 1;
}


if(
    $page >
    $total_page
){
    $page = $total_page;
}


$offset =
    ($page - 1) *
    $limit;



/*
 * =========================================================
 * LIST
 * =========================================================
 */

$list =
    array();


$query = "

    SELECT

        a.id,
        a.login_id,
        a.name,
        a.email,
        a.role,
        a.status,
        a.failed_login_count,
        a.locked_until,
        a.last_login_at,
        a.last_login_ip,
        a.password_changed_at,
        a.created_at,
        a.updated_at

    FROM admin_users a

    WHERE
        ".$where_sql."

    ORDER BY

        CASE a.role
            WHEN 'super' THEN 1
            WHEN 'admin' THEN 2
            WHEN 'editor' THEN 3
            ELSE 4
        END ASC,

        a.id ASC

    LIMIT ".$offset.", ".$limit."

";


$ps =
    pdo_query(
        $db,
        $query,
        $params
    );


while(
    $data =
        $ps->fetch(
            PDO::FETCH_ASSOC
        )
){

    $list[] =
        $data;

}



/*
 * =========================================================
 * PAGINATION
 * =========================================================
 */

$page_start =
    floor(
        ($page - 1) / 10
    ) * 10 + 1;


$page_end =
    $page_start + 9;


if(
    $page_end >
    $total_page
){
    $page_end = $total_page;
}



/*
 * =========================================================
 * URL
 * =========================================================
 */

function admin_list_url(
    $page,
    $limit,
    $keyword,
    $role,
    $status
){

    return

        '/manage/admin_list?'.

        http_build_query(
            array(
                'page'    => $page,
                'limit'   => $limit,
                'keyword' => $keyword,
                'role'    => $role,
                'status'  => $status
            )
        );

}



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/admin.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="admin-manage-page-head">

            <div class="admin-manage-page-title">

                <span>
                    시스템 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    관리자 관리
                </h2>

            </div>


            <a
                href="/manage/admin_write"
                class="btn btn-primary"
            >

                <i class="ri-user-add-line"></i>

                관리자 등록

            </a>

        </div>



        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="admin-summary-grid">


            <a
                href="/manage/admin_list"
                class="admin-summary-item <?=($status == '' ? 'active' : '')?>"
            >

                <span>
                    전체 관리자
                </span>

                <strong>
                    <?=number_format($summary['all'])?>
                </strong>

            </a>



            <a
                href="/manage/admin_list?status=active"
                class="admin-summary-item <?=($status == 'active' ? 'active' : '')?>"
            >

                <span>
                    사용
                </span>

                <strong>
                    <?=number_format($summary['active'])?>
                </strong>

            </a>



            <a
                href="/manage/admin_list?status=inactive"
                class="admin-summary-item <?=($status == 'inactive' ? 'active' : '')?>"
            >

                <span>
                    사용중지
                </span>

                <strong>
                    <?=number_format($summary['inactive'])?>
                </strong>

            </a>



            <a
                href="/manage/admin_list?status=locked"
                class="admin-summary-item <?=($status == 'locked' ? 'active' : '')?>"
            >

                <span>
                    잠금
                </span>

                <strong>
                    <?=number_format($summary['locked'])?>
                </strong>

            </a>


        </div>



        <!-- =================================================
             SEARCH
        ================================================== -->

        <div class="card">

            <div class="card-header">

                <h3>
                    검색조건
                </h3>

            </div>


            <div class="card-body">

                <form
                    method="get"
                    action="/manage/admin_list"
                >

                    <input
                        type="hidden"
                        name="limit"
                        value="<?=$limit?>"
                    >


                    <div class="admin-manage-search-grid">


                        <div class="form-group">

                            <label>
                                검색어
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                value="<?=admin_list_h($keyword)?>"
                                placeholder="아이디, 이름, 이메일"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                권한
                            </label>

                            <select name="role">

                                <option value="">
                                    전체
                                </option>

                                <?
                                foreach(
                                    $role_config as
                                    $role_key => $role_data
                                ){
                                ?>

                                    <option
                                        value="<?=$role_key?>"
                                        <?=$role == $role_key ? 'selected' : ''?>
                                    >

                                        <?=admin_list_h(
                                            $role_data['label']
                                        )?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

                        </div>



                        <div class="form-group">

                            <label>
                                상태
                            </label>

                            <select name="status">

                                <option value="">
                                    전체
                                </option>

                                <?
                                foreach(
                                    $status_config as
                                    $status_key => $status_data
                                ){
                                ?>

                                    <option
                                        value="<?=$status_key?>"
                                        <?=$status == $status_key ? 'selected' : ''?>
                                    >

                                        <?=admin_list_h(
                                            $status_data['label']
                                        )?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

                        </div>


                    </div>


                    <div class="admin-manage-search-action">

                        <a
                            href="/manage/admin_list"
                            class="btn btn-white"
                        >
                            초기화
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="ri-search-line"></i>

                            검색

                        </button>

                    </div>

                </form>

            </div>

        </div>



        <!-- =================================================
             LIST
        ================================================== -->

        <div class="card admin-manage-list-card">

            <div class="card-header">

                <h3>
                    관리자 목록
                </h3>

            </div>


            <div class="card-body">


                <div class="admin-manage-toolbar">

                    <div class="admin-manage-total">

                        총

                        <strong>
                            <?=number_format($total)?>
                        </strong>

                        명

                    </div>


                    <select id="adminLimit">

                        <option
                            value="20"
                            <?=$limit == 20 ? 'selected' : ''?>
                        >
                            20명씩 보기
                        </option>

                        <option
                            value="50"
                            <?=$limit == 50 ? 'selected' : ''?>
                        >
                            50명씩 보기
                        </option>

                      
                    </select>

                </div>



                <div class="admin-manage-table-wrap">

                    <table class="admin-manage-table">

                        <thead>

                            <tr>

                                <th width="70">
                                    No.
                                </th>

                                <th width="150">
                                    아이디
                                </th>

                                <th width="120">
                                    이름
                                </th>

                                <th>
                                    이메일
                                </th>

                                <th width="120">
                                    권한
                                </th>

                                <th width="100">
                                    상태
                                </th>

                                <th width="170">
                                    최근 로그인
                                </th>

                                <th width="145">
                                    등록일
                                </th>

                                <th width="90">
                                    관리
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?
                        if(
                            count($list) > 0
                        ){

                            $no =
                                $total -
                                $offset;


                            foreach(
                                $list as
                                $data
                            ){

                                $role_data =
                                    isset(
                                        $role_config[
                                            $data['role']
                                        ]
                                    )
                                        ? $role_config[
                                            $data['role']
                                        ]
                                        : array(
                                            'label' => $data['role'],
                                            'class' => ''
                                        );


                                $status_data =
                                    isset(
                                        $status_config[
                                            $data['status']
                                        ]
                                    )
                                        ? $status_config[
                                            $data['status']
                                        ]
                                        : array(
                                            'label' => $data['status'],
                                            'class' => ''
                                        );


                                $is_me =
                                    isset($_SESSION['ADMIN_ID']) &&
                                    intval($_SESSION['ADMIN_ID']) ==
                                    intval($data['id']);
                        ?>

                            <tr>


                                <td>
                                    <?=number_format($no)?>
                                </td>



                                <td>

                                    <div class="admin-account">

                                        <strong>

                                            <?=admin_list_h(
                                                $data['login_id']
                                            )?>

                                        </strong>


                                        <?
                                        if($is_me){
                                        ?>

                                            <span>
                                                ME
                                            </span>

                                        <?
                                        }
                                        ?>

                                    </div>

                                </td>



                                <td>

                                    <?=admin_list_h(
                                        $data['name']
                                    )?>

                                </td>



                                <td>

                                    <a
                                        href="mailto:<?=admin_list_h($data['email'])?>"
                                        class="admin-manage-email"
                                    >

                                        <?=admin_list_h(
                                            $data['email']
                                        )?>

                                    </a>

                                </td>



                                <td>

                                    <span class="admin-role <?=$role_data['class']?>">

                                        <?=admin_list_h(
                                            $role_data['label']
                                        )?>

                                    </span>

                                </td>



                                <td>

                                    <span class="admin-status <?=$status_data['class']?>">

                                        <?=admin_list_h(
                                            $status_data['label']
                                        )?>

                                    </span>

                                </td>



                                <td>

                                    <?
                                    if(
                                        $data['last_login_at'] != ''
                                    ){
                                    ?>

                                        <div class="admin-last-login">

                                            <strong>

                                                <?=admin_list_h(
                                                    substr(
                                                        $data['last_login_at'],
                                                        0,
                                                        16
                                                    )
                                                )?>

                                            </strong>


                                            <?
                                            if(
                                                $data['last_login_ip'] != ''
                                            ){
                                            ?>

                                                <span>

                                                    <?=admin_list_h(
                                                        $data['last_login_ip']
                                                    )?>

                                                </span>

                                            <?
                                            }
                                            ?>

                                        </div>

                                    <?
                                    }else{
                                    ?>

                                        <span class="admin-manage-muted">
                                            -
                                        </span>

                                    <?
                                    }
                                    ?>

                                </td>



                                <td>

                                    <?=admin_list_h(
                                        substr(
                                            $data['created_at'],
                                            0,
                                            10
                                        )
                                    )?>

                                </td>



                                <td>

                                    <a
                                        href="/manage/admin_write?id=<?=intval($data['id'])?>"
                                        class="btn btn-white btn-sm admin-manage-edit"
                                    >

                                        수정

                                    </a>

                                </td>


                            </tr>

                        <?
                                $no--;

                            }

                        }else{
                        ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="admin-manage-empty"
                                >

                                    <i class="ri-user-settings-line"></i>

                                    등록된 관리자가 없습니다.

                                </td>

                            </tr>

                        <?
                        }
                        ?>

                        </tbody>

                    </table>

                </div>



                <!-- =================================================
                     PAGINATION
                ================================================== -->

                <div class="admin-manage-pagination">


                    <?
                    if(
                        $page > 1
                    ){
                    ?>

                        <a
                            href="<?=admin_list_h(
                                admin_list_url(
                                    $page - 1,
                                    $limit,
                                    $keyword,
                                    $role,
                                    $status
                                )
                            )?>"
                        >

                            <i class="ri-arrow-left-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="disabled">

                            <i class="ri-arrow-left-s-line"></i>

                        </span>

                    <?
                    }
                    ?>



                    <?
                    for(
                        $i = $page_start;
                        $i <= $page_end;
                        $i++
                    ){
                    ?>

                        <?
                        if(
                            $i == $page
                        ){
                        ?>

                            <span class="active">
                                <?=$i?>
                            </span>

                        <?
                        }else{
                        ?>

                            <a
                                href="<?=admin_list_h(
                                    admin_list_url(
                                        $i,
                                        $limit,
                                        $keyword,
                                        $role,
                                        $status
                                    )
                                )?>"
                            >

                                <?=$i?>

                            </a>

                        <?
                        }
                        ?>

                    <?
                    }
                    ?>



                    <?
                    if(
                        $page <
                        $total_page
                    ){
                    ?>

                        <a
                            href="<?=admin_list_h(
                                admin_list_url(
                                    $page + 1,
                                    $limit,
                                    $keyword,
                                    $role,
                                    $status
                                )
                            )?>"
                        >

                            <i class="ri-arrow-right-s-line"></i>

                        </a>

                    <?
                    }else{
                    ?>

                        <span class="disabled">

                            <i class="ri-arrow-right-s-line"></i>

                        </span>

                    <?
                    }
                    ?>


                </div>


            </div>

        </div>


    </div>

</main>



<script>

/*
 * =========================================================
 * LIMIT
 * =========================================================
 */

var adminLimit =
    document.getElementById(
        'adminLimit'
    );


if(adminLimit){

    adminLimit.addEventListener(
        'change',
        function(){

            var url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                'limit',
                this.value
            );


            url.searchParams.set(
                'page',
                '1'
            );


            window.location.href =
                url.toString();

        }
    );

}

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>