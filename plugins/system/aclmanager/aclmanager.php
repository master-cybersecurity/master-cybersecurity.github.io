<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemAclmanager extends JPlugin
{	
	function onAfterRoute() {
		$app 						= JFactory::getApplication();
		$user 						= JFactory::getUser();
		$option						= JRequest::getCmd('option'); 
		$component					= JRequest::getCmd('component');
		$view						= JRequest::getCmd('view');
		$params 					= JComponentHelper::getParams('com_aclmanager');
		$acl_categorymanager 		= $params->get('acl_categorymanager',1);
		$acl_onlyallowed 			= $params->get('acl_onlyallowed',1);
		$acl_modules 				= $params->get('acl_modules',1);
		$acl_modules_only_editable	= $params->get('acl_modules_only_editable',1);
		
		if($app->isAdmin()) {
			// Joomla version
			if (version_compare(JVERSION, '3.2', 'ge')) {
				$jversion = '32';
			} elseif (version_compare(JVERSION, '3.1', 'ge')) {
				$jversion = '31';
			} elseif (version_compare(JVERSION, '2.5', 'ge')) {
				$jversion = '25';
			}
			
			// Only show editable articles if set
			if($acl_onlyallowed && ($option == 'com_content')) {
				if(($view == 'articles') || (!$view)) {
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_content/models/articles.php';
				} elseif($view == 'featured') {
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_content/models/featured.php';
				}
			}
			
			// Add ACL to Module Manager if set
			if(($jversion == '25') || ($jversion == '31')) {
				if($acl_modules && ($option == 'com_modules')) {	
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_modules/controllers/module.php';
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_modules/models/module.php';
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_modules/views/module/view.html.php';
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_modules/views/modules/view.html.php';
				}
			}
			
			// Only show editable modules if set
			if($acl_modules_only_editable  && ($option == 'com_modules')) {
				if(($view == 'modules') || (!$view)) {
					include_once dirname(__FILE__).'/overrides/'.$jversion.'/com_modules/models/modules.php';
				}
			}
		
			
			// Access check for extensions without ACL support
			if($acl_categorymanager) {
				$corecomponents = array('com_admin','com_config','com_cpanel','com_login','com_mailto','com_massmail','com_wrapper');
			} else {
				$corecomponents = array('com_admin','com_config','com_categories','com_cpanel','com_login','com_mailto','com_massmail','com_wrapper');
			}
			if ((in_array($option, $corecomponents)) || empty($option)) {
				$core = true;
			} else {
				$core = false;
			}
			
			// Check for ACL Support
			$extensionfolder = is_dir(JPATH_ADMINISTRATOR . '/components/'.$option);
			$accessfile = JPATH_ADMINISTRATOR.'/components/'.$option.'/access.xml';
			$configfile = JPATH_ADMINISTRATOR.'/components/'.$option.'/config.xml';
			$permissions = false;
			
			// Check if extension has ACL support
			if(!$core) {
				if((is_file($accessfile)) && (is_file($configfile))) {
					$permissions = true;
				} elseif (is_file($configfile)) {
					$xml = simplexml_load_file($configfile);
					foreach($xml->children()->fieldset as $fieldset)
					{
						if ('permissions' == (string) $fieldset['name']) {
							$permissions = true;
						} 
					}
				} 
			}
			
			// Check if user has access
			if($user->id && $extensionfolder && !$permissions && !$core) {
				if (!JFactory::getUser()->authorise('core.manage', $option)) {
					JRequest::setVar('option', 'com_aclmanager');
					JRequest::setVar('view', 'notauthorised' );
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
			
			// Load ACL Manager language files for fallback in options
			if(($option == 'com_config') && ($component == 'com_aclmanager')) {
				$jlang = JFactory::getLanguage();
				$jlang->load('com_aclmanager', JPATH_ADMINISTRATOR, 'en-GB', true);
				$jlang->load('com_aclmanager', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
				$jlang->load('com_aclmanager', JPATH_ADMINISTRATOR, null, true);
			}
		}
    }
	
	function onAfterRender()
	{
		$params 				= JComponentHelper::getParams('com_aclmanager');
		$acl_categorymanager 	= $params->get('acl_categorymanager',1);

		if (($acl_categorymanager) && (!JFactory::getUser()->authorise('core.manage', 'com_categories'))) {
			$output = JResponse::getBody();
			$output = preg_replace("/<a.*?com_categories.*?>(.*?)<\/a>/","",$output);
			JResponse::setBody($output);
			return true;
		}
	}
}