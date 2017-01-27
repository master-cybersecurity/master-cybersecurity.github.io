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

// Include the syndicate functions only once
require_once(dirname(__FILE__).DS.'helper.php');

// get a parameter from the module's configuration

$folder	= modRandomImageWithLightBox::getFolder($params);
$images	= modRandomImageWithLightBox::getImages($params, $folder);

if (!count($images)) 
{
	echo JText::_('NO IMAGES ' . $folder . '<br><br>');
	return;
}

require JModuleHelper::getLayoutPath('mod_random_image_with_light_box', $params->get('layout', 'default'));
modRandomImageWithLightBox::loadScripts($params);
?>