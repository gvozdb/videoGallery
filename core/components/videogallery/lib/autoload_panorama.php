<?php

function panorama_lib_autoload( $path )
{
    $path = explode("\\", $path);
	
	if( count($path) == 1 ) { return; }
	
	if( $path[0] == "Panorama" )
	{
        require_once realpath(__DIR__.'/'.implode('/', $path).'.php');
    }
}

spl_autoload_register("panorama_lib_autoload");
