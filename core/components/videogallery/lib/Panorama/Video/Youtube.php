<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Wrapper class for Youtube.
 *
 * @author Fran Diéguez <fran@openhost.es>
 **/
namespace Panorama\Video;

class Youtube implements VideoInterface
{
    public $url;
    public $params = [];

    protected $feedType;

    /**
     * @param $url
     * @param array $params
     *
     * @throws \Exception
     */
    public function __construct($url, $params = [])
    {
        $this->url = $url;
        $this->params = $params;

        if (empty($this->params['youtube']['api_key'])) {
            $this->feedType = 'oembed';
        } else {
            $this->feedType = 'googleapis';
        }

        if (!$this->getVideoId()) {
            throw new \Exception('Video ID not valid.', 1);
        }
        $this->getFeed();

        return $this;
    }

    /*
     * Returns the feed that contains information of video
     */
    public function getFeed()
    {
        if (!empty($this->feed)) {
            return $this->feed;
        }

        if ($this->feedType === 'oembed') {
            $info = file_get_contents('https://www.youtube.com/oembed?url=' . ('https://www.youtube.com/watch?v=' . $this->getVideoId()) . '&format=json');
            $this->feed = json_decode($info);
        }
        elseif ($this->feedType === 'googleapis') {
            $videoId = $this->getVideoID();
            $apikey = @$this->params['youtube']['api_key'] ?: null;

            // Fetch and decode information from the API
            $data = file_get_contents(
                'https://www.googleapis.com/youtube/v3/videos?key='.$apikey
                .'&id='.$videoId.'&part=snippet,contentDetails,statistics,player'
            );
            $videoObj = @json_decode($data);

            if (empty($videoObj->items)) {
                throw new \Exception('Video Id not valid.');
            }
            $this->feed = $videoObj->items[0];
        }

        return $this->feed;
    }

    /*
     * Returns the video ID from the video url
     *
     * @returns string, the Youtube ID of this video
     */
    public function getVideoId()
    {
        if (!isset($this->videoId)) {
            $this->videoId = $this->getUrlParam('v');
        }

        return $this->videoId;
    }

    /*
     * Returns the video title
     *
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            if ($this->feedType === 'oembed') {
                $this->title = (string) @$this->getFeed()->title ?: '';
            }
            elseif ($this->feedType === 'googleapis') {
                $this->title = (string) @$this->getFeed()->snippet->title ?: '';
            }
        }

        return $this->title;
    }

    /*
     * Returns the descrition for this video
     *
     * @returns string, the description of this video
     */
    public function getDescription()
    {
        if (!isset($this->description)) {
            if ($this->feedType === 'oembed') {
                $this->description = (string) @$this->getFeed()->description ?: '';
            }
            elseif ($this->feedType === 'googleapis') {
                $this->description = (string) @$this->getFeed()->snippet->description ?: '';
            }
        }

        return $this->description;
    }

    /*
     * Returs the object HTML with a specific width, height and options
     *
     * @param width,   the width of the final flash object
     * @param height,  the height of the final flash object
     * @param options, you can read more about the youtube player options
     *                 in  http://code.google.com/intl/en/apis/
     *                     youtube/player_parameters.html
     *                 Use them in options
     *                 (ex {:rel => 0, :color1 => '0x333333'})
     */
    public function getEmbedHTML($options = [])
    {
        $defaultOptions = ['width' => 560, 'height' => 349];
        $options        = array_merge($defaultOptions, $options);

        // convert options into
        $htmlOptions = '';
        if (count($options) > 0) {
            foreach ($options as $key => $value) {
                if (in_array($key, ['width', 'height'])) {
                    continue;
                }
                $htmlOptions .= '&'.$key.'='.$value;
            }
        }
        $embedUrl = $this->getEmbedUrl();

        // if this video is not embed
        return   "<iframe type='text/html' src='{$embedUrl}'"
            ." width='{$options['width']}' height='{$options['height']}'"
            ." frameborder='0' allowfullscreen='true'></iframe>";
    }

    /*
     * Returns the FLV url
     *
     * @returns string, the url to the video URL
     */
    public function getFLV()
    {
        return;
    }

    /*
     * Returns the embed url of the video
     *
     * @returns string, the embed url of the video
     */
    public function getEmbedUrl()
    {
        if (empty($this->embedUrl)) {
            $this->embedUrl = 'https://www.youtube.com/embed/' . $this->getVideoId();
        }

        return $this->embedUrl;
    }

    /*
     * Returns the service name for this video
     *
     * @returns string, the service name of this video
     */
    public function getService()
    {
        return 'Youtube';
    }

    /*
     * Returns the url for downloading the flv video file
     *
     * @returns string, the url for downloading the flv video file
     */
    public function getDownloadUrl()
    {
        if (!isset($this->downloadUrl)) {
            $this->downloadUrl = $this->getEmbedUrl();
        }

        return $this->downloadUrl;
    }

    /*
     * Returns the duration in sec of the video
     *
     * @returns string, the duration in sec of the video
     */
    public function getDuration()
    {
        if (!isset($this->duration)) {
            if ($this->feedType === 'oembed') {
                $this->duration = (string) @$this->getFeed()->duration ?: '';
            }
            elseif ($this->feedType === 'googleapis') {
                $dt = new \DateTime('@0'); // Unix epoch
                $dt->add(
                    new \DateInterval(
                        $this->getFeed()->contentDetails->duration
                    )
                );
                $this->duration = $dt->format('H:i:s');
            }
        }

        return $this->duration;
    }

    /*
     * Returns the video Thumbnail
     *
     * @returns string, the video thumbnail url
     */
    public function getThumbnail()
    {
        if (!isset($this->thumbnail)) {
            if ($this->feedType === 'oembed') {
                $this->thumbnail = (string) $this->getFeed()->thumbnail_url;
            }
            elseif ($this->feedType === 'googleapis') {
                if (!isset($this->thumbnail)) {
                    $thumbnailArray = $this->getFeed()->snippet->thumbnails;

                    if (isset($thumbnailArray->standard->url)) {
                        $this->thumbnail = $thumbnailArray->standard->url;
                    } elseif (isset($thumbnailArray->high->url)) {
                        $this->thumbnail = $thumbnailArray->high->url;
                    } elseif (isset($thumbnailArray->medium->url)) {
                        $this->thumbnail = $thumbnailArray->medium->url;
                    } else {
                        $this->thumbnail = $thumbnailArray->default->url;
                    }
                }
            }
        }

        return $this->thumbnail;
    }

    /**
     * Returns the value of the param given.
     *
     * @param string, the param to look for
     *
     * @return string, the value of the param
     */
    private function getUrlParam($param)
    {
        $queryParamsRAW = parse_url($this->url, PHP_URL_QUERY);
        preg_match('@v=([a-zA-Z0-9_-]*)@', $queryParamsRAW, $matches);

        return $matches[1];
    }
}
