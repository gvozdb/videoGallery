<?php
/*
	Плагин компонента videoGallery
	
	Служит для:
	1. перезалива картинок из временной папки в постоянную
	2. удаления старых изображений видеороликов, которые не используются
*/

switch($modx->event->name)
{
	/* >> Сохранение документа */
	case 'OnDocFormSave':
		
		if( !$resource = & $scriptProperties['resource'] ) {return;}
		
		if( !$videoGallery = $modx->getService('videoGallery', 'videoGallery', MODX_CORE_PATH .'components/videogallery/model/videogallery/') )
		{
			$modx->log(xPDO::LOG_LEVEL_ERROR, "Не был подгружен компонент videoGallery");
			return;
		}
		
		
		/* >> Правим картинки из папки "/0/" в папку "/res_id/" */
		$field['image'] = $modx->getOption('videogallery_field_image', null, '');
		
		$q = $modx->newQuery( 'modTemplateVarResource' );
		$q->innerJoin(
			'modTemplateVar',
			'modTemplateVar',
			'modTemplateVar.id = modTemplateVarResource.tmplvarid'
		);
		$q->select(
			array(
				'modTemplateVarResource.value as json',
				'modTemplateVarResource.tmplvarid as tv_id',
				'modTemplateVar.name as tv_name',
			)
		);
		$q->where( array(
			'modTemplateVar.type = "videoGallery"'.
			' AND '.
			'modTemplateVarResource.contentid = '. $resource->id .''.
			' AND '.
			'('.
				'modTemplateVarResource.value LIKE "%/0/%"'.
				' OR '.
				'modTemplateVarResource.value LIKE "%/0_/%"'.
			')'.
		''));
		$s = $q->prepare(); //print_r( $q->toSQL() );
		$s->execute();
		$rows = $s->fetchAll(PDO::FETCH_ASSOC);
		unset($q, $s);
		//print 'res'.$resource->id.' '; print_r( $rows ); return;
		
		foreach( $rows as $row )
		{
			$array = array();
			$array = $modx->fromJSON( $row['json'] );
			
			if( is_array($array) && isset($array['image']) )
			{
				$file_old		= $array['image'];
				$file_new		= str_replace( '/0/', '/'. $resource->id .'/', $array['image'] );
				$file_full_old	= str_replace( MODX_ASSETS_URL, MODX_ASSETS_PATH, $file_old );
				$file_full_new	= str_replace( MODX_ASSETS_URL, MODX_ASSETS_PATH, $file_new );
				
				$pathinfo_new	= pathinfo( $file_full_new );
				$dirname_new	= $pathinfo_new['dirname'] .'/';
				
				@mkdir($dirname_new, 0755, true);
				
				$copy = false;
				if( copy( $file_full_old, $file_full_new ) )
				{
					$copy = true;
					unlink( $file_full_old );
				}
				
				if( $copy )
				{
					$array['image'] = $file_new;
					
					$resource->setTVValue( $row['tv_name'], $modx->toJSON($array) );
					$resource->save();
					
					// >> Если в настройках указано поле для хранения изображений
					if( !empty($field['image']) )
					{
						if( stristr( $field['image'], 'tv.' ) )
						{
							$tv_name = str_replace( 'tv.', '', $field['image'] );
							$resource->setTVValue( $tv_name, $file_new );
						}
						else {
							$resource->set( $field['image'], $file_new );
						}
						
						$resource->save();
					}
					// << Если в настройках указано поле для хранения изображений
				}
			}
		}
		/* << Правим картинки из папки "/0/" в папку "/res_id/" */
		
		
		/* >> Удаляем ненужные картинки */
		$q = $modx->newQuery( 'modTemplateVarResource' );
		$q->innerJoin(
			'modTemplateVar',
			'modTemplateVar',
			'modTemplateVar.id = modTemplateVarResource.tmplvarid'
		);
		$q->select(
			array(
				'modTemplateVarResource.value as json',
			)
		);
		$q->where( array(
			'modTemplateVar.type = "videoGallery"'.
			' AND '.
			'modTemplateVarResource.contentid = '. $resource->id .''.
		''));
		$s = $q->prepare(); //print_r( $q->toSQL() ); die;
		$s->execute();
		$rows = $s->fetchAll(PDO::FETCH_ASSOC);
		unset($q, $s);
		//print_r( $rows ); die;
		if( !is_array($rows) || !count($rows) ) { return; }
		
		foreach( $rows as $row )
		{
			$array = array();
			$array = $modx->fromJSON( $row['json'] );
			
			if( is_array($array) && isset($array['image']) )
			{
				$pathinfo = pathinfo( $array['image'] );
				$dirname = str_replace( MODX_ASSETS_URL, MODX_ASSETS_PATH, $pathinfo['dirname'] );
				
				$videoGallery->remove_files_from_folder( $dirname, $pathinfo['basename'] ); // удаляем старые картинки
				
				//$modx->log(xPDO::LOG_LEVEL_ERROR, print_r($pathinfo, true) );
			}
		}
		/* << Удаляем ненужные картинки */
		
		
	break;
	/* << Сохранение документа */
}