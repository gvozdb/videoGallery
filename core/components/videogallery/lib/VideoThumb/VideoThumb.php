<?php

class VideoThumb
{
    /**
     * @var array $config
     */
    public $config;

    /**
     * @param $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge([
            'imagesPath' => dirname(__FILE__) . '/images/',
            'imagesUrl' => '/images/',
            'emptyImage' => '/images/_empty.png',
        ], $config);

        if (!empty($this->config['imagesPath']) && !is_dir($this->config['imagesPath'])) {
            @mkdir($this->config['imagesPath'], 0755, true);
        }
    }

    /*
     * Return error message from lexicon array
     * @param string $msg Array key
     * @return string Message
     * */
    public function lexicon($msg = '')
    {
        $array = [
            'video_err_ns' => 'Вы забыли указать ссылку на видео.',
            'video_err_nf' => 'Не могу найти видео, может неверная ссылка?',
        ];

        return @$array[$msg];
    }

    /*
     * Check and format video link, then fire download of preview image
     * @param string $video Remote url on video hosting
     * @return array $array Array with formatted video link and preview url
     * */
    public function process($video = '')
    {
        if (empty($video)) {
            return ['error' => $this->lexicon('video_err_ns')];
        }

        $data = [];
        if (!preg_match('~^https?://~i', $video)) {
            $video = 'https://' . $video;
        }

        // Youtube
        if (preg_match('~https?://(?:www\.|)youtube\.com/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)~i', $video, $matches) ||
            preg_match('~https?://(?:www\.|)youtube\.com/embed/([a-zA-Z0-9_\-]+)/?~i', $video, $matches) ||
            preg_match('~https?://(?:www\.|)youtu\.be/([a-zA-Z0-9_\-]+)/?~i', $video, $matches)) {
            $video = 'https://www.youtube.com/embed/' . $matches[1];
            $image = 'https://img.youtube.com/vi/' . $matches[1] . '/0.jpg';

            $data = [
                'video' => $video,
                'videoId' => $matches[1],
                'image' => $this->getRemoteImage($image),
            ];
        }

        // Vimeo
        elseif (preg_match('~https?://(?:www\.|)vimeo\.com/[a-zA-Z0-9_\-/]*?([a-zA-Z0-9_\-]+)(&.+)?$~i', $video, $matches) ||
                preg_match('~https?://player\.vimeo\.com/video/([a-zA-Z0-9_\-]+)(&.+)?~i', $video, $matches)) {
            $video = 'https://player.vimeo.com/video/' . $matches[1];
            $image = '';
            if ($json  = json_decode(file_get_contents('https://vimeo.com/api/oembed.json?url=' . $video), true)) {
                $image = $json['thumbnail_url'];
                $image = preg_replace(['~_[0-9]+x[0-9]+\.~', '~_[0-9]+\.~', '~_[0-9]+x[0-9]+~'], ['.', '.', ''], $image);
                $image = $this->getRemoteImage($image);
            }
            // if ($xml = simplexml_load_file('http://vimeo.com/api/v2/video/' . $matches[1] . '.xml')) {
            //     $image = $xml->video->thumbnail_large ? (string)$xml->video->thumbnail_large : (string)$xml->video->thumbnail_medium;
            //     $image = str_replace(array('_640.', '_200x150.'), array('.', '.'), $image);
            //     $image = $this->getRemoteImage($image);
            // }
            $data = [
                'video' => $video,
                'videoId' => $matches[1],
                'image' => $image,
            ];
        }

        // Rutube
        elseif (preg_match('~https?://(?:www\.|)rutube\.ru/video/embed/([a-zA-Z0-9_\-]+)/?~i', $video, $matches) ||
                preg_match('~https?://(?:www\.|)rutube\.ru/video/([a-zA-Z0-9_\-]+)/?~i', $video, $matches) ||
                preg_match('~https?://(?:www\.|)rutube\.ru/tracks/([a-zA-Z0-9_\-]+)(&.+)?/?~i', $video, $matches)) {
            $video = 'https://rutube.ru/video/embed/' . $matches[1];
            $image = '';
            if ($json = json_decode(file_get_contents('https://rutube.ru/api/video/' . $matches[1]), true)) {
                $image = $json['thumbnail_url'];
                $image = $this->getRemoteImage($image);
            }
            $data = [
                'video' => $video,
                'videoId' => $matches[1],
                'image' => $image,
            ];
        }

        // Vk
        elseif (preg_match('~https?://(?:www\.|)vk\.com~i', $video)) {
            $video_id = '';
            if (preg_match('~(video-?[0-9]+_[0-9]+)~ui', $video, $matches)) {
                $video_id = str_replace('video', '', $matches[1]);
                $video = 'https://vk.com/' . $matches[1];
            } elseif (preg_match('~[?&]oid=(-?[0-9]+)~ui', $video, $matches_oid) && preg_match('~[?&]id=([0-9]+)~ui', $video, $matches_id)) {
                $video_id = $matches_oid[1] . '_' . $matches_id[1];
                $video = 'https://vk.com/video' . $matches_oid[1] . '_' . $matches_id[1];
            } else {
                $video_id = $video = null;
            }
            $image = '';
            if (!empty($video)) {
                // Get html content
                $content = file_get_contents($video, false, stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => implode("\r\n", ['Accept: *', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/76.0.3809.132 Edg/44.18362.267.0'])
                    ]
                ]));
                $content = iconv('windows-1251//IGNORE', 'UTF-8//IGNORE', $content);

                preg_match('~<meta property="og:video" content="([^"]*)"~usi', $content, $matches);
                $embed_url = $matches[1];

                preg_match('~<meta property="og:image" content="([^"]*)"~usi', $content, $matches);
                $image = str_replace(['http:', '&amp;'], ['https:', '&'], $matches[1]);
                $image = $this->getRemoteImage($image);

                $data = [
                    'video' => $embed_url,
                    'videoId' => $video_id,
                    'image' => $image,
                ];
            }
        }

        // No matches
        if (empty($data)) {
            $data = ['error' => $this->lexicon('video_err_nf')];
        }

        return $data;
    }

    /*
     * Download ans save image from remote service
     * @param string $url Remote url
     * @return string $image Url to image or false
     * */
    public function getRemoteImage($url = '')
    {
        if (empty($url) || empty($this->config['imagesPath'])) {
            return false;
        }

        $image = '';
        $response = $this->Curl($url);
        if (!empty($response)) {
            // $tmp = explode('.', $url);
            // $ext = '.' . end($tmp);
            $ext = '.jpg';

            $filename = md5($url) . $ext;
            if (file_put_contents($this->config['imagesPath'] . $filename, $response)) {
                $image = $this->config['imagesUrl'] . $filename;
            }
        }
        if (empty($image)) {
            $image = $this->config['emptyImage'];
        }

        return $image;
    }

    /*
     * Method for loading remote url
     * @param string $url Remote url
     * @return mixed $data Results of an request
     * */
    public function Curl($url = '')
    {
        if (empty($url)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        $data = curl_exec($ch);

        return $data;
    }
}
