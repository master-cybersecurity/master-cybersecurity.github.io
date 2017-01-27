<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewWafconfig extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		// Set the toolbar title
		/** @var AdmintoolsModelWafconfig $model */
		$model = $this->getModel();
		$config = $model->getConfig();

		$this->assign('wafconfig',			$config);
	}
}