<?php

class videoThumb
{
    public $config;

    public function __construct($config = array())
    {
        $this->config = array_merge(array(
            'imagesPath' => dirname(__FILE__).'/images/',
            'imagesUrl' => '/images/',
            'emptyImage' => '/images/_empty.png',
        ), $config);

        if (!is_dir($this->config['imagesPath'])) {
            mkdir($this->config['imagesPath'], 0755, true);
        }
    }

    /*
     * Return error message from lexicon array
     * @param string $msg Array key
     * @return string Message
     * */
    public function lexicon($msg = '')
    {
        $array = array(
            'video_err_ns' => 'Вы забыли указать ссылку на видео.',
            'video_err_nf' => 'Не могу найти видео, может - неверная ссылка?',
        );

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
            return array('error' => $this->lexicon('video_err_ns'));
        }
        if (!preg_match('/^(http|https)\:\/\//i', $video)) {
            $video = 'http://'.$video;
        }

        // YouTube
        if (preg_match('/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i', $video, $matches) || preg_match('/[http|https]+:\/\/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i', $video, $matches) || preg_match('/[http|https]+:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i', $video, $matches)) {
            $video = 'http://www.youtube.com/embed/'.$matches[1];
            $image = 'http://img.youtube.com/vi/'.$matches[1].'/0.jpg';

            $array = array(
                'video' => $video, 'videoId' => $matches[1], 'image' => $this->getRemoteImage($image),
            );
        }

        // Vimeo
        elseif (preg_match('/[http|https]+:\/\/(?:www\.|)vimeo\.com\/[a-zA-Z0-9_\-\/]*?([a-zA-Z0-9_\-]+)(&.+)?$/i', $video, $matches) || preg_match('/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i', $video, $matches)) {
            $video = 'http://player.vimeo.com/video/'.$matches[1];
            $image = '';
            if ($xml = simplexml_load_file('http://vimeo.com/api/v2/video/'.$matches[1].'.xml')) {
                $image = $xml->video->thumbnail_large
                    ? (string) $xml->video->thumbnail_large
                    : (string) $xml->video->thumbnail_medium;
                $image = str_replace(array('_640.', '_200x150.'), array('.', '.'), $image);

                $image = $this->getRemoteImage($image);
            }
            $array = array(
                'video' => $video, 'videoId' => $matches[1], 'image' => $image,
            );
        }

        // ruTube
        elseif (preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i', $video, $matches) || preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/tracks\/([a-zA-Z0-9_\-]+)(&.+)?/i', $video, $matches)) {
            $video = 'http://rutube.ru/video/embed/'.$matches[1];
            $image = '';
            if ($xml = simplexml_load_file('http://rutube.ru/cgi-bin/xmlapi.cgi?rt_mode=movie&rt_movie_id='.$matches[1].'&utf=1')) {
                $image = (string) $xml->movie->thumbnailLink;
                $image = $this->getRemoteImage($image);
            }
            $array = array(
                'video' => $video, 'videoId' => $matches[1], 'image' => $image,
            );
        } elseif (preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)\//i', $video, $matches)) {
            $html = $this->Curl($matches[0]);

            return $this->process($html);
        }

        // No matches
        else {
            $array = array('error' => $this->lexicon('video_err_nf'));
        }

        return $array;
    }

    /*
     * Download ans save image from remote service
     * @param string $url Remote url
     * @return string $image Url to image or false
     * */
    public function getRemoteImage($url = '')
    {
        if (empty($url)) {
            return false;
        }

        $image = '';
        $response = $this->Curl($url);
        if (!empty($response)) {
            $tmp = explode('.', $url);
            $ext = '.'.end($tmp);

            $filename = md5($url).$ext;
            if (file_put_contents($this->config['imagesPath'].$filename, $response)) {
                $image = $this->config['imagesUrl'].$filename;
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
