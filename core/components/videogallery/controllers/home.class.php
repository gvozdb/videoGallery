<?php

/**
 * The home manager controller for videoGallery.
 */
class videoGalleryHomeManagerController extends modExtraManagerController
{
    /**
     * @var videoGallery $vg
     */
    public $vg;

    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('videogallery_core_path', null, $this->modx->getOption('core_path') . 'components/videogallery/');
        require_once $corePath . 'model/videogallery/videogallery.class.php';

        $this->vg = new videoGallery($this->modx);
        $this->addCss($this->vg->config['cssUrl'] . 'mgr/default.css');
        $this->addJavascript($this->vg->config['jsUrl'] . 'mgr/videogallery.js');
        $this->addHtml('
		<script type="text/javascript">
			videoGallery.config = ' . $this->modx->toJSON($this->vg->config) . ';
			videoGallery.config.connector_url = "' . $this->vg->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('videogallery:default');
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
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
