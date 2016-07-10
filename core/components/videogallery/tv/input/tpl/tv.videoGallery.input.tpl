{$array = $modx->fromJSON($tv->value)}
{if is_array($array)}
    {$array['title'] = htmlspecialchars($array['title'])}
    {$array['desc'] = htmlspecialchars($array['desc'])}
{/if}
{$json = $modx->toJSON($array)}

<div class="videogallery-wrapper">
    <div class="videogallery-form">
        <input type="text" id="vgUrl_{$tv->id}" class="videogallery-url" placeholder="Ссылка на видео для обработки"
               value="{if isset($array['video'])}{$array['video']}{/if}">
    </div>
    <div>
        <small>Вставьте в это поле ссылку на видео YouTube, чтобы её обработать.</small>
    </div>
    <div>
        <small id="vgError_{$tv->id}" style="color:red"></small>
    </div>

    <div class="videogallery-video" id="vgVideo_{$tv->id}"></div>
    <div class="videogallery-image" id="vgImage_{$tv->id}"></div>
    <div class="clear"></div>
</div>
<div>
    <div style="display:none">
        <div><b>JSON строка с данными о ролике:</b></div>
        <div>
            <textarea id="tv{$tv->id}" name="tv{$tv->id}" class="x-form-textarea x-form-field"
                      style="width:100%; height:70px;">{$json}</textarea>
        </div>
        <div>
            <small id="vgTvError_{$tv->id}" style="color:red"></small>
        </div>
    </div>
</div>

<script type="text/javascript">
    // <![CDATA[
    {literal}
    Ext.onReady(function () {
        var fld = MODx.load({
            {/literal}
            xtype: 'textfield',
            applyTo: 'tv{$tv->id}',
            width: '99%',
            id: 'tv{$tv->id}',
            enableKeyEvents: true,
            allowBlank: {if $params.allowBlank == 1 || $params.allowBlank == 'true'}true{else}false{/if},
            value: '{$tv->value}',
            {literal}
            listeners: {'change': {fn: MODx.fireResourceFormChange, scope: this}},
        });
        Ext.getCmp('modx-panel-resource').getForm().add(fld);
        MODx.makeDroppable(fld);
    });
    {/literal}
    // ]]>
</script>

{$fields = [
'title' => $_config.videogallery_field_title,
'desc' => $_config.videogallery_field_desc,
'image' => $_config.videogallery_field_image,
'video' => $_config.videogallery_field_video,
'videoId' => $_config.videogallery_field_videoId,
'videoDuration' => $_config.videogallery_field_videoDuration
]}
{foreach from=$fields key=name item=item}
    {if $item != ''}
        {if substr($item, 0,3) == 'tv.'}
            {$vg_fields[$name] = str_replace('tv.', '', $item)}
            {$input_ids_data = "tv`$modx->getObject('modTemplateVar', ['name'=>$vg_fields[$name]] )->id`"}

            {if $input_ids_data != 'tv' AND $input_ids_data != ''}
                {$input_ids[$name] = $input_ids_data}
            {/if}
        {else}
            {$vg_fields[$name] = $item}
            {$input_ids[$name] = "modx-resource-`$vg_fields[$name]`"}
        {/if}
    {/if}
{/foreach}

<script type="text/javascript">
    // <![CDATA[
    Ext.onReady(function () {
        if (typeof vgHandlers == 'undefined') {
            vgHandlers = {
                /* */
            };
        }
        vgHandlers[{$tv->id}] = new videoGalleryHandler({
            tv: {$tv->id},
            resource: {$modx->resource->id},
            actionUrl: MODx.config.assets_url + 'components/videogallery/connector.php?HTTP_MODAUTH=' + MODx.siteId + '&wctx=mgr&action=mgr/gallery/handle',
            selectors: {
                tvInput: '#tv{$tv->id}',
            },
            callbacks: {
                success: function (resp) {
                    var element = {
                        /* */
                    };
                    element[{$tv->id}] = {
                        /* */
                    };

                    {foreach from=$fields key=name item=field}
                    {if $field != '' AND $field != 'tv'}
                    if (resp.object.hasOwnProperty('{$name}')) {
                        element[{$tv->id}]['{$name}'] = document.querySelector('#{$input_ids[$name]}');
                        if (element[{$tv->id}]['{$name}']) {
                            element[{$tv->id}]['{$name}'].value = resp.object['{$name}'];
                        }
                    }
                    {/if}
                    {/foreach}
                },
                reset: function () {
                    var element = {
                        /* */
                    };
                    element[{$tv->id}] = {
                        /* */
                    };

                    {foreach from=$fields key=name item=field}
                    {if $field != '' AND $field != 'tv'}
                    element[{$tv->id}]['{$name}'] = document.querySelector('#{$input_ids[$name]}');
                    if (element[{$tv->id}]['{$name}']) {
                        element[{$tv->id}]['{$name}'].value = '';
                    }
                    {/if}
                    {/foreach}
                },
            },
        });
    });
    // ]]>
</script>
