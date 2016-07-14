<?php

include_once 'setting.inc.php';

// Основное
$_lang['videogallery'] = 'videoGallery';
$_lang['videogallery_menu_desc'] = 'Компонент видео галереи.';

// Табы
$_lang['videogallery_tab_reparsing'] = 'Обновить';

// Блоки сообщений
$_lang['videogallery_reparsing_intro_msg'] = 'Здесь можно заново распарсить информацию о роликах на сайте и записать её в поля, указанные в настройках (image, title, description, duration, embed url).';

// Кнопки
$_lang['videogallery_buttons_run'] = '<i class="icon icon-play"></i>&nbsp; Запустить';
$_lang['videogallery_buttons_continue'] = '<i class="icon icon-play"></i>&nbsp; Продолжить';
$_lang['videogallery_buttons_pause'] = '<i class="icon icon-pause"></i>&nbsp; Пауза';
$_lang['videogallery_buttons_reset'] = '<i class="icon icon-undo"></i> Сбросить';

// Комбобоксы и Суперселектбоксы
$_lang['videogallery_reparsing_sbs_fields'] = 'Поля для обновления';
$_lang['videogallery_reparsing_sbs_fields_empty'] = 'Выберите поля для обновления в них информации';

// Сообщения
$_lang['videogallery_msg_please_wait'] = 'Пожалуйста, подождите...';
$_lang['videogallery_msg_reparsing_done'] = 'Операция обновления завершена!';

// Ошибки
$_lang['videogallery_err_resources_nf'] = 'Не найдено ресурсов с заполненными роликами.';
$_lang['videogallery_err_ns'] = 'Вы забыли указать ссылку на видео.';
$_lang['videogallery_err_nf'] = 'Не могу найти видео, может - неверная ссылка?';
$_lang['videogallery_item_err_undefined'] = 'Неизвестная ошибка. Проверьте ссылку на ошибки и попытайтесь ещё раз.';
$_lang['videogallery_pdotools_install'] = 'Установите pdoTools';