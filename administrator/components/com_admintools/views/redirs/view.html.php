<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewRedirs extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		// Add toolbar buttons
		JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_admintools');

		$model = $this->getModel();
		$urlredirection = $model->getRedirectionState();
		$this->assign('urlredirection',$urlredirection);

		$this->loadHelper('select');

		// Run the parent method
		parent::onDisplay();
	}
}