<?php

$settings = [];

$tmp = [

    'youtube_api_key' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_main',
    ],

    'field_title' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
    'field_desc' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
    'field_image' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
    'field_video' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
    'field_videoId' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
    'field_videoDuration' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'videogallery_fields',
    ],
];

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge([
        'key' => 'videogallery_' . $k,
        'namespace' => PKG_NAME_LOWER,
    ], $v), '', true, true);

    $settings[] = $setting;
}

unset($tmp);

return $settings;
