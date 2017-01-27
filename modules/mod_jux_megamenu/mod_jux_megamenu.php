<?php
/**
 * @version		$Id$
 * @author		JoomlaUX!
 * @package		Joomla.Site
 * @subpackage	mod_jux_megamenu
 * @copyright	Copyright (C) 2008 - 2013 by JoomlaUX. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL version 3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).'/helper.php');

// Change DEMO_MODE value to 1 to enable the demo mode.

$menutype 	= $params->get('menutype', 'mainmenu');

$responsive	= $params->get('responsive_menu',	'1');
$layout		= $params->get('layout', 'default');

$menuStyle			= 'megamenu';
$menuOrientation	= $params->get('hozorver', 'horizontal');
$menuAlignment		= 'left';
if($menuOrientation == 'horizontal') {
    $menuAlignment  = $params->get('horizontal_menustyle', 'left');
} else {
    $menuAlignment	= $params->get('vertical_submenu_direction', 'lefttoright') == 'lefttoright' ? 'left' : 'right';
}

$menuStyle	.= " $menuOrientation $menuAlignment $layout";

$document	= JFactory::getDocument();

$document->addStyleSheet(JUri::base(true).'/modules/mod_jux_megamenu/assets/css/style.css');
$document->addStyleSheet(JUri::base(true).'/modules/mod_jux_megamenu/assets/css/style/'.$layout.'.css');
// css for IE
if(preg_match('/(?i)msie [7-8]/',$_SERVER['HTTP_USER_AGENT'])) {
    // if IE<=8
    $document->addStyleSheet(JUri::base(true).'/modules/mod_jux_megamenu/assets/css/ie8.css');
}
if($responsive){
    $document->addStyleSheet(JUri::base(true).'/modules/mod_jux_megamenu/assets/css/style_responsive.css');
}

// if use CSS3 only, disable mootools and mega menu script
if ($params->get('css3_noJS', 0)) {
	$menuStyle	.= ' noJS';
} else { // Use mootools libraries and enable mega menu script
	JHTML::_('behavior.framework', true);
	JHTML::_('behavior.tooltip', true);
	$document->addScript(JUri::base(true).'/modules/mod_jux_megamenu/assets/js/HoverIntent.js');
	$document->addScript(JUri::base(true).'/modules/mod_jux_megamenu/assets/js/script.js');
}

$dropdownmenu    = new Mod_JUX_MegaMenu($params);
require(JModuleHelper::getLayoutPath('mod_jux_megamenu'));