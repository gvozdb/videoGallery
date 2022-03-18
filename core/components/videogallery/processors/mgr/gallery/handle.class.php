<?php

class vgHandleProcessor extends modObjectProcessor
{
    public $languageTopics = ['videogallery:default'];
    public $permission = '';
    private $resource = 0;
    private $tv = 0;
    private $video_data = [];
    private $video_duration = 0;
    private $imagesPath = '';
    private $imagesUrl = '';
    private $errors = [];
    /**
     * @var videoGallery $vg
     */
    protected $vg;

    /**
     * @return bool
     */
    public function initialize()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }
        $this->vg = $this->modx->getService('videogallery', 'videoGallery',
            $this->modx->getOption('vg_core_path', null, MODX_CORE_PATH . 'components/videogallery/') . 'model/videogallery/');

        $this->resource = (int)$this->getProperty('resource', 0);
        $this->tv = $this->getProperty('tv');
        if (empty($this->tv)) {
            return $this->modx->lexicon('access_denied');
        }

        $imagesDirPath = $this->modx->getOption('videogallery_images_dir_path',null,'videoGallery/');
        $this->imagesPath = MODX_ASSETS_PATH . $imagesDirPath . $this->tv . '/' . $this->resource . '/';
        $this->imagesUrl = MODX_ASSETS_URL . $imagesDirPath . $this->tv . '/' . $this->resource . '/';

        return true;
    }


    /**
     * @return array|string
     * @throws Exception
     */
    public function process()
    {
        $matches = [];
        $video_url = $this->getProperty('video');
        if (preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i', $video_url, $matches) ||
            preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i', $video_url, $matches) ||
            preg_match('/[http|https]+:\/\/(?:www\.|)(?:m\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i', $video_url, $matches)) {
            $video_url = 'https://www.youtube.com/watch?v=' . $matches[1];
        }
        elseif (preg_match('/[http|https]+:\/\/(?:www\.|)vimeo\.com\/[a-zA-Z0-9_\-\/]*?([a-zA-Z0-9_\-]+)(&.+)?$/i', $video_url, $matches) ||
                preg_match('/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i', $video_url, $matches)) {
            $video_url = 'https://vimeo.com/' . $matches[1];
        }
        // elseif (preg_match('~https?://(www\.)?vk\.com~i', $video_url)) {
        //     if (preg_match('~(video-?[0-9]+_[0-9]+)~ui', $video_url, $matches)) {
        //         $video_url = 'https://vk.com/' . $matches[1];
        //     } elseif (preg_match('~[?&]oid=(-?[0-9]+)~ui', $video_url, $matches['oid']) && preg_match('~[?&]id=([0-9]+)~ui', $video_url, $matches['id'])) {
        //         $video_url = 'https://vk.com/video' . $matches['oid'][1] . '_' . $matches['id'][1];
        //     }
        // }

        // Get service name
        $service_name = $this->getServiceName($video_url);

        // $this->modx->log(1, '$service_name ' . print_r($service_name, 1));
        $this->modx->log(1, '$video_url ' . print_r($video_url, 1));

        // Обрабатываем видео через VideoThumb
        if (!class_exists('VideoThumb')) {
            require_once $this->vg->config['corePath'] . 'lib/VideoThumb/VideoThumb.php';
        }
        $vt = new VideoThumb([
            'imagesUrl' => $this->imagesUrl,
            'imagesPath' => $this->imagesPath,
            'emptyImage' => $this->vg->config['assetsUrl'] . 'img/web/empty.jpg',
        ]);
        $data = $vt->process($video_url);

        // Check errors
        if (!empty($data['error'])) {
            return $this->failure($data['error']);
        }

        // Обрабатываем видео через Panorama
        if (!class_exists('\Panorama\Video')) {
            require_once $this->vg->config['corePath'] . 'lib/autoload_panorama.php';
        }
        try {
            $panoramaInstance = new \Panorama\Video($video_url, [
                'youtube' => ['api_key' => $this->modx->getOption('videogallery_youtube_api_key', null, '')]
            ]);
            $videoObject = $panoramaInstance->getObject();
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }

        // $this->modx->log(1, '$videoObject->getVideoID() ' . print_r($videoObject->getVideoID(), 1));
        // $this->modx->log(1, '$videoObject->getFeed() ' . print_r($videoObject->getFeed(), 1));

        foreach ([
            'title' => 'getTitle',
            'desc' => 'getDescription',
        ] as $key => $method) {
            $data[$key] = method_exists($videoObject, $method) ? $videoObject->{$method}() : '';
            $data[$key] = nl2br(htmlspecialchars($data[$key]));
        }

        $duration = $videoObject->getDuration();
        if (is_numeric($duration)) {
            $dt_hms = new DateTime('', new DateTimeZone('+0000'));
            $dt_hms->setTimestamp($duration);
            $duration = $dt_hms->format('G:i:s');
        }
        // $this->modx->log(1, print_r($duration, 1));
        if (!strstr($duration, ':')) {
            $data['videoDuration'] = 0;
        } else {
            $dt_iso8601 = new DateTime($duration, new DateTimeZone('+0000'));
            $sr_search = ['T0H00M', 'T0H0M', 'T0H', '00'];
            $sr_replace = ['T', 'T', 'T', '0'];
            $data['videoDuration'] = str_replace($sr_search, $sr_replace, $dt_iso8601->format('\P\TG\Hi\Ms\S'));
        }

        // // Обрабатываем ошибки
        // if (!empty($data['error'])) {
        //     return $this->failure($data['error']);
        // }

        // удаляем фотки старых видео из папки
        //$pathinfo = pathinfo( $data['image'] );
        //$this->remove_files_from_folder( $this->imagesPath, $pathinfo['basename'] );

        $data['json'] = $this->modx->toJSON($data);
        $data['json'] = str_replace(["\r", "\n", '\r', '\n'], '', $data['json']);

        return $this->success($video_url, $data);
    }


    /**
     * @param string $video
     *
     * @return mixed
     */
    private function getServiceName($video)
    {
        $host = parse_url($video);
        $domainParts = preg_split("@\.@", $host['host']);
        return $domainParts[count($domainParts) - 2];
    }
}

return 'vgHandleProcessor';