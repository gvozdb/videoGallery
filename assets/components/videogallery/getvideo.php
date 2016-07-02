<?php

// >> Подключаем
define('MODX_API_MODE', true);

$current_dir = dirname(__FILE__) .'/';
$index_php = $current_dir .'index.php';

$i=0;
while( !file_exists( $index_php ) && $i < 9 )
{
	$current_dir = dirname(dirname($index_php)) .'/';
	$index_php = $current_dir .'index.php';
	$i++;
}

if( file_exists($index_php) )
{
	require_once $index_php;
}
else {
	print "Error. Dont require MODX."; die;
}
// << Подключаем


if( isset($_REQUEST['resource']) && $_REQUEST['resource'] != '' && !empty($_REQUEST['tv']) && !empty($_REQUEST['video']) )
{
	$response = $modx->runProcessor('gallery/handle', array(
			'resource'	=> $_REQUEST['resource'],
			'tv'		=> $_REQUEST['tv'],
			'video'		=> $_REQUEST['video'],
		),
		array('processors_path' => MODX_CORE_PATH .'components/videogallery/processors/mgr/')
	);
	
	print_r( $modx->toJSON( $response->response ) ); die;
}
else {
	print 'Access denied'; die;
}