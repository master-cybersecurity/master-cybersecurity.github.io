<?php
/*------------------------------------------------------------------------
# mod_drilldown_menu - VTEM DrillDown Module
# ------------------------------------------------------------------------
# author Nguyen Van Tuyen
# copyright Copyright (C) 2010 VTEM.NET. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.vtem.net
# Technical Support: Forum - http://vtem.net/en/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;
if(!defined('DS')){define('DS',DIRECTORY_SEPARATOR);}
require_once dirname(__FILE__).'/helper.php';
$app	= JFactory::getApplication();
$menu	= $app->getMenu();
$active	= $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path	= isset($active) ? $active->tree : array();
$showAll	= $params->get('showAllChildren', 1);
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

$slideID = 'vtemdrilldownid'.$module->id;
$width = $params->get('width', '100%');
$showSpeed = $params->get('showSpeed', 500);
$defaultText = $params->get('defaultText', 'Select Option');
$linkType = $params->get('linkType', 'breadcrumb');
$backLinkText = $params->get('backLinkText','Back');
$topLinkText = $params->get('topLinkText','All');
$showStick = $params->get('showStick',0);
$stickLabel = $params->get('stickLabel', 'You chose');
$saveState = $params->get('saveState',1);
$showCount = $params->get('showCount',1);
$modstyle = $params->get('modstyle', 0);
$vtemstyle = $params->get('vtemstyle',1);

$background = $params->get('background','#555');
$textcolor = $params->get('textcolor','#ccc');
$hover_background = $params->get('hover_background','#f90');
$hover_textcolor = $params->get('hover_textcolor','#fff');
$jquery = $params->get('jquery', 2);
$css = $params->get('css');



$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root().'modules/mod_vtem_drilldown_menu/asset/default.css');
$doc->addStyleDeclaration('.modstyle1.wrap-'.$slideID.' .oDrill-wrapper{background-color:'.$background.'; color:'.$textcolor.'} .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-menu a, .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-header li a, .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-menu a .oDrill-icon, .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-header.oDrill-backlink a .oDrill-icon{color:'.$textcolor.';}.modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-menu a .oDrill-icon, .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-header.oDrill-backlink a .oDrill-icon{border-color:'.$textcolor.';}.modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-menu a:hover, .modstyle1.wrap-'.$slideID.' .oDrill-wrapper .oDrill-menu li.current a{background-color:'.$hover_background.'!important; color:'.$hover_textcolor.'!important}'.$css);

if($jquery == 2){
   $jversion = new JVersion;
   if (version_compare($jversion->getShortVersion(), '3.0.0', '<'))
   	$doc->addScript(JURI::root().'modules/mod_vtem_drilldown_menu/asset/jquery-1.7.2.min.js');
}elseif($jquery == 1)
	$doc->addScript(JURI::root().'modules/mod_vtem_drilldown_menu/asset/jquery-1.7.2.min.js');

// Output
$module_usage = $params->get('module_usage',0);
switch ($module_usage) {
   case '0':
    $list	= modDrillDownMenuHelper::getList($params);
    require(JModuleHelper::getLayoutPath('mod_vtem_drilldown_menu', 'default'));
    break;
  case '1':
    $output = modDrillDownMenuHelper::treerecurse($params, 0, 0, true);
    require (JModuleHelper::getLayoutPath('mod_vtem_drilldown_menu', 'categories'));
    break;    
}
