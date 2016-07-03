<link rel="stylesheet" href="[[++assets_url]]components/videogallery/css/mgr/default.css" type="text/css">
<script type="text/javascript">
    if (typeof window.videoGalleryHandler == 'undefined') {
        document.write('<script type="text/javascript" src="[[++assets_url]]components/videogallery/js/libs/vg-handler.js" ></' + 'script>');
    }
</script>

<div class="videogallery-wrapper">
    <div class="videogallery-form">
        <input type="text" id="vgUrl_[[+tv_id]]" class="videogallery-url" placeholder="Ссылка на видео для обработки"
               value="">
    </div>
    <div>
        <small>Вставьте в это поле ссылку на видео YouTube, чтобы её обработать.</small>
    </div>
    <div>
        <small id="vgError_[[+tv_id]]" style="color:red"></small>
    </div>

    <div class="videogallery-video" id="vgVideo_[[+tv_id]]"></div>
    <div class="videogallery-image" id="vgImage_[[+tv_id]]"></div>
    <div class="clear"></div>
</div>
<div>
    <div style="display:none">
        <div><b>JSON строка с данными о ролике:</b></div>
        <div><input type="text" id="vgTv_[[+tv_id]]" name="[[+tv_input_name]]" value="[[+tv_value]]" class="textfield"/>
        </div>
        <div>
            <small id="vgTvError_[[+tv_id]]" style="color:red"></small>
        </div>
    </div>
</div>

<script>
    if (typeof vgHandlers == 'undefined') {
        vgHandlers = {
            /* */
        };
    }
    vgHandlers[[[+tv_id]]] = new videoGalleryHandler({
        tv: [[+tv_id]],
        resource: [[+res_id]],
        actionUrl: '[[++assets_url]]components/videogallery/getvideo.php',
    });
</script>