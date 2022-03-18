<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx = &$object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            // Удаляем лишние плагины
            $remove = [
                'plugins' => [
                    'videoGallery',
                    'videoGallery_removeOldFiles',
                ],
            ];
            foreach ($remove['plugins'] as $v) {
                if ($plugin = $modx->getObject('modPlugin', ['name' => $v])) {
                    $plugin->remove();
                    $modx->log(modX::LOG_LEVEL_INFO, 'Removed old plugin "<b>' . $v . '</b>"');
                }
            }

            // Удаляем у плагина videoGalleryTv лишние события
            if ($plugin = $modx->getObject('modPlugin', ['name' => 'videoGalleryTv'])) {
                if ($events = $plugin->PluginEvents) {
                    foreach ($events as $event) {
                        if (in_array($event->event, ['OnTVOutputRenderList', 'OnTVOutputRenderPropertiesList'])) {
                            $event->remove();
                            $modx->log(modX::LOG_LEVEL_INFO, 'Removed event "<b>' . $event->event . '</b>" from plugin "<b>videoGalleryTv</b>"');
                        }
                    }
                }
            }

            // Фиксим меню
            if ($menu = $modx->getObject('modMenu', [
			        'text' => PKG_NAME_LOWER,
				])) {
				$menu->fromArray([
					'action' => 'home',
					'namespace' => PKG_NAME_LOWER,
				]);
				$menu->save();
			}
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;