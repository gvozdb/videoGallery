<?php

$properties = array();

$tmp = array(

    'tv' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'tvId' => array(
        'type' => 'numberfield',
        'value' => '',
    ),
    'tvInput' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'res' => array(
        'type' => 'numberfield',
        'value' => '',
    ),
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.videoGallery.input',
    ),

);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name' => $k,
            'desc' => PKG_NAME_LOWER.'_prop_'.$k,
            'lexicon' => PKG_NAME_LOWER.':properties',
        ), $v
    );
}

return $properties;
