<?php
/**
 * Random image with light box
 *
 * @package Random image with light box
 * @subpackage Random image with light box
 * @version   2.0 February, 2012
 * @author    Gopi http://www.gopiplus.com
 * @copyright Copyright (C) 2010 - 2012 www.gopiplus.com, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die;

if ( ! empty($images) ) 
{
	$width	= $params->get('width');
	
	$LiveSite = JURI::base() . "modules/mod_random_image_with_light_box/";
	
	$pickoneArray = array();
	$pickone = rand(0, count($images)-1);
	
	$pickoneArray[0]->name = $images[$pickone]->name;
	$pickoneArray[0]->folder = $images[$pickone]->folder . "\\";
	
	$folderlink = str_replace('\\','/', $pickoneArray[0]->folder);
	
	echo '<div>';
	echo '<a href="'.JURI::base().$folderlink . $pickoneArray[0]->name .'" rel="lightbox">';
	echo '<img src="'.$LiveSite.'crop-random-image.php?AC=YES&DIR='.$pickoneArray[0]->folder.'&IMGNAME='.$pickoneArray[0]->name.'&MAXWIDTH='.$width.'"> ';
	echo '</a>';
	echo '</div>';
}
?>