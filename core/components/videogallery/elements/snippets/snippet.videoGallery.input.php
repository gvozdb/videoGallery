<?php

$tv = $modx->getOption('tv', $scriptProperties, ''); // TV name or...
$tvid = $modx->getOption('tvId', $scriptProperties, ''); // ... TV id
$tvInput = $modx->getOption('tvInput', $scriptProperties, ''); // TV input name
$res = $modx->getOption('res', $scriptProperties, 0); // Resource id
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.videoGallery.input');

$tv_where = $tv ? array('name' => $tv) : '';
$tv_where = $tv_where ?: ($tvid ? array('id' => $tvid) : '');

if (empty($tv_where)) {
    return;
}

if ($tv_obj = $modx->getObject('modTemplateVar', $tv_where)) {
    $value = '';

    if ($res && $tv_val_obj = $modx->getObject('modTemplateVarResource', array(
            'tmplvarid' => $tv_obj->id,
            'contentid' => $res,
        ))
    ) {
        $value = $tv_val_obj->value;
    }

    $return = $modx->getChunk($tpl, array(
        'tv_id' => $tv_obj->id,
        'tv_name' => $tv_obj->name,
        'tv_input_name' => $tvInput ?: $tv_obj->name,
        'tv_value' => htmlspecialchars($value),
        'res_id' => $res,
    ));

    return $return;
} else {
    return;
}