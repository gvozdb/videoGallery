<?php

$assetsPath = $modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/videogallery/';
$assetsUrl = $modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/videogallery/';

if (!is_object($modx->videogallery) || !($modx->videogallery instanceof videoGallery)) {
    $corePath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/videogallery/';
    $vg = $modx->getService('videogallery', 'videogallery', $corePath . 'model/videogallery/', $scriptProperties);
} else {
    $vg = $modx->videogallery;
}
if (!$vg->initialized[$modx->context->key]) {
    $vg->initialize($modx->context->key);
}

switch ($modx->event->name) {
    case 'OnTVInputRenderList':
        $modx->event->output($vg->config['corePath'] . 'tv/input/');
        break;
    case 'OnTVInputPropertiesList':
        $modx->event->output($vg->config['corePath'] . 'tv/inputproperties/');
        break;
    case 'OnManagerPageBeforeRender':
        break;
    case 'OnDocFormRender':
        $modx->regClientCSS($vg->config['cssUrl'] . 'mgr/default.css');

        $data = '<script type="text/javascript">
            if (typeof window.videoGalleryHandler == \'undefined\') {
                document.write(\'<script type="text/javascript" src="' . $vg->config['jsUrl'] . 'libs/vg-handler.js?v=1.3.0" ></\' + \'script>\');
            }
        </script>';
        $modx->regClientStartupScript($data, true);
        break;
}