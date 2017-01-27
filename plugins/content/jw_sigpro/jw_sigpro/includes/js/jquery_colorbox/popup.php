<?php
/**
 * @version		2.6.0
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'colorbox';
$extraClass = ' sigProColorbox';

$stylesheets = array('example1/colorbox.css');
$stylesheetDeclarations = array();
$scripts = array(
	'colorbox/jquery.colorbox-min.js'
);
$scriptDeclarations = array('
	//<![CDATA[
	jQuery.noConflict();
	jQuery(document).ready(function(){
		jQuery(".sigProColorbox").colorbox({
			transition:"fade",
			contentCurrent:"Image {current} of {total}",
			bgOpacity:"0.9",
			//slideshow:true,
			//width:"75%",
			//height:"75%"
		});
	});
	//]]>
');
