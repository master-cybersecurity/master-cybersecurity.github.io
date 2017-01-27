<?php

/**
* @copyright	Copyright (C) 2012 JoomSpirit. All rights reserved.
* Slideshow based on the JQuery script Flexslider
* @license		GNU General Public License version 2 or later
*/

defined('_JEXEC') or die;


//add stylesheet

$doc = JFactory::getDocument();


//include the class of the syndicate functions only once

require_once(dirname(__FILE__).'/helper.php');

$module_id	= $module->id;

//keeps module class suffix even if templateer tries to stop it

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$listofimages = mod_js_flexsliderHelper::getImages($params);

if ($params->get('imagefolder') != '-1' ) {
	$folderimages = mod_js_flexsliderHelper::getImagesFromFolder($params);
	
	foreach ($folderimages as $img)
		array_push($listofimages , $img);
}

mod_js_flexsliderHelper::load_jquery($params);

$doc->addStyleSheet(JURI::base(true) . '/modules/mod_js_flexslider/assets/css/flexslider.css', 'text/css' ); 	// original Flexslider CSS
$doc->addStyleSheet(JURI::base(true) . '/modules/mod_js_flexslider/assets/css/style.css', 'text/css' );			// CSS style of JoomSpirit

// Get basic parameters
$slide_theme				= $params->get('slide_theme');

$enable_minheight			= $params->get('enable_minheight');
$min_height					= $params->get('min_height');

$bg_color					= $params->get('bg_color');
$theme_shadow				= $params->get('theme_shadow');
$theme_border				= $params->get('theme_border');
$theme_border_radius		= $params->get('theme_border_radius');

$transition					= $params->get('transition');
$easing						= $params->get('easing');
$direction					= $params->get('direction');

$animSpeed					= intval($params->get('animSpeed'));
$pauseTime					= intval($params->get('pauseTime'));

$controlNav					= $params->get('controlNav');
$positionNav				= $params->get('positionNav');
$colorNav					= $params->get('colorNav');
$colorNavActive				= $params->get('colorNavActive');

$directionNav				= $params->get('directionNav');

$pauseOnHover				= $params->get('pauseOnHover');
$initDelay					= $params->get('initDelay');
$randomize					= $params->get('randomize');

$bg_caption					= $params->get('bg_caption');
$transparency_caption		= $params->get('transparency_caption');
$position_caption			= $params->get('position_caption');
$text_align					= $params->get('text_align');
$color_caption				= $params->get('color_caption');


require(JModuleHelper::getLayoutPath('mod_js_flexslider'));