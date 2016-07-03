<?php

// Подключаем MODX
define('MODX_API_MODE', true);
do {
    $dir = dirname(!empty($file) ? dirname($file) : __FILE__);
    $file = $dir . '/index.php';
    $i = isset($i) ? --$i : 10;
} while ($i && !file_exists($file));
if (file_exists($file)) {
    require_once $file;
} else {
    exit("Access denied!");
}
$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

if (isset($_REQUEST['resource']) && $_REQUEST['resource'] != '' && !empty($_REQUEST['tv']) && !empty($_REQUEST['video'])) {
    $response = $modx->runProcessor('gallery/handle', array(
        'resource' => $_REQUEST['resource'],
        'tv' => $_REQUEST['tv'],
        'video' => $_REQUEST['video'],
    ), array('processors_path' => MODX_CORE_PATH . 'components/videogallery/processors/mgr/'));

    print_r($modx->toJSON($response->response));
    die;
} else {
    exit("Access denied!");
}