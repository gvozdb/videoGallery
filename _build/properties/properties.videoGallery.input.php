<?php

$properties = [];

$tmp = [

    'tv' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'tvId' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'tvInput' => [
        'type' => 'textfield',
        'value' => '',
    ],
    'res' => [
        'type' => 'numberfield',
        'value' => '',
    ],
    'tpl' => [
        'type' => 'textfield',
        'value' => 'tpl.videoGallery.input',
    ],

];

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        [
            'name' => $k,
            'desc' => PKG_NAME_LOWER.'_prop_'.$k,
            'lexicon' => PKG_NAME_LOWER.':properties',
        ], $v
    );
}

return $properties;
