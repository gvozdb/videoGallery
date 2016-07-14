<?php

class videoGallerySuperBoxSelectFieldsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'modSystemSetting';
    public $classKey = 'modSystemSetting';
    public $defaultSortField = 'key';
    public $defaultSortDirection = 'ASC';
    /* @var videoGallery $vg */
    public $vg;

    //public $permission = 'list';

    public function initialize()
    {
        // Подключаем класс videoGallery и класс vgTools
        $corePath = $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/videogallery/';
        if (!is_object($this->modx->videogallery) || !($this->modx->videogallery instanceof videoGallery)) {
            $this->vg = $this->modx->getService('videogallery', 'videogallery', $corePath . 'model/videogallery/');
        } else {
            $this->vg = $this->modx->videogallery;
        }
        if ($this->vg instanceof videoGallery) {
            if (!$this->vg->loadTools()) {
                return false;
            }
        } else {
            return false;
        }

        return parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('videogallery:default');
    }

    /**
     * * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject.
     * @return bool|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->where(array(
            "{$this->classKey}.area = 'videogallery_fields'",
            "{$this->classKey}.value != ''",
            "{$this->classKey}.key = 'videogallery_field_title'
            OR {$this->classKey}.key = 'videogallery_field_desc'
            OR {$this->classKey}.key = 'videogallery_field_image'
            OR {$this->classKey}.key = 'videogallery_field_video'
            OR {$this->classKey}.key = 'videogallery_field_videoId'
            OR {$this->classKey}.key = 'videogallery_field_videoDuration'",
        ));

        if ($query = $this->getProperty('query', null)) {
            $c->where(array(
                "{$this->classKey}.key:LIKE" => '%' . $query . '%',
                "OR:{$this->classKey}.value:LIKE" => '%' . $query . '%',
            ));
        }

        // $c->prepare();
        // $this->modx->log(1, print_r($c->toSQL(), 1));

        return $c;
    }

    /**
     * @param xPDOObject $obj
     *
     * @return array
     */
    public function prepareRow(xPDOObject $obj)
    {
        // $this->modx->log(1, print_r($obj->toArray(), 1));

        return array(
            'display' => $this->modx->lexicon('setting_' . $obj->get('key')),
            'value' => $obj->get('key'),
            'field' => $obj->get('value'),
        );
    }
}

return 'videoGallerySuperBoxSelectFieldsGetListProcessor';
