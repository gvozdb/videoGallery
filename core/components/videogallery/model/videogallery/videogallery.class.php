<?php

class videoGallery
{
    /* @var modX $modx */
    public $modx;
    
    /* @var pdoTools $pdoTools *
     * public $pdoTools;*/
    
    public $initialized = array();

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('core_path') . 'components/videogallery/';
        $assetsUrl = $this->modx->getOption('assets_url') . 'components/videogallery/';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
        ), $config);

        $this->modx->addPackage('videogallery', $this->config['modelPath']);
        $this->modx->lexicon->load('videogallery:default');
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array  $scriptProperties
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);

        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    /*$config = $this->makePlaceholders($this->config);
                    $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));*/

                    $this->modx->regClientCSS($this->config['cssUrl'] . 'web/default.css');
                }

                $this->initialized[$ctx] = true;
                break;
        }

        return true;
    }

    /* >> Удалить файлы из $dir, кроме $file */
    public function remove_files_from_folder($dir, $file)
    {
        $d = opendir($dir);

        while ($f = readdir($d)) {
            if ($f != '.' && $f != '..') {
                if (is_file($dir . '/' . $f) && $f != $file) {
                    unlink($dir . '/' . $f);
                }
            }
        }

        closedir($d);
    }
    /* << Удалить файлы из $dir, кроме $file */

    /**
     * Method for transform array to placeholders
     * @var array  $array  With keys and values
     * @var string $prefix Prefix for array keys
     * @return array $array Two nested arrays with placeholders and values
     * public function makePlaceholders(array $array = array(), $prefix = '') {
     * if (!$this->pdoTools) {
     * $this->loadPdoTools();
     * }
     * return $this->pdoTools->makePlaceholders($array, $prefix);
     * }*/

    /**
     * Loads an instance of pdoTools
     * @return boolean
     * public function loadPdoTools() {
     * if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
     * // @var pdoFetch $pdoFetch
     * $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
     * if ($pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
     * $this->pdoTools = new $pdoClass($this->modx, $this->config);
     * }
     * elseif ($pdoClass = $this->modx->loadClass($fqn, MODX_CORE_PATH . 'components/pdotools/model/', false, true)) {
     * $this->pdoTools = new $pdoClass($this->modx, $this->config);
     * }
     * else {
     * $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load pdoFetch from
     * "MODX_CORE_PATH/components/pdotools/model/".');
     * }
     * }
     * return !empty($this->pdoTools) && $this->pdoTools instanceof pdoTools;
     * }*/

}