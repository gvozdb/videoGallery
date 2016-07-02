<?php

$corePath = $modx->getOption('videogallery_core_path', $config, $modx->getOption('core_path') . 'components/videogallery/');
$assetsUrl = $modx->getOption('videogallery_assets_url', $config, $modx->getOption('assets_url') . 'components/videogallery/');

switch ($modx->event->name) {
	case 'OnTVInputRenderList':
		$modx->event->output($corePath.'tv/input/');
		break;
	case 'OnTVOutputRenderList':
		$modx->event->output($corePath.'tv/output/');
		break;
	case 'OnTVInputPropertiesList':
		$modx->event->output($corePath.'tv/inputproperties/');
		break;
	case 'OnTVOutputRenderPropertiesList':
		$modx->event->output($corePath.'tv/properties/');
		break;
	case 'OnManagerPageBeforeRender':
		break;
	case 'OnDocFormRender':
		$modx->regClientCSS($assetsUrl.'css/mgr/default.css');
		
		$jqueryScript = '<script type="text/javascript">';
		$jqueryScript .= "\n";
		$jqueryScript .= 'if(typeof jQuery == "undefined"){';
		$jqueryScript .= "\n";
		$jqueryScript .= 'document.write(\'<script type="text/javascript" src="'. $assetsUrl .'js/mgr/jquery-2.1.1.min.js" ></\'+\'script>\');';
		$jqueryScript .= "\n";
		$jqueryScript .= '}';
		$jqueryScript .= "\n";
		$jqueryScript .= '</script>';
		$jqueryScript .= "\n";
		
		/*$jqueryScript .= '<script type="text/javascript">';
		$jqueryScript .= "\n";
		$jqueryScript .= 'if(typeof $.event.special.textchange == "undefined"){';
		$jqueryScript .= "\n";
		$jqueryScript .= 'document.write(\'<script type="text/javascript" src="'. $assetsUrl .'js/mgr/jquery.textchange.js" ></\'+\'script>\');';
		$jqueryScript .= "\n";
		$jqueryScript .= '}';
		$jqueryScript .= "\n";
		$jqueryScript .= '</script>';
		$jqueryScript .= "\n";*/
		
		$modx->regClientStartupScript($jqueryScript, true);
		
		//$modx->regClientScript($assetsUrl .'js/mgr/jquery.textchange.js');
	
		break;
}