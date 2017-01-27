<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

?>

<h3><?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></h3>
<dl class="uk-description-list uk-description-list-horizontal">
	<dt><strong><?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>:</strong></dt><dd><?php echo $this->data->name; ?></dd>
	<dt><strong><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>:</strong></dt><dd><?php echo htmlspecialchars($this->data->username); ?></dd>
	<dt><strong><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>:</strong></dt><dd><?php echo JHtml::_('date', $this->data->registerDate); ?></dd>
	<dt><strong><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>:</strong></dt><dd><?php echo ($this->data->lastvisitDate != '0000-00-00 00:00:00' ? JHtml::_('date', $this->data->lastvisitDate) : JText::_('COM_USERS_PROFILE_NEVER_VISITED')); ?></dd>
</dl>