<?php

/**
* @copyright	Copyright (C) 2012 JoomSpirit. All rights reserved.
* Slideshow based on the JQuery script Flexslider
* @license		GNU General Public License version 2 or later
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');

$target	= $params->get('target');

class mod_js_flexsliderHelper{
	
	public static function getImages(&$params){
		
		$imgsAndCaps = array(); 
		
		$i = 1;
		
		while($i < 22) :
		
			
			// if image + caption + link
			if( ($params->get('image'.$i)) && ($params->get('image'.$i.'cap')) && ($params->get('image'.$i.'customlink') != '') ) {
				
					$listitem = "<li><a target='".$params->get('target')."' href='".$params->get('image'.$i.'customlink')."'><img title='' alt='".$params->get('image'.$i.'alt')."' src='".JURI::root().$params->get('image'.$i)."' /><span class='flex-caption' style='color:".$params->get('color_caption')."'>".$params->get('image'.$i.'cap')."</span></a></li>";
					array_push($imgsAndCaps, $listitem);
				
			
			// if image + no caption + link
			} else if ( ($params->get('image'.$i)) && ($params->get('image'.$i.'cap') == null) && ($params->get('image'.$i.'customlink') != '') ) {
				
					$listitem = "<li><a target='".$params->get('target')."' href='".$params->get('image'.$i.'customlink')."'><img title='' alt='".$params->get('image'.$i.'alt')."' src='".JURI::root().$params->get('image'.$i)."' /></a></li>";
					array_push($imgsAndCaps, $listitem);
				

			// if image + caption + no link
			} else if ( ($params->get('image'.$i)) && ($params->get('image'.$i.'cap')) && ($params->get('image'.$i.'customlink') == '') ) {
				
					$listitem = "<li><img title='' alt='".$params->get('image'.$i.'alt')."' src='".JURI::root().$params->get('image'.$i)."' /><span class='flex-caption' style='color:".$params->get('color_caption')."'>".$params->get('image'.$i.'cap')."</span></li>";
					array_push($imgsAndCaps, $listitem);
				
			
			// if image + no caption + no link
			} else if ( ($params->get('image'.$i)) && ($params->get('image'.$i.'cap') == null) && ($params->get('image'.$i.'customlink') == '') ) {
				
					$listitem = "<li><img title='' alt='".$params->get('image'.$i.'alt')."' src='".JURI::root().$params->get('image'.$i)."' /></li>";
					array_push($imgsAndCaps, $listitem);
			}		
		
		$i++;
		
		endwhile;
		
		return $imgsAndCaps;
		
	}
	
	public static function getImagesFromFolder(&$params) { 
		$imgsAndCaps = array(); 
		$i=0;
		$imageFolder = JPATH_BASE.'/images/'.$params->get('imagefolder');

		$filesFromFolder = JFolder::files($imageFolder, '.', false, false, array('.db','.svn', 'CVS','.DS_Store','__MACOSX','index.html'));
		
		foreach ($filesFromFolder as $image) { 
			$i++;
			$imgpath = JURI::root().'images/'.$params->get('imagefolder').'/'.$image;
					
			$listitem = "<li><img alt='' src='".$imgpath."' /></li>";
			array_push($imgsAndCaps, $listitem);
						
		}
		
		return $imgsAndCaps;
	}
	
	public static function load_jquery(&$params){
		$doc = JFactory::getDocument();
		
		if ($params->get('load_jquery')) {
			$doc->addScript(JURI::root(true).'/modules/mod_js_flexslider/assets/js/jquery-1.8.2.min.js');
			$doc->addScript(JURI::root(true).'/modules/mod_js_flexslider/assets/js/noconflict.js');
		}
		
		$doc->addScript(JURI::root(true).'/modules/mod_js_flexslider/assets/js/jquery.easing.js');
		
					
	}
	
}