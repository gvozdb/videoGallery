<?php
/**
 * @package modx
 * @subpackage processors.element.tv.inputproperties
 */

$modx->lexicon->load('videogallery:tv');
$lang = $modx->lexicon->fetch('videogallery_',true);
$modx->smarty->assign('vglex', $lang);

$corePath = $modx->getOption('videogallery_core_path', null, $modx->getOption('core_path') . 'components/videogallery/');
return $modx->controller->fetchTemplate($corePath . 'tv/inputproperties/tpl/tv.videoGallery.tpl');