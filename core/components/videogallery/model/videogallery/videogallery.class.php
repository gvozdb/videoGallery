<?php

class videoGallery
{
    public $initialized = [];
    /* @var modX $modx */
    public $modx;
    /* @var vgTools $Tools */
    public $Tools;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/videogallery/';
        $assetsPath = $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/videogallery/';
        $assetsUrl = $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/videogallery/';

        $this->config = array_merge([
            'assetsBasePath' => MODX_ASSETS_PATH,
            'assetsBaseUrl' => MODX_ASSETS_URL,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'handlersPath' => $corePath . 'handlers/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
        ], $config);

        $this->modx->addPackage('videogallery', $this->config['modelPath']);
        $this->modx->lexicon->load('videogallery:default');
    }

    /**
     * @param string $ctx The context to load. Defaults to web.
     * @param array $sp
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $sp = [])
    {
        $this->config = array_merge($this->config, $sp, ['ctx' => $ctx]);

        if (!$this->Tools) {
            $this->loadTools();
        }

        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    $this->modx->regClientCSS($this->config['cssUrl'] . 'web/default.css');
                }

                $this->initialized[$ctx] = true;
                break;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function loadTools()
    {
        if (!is_object($this->Tools) || !($this->Tools instanceof vgToolsInterface)) {
            if ($toolsClass = $this->modx->loadClass('tools.vgTools', $this->config['handlersPath'], true, true)) {
                $this->Tools = new $toolsClass($this->modx, $this->config);
            }
        }

        return !empty($this->Tools) && $this->Tools instanceof vgToolsInterface;
    }
}