<?php

class modTemplateVarInputRendervideoGalleryTV extends modTemplateVarInputRender
{
    public function getTemplate()
    {
        $corePath = $this->modx->getOption('core_path') . 'components/videogallery/';

        return $corePath . 'tv/input/tpl/tv.videoGallery.input.tpl';
    }
    public function process($value,array $params = array()){
        $tvid=$this->tv->id;
        if(!is_numeric($tvid)){
            $tvid = preg_replace('/.*?_(\d*?)_[^_]*?$/ui','$1',$tvid);
        }
        $this->setPlaceholder('tvid',$tvid);
        $this->modx->smarty->assign('modx', $this->modx);
        
        parent::process($value,$params);
    }
}

return 'modTemplateVarInputRendervideoGalleryTV';