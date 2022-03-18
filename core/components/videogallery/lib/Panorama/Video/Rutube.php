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
 * Wrapper class for Rutube videos.
 *
 * @author Fran Diéguez <fran@openhost.es>
 *
 * @version \$Id\$
 *
 * @copyright OpenHost S.L., Mér Xuñ 01 15:58:58 2011
 **/
namespace Panorama\Video;

class Rutube implements VideoInterface
{
    public $url;
    public $params = [];

    private $rtXmlAPIUrl = 'https://rutube.ru/api/video/';

    /**
     * @param $url
     * @param array $params
     */
    public function __construct($url, $params = [])
    {
        $this->url = $url;
        $this->params = $params;
    }

    /*
     * Loads the video information from Rutube API
     *
     */
    public function getFeed()
    {
        if (!isset($this->feed)) {
            $videoId = (string) $this->getVideoId();
            $feed_url = $this->rtXmlAPIUrl . $videoId;

            $content = file_get_contents($feed_url);
            $this->feed = json_decode($content);
        }

        return $this->feed;
    }

    /*
     * Returns the title for this Rutube video
     *
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            $rtInfo = $this->getFeed();
            $this->title = trim($rtInfo->title);
        }

        return $this->title;
    }

    /*
     * Returns the title for this Rutube video
     *
     */
    public function getDescription()
    {
        if (!isset($this->description)) {
            $rtInfo = $this->getFeed();
            $this->description = trim($rtInfo->description);
        }

        return $this->description;
    }

    /*
     * Returns the thumbnail for this Rutube video
     *
     */
    public function getThumbnail()
    {
        if (!isset($this->thumbnail)) {
            $rtInfo = $this->getFeed();
            $this->thumbnail = trim($rtInfo->thumbnail_url);
        }

        return $this->thumbnail;
    }

    /*
     * Returns the duration in secs for this Rutube video
     *
     */
    public function getDuration()
    {
        if (!isset($this->duration)) {
            $rtInfo = $this->getFeed();
            $this->duration = $rtInfo->duration;
        }

        return $this->duration;
    }

    /*
     * Returns the embed url for this Rutube video
     *
     */
    public function getEmbedUrl()
    {
        // //rutube.ru/play/embed/4436308
        if (!isset($this->embed_url)) {
            $rtInfo = $this->getFeed();
            $this->embed_url = $rtInfo->embed_url;
        }

        return $this->embed_url;
    }

    /*
     * Returns the HTML object to embed for this Rutube video
     *
     */
    public function getEmbedHTML($options = [])
    {
        $defaultOptions = ['width' => 560, 'height' => 349];
        $options = array_merge($defaultOptions, $options);

        $this->embed_html = sprintf(
            '<iframe width="' . $options['width'] . '" height="' . $options['height'] . '" '
            . 'src="' . $this->getEmbedUrl() . '" '
            . 'frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>'
        );

        return $this->embed_html;
    }

    /*
     * Returns the FLV url for this Rutube video
     *
     */
    public function getFLV()
    {
        return;
    }

    /*
     * Returns the Download url for this Rutube video
     *
     */
    public function getDownloadUrl()
    {
        return;
    }

    /*
     * Returns the name of the Video service
     *
     */
    public function getService()
    {
        return 'Rutube';
    }

    /*
     * Calculates the Video ID from an Rutube URL
     *
     * @param string $url
     */
    public function getVideoID()
    {
        if (!isset($this->videoId)) {
            if (preg_match('~/video/([a-z0-9]+)~', $this->url, $matches) ||
                preg_match('~/video/embed/([a-z0-9]+)~', $this->url, $matches) ||
                preg_match('~/tracks/([a-z0-9]+)~', $this->url, $matches)) {
                $this->videoId = $matches[1];
            }
        }

        return $this->videoId;
    }
}
