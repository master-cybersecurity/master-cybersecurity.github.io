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
// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
} 
else { $linktype = $item->title;
}

?><span class="separator"><?php echo $title; ?><?php echo $linktype; ?></span>
