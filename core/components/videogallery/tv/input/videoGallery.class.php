<?php
class modTemplateVarInputRendervideoGalleryTV extends modTemplateVarInputRender
{
	public function getTemplate()
	{
		$corePath = $this->modx->getOption('videogallery_core_path', null, $this->modx->getOption('core_path') . 'components/videogallery/');
		return $corePath . 'tv/input/tpl/tv.videoGallery.input.tpl';
	}

}

return 'modTemplateVarInputRendervideoGalleryTV';