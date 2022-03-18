<?php

/**
 * Плагин для:
 * 1. перезалива картинок из временной папки в постоянную
 * 2. удаления старых изображений видеороликов, которые не используются
 */
class videogalleryOnDocFormSave extends videogalleryPlugin
{
    public function run()
    {
        if (!$resource = &$this->sp['resource']) {
            return;
        }

        // Правим картинки из папки /0/ в папку /$resource->id/
        $field = $this->modx->getOption('videogallery_field_image');

        $q = $this->modx->newQuery('modTemplateVarResource');
        $q->innerJoin('modTemplateVar', 'modTemplateVar', 'modTemplateVar.id = modTemplateVarResource.tmplvarid');
        $q->select([
            'modTemplateVarResource.value as json',
            'modTemplateVarResource.tmplvarid as tv_id',
            'modTemplateVar.name as tv_name',
        ]);
        $q->where([
            [
                'modTemplateVar.type' => 'videoGallery',
                'modTemplateVarResource.contentid' => $resource->id,
            ],
            [
                'modTemplateVarResource.value:LIKE' => '%/0/%',
                'OR:modTemplateVarResource.value:LIKE' => '%/0_/%',
            ],
        ]);
        $q->prepare();
        $q->stmt->execute();
        $rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $data = $this->modx->fromJSON($row['json']);
            if (is_array($data) && isset($data['image'])) {
                $old['fileurl'] = $data['image'];
                $old['filepath'] = str_replace(MODX_ASSETS_URL, MODX_ASSETS_PATH, $old['fileurl']);

                $new['fileurl'] = str_replace('/0/', '/' . $resource->id . '/', $data['image']);
                $new['filepath'] = str_replace(MODX_ASSETS_URL, MODX_ASSETS_PATH, $new['fileurl']);
                $new['pathinfo'] = pathinfo($new['filepath']);
                $new['dirname'] = $new['pathinfo']['dirname'] . '/';

                @mkdir($new['dirname'], 0755, true);

                if (file_exists($old['filepath'])) {
                    $copied = false;

                    if (copy($old['filepath'], $new['filepath'])) {
                        $copied = true;
                        @unlink($old['filepath']);
                    }

                    if ($copied) {
                        $data['image'] = $new['fileurl'];
                        $resource->setTVValue($row['tv_name'], $this->modx->toJSON($data));
                        $resource->save();

                        // Если в настройках указано поле для хранения изображений - сохраним в него новый путь до картинки
                        if ($field) {
                            if (stristr($field, 'tv.')) {
                                $resource->setTVValue(str_replace('tv.', '', $field), $new['fileurl']);
                            } else {
                                $resource->set($field, $new['fileurl']);
                            }
                            $resource->save();
                        }
                    }
                }
            }
        }
        unset($q, $rows);

        // Удаляем ненужные картинки
        $q = $this->modx->newQuery('modTemplateVarResource');
        $q->innerJoin('modTemplateVar', 'modTemplateVar', 'modTemplateVar.id = modTemplateVarResource.tmplvarid');
        $q->select([
            'modTemplateVarResource.value as json',
        ]);
        $q->where([
            'modTemplateVar.type' => 'videoGallery',
            'modTemplateVarResource.contentid' => $resource->id,
        ]);
        $q->prepare();
        $q->stmt->execute();
        if ($rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                $data = $this->modx->fromJSON($row['json']);

                if (is_array($data) && isset($data['image'])) {
                    $pathinfo = pathinfo($data['image']);
                    $pathinfo['dirname'] = str_replace(MODX_ASSETS_URL, MODX_ASSETS_PATH, $pathinfo['dirname']);

                    $this->vg->Tools->cleanFolder($pathinfo['dirname'], $pathinfo['basename']);
                }
            }
        }
        unset($q, $rows);
    }
}
