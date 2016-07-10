<?php

$settings = array();

$tmp = array(

    'youtube_api_key' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_main',
    ),

    'field_title' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
    'field_desc' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
    'field_image' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
    'field_video' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
    'field_videoId' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
    'field_videoDuration' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ),
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(array(
        'key' => 'videogallery_' . $k,
        'namespace' => PKG_NAME_LOWER,
    ), $v), '', true, true);

    $settings[] = $setting;
}

unset($tmp);

return $settings;
