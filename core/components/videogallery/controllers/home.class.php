<?php

/**
 * The home manager controller for videoGallery.
 */
class videoGalleryHomeManagerController extends videoGalleryMainController
{
    /* @var videoGallery $vg */
    public $vg;

    /**
     * @param array $sp
     */
    public function process(array $sp = array())
    {
    }

    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('videogallery');
    }

    /**
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->vg->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->vg->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/widgets/reparsing.form.js');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/widgets/reparsing.page.js');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "videogallery-page-home"});
		});
		</script>');
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->vg->config['templatesPath'] . 'home.tpl';
    }
}
