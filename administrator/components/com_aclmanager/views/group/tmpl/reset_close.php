<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// close a modal window
JFactory::getDocument()->addScriptDeclaration('
	window.parent.location.href=window.parent.location.href;
	window.parent.SqueezeBox.close();
');
?>