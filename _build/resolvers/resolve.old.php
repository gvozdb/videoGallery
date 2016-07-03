<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx = &$object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
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
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
