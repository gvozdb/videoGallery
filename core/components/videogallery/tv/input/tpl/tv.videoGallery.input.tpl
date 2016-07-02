<div class="videogallery-wrapper">
	
	<form class="videogallery-form" id="videogallery-url-form-{$tv->id}">
		<input type="text" id="videogallery-url-{$tv->id}" class="videogallery-url" placeholder="Ссылка на видео для обработки" value="" />
	</form>
	
	<small>Вставьте в это поле ссылку на видео YouTube, чтобы её обработать.</small>
	
	<div>
		<small id="vg_error-{$tv->id}" style="color:red"></small>
	</div>
	
	<div class="videogallery-video" id="videogallery-video-{$tv->id}"> </div>
	<div class="videogallery-image" id="videogallery-image-{$tv->id}"> </div>
	
	<div class="clear"></div>
	
</div>

<div>
	<div style="display:none">
		<div>
			<b>JSON строка с данными о ролике:</b>
		</div>
		<div>
			{$array = $modx->fromJSON($tv->value)}
			{$array['title'] = htmlspecialchars($array['title'])}
			{$array['desc'] = htmlspecialchars($array['desc'])}
			{$json = $modx->toJSON($array)}
			<!--input type="text" id="tv{$tv->id}" name="tv{$tv->id}" value="{$json}" class="textfield" /-->
			<textarea id="tv{$tv->id}" name="tv{$tv->id}" class="x-form-textarea x-form-field" style="width:100%; height:70px;">{$json}</textarea>
		</div>
		<div>
			<small id="vg_tv_error-{$tv->id}" style="color:red"></small>
		</div>
	</div>
</div>

<script type="text/javascript">
// <![CDATA[
{literal}
var $ = jQuery.noConflict();
Ext.onReady(function(){
	var fld = MODx.load({
		{/literal}
		xtype: 'textfield'
		,applyTo: 'tv{$tv->id}'
		,width: '99%'
		,id: 'tv{$tv->id}'
		,enableKeyEvents: true
		,allowBlank: {if $params.allowBlank == 1 || $params.allowBlank == 'true'}true{else}false{/if}
		,value: '{$tv->value}'
		{literal}
		,listeners: { 'change': { fn:MODx.fireResourceFormChange, scope:this}}
	});
	Ext.getCmp('modx-panel-resource').getForm().add(fld);
	MODx.makeDroppable(fld);
});
{/literal}
// ]]>
</script>


{$fields =
	[
		'title'		=> $_config.videogallery_field_title,
		'desc'		=> $_config.videogallery_field_desc,
		'image'		=> $_config.videogallery_field_image,
		'video'		=> $_config.videogallery_field_video,
		'videoId'	=> $_config.videogallery_field_videoId
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

function vg_error_{$tv->id}( text )
{
	$('#vg_error-{$tv->id}').text( text );
}

function vg_tv_error_{$tv->id}( text )
{
	$('#vg_tv_error-{$tv->id}').text( text );
}

function vg_show_image_and_video_{$tv->id} ( obj )
{
	if( obj.hasOwnProperty('image') )
	{
		$('#videogallery-image-{$tv->id}').html( '<img width="140" height="105" src="' + obj.image + '" />' );
	}
	
	if( obj.hasOwnProperty('video') )
	{
		$('#videogallery-video-{$tv->id}').html( '<iframe width="140" height="105" src="' + obj.video + '" frameborder="0" allowfullscreen></iframe>' );
	}
}

Ext.onReady(function()
{
	var assets_url,
		vg_connector,
		vg_connector_params;
	
	assets_url		= MODx.config.videogallery_assets_url !== undefined ? MODx.config.videogallery_assets_url : MODx.config.assets_url + 'components/videogallery/';
	vg_connector	= assets_url + 'connector.php';
	
	vg_connector_params =	'&HTTP_MODAUTH=' + MODx.siteId;
	vg_connector_params += 	'&wctx=mgr';
	vg_connector_params += 	'&action=mgr/gallery/handle';
	
	var json_string_{$tv->id} = $('#tv{$tv->id}').val();
	
	try
	{
		var json_array_{$tv->id} = JSON.parse( json_string_{$tv->id} )
		
		vg_show_image_and_video_{$tv->id}( json_array_{$tv->id} );
	}
	catch (e)
	{
		//alert('Произошла ошибка!');
		vg_tv_error_{$tv->id}('Неудалось распарсить JSON строку. Может это не JSON?');
	}
	
	
	$('#videogallery-url-form-{$tv->id}').submit(function(e)
	{
		e.preventDefault(); // остановим отправку формы
	});
	
	//$('#videogallery-url-{$tv->id}').bind('textchange', function(e)
	$('#videogallery-url-{$tv->id}').on('input', function(e)
	{
		//e.preventDefault(); // остановим отправку формы
		vg_error_{$tv->id}(''); // сбрасываем поле ошибки
		
		
		var video = $('#videogallery-url-form-{$tv->id}').find('#videogallery-url-{$tv->id}').val();
		
		
		if( video == 'undefined' || video == '' )
		{
			vg_error_{$tv->id}('Необходимо ввести ссылку на видео');
			return false;
		}
		
		_vg_connector_params =		vg_connector_params;
		_vg_connector_params +=		'&resource={$modx->resource->id}';
		_vg_connector_params +=		'&tv={$tv->id}';
		_vg_connector_params +=		'&video=' + video;
		
		$.ajax({
			url: vg_connector,
			type: "POST",
			data: _vg_connector_params,
			dataType : "json",
			success: function (data, text)
			{
				if( data.success )
				{
					if( data.object.hasOwnProperty('json') )
					{
						$('#tv{$tv->id}').val( data.object.json );
					}
					
					vg_show_image_and_video_{$tv->id}( data.object );
					
					{foreach from=$fields key=name item=field}
						{if $field != '' AND $field != 'tv'}
							
							if( data.object.hasOwnProperty('{$name}') )
							{
								$('#{$input_ids[$name]}' ).val( data.object.{$name} );
							}
							
						{/if}
					{/foreach}
				}
				else {
					vg_error_{$tv->id}( data.message );
				}
				//console.log( data );
				//console.log( text );
			},
			error: function (data, text)
			{
				vg_error_{$tv->id}( text );
			}
		});
	});
});
// ]]>
</script>
