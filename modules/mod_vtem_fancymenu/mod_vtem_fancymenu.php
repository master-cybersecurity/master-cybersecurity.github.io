<?php
/**
* @Copyright Copyright (C) 2010 VTEM . All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @link     	http://www.vtem.net
**/
// no direct access
defined('_JEXEC') or die;
if(!defined('DS')){define('DS',DIRECTORY_SEPARATOR);}
$module_id = 'fancyid'.$module->id;
$modwidth = $params->get('modwidth', '200px');
$modposition = $params->get('modposition', 'left');
$mouseEvent = $params->get('mouseEvent', 'hover');
$duration = $params->get('duration', 500);
$transition = $params->get('transition', 'bounce');
$transitionEase = $params->get('transitionEase', 'easeInQuad');
$sub_width = $params->get('sub_width', '200px');
$background = $params->get('background', '#333');
$textcolor = $params->get('textcolor', '#ccc');
$sub_background = $params->get('sub_background', '#eee');
$sub_textcolor = $params->get('sub_textcolor', '#555');
$headertext = $params->get('headertext', 'Panel');
$jquery = $params->get('jquery', '1');
$css = $params->get('css');

$document = JFactory::getDocument();
if($jquery ==1){
   $jversion = new JVersion;
   if (version_compare($jversion->getShortVersion(), '3.0.0', '<'))
   	$document->addScript(JURI::root(true).'/modules/mod_vtem_fancymenu/styles/jquery-1.7.2.min.js');
}elseif($jquery ==2){
   $document->addScript(JURI::root(true).'/modules/mod_vtem_fancymenu/styles/jquery-1.7.2.min.js');
}
$document->addStyleSheet(JURI::base() . 'modules/mod_vtem_fancymenu/styles/styles.css');
$customstyle='#fancyid'.$module->id.', #fancyid'.$module->id.' > li > a,.metro-menu-wrapper > .menu-stick{background:'.$background.'; color:'.$textcolor.';} #fancyid'.$module->id.' .sub-container-wrap{background:'.$sub_background.'; color:'.$sub_textcolor.';}#fancyid'.$module->id.' .sub-container-wrap .vt_menu_sub > li > a{color:'.$sub_textcolor.';}'
.$css;
$document->addStyleDeclaration($customstyle);

require_once (dirname(__FILE__).DS.'helper.php');
$items = modVtemMenuHelper::GetMenu($params);
require JModuleHelper::getLayoutPath('mod_vtem_fancymenu');