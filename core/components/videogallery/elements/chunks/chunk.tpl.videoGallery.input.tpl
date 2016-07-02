<link rel="stylesheet" href="[[++assets_url]]components/videogallery/css/mgr/default.css" type="text/css" />
<script type="text/javascript">
if(typeof jQuery == "undefined"){
document.write('<script type="text/javascript" src="[[++assets_url]]components/videogallery/js/mgr/jquery-2.1.1.min.js" ></'+'script>');
}
</script>


<div class="videogallery-wrapper">

	<div>
		<input type="text" id="videogallery-url-[[+tv_id]]" class="videogallery-url" placeholder="Ссылка на видео для обработки" value="" />
	</div>
	<small>Вставьте в это поле ссылку на видео YouTube, чтобы её обработать.</small>

	<div>
		<small id="vg_error-[[+tv_id]]" style="color:red"></small>
	</div>

	<div class="videogallery-video" id="videogallery-video-[[+tv_id]]"> </div>
	<div class="videogallery-image" id="videogallery-image-[[+tv_id]]"> </div>

	<div class="clear"></div>

</div>

<div>
	<div style="display:none">
		<div>
			<b>JSON строка с данными о ролике:</b>
		</div>
		<div>
			<input type="text" id="tv[[+tv_id]]" name="[[+tv_input_name]]" value="[[+tv_value]]" class="textfield" />
		</div>
		<div>
			<small id="vg_tv_error-[[+tv_id]]" style="color:red"></small>
		</div>
	</div>
</div>

<script type="text/javascript">
function vg_error_[[+tv_id]]( text )
{
	$('#vg_error-[[+tv_id]]').text( text );
}

function vg_tv_error_[[+tv_id]]( text )
{
	$('#vg_tv_error-[[+tv_id]]').text( text );
}

function vg_show_image_and_video_[[+tv_id]] ( obj )
{
	if( obj.hasOwnProperty('image') )
	{
		$('#videogallery-image-[[+tv_id]]').html( '<img width="140" height="105" src="' + obj.image + '" />' );
	}

	if( obj.hasOwnProperty('video') )
	{
		$('#videogallery-video-[[+tv_id]]').html( '<iframe width="140" height="105" src="' + obj.video + '" frameborder="0" allowfullscreen></iframe>' );
	}
}

$(document).ready(function()
{
	var vg_connector = '[[++assets_url]]components/videogallery/getvideo.php',
		vg_connector_params;

	var json_string_[[+tv_id]] = $('#tv[[+tv_id]]').val();

	try {
		var json_array_[[+tv_id]] = JSON.parse( json_string_[[+tv_id]] );
		vg_show_image_and_video_[[+tv_id]]( json_array_[[+tv_id]] );
	}
	catch(e)
	{
		//alert('Произошла ошибка!');
		vg_tv_error_[[+tv_id]]('Неудалось распарсить JSON строку. Может это не JSON?');
	}


	$('#videogallery-url-form-[[+tv_id]]').submit(function(e)
	{
		e.preventDefault(); // остановим отправку формы
	});

	//$('#videogallery-url-[[+tv_id]]').bind('textchange', function(e)
	$('#videogallery-url-[[+tv_id]]').on('input', function(e)
	{
		//e.preventDefault(); // остановим отправку формы
		vg_error_[[+tv_id]](''); // сбрасываем поле ошибки


		var video = $('#videogallery-url-[[+tv_id]]').val();


		if( video == 'undefined' || video == '' )
		{
			vg_error_[[+tv_id]]('Необходимо ввести ссылку на видео');
			return false;
		}

		vg_connector_params =	'&resource=[[+res_id]]';
		vg_connector_params +=	'&tv=[[+tv_id]]';
		vg_connector_params +=	'&video=' + video;

		$.ajax({
			url: vg_connector,
			type: "POST",
			data: vg_connector_params,
			dataType: "json",
			success: function (data, text) {
				if( data.success )
				{
					if(data.object.hasOwnProperty('json')) {
                        console.log('data.object.json', data.object.json)
						$('#tv[[+tv_id]]').val(data.object.json);
					}

					vg_show_image_and_video_[[+tv_id]]( data.object );
				}
				else {
					vg_error_[[+tv_id]]( data.message );
				}
				console.log('data', data);
				console.log('text', text);
			},
			error: function (data, text) {
				vg_error_[[+tv_id]]( text );
			}
		});
	});
});
</script>