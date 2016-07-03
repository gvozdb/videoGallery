<?php

abstract class videogalleryPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var videoGallery $vg */
    protected $vg;
    /** @var array $sp */
    protected $sp;

    public function __construct($modx, &$sp)
    {
        $this->sp = &$sp;
        $this->modx = $modx;
        $this->vg = $this->modx->videogallery;

        if (!is_object($this->vg) || !($this->vg instanceof videoGallery)) {
            $corePath = $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/videogallery/';
            $this->vg = $this->modx->getService('videogallery', 'videogallery', $corePath . 'model/videogallery/', $this->sp);
        }
        if (!$this->vg->initialized[$this->modx->context->key]) {
            $this->vg->initialize($this->modx->context->key);
        }
    }

    abstract public function run();
}
