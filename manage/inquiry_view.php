<?
/*
 * =========================================================
 * WEEKENDS CMS
 * 문의 상세
 *
 * FILE
 * /manage/inquiry_view.php
 *
 * URL
 * /manage/inquiry_view
 * =========================================================
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/common.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/manage/inc/auth.php';


if($_SESSION['ADMIN_ROLE']!='super' && $_SESSION['ADMIN_ROLE']!='admin' ){
    page_move("/manage/");
    exit;
}


/*
 * =========================================================
 * Escape
 * =========================================================
 */

function inquiry_view_h($str)
{
    return htmlspecialchars(
        $str,
        ENT_QUOTES,
        'UTF-8'
    );
}



/*
 * =========================================================
 * FILE SIZE
 * =========================================================
 */

function inquiry_view_file_size($size)
{
    $size =
        intval($size);


    if(
        $size <= 0
    ){

        return '';

    }


    if(
        $size >=
        1024 * 1024
    ){

        return
            round(
                $size /
                1024 /
                1024,
                2
            ).
            ' MB';

    }


    if(
        $size >=
        1024
    ){

        return
            round(
                $size /
                1024,
                1
            ).
            ' KB';

    }


    return
        $size.
        ' B';
}



/*
 * =========================================================
 * STATUS
 * =========================================================
 */

$status_config =
    array(

        'received' => array(
            'label' => '접수',
            'class' => 'received'
        ),

        'confirmed' => array(
            'label' => '확인',
            'class' => 'confirmed'
        ),

        'in_progress' => array(
            'label' => '진행중',
            'class' => 'progress'
        ),

        'completed' => array(
            'label' => '완료',
            'class' => 'completed'
        )

    );



/*
 * =========================================================
 * STEP
 * =========================================================
 */

$step_config =
    array(

        1 => array(
            'number' => '01',
            'day'    => 'Monday',
            'title'  => '프로젝트 유형'
        ),

        2 => array(
            'number' => '02',
            'day'    => 'Tuesday',
            'title'  => '미팅 장소'
        ),

        3 => array(
            'number' => '03',
            'day'    => 'Wednesday',
            'title'  => '프로젝트 일정'
        ),

        4 => array(
            'number' => '04',
            'day'    => 'Thursday',
            'title'  => '프로젝트 형태'
        )

    );



/*
 * =========================================================
 * ID
 * =========================================================
 */

$inquiry_id =
    isset($_GET['id'])
        ? intval($_GET['id'])
        : 0;


if(
    $inquiry_id <= 0
){

    header(
        'Location: /manage/inquiry_list'
    );

    exit;

}



/*
 * =========================================================
 * INQUIRY
 * =========================================================
 */

$query = "

    SELECT

        i.id,
        i.name,
        i.company_name,
        i.phone,
        i.email,
        i.attachment_media_id,
        i.status,
        i.admin_memo,
        i.created_at,
        i.updated_at,

        m.storage_key AS attachment_path,
        m.original_name AS attachment_name,
        m.mime_type AS attachment_mime,
        m.byte_size AS attachment_size

    FROM inquiries i

    LEFT JOIN media m
        ON m.id = i.attachment_media_id
        AND m.deleted_at IS NULL

    WHERE
        i.id = ?
        AND i.deleted_at IS NULL

    LIMIT 1

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $inquiry_id
        )
    );


$inquiry =
    $ps->fetch(
        PDO::FETCH_ASSOC
    );


if(!$inquiry){

    header(
        'Location: /manage/inquiry_list'
    );

    exit;

}



/*
 * =========================================================
 * ANSWERS
 * =========================================================
 */

$answers =
    array(
        1 => array(),
        2 => array(),
        3 => array(),
        4 => array()
    );


$query = "

    SELECT

        id,
        inquiry_id,
        step_no,
        answer_code,
        answer_label,
        sort_order

    FROM inquiry_answers

    WHERE
        inquiry_id = ?

    ORDER BY
        step_no ASC,
        sort_order ASC,
        id ASC

";


$ps =
    pdo_query(
        $db,
        $query,
        array(
            $inquiry_id
        )
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
        isset(
            $answers[$step_no]
        )
    ){

        $answers[$step_no][] =
            $data;

    }

}



/*
 * =========================================================
 * STATUS DATA
 * =========================================================
 */

$status_data =
    isset(
        $status_config[
            $inquiry['status']
        ]
    )
        ? $status_config[
            $inquiry['status']
        ]
        : array(
            'label' => $inquiry['status'],
            'class' => ''
        );



include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/header.php';
?>

<link
    type="text/css"
    rel="stylesheet"
    href="/manage/css/inquiry.css"
>


