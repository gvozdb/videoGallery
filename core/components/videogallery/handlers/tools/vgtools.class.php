<?php

interface vgToolsInterface
{
}

/**
 * Class vgTools.
 */
class vgTools implements vgToolsInterface
{
    /** @var modX $modx */
    protected $modx;
    /** @var videoGallery $videogallery */
    protected $videogallery;
    /** @var array $config */
    public $config = [];

    /**
     * @param $modx
     * @param $config
     */
    public function __construct($modx, &$config)
    {
        $this->modx = $modx;
        $this->config = &$config;

        $corePath = $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/videogallery/';

        if (!is_object($this->modx->videogallery) || !($this->modx->videogallery instanceof videoGallery)) {
            $this->videogallery = $this->modx->getService('videogallery', 'videogallery', $corePath . 'model/videogallery/');
        } else {
            $this->videogallery = $this->modx->videogallery;
        }
    }

    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param       $eventName
     * @param array $params
     * @param       $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = [], $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }
        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }
        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return [
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        ];
    }

    /**
     * More convenient error messages.
     *
     * @param modProcessorResponse $response
     * @param string               $glue
     *
     * @return string
     */
    public function formatProcessorErrors(modProcessorResponse $response, $glue = '<br>')
    {
        $errormsgs = [];

        if ($response->hasMessage()) {
            $errormsgs[] = $response->getMessage();
        }
        if ($response->hasFieldErrors()) {
            if ($errors = $response->getFieldErrors()) {
                foreach ($errors as $error) {
                    $errormsgs[] = $error->message;
                }
            }
        }

        return implode($glue, $errormsgs);
    }

    /**
     * Приводит URL или PATH в порядок.
     *
     * @param $url
     *
     * @return string
     */
    public function cleanUrl($url)
    {
        $slashes = ['////', '///', '//'];

        return str_replace($slashes, '/', $url);
    }

    /**
     * Очищает папку $dir оставляя в ней файл $file.
     *
     * @param $dir
     * @param $file
     *
     * @return bool
     */
    public function cleanFolder($dir, $file = '')
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

        return true;
    }

    /**
     * Translate a string using a named transliteration table.
     *
     * @param string $string The string to transliterate.
     *
     * @return string The translated string.
     */
    public function translate($string)
    {
        $table = [
            '/' => '-',
            '.' => '_',
            ',' => '_',
            ' ' => '_',
            '&' => 'and',
            '%' => '',
            '\'' => '',
            'À' => 'A',
            'À' => 'A',
            'Á' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ã' => 'A',
            'Ä' => 'e',
            'Ä' => 'A',
            'Å' => 'A',
            'Å' => 'A',
            'Æ' => 'e',
            'Æ' => 'E',
            'Ā' => 'A',
            'Ą' => 'A',
            'Ă' => 'A',
            'Ç' => 'C',
            'Ç' => 'C',
            'Ć' => 'C',
            'Č' => 'C',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Ď' => 'D',
            'Đ' => 'D',
            'È' => 'E',
            'È' => 'E',
            'É' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ë' => 'E',
            'Ē' => 'E',
            'Ę' => 'E',
            'Ě' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'Ĝ' => 'G',
            'Ğ' => 'G',
            'Ġ' => 'G',
            'Ģ' => 'G',
            'Ĥ' => 'H',
            'Ħ' => 'H',
            'Ì' => 'I',
            'Ì' => 'I',
            'Í' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ï' => 'I',
            'Ī' => 'I',
            'Ĩ' => 'I',
            'Ĭ' => 'I',
            'Į' => 'I',
            'İ' => 'I',
            'Ĳ' => 'J',
            'Ĵ' => 'J',
            'Ķ' => 'K',
            'Ľ' => 'K',
            'Ĺ' => 'K',
            'Ļ' => 'K',
            'Ŀ' => 'K',
            'Ñ' => 'N',
            'Ñ' => 'N',
            'Ń' => 'N',
            'Ň' => 'N',
            'Ņ' => 'N',
            'Ŋ' => 'N',
            'Ò' => 'O',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Õ' => 'O',
            'Ö' => 'e',
            'Ö' => 'e',
            'Ø' => 'O',
            'Ø' => 'O',
            'Ō' => 'O',
            'Ő' => 'O',
            'Ŏ' => 'O',
            'Œ' => 'E',
            'Ŕ' => 'R',
            'Ř' => 'R',
            'Ŗ' => 'R',
            'Ś' => 'S',
            'Ş' => 'S',
            'Ŝ' => 'S',
            'Ș' => 'S',
            'Ť' => 'T',
            'Ţ' => 'T',
            'Ŧ' => 'T',
            'Ț' => 'T',
            'Ù' => 'U',
            'Ù' => 'U',
            'Ú' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Û' => 'U',
            'Ü' => 'e',
            'Ū' => 'U',
            'Ü' => 'e',
            'Ů' => 'U',
            'Ű' => 'U',
            'Ŭ' => 'U',
            'Ũ' => 'U',
            'Ų' => 'U',
            'Ŵ' => 'W',
            'Ŷ' => 'Y',
            'Ÿ' => 'Y',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'e',
            'ä' => 'e',
            'å' => 'a',
            'ā' => 'a',
            'ą' => 'a',
            'ă' => 'a',
            'å' => 'a',
            'æ' => 'e',
            'ç' => 'c',
            'ć' => 'c',
            'č' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            'ď' => 'd',
            'đ' => 'd',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ē' => 'e',
            'ę' => 'e',
            'ě' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ƒ' => 'f',
            'ĝ' => 'g',
            'ğ' => 'g',
            'ġ' => 'g',
            'ģ' => 'g',
            'ĥ' => 'h',
            'ħ' => 'h',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ī' => 'i',
            'ĩ' => 'i',
            'ĭ' => 'i',
            'į' => 'i',
            'ı' => 'i',
            'ĳ' => 'j',
            'ĵ' => 'j',
            'ķ' => 'k',
            'ĸ' => 'k',
            'ł' => 'l',
            'ľ' => 'l',
            'ĺ' => 'l',
            'ļ' => 'l',
            'ŀ' => 'l',
            'ñ' => 'n',
            'ń' => 'n',
            'ň' => 'n',
            'ņ' => 'n',
            'ŉ' => 'n',
            'ŋ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'e',
            'ö' => 'e',
            'ø' => 'o',
            'ō' => 'o',
            'ő' => 'o',
            'ŏ' => 'o',
            'œ' => 'e',
            'ŕ' => 'r',
            'ř' => 'r',
            'ŗ' => 'r',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'e',
            'ū' => 'u',
            'ü' => 'e',
            'ů' => 'u',
            'ű' => 'u',
            'ŭ' => 'u',
            'ũ' => 'u',
            'ų' => 'u',
            'ŵ' => 'w',
            'ÿ' => 'y',
            'ŷ' => 'y',
            'ż' => 'z',
            'ź' => 'z',
            'ß' => 's',
            'ſ' => 's',
            'Α' => 'A',
            'Ά' => 'A',
            'Β' => 'B',
            'Γ' => 'G',
            'Δ' => 'D',
            'Ε' => 'E',
            'Έ' => 'E',
            'Ζ' => 'Z',
            'Η' => 'I',
            'Ή' => 'I',
            'Θ' => 'TH',
            'Ι' => 'I',
            'Ί' => 'I',
            'Ϊ' => 'I',
            'Κ' => 'K',
            'Λ' => 'L',
            'Μ' => 'M',
            'Ν' => 'N',
            'Ξ' => 'KS',
            'Ο' => 'O',
            'Ό' => 'O',
            'Π' => 'P',
            'Ρ' => 'R',
            'Σ' => 'S',
            'Τ' => 'T',
            'Υ' => 'Y',
            'Ύ' => 'Y',
            'Ϋ' => 'Y',
            'Φ' => 'F',
            'Χ' => 'X',
            'Ψ' => 'PS',
            'Ω' => 'O',
            'Ώ' => 'O',
            'α' => 'a',
            'ά' => 'a',
            'β' => 'b',
            'γ' => 'g',
            'δ' => 'd',
            'ε' => 'e',
            'έ' => 'e',
            'ζ' => 'z',
            'η' => 'i',
            'ή' => 'i',
            'θ' => 'th',
            'ι' => 'i',
            'ί' => 'i',
            'ϊ' => 'i',
            'ΐ' => 'i',
            'κ' => 'k',
            'λ' => 'l',
            'μ' => 'm',
            'ν' => 'n',
            'ξ' => 'ks',
            'ο' => 'o',
            'ό' => 'o',
            'π' => 'p',
            'ρ' => 'r',
            'σ' => 's',
            'τ' => 't',
            'υ' => 'y',
            'ύ' => 'y',
            'ϋ' => 'y',
            'ΰ' => 'y',
            'φ' => 'f',
            'χ' => 'x',
            'ψ' => 'ps',
            'ω' => 'o',
            'ώ' => 'o',
            'А' => 'a',
            'Б' => 'b',
            'В' => 'v',
            'Г' => 'g',
            'Д' => 'd',
            'Е' => 'e',
            'Ё' => 'yo',
            'Ж' => 'zh',
            'З' => 'z',
            'И' => 'i',
            'Й' => 'j',
            'К' => 'k',
            'Л' => 'l',
            'М' => 'm',
            'Н' => 'n',
            'О' => 'o',
            'П' => 'p',
            'Р' => 'r',
            'С' => 's',
            'Т' => 't',
            'У' => 'u',
            'Ф' => 'f',
            'Х' => 'x',
            'Ц' => 'cz',
            'Ч' => 'ch',
            'Ш' => 'sh',
            'Щ' => 'shh',
            'Ъ' => '',
            'Ы' => 'yi',
            'Ь' => '',
            'Э' => 'e',
            'Ю' => 'yu',
            'Я' => 'ya',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'x',
            'ц' => 'cz',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'yi',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        ];

        return strtr($string, $table);
    }
}
