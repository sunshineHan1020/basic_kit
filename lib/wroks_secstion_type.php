<?
$WORK_SECTION_TYPES = array(

    'PROJECT_OVERVIEW' => array(
        'name'  => '프로젝트 개요',
        'class' => 'project-overview',
        'icon'  => '/@resource/images/@ico/project_overview.svg',
        'fields' => array(
            'logo',
            'title',
            'summary_items',
            'detail_items'
        )
    ),

    'IMG_FULL_01' => array(
        'name'  => '풀 이미지 1',
        'class' => 'img-area full-type-01',
        'icon'  => '/@resource/images/@ico/img_full_01.svg',
        'fields' => array(
            'image'
        )
    ),

    'IMG_FULL_02' => array(
        'name'  => '풀 이미지 2단',
        'class' => 'img-area full-type-02 grid-wrap col-2',
        'icon'  => '/@resource/images/@ico/img_full_02.svg',
        'fields' => array(
            'image_01',
            'image_02'
        )
    ),

    'TEXT_01' => array(
        'name'  => '텍스트 기본',
        'class' => 'text-area text-type-01',
        'icon'  => '/@resource/images/@ico/text_01.svg',
        'fields' => array(
            'title',
            'body_html'
        )
    ),

    'IMG_MAX_01' => array(
        'name'  => '맥스 이미지 1',
        'class' => 'img-area max-type-01',
        'icon'  => '/@resource/images/@ico/img_max_01.svg',
        'fields' => array(
            'image'
        )
    ),

    'IMG_MAX_02' => array(
        'name'  => '맥스 이미지 2단',
        'class' => 'img-area max-type-02 grid-wrap col-2',
        'icon'  => '/@resource/images/@ico/img_max_02.svg',
        'fields' => array(
            'image_01',
            'image_02'
        )
    ),

    'IMG_MAX_CUSTOM' => array(
        'name'  => '커스텀 이미지',
        'class' => 'img-area max-custom-type',
        'icon'  => '/@resource/images/@ico/img_max_custom.svg',
        'fields' => array(
            'image'
        )
    ),

    'MIXED_02_01' => array(
        'name'  => '이미지 + 텍스트',
        'class' => 'mixed-area grid-wrap col-2-1',
        'icon'  => '/@resource/images/@ico/mixed_02_01.svg',
        'fields' => array(
            'image',
            'title',
            'body_html'
        )
    ),

    'TEXT_03' => array(
        'name'  => '텍스트 중앙',
        'class' => 'text-area text-type-03',
        'icon'  => '/@resource/images/@ico/text_03.svg',
        'fields' => array(
            'title',
            'body_html'
        )
    )

);
?>