<main class="content">

    <div class="content-inner">


        <!-- =================================================
             PAGE HEAD
        ================================================== -->

        <div class="inquiry-page-head">

            <div class="inquiry-page-title">

                <span>
                    문의 관리
                </span>

                <i class="ri-arrow-right-s-line"></i>

                <h2>
                    문의 상세
                </h2>

            </div>


            <div class="inquiry-page-action">

                <a
                    href="/manage/inquiry_list"
                    class="btn btn-white"
                >

                    <i class="ri-list-check-3"></i>

                    목록

                </a>

            </div>

        </div>



        <!-- =================================================
             TOP
        ================================================== -->

        <div class="inquiry-view-head">

            <div class="inquiry-view-head-info">

                <span class="inquiry-view-no">

                    Inquiry #<?=intval($inquiry['id'])?>

                </span>


                <h3>

                    <?=inquiry_view_h(
                        $inquiry['company_name']
                    )?>

                    <strong>

                        <?=inquiry_view_h(
                            $inquiry['name']
                        )?>

                    </strong>

                </h3>


                <p>

                    접수일

                    <strong>

                        <?=inquiry_view_h(
                            $inquiry['created_at']
                        )?>

                    </strong>

                </p>

            </div>


            <span class="inquiry-manage-status <?=$status_data['class']?>">

                <?=inquiry_view_h(
                    $status_data['label']
                )?>

            </span>

        </div>



        <!-- =================================================
             GRID
        ================================================== -->

        <div class="inquiry-view-layout">


            <!-- =================================================
                 LEFT
            ================================================== -->

            <div class="inquiry-view-main">


                <!-- =================================================
                     ANSWERS
                ================================================== -->

                <div class="card inquiry-view-card">

                    <div class="card-header">

                        <h3>
                            문의 내용
                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="inquiry-answer-list">

                            <?
                            foreach(
                                $step_config as
                                $step_no => $step
                            ){
                            ?>

                                <div class="inquiry-answer-item">

                                    <div class="inquiry-answer-head">

                                        <span class="inquiry-answer-number">

                                            <?=$step['number']?>

                                        </span>


                                        <div>

                                            <strong>

                                                <?=inquiry_view_h(
                                                    $step['day']
                                                )?>

                                            </strong>


                                            <p>

                                                <?=inquiry_view_h(
                                                    $step['title']
                                                )?>

                                            </p>

                                        </div>

                                    </div>


                                    <div class="inquiry-answer-values">

                                        <?
                                        if(
                                            isset(
                                                $answers[$step_no]
                                            ) &&
                                            count(
                                                $answers[$step_no]
                                            ) > 0
                                        ){

                                            foreach(
                                                $answers[$step_no] as
                                                $answer
                                            ){
                                        ?>

                                            <span class="inquiry-answer-tag">

                                                <?=inquiry_view_h(
                                                    $answer['answer_label']
                                                )?>

                                            </span>

                                        <?
                                            }

                                        }else{
                                        ?>

                                            <span class="inquiry-answer-empty">
                                                -
                                            </span>

                                        <?
                                        }
                                        ?>

                                    </div>

                                </div>

                            <?
                            }
                            ?>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     CUSTOMER
                ================================================== -->

                <div class="card inquiry-view-card">

                    <div class="card-header">

                        <h3>
                            문의자 정보
                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="inquiry-info-grid">


                            <div class="inquiry-info-item">

                                <span>
                                    이름
                                </span>

                                <strong>

                                    <?=inquiry_view_h(
                                        $inquiry['name']
                                    )?>

                                </strong>

                            </div>



                            <div class="inquiry-info-item">

                                <span>
                                    회사명
                                </span>

                                <strong>

                                    <?=inquiry_view_h(
                                        $inquiry['company_name']
                                    )?>

                                </strong>

                            </div>



                            <div class="inquiry-info-item">

                                <span>
                                    연락처
                                </span>

                                <a
                                    href="tel:<?=inquiry_view_h($inquiry['phone'])?>"
                                >

                                    <?=inquiry_view_h(
                                        $inquiry['phone']
                                    )?>

                                </a>

                            </div>



                            <div class="inquiry-info-item">

                                <span>
                                    이메일
                                </span>

                                <a
                                    href="mailto:<?=inquiry_view_h($inquiry['email'])?>"
                                >

                                    <?=inquiry_view_h(
                                        $inquiry['email']
                                    )?>

                                </a>

                            </div>


                        </div>

                    </div>

                </div>



                <!-- =================================================
                     FILE
                ================================================== -->

                <div class="card inquiry-view-card">

                    <div class="card-header">

                        <h3>
                            첨부파일
                        </h3>

                    </div>


                    <div class="card-body">

                        <?
                        if(
                            intval(
                                $inquiry['attachment_media_id']
                            ) > 0 &&
                            $inquiry['attachment_path'] != ''
                        ){
                        ?>

                            <div class="inquiry-attachment">

                                <div class="inquiry-attachment-icon">

                                    <i class="ri-file-3-line"></i>

                                </div>


                                <div class="inquiry-attachment-info">

                                    <strong>

                                        <?=inquiry_view_h(
                                            $inquiry['attachment_name']
                                        )?>

                                    </strong>


                                    <span>

                                        <?=inquiry_view_h(
                                            $inquiry['attachment_mime']
                                        )?>

                                        <?
                                        $file_size =
                                            inquiry_view_file_size(
                                                $inquiry['attachment_size']
                                            );


                                        if(
                                            $file_size != ''
                                        ){
                                        ?>

                                            ·

                                            <?=inquiry_view_h(
                                                $file_size
                                            )?>

                                        <?
                                        }
                                        ?>

                                    </span>

                                </div>


                                <a
                                    href="<?=inquiry_view_h($inquiry['attachment_path'])?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-white"
                                >

                                    <i class="ri-download-line"></i>

                                    다운로드

                                </a>

                            </div>

                        <?
                        }else{
                        ?>

                            <div class="inquiry-no-file">

                                <i class="ri-attachment-2"></i>

                                첨부된 파일이 없습니다.

                            </div>

                        <?
                        }
                        ?>

                    </div>

                </div>


            </div>



            <!-- =================================================
                 RIGHT
            ================================================== -->

            <div class="inquiry-view-side">


                <!-- =================================================
                     STATUS
                ================================================== -->

                <div class="card inquiry-view-side-card">

                    <div class="card-header">

                        <h3>
                            처리 상태
                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="form-group">

                            <label>
                                상태
                            </label>


                            <select id="inquiryStatus">

                                <?
                                foreach(
                                    $status_config as
                                    $status_key => $status_value
                                ){
                                ?>

                                    <option
                                        value="<?=$status_key?>"
                                        <?=$inquiry['status'] == $status_key ? 'selected' : ''?>
                                    >

                                        <?=inquiry_view_h(
                                            $status_value['label']
                                        )?>

                                    </option>

                                <?
                                }
                                ?>

                            </select>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     MEMO
                ================================================== -->

                <div class="card inquiry-view-side-card">

                    <div class="card-header">

                        <h3>
                            관리자 메모
                        </h3>

                    </div>


                    <div class="card-body">

                        <textarea
                            id="inquiryAdminMemo"
                            class="inquiry-admin-memo"
                            placeholder="문의 처리 내용이나 메모를 입력해주세요."
                        ><?=inquiry_view_h($inquiry['admin_memo'])?></textarea>

                    </div>

                </div>



                <!-- =================================================
                     SAVE
                ================================================== -->

                <button
                    type="button"
                    class="btn btn-primary inquiry-view-save"
                    id="inquirySave"
                >

                    <i class="ri-save-line"></i>

                    상태 / 메모 저장

                </button>


            </div>


        </div>


    </div>

