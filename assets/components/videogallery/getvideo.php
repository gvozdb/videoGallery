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
}
if (!is_object($modx)) {
    exit("Access denied");
}

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

if (!empty($_REQUEST['resource']) && !empty($_REQUEST['tv']) && !empty($_REQUEST['video'])) {
    $response = $modx->runProcessor('gallery/handle', [
        'resource' => (int)$_REQUEST['resource'],
        'tv' => (int)$_REQUEST['tv'],
        'video' => $_REQUEST['video'],
    ], ['processors_path' => MODX_CORE_PATH . 'components/videogallery/processors/mgr/']);

    print_r($modx->toJSON($response->response));
} else {
    print "Access denied";
}

exit();