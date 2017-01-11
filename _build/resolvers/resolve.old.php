<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx = &$object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            // Удаляем лишние плагины
            $remove = array(
                'plugins' => array(
                    'videoGallery',
                    'videoGallery_removeOldFiles',
                ),
            );
            foreach ($remove['plugins'] as $v) {
                if ($plugin = $modx->getObject('modPlugin', array('name' => $v))) {
                    $plugin->remove();
                    $modx->log(modX::LOG_LEVEL_INFO, 'Removed old plugin "<b>' . $v . '</b>"');
                }
            }

            // Удаляем у плагина videoGalleryTv лишние события
            if ($plugin = $modx->getObject('modPlugin', array('name' => 'videoGalleryTv'))) {
                if ($events = $plugin->PluginEvents) {
                    foreach ($events as $event) {
                        if (in_array($event->event, array('OnTVOutputRenderList', 'OnTVOutputRenderPropertiesList'))) {
                            $event->remove();
                            $modx->log(modX::LOG_LEVEL_INFO, 'Removed event "<b>' . $event->event . '</b>" from plugin "<b>videoGalleryTv</b>"');
                        }
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;