</main>



<script>

/*
 * =========================================================
 * ELEMENT
 * =========================================================
 */

var inquirySave =
    document.getElementById(
        'inquirySave'
    );


var inquiryStatus =
    document.getElementById(
        'inquiryStatus'
    );


var inquiryAdminMemo =
    document.getElementById(
        'inquiryAdminMemo'
    );



/*
 * =========================================================
 * SAVE
 * =========================================================
 */

inquirySave.addEventListener(
    'click',
    function(){

        if(
            inquirySave.getAttribute(
                'data-loading'
            ) == 'Y'
        ){

            return;

        }


        inquirySave.setAttribute(
            'data-loading',
            'Y'
        );


        inquirySave.disabled =
            true;



        var xhr =
            new XMLHttpRequest();


        xhr.open(
            'POST',
            '/manage/proc/inquiry_status',
            true
        );


        xhr.setRequestHeader(
            'Content-Type',
            'application/x-www-form-urlencoded; charset=UTF-8'
        );


        xhr.onreadystatechange =
            function(){

                if(
                    xhr.readyState != 4
                ){

                    return;

                }


                inquirySave.removeAttribute(
                    'data-loading'
                );


                inquirySave.disabled =
                    false;


                if(
                    xhr.status != 200
                ){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                var result;


                try{

                    result =
                        JSON.parse(
                            xhr.responseText
                        );

                }catch(e){

                    alert(
                        '저장 처리 중 오류가 발생했습니다.'
                    );

                    return;

                }


                if(!result.code){

                    alert(
                        result.msg
                            ? result.msg
                            : '문의 정보를 저장하지 못했습니다.'
                    );

                    return;

                }


                alert(
                    result.msg
                        ? result.msg
                        : '문의 정보가 저장되었습니다.',
                    function(){

                        location.reload();

                    }
                );

            };


        var params =

            'id=' +
            encodeURIComponent(
                '<?=intval($inquiry_id)?>'
            ) +

            '&status=' +
            encodeURIComponent(
                inquiryStatus.value
            ) +

            '&admin_memo=' +
            encodeURIComponent(
                inquiryAdminMemo.value
            );


        xhr.send(
            params
        );

    }
);

</script>


<?
include $_SERVER['DOCUMENT_ROOT'].'/manage/inc/footer.php';
?>