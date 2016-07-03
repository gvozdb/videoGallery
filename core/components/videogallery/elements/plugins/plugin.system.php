<?php

$corePath = $modx->getOption('core_path', null, MODX_CORE_PATH).'components/videogallery/';

/* @var videoGallery $videogallery */
if (!is_object($modx->videogallery) || !($modx->videogallery instanceof videoGallery)) {
    $videogallery = $modx->getService(
        'videogallery',
        'videogallery',
        $corePath.'model/videogallery/'
    );
} else {
    $videogallery = $modx->videogallery;
}

$className = 'videogallery'.$modx->event->name;
$modx->loadClass('videogalleryPlugin', $videogallery->config['modelPath'].'videogallery/plugins/', true, true);
$modx->loadClass($className, $videogallery->config['modelPath'].'videogallery/plugins/', true, true);

if (class_exists($className)) {
    /** @var $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}

return;
