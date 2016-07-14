<?php

class videoGalleryReparsingRunProcessor extends modProcessor
{
    public $debug = false;
    /**
     * Поля для обновления.
     * @var array
     */
    protected $fields = array();
    /**
     * Кол-во секунд для итерации.
     * @var int
     */
    protected $seconds = 5;
    /**
     * Отступ от начала результатов.
     * @var int
     */
    protected $offset = 0;
    /**
     * Общее кол-во результатов.
     * @var int
     */
    protected $count = 0;
    /**
     * Запрос xPDO.
     * @var array
     */
    protected $q = array();
    /**
     * Время выполнения скрипта.
     * @var null
     */
    protected $microtime = null;

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('videogallery:default');
    }

    public function initialize()
    {
        // return print_r($this->getProperties(), 1);
        // $this->modx->log(1, print_r($this->getProperties(), 1));

        // Начинаем отсчёт
        $this->time();

        // Собираем поля для обновления
        if ($fields = $this->getProperty('fields', null)) {
            $this->fields = array_map(function($value) {
                return array(
                    str_replace('videogallery_field_', '', $value) => $this->modx->getOption($value),
                );
            }, explode(',', $fields));
        }
        // return print_r($this->fields, 1);

        // Формируем запрос к базе
        $this->q = $this->modx->newQuery('modResource');
        $this->q->innerJoin('modTemplateVarResource', 'modTemplateVarResource', 'modTemplateVarResource.contentid = modResource.id');
        $this->q->innerJoin('modTemplateVar', 'modTemplateVar', 'modTemplateVar.id = modTemplateVarResource.tmplvarid');
        $this->q->select(array(
            'modResource.id as resource_id',
            'modTemplateVar.id as tv_id',
            'modTemplateVar.name as tv_name',
            'modTemplateVarResource.value as tv_value',
            'modTemplateVarResource.id as id',
        ));
        $this->q->where(array(
            'modTemplateVar.type' => 'videoGallery',
        ));

        // Считаем кол-во записей
        if (!$this->count = $this->modx->getCount('modResource', $this->q)) {
            return $this->modx->lexicon('videogallery_err_resources_nf');
        }

        // Записываем пропуск
        $this->offset = $this->getProperty('offset', 0);

        return parent::initialize();
    }

    public function process()
    {
        $this->q->limit($this->count, $this->offset);

        $this->q->prepare();
        $this->q->stmt->execute();
        if ($rows = $this->q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                if ($this->time() > $this->seconds) {
                    break;
                }
                // $this->modx->log(1, print_r($row, 1));

                $data = $this->modx->fromJSON($row['tv_value']);
                if (is_array($data) && isset($data['video'])) {
                    $old['fileurl'] = $data['image'];
                    $old['filepath'] = str_replace(MODX_ASSETS_URL, MODX_ASSETS_PATH, $old['fileurl']);

                    $resp = $this->modx->runProcessor('gallery/handle', array(
                        'resource' => $row['resource_id'],
                        'tv' => $row['tv_id'],
                        'video' => $data['video'],
                    ), array('processors_path' => MODX_CORE_PATH . 'components/videogallery/processors/mgr/'));

                    $resp = $resp->response;

                    if ($resp['success']) {
                        // $this->modx->log(1, print_r($resp['object'], 1));

                        if ($resource = $this->modx->getObject('modResource', array('id' => $row['resource_id']))) {
                            $resource->setTVValue($row['tv_name'], $resp['object']['json']);
                            $resource->save();

                            // Если указаны поля для обновления - обновляем
                            if ($this->fields) {
                                foreach ($this->fields as $field_array) {
                                    if (is_array($field_array)) {
                                        foreach ($field_array as $key => $field) {
                                            if (stristr($field, 'tv.')) {
                                                $resource->setTVValue(str_replace('tv.', '', $field), $resp['object'][$key]);
                                            } else {
                                                $resource->set($field, $resp['object'][$key]);
                                            }
                                            $resource->save();
                                        }
                                    }
                                }
                            }

                            // Удалим старое изображение
                            if (file_exists($old['filepath']) && $old['fileurl'] != $resp['object']['image']) {
                                @unlink($old['filepath']);
                            }
                        }
                    }
                }

                ++$this->offset;
            }
        }

        return $this->success('', array_merge($this->getProperties(), array(
            'offset' => $this->offset,
            'done' => ($this->count == $this->offset),
            'time' => $this->time() + $this->getProperty('time', 0),
            'log' => array(),
        )));
    }

    /**
     * @return float
     */
    protected function time()
    {
        $time = ($this->microtime !== null) ? microtime(true) - $this->microtime : 0;
        if ($this->microtime === null) {
            $this->microtime = microtime(true); // Время старта
        }

        return (float)number_format($time, 4, '.', '');
    }
}

return 'videoGalleryReparsingRunProcessor';
