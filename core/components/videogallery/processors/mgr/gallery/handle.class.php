<?php

class vgHandleProcessor extends modObjectProcessor
{
    public $languageTopics = array('videogallery:default');
    //public $permission = 'save';

    private $resource = 0;
    private $tv = 0;
    private $video = '';
    private $video_data = array();
    private $video_duration = 0;
    private $imagesBasePath = '';
    private $imagesPath = '';
    private $imagesUrl = '';
    private $errors = array();
    protected $assetsPath = '';
    protected $assetsUrl = '';
    protected $corePath = '';

    /**
     * @return bool
     */
    public function initialize()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        $this->resource = (int)$this->getProperty('resource', 0);
        $this->tv = (int)$this->getProperty('tv');
        $this->video = $this->getProperty('video');

        if (!$this->tv) {
            return $this->modx->lexicon('access_denied');
        }

        $this->assetsPath = $this->modx->getOption('assets_path') . 'components/videogallery/';
        $this->assetsUrl = $this->modx->getOption('assets_url') . 'components/videogallery/';
        $this->corePath = $this->modx->getOption('core_path') . 'components/videogallery/';
        $this->imagesBasePath = $this->getOption('videogallery_images_base_path',null,'videoGallery/')
        $this->imagesPath = MODX_ASSETS_PATH . $this->imagesBasePath . $this->tv . '/' . $this->resource . '/';
        $this->imagesUrl = MODX_ASSETS_URL . $this->imagesBasePath . $this->tv . '/' . $this->resource . '/';

        if (preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i', $this->video, $matches) || preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches) || preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i', $this->video, $matches)) {
            $this->video = 'http://www.youtube.com/watch?v=' . $matches[1];
        } elseif (preg_match('/[http|https]+:\/\/(?:www\.|)vimeo\.com\/[a-zA-Z0-9_\-\/]*?([a-zA-Z0-9_\-]+)(&.+)?$/i', $this->video, $matches) || preg_match('/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i', $this->video, $matches)) {
            $this->video = 'http://vimeo.com/' . $matches[1];
        }

        $youtube_api_key = $this->modx->getOption('videogallery_youtube_api_key', null, '');

        // Подгружаем autoload для класса Panorama
        if (empty($this->_Panorama) || !is_object($this->_Panorama)) {
            require_once $this->corePath . 'lib/autoload_panorama.php';

            //
            if (!($this->getServiceName() == 'youtube' && $youtube_api_key == '')) {
                $panorama_params = array(
                    'youtube' => array(
                        'api_key' => $youtube_api_key,
                    ),
                );

                //
                try {
                    $panorama_video = new \Panorama\Video($this->video, $panorama_params);
                } catch (Exception $e) {
                    //echo 'Выброшено исключение: ',  $e->getMessage(), "\n";

                    return $error_text = $e->getMessage();
                    //$panorama_video = '';
                }

                //
                if (is_object($panorama_video)) {
                    $this->video_data = $this->object2array($panorama_video->getObject()->getFeed());

                    $duration = $panorama_video->getObject()->getDuration();
                    if (is_numeric($duration)) {
                        $dt_hms = new DateTime('', new DateTimeZone('+0000'));
                        $dt_hms->setTimestamp($duration);
                        $duration = $dt_hms->format('G:i:s');
                    }
                    // $this->modx->log(1, print_r($duration, 1));
                    if (!strstr($duration, ':')) {
                        $this->video_duration = 0;
                    } else {
                        $dt_iso8601 = new DateTime($duration, new DateTimeZone('+0000'));
                        $sr_search = array('T0H00M', 'T0H0M', 'T0H', '00');
                        $sr_replace = array('T', 'T', 'T', '0');
                        $this->video_duration = str_replace($sr_search, $sr_replace, $dt_iso8601->format('\P\TG\Hi\Ms\S'));
                    }
                }
            }
        }

        // Подгружаем класс videoThumb
        if ((empty($this->_videoThumb) || !is_object($this->_videoThumb)) && !count($this->errors)) {
            require_once $this->corePath . 'lib/videoThumb/videoThumb.php';

            $this->_videoThumb = new videoThumb(array(
                'imagesPath' => $this->imagesPath,
                'imagesUrl' => $this->imagesUrl,
                'emptyImage' => $this->assetsUrl . 'img/web/empty.jpg',
            ));
        }

        return true;
    }

    /**
     * @return array|string
     */
    public function process()
    {
        $data = $this->_videoThumb->process($this->video);

        $data['title'] = '';
        $data['desc'] = '';

        // Обрабатываем ошибки
        if (!empty($data['error'])) {
            return $this->failure($data['error']);
        }
        if (count($this->errors) > 0) {
            return $this->failure($this->errors[0]);
        }

        // Получаем название и описание ролика, если panorama отработала корректно
        if (is_array($this->video_data) && count($this->video_data) > 0) {
            if (isset($this->video_data['snippet']['title'])) {
                $data['title'] = nl2br(htmlspecialchars($this->video_data['snippet']['title']));
            }

            if (isset($this->video_data['snippet']['description'])) {
                $data['desc'] = nl2br(htmlspecialchars($this->video_data['snippet']['description']));
            }
        }

        $data['videoDuration'] = $this->video_duration;

        // >> удаляем фотки старых видео из папки
        //$pathinfo = pathinfo( $data['image'] );
        //$this->remove_files_from_folder( $this->imagesPath, $pathinfo['basename'] );
        // << удаляем фотки старых видео из папки

        $data['json'] = $this->modx->toJSON($data);
        $data['json'] = str_replace(array("\r", "\n", '\r', '\n'), '', $data['json']);

        return $this->success($this->video, $data);
    }

    /**
     * @param       $xmlObject
     * @param array $out
     *
     * @return array
     */
    private function object2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node) {
            if (is_object($node) || is_array($node)) {
                $out[$index] = $this->object2array($node);
            } else {
                $out[$index] = (string)$node;
            }
            unset($node);
        }
        unset($xmlObject);

        return $out;
    }

    /**
     * @param string $video
     *
     * @return mixed
     */
    private function getServiceName($video = '')
    {
        $video = $video ?: $this->video;

        $host = parse_url($video);
        $domainParts = preg_split("@\.@", $host['host']);

        return $domainParts[count($domainParts) - 2];
    }
}

return 'vgHandleProcessor';