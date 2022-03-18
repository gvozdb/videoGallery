<?php

/**
 * Wrapper class for Vk videos.
 **/
namespace Panorama\Video;

class Vk implements VideoInterface
{
    public $url;
    public $params = [];

    /**
     * @param $url
     * @param array $params
     */
    public function __construct($url, $params = [])
    {
        if (preg_match('~(video-?[0-9]+_[0-9]+)~ui', $url, $match)) {
            $url = 'https://vk.com/' . $match[1];
        } elseif (preg_match('~[?&]oid=(-?[0-9]+)~ui', $url, $match_oid) && preg_match('~[?&]id=([0-9]+)~ui', $url, $match_id)) {
            $url = 'https://vk.com/video' . $match_oid[1] . '_' . $match_id[1];
        }

        $this->url = $url;
        $this->params = $params;
    }

    /*
     * Loads the video information from Rutube API
     *
     */
    public function getFeed()
    {
        $data = [];

        // Get html content
        $content = file_get_contents($this->url, false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", ['Accept: *', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/76.0.3809.132 Edg/44.18362.267.0'])
            ]
        ]));
        $content = iconv('windows-1251//IGNORE', 'UTF-8//IGNORE', $content);

        // Get video data
        preg_match('~<meta property="og:title" content="([^"]*)"~usi', $content, $matches);
        $data['title'] = $matches[1];

        preg_match('~<meta property="og:description" content="([^"]*)"~usi', $content, $matches);
        $data['description'] = $matches[1];

        preg_match('~<meta property="og:video" content="([^"]*)"~usi', $content, $matches);
        $data['embed_url'] = $matches[1];

        preg_match('~<meta property="og:image" content="([^"]*)"~usi', $content, $matches);
        $data['thumbnail'] = str_replace(['http:', '&amp;'], ['https:', '&'], $matches[1]);

        preg_match('~<meta property="og:video:duration" content="([^"]*)"~usi', $content, $matches);
        $data['duration'] = $matches[1];

        // // Thumbnail
        // if (preg_match('~src="([^"]+\.jpg[^"]*)|src="([^"]+\getVideoPreview[^"]+)"|poster="([^"]+\getVideoPreview[^"]+)"~usi', $content, $matches)) {
        //     for ($i = 1; $i < count($matches); ++$i) {
        //         if ($data['thumbnail'] = @$matches[$i]) {
        //             break;
        //         }
        //     }
        // }

        return json_decode(json_encode($data));
    }

    /*
     * Returns the title for this Rutube video
     *
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            $this->title = (string) $this->getFeed()->title;
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
            $this->description = (string) $this->getFeed()->description;
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
            $this->thumbnail = $this->getFeed()->thumbnail_url;
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
            $this->duration = $this->getFeed()->duration;
        }

        return $this->duration;
    }

    /*
     * Returns the embed url for this Rutube video
     *
     */
    public function getEmbedUrl()
    {
        if (!isset($this->embed_url)) {
            $this->embed_url = $this->getFeed()->embed_url;
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
            . 'allow="autoplay; encrypted-media; fullscreen; picture-in-picture;" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>'
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
        return 'Vk';
    }

    /*
     * Calculates the Video ID from an Rutube URL
     *
     * @param string $url
     */
    public function getVideoID()
    {
        if (!isset($this->videoId)) {
            if (preg_match('~(video-?[0-9]+_[0-9]+)~ui', $this->url, $matches)) {
                $this->videoId = $matches[1];
            }
        }

        return $this->videoId;
    }
}
