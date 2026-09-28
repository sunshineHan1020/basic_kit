<?php

include $_SERVER['DOCUMENT_ROOT'].'/src/controller/head.php';



/*
 * =========================================================
 * MAIN EXHIBITION
 * =========================================================
 */

$main_exhibitions =
    array();


$query = "

    SELECT

        me.id,
        me.work_id,
        me.image_media_id,
        me.video_media_id,

        me.intro_text,

        me.band_color,
        me.text_color,

        me.sort_order,

        w.title,
        w.list_title,
        w.slug,
        w.summary,

        im.storage_key AS image_path,
        im.original_name AS image_name,

        vm.storage_key AS video_path,
        vm.original_name AS video_name,
        vm.mime_type AS video_mime

    FROM main_exhibitions me

    INNER JOIN works w
        ON w.id = me.work_id
        AND w.deleted_at IS NULL

    LEFT JOIN media im
        ON im.id = me.image_media_id
        AND im.deleted_at IS NULL

    LEFT JOIN media vm
        ON vm.id = me.video_media_id
        AND vm.deleted_at IS NULL

    WHERE
        me.is_active = 1

    ORDER BY
        me.sort_order ASC,
        me.id ASC

    LIMIT 4

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

    /*
     * =====================================================
     * DISPLAY TITLE
     * =====================================================
     */

    $data['display_title'] =
        trim(
            $data['list_title']
        ) != ''
            ? $data['list_title']
            : $data['title'];



    /*
     * =====================================================
     * MAIN IMAGE PATH
     * =====================================================
     */

    $data['image_path'] =
        isset($data['image_path'])
            ? trim($data['image_path'])
            : '';


    if(
        $data['image_path'] != '' &&
        substr(
            $data['image_path'],
            0,
            1
        ) != '/'
    ){

        $data['image_path'] =
            '/'.$data['image_path'];

    }



    /*
     * =====================================================
     * VIDEO PATH
     * =====================================================
     */

    $data['video_path'] =
        isset($data['video_path'])
            ? trim($data['video_path'])
            : '';


    if(
        $data['video_path'] != '' &&
        substr(
            $data['video_path'],
            0,
            1
        ) != '/'
    ){

        $data['video_path'] =
            '/'.$data['video_path'];

    }



    /*
     * =====================================================
     * CLIENTS
     * =====================================================
     */

    $data['clients'] =
        array();


    $data['client_names'] =
        array();


    $client_query = "

        SELECT

            c.id,
            c.client_name,
            c.media_id,
            c.is_active,

            m.storage_key AS logo_path

        FROM main_exhibition_clients mec

        INNER JOIN clients c
            ON c.id = mec.client_id

        LEFT JOIN media m
            ON m.id = c.media_id
            AND m.deleted_at IS NULL

        WHERE
            mec.exhibition_id = ?

        ORDER BY
            mec.sort_order ASC,
            mec.client_id ASC

    ";


    $client_ps =
        pdo_query(
            $db,
            $client_query,
            array(
                $data['id']
            )
        );


    while(
        $client =
            $client_ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $client['logo_path'] =
            isset($client['logo_path'])
                ? trim($client['logo_path'])
                : '';


        if(
            $client['logo_path'] != '' &&
            substr(
                $client['logo_path'],
                0,
                1
            ) != '/'
        ){

            $client['logo_path'] =
                '/'.$client['logo_path'];

        }


        $data['clients'][] =
            $client;


        $data['client_names'][] =
            $client['client_name'];

    }



    /*
     * =====================================================
     * CLIENT TEXT
     *
     * ex)
     * PUBGM x KIA
     * =====================================================
     */

    $data['client_text'] =
        implode(
            ' x ',
            $data['client_names']
        );



    /*
     * =====================================================
     * WORK BODY IMAGES
     *
     * 메인 Collab 영역에서 사용할
     * 기존 WORKS 이미지 최대 2개
     * =====================================================
     */

    $data['body_images'] =
        array();


    $image_query = "

        SELECT

            wm.id,
            wm.media_id,
            wm.slot_key,
            wm.sort_order,
            wm.alt_text,
            wm.caption,

            m.storage_key,
            m.original_name,
            m.width,
            m.height

        FROM work_media wm

        INNER JOIN media m
            ON m.id = wm.media_id
            AND m.deleted_at IS NULL

        WHERE
            wm.work_id = ?
            AND wm.role = 'body'

        ORDER BY
            wm.sort_order ASC,
            wm.id ASC

        LIMIT 2

    ";


    $image_ps =
        pdo_query(
            $db,
            $image_query,
            array(
                $data['work_id']
            )
        );


    while(
        $image =
            $image_ps->fetch(
                PDO::FETCH_ASSOC
            )
    ){

        $image['storage_key'] =
            isset($image['storage_key'])
                ? trim($image['storage_key'])
                : '';


        if(
            $image['storage_key'] != '' &&
            substr(
                $image['storage_key'],
                0,
                1
            ) != '/'
        ){

            $image['storage_key'] =
                '/'.$image['storage_key'];

        }


        $data['body_images'][] =
            $image;

    }



    /*
 * =====================================================
 * LINE TEXT
 *
 * ex)
 * PUBGM x KIA / 제8구역
 * =====================================================
 */

$line_text =
    array();


if(
    $data['client_text'] != ''
){

    $line_text[] =
        $data['client_text'];

}


if(
    $data['display_title'] != ''
){

    $line_text[] =
        $data['display_title'];

}


$data['line_text'] =
    implode(
        ' / ',
        $line_text
    );



    /*
     * =====================================================
     * PROJECT URL
     * =====================================================
     */

    $data['project_url'] =
        '/project/'.
        rawurlencode(
            $data['slug']
        );



    /*
     * =====================================================
     * COLOR DEFAULT
     * =====================================================
     */

    if(
        !preg_match(
            '/^#[0-9A-Fa-f]{6}$/',
            $data['band_color']
        )
    ){

        $data['band_color'] =
            '#FFFFFF';

    }


    if(
        !preg_match(
            '/^#[0-9A-Fa-f]{6}$/',
            $data['text_color']
        )
    ){

        $data['text_color'] =
            '#FFFFFF';

    }



    /*
     * =====================================================
     * RESULT
     * =====================================================
     */

    $main_exhibitions[] =
        $data;

}



/*
 * =========================================================
 * VIEW
 * =========================================================
 */

include $_SERVER['DOCUMENT_ROOT'].'/src/view/main.php';



include $_SERVER['DOCUMENT_ROOT'].'/src/controller/footer.php';

?>


<script src="/@resource/js/lib/swiper-bundle.min.js"></script>
<script src="/@resource/js/lib/lenis.min.js"></script>

<script src="/@resource/js/lib/gsap.min.js"></script>
<script src="/@resource/js/lib/ScrollTrigger.min.js"></script>
<script src="/@resource/js/lib/SplitText.min.js"></script>
<script src="/@resource/js/lib/ScrambleTextPlugin.min.js"></script>
<script src="/@resource/js/lib/Flip.min.js"></script>

<script type="module">

    import * as THREE
        from '/@resource/js/lib/three.module.min.js';

    window.THREE =
        THREE;

    window.dispatchEvent(
        new Event(
            'three:ready'
        )
    );

</script>

<script src="/@resource/js/lib/matter.min.js"></script>

<script src="/@resource/js/motion.js"></script>
<script src="/@resource/js/main.js"></script>

</body>
</